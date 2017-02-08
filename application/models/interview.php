<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interview extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    function load_interview_notes($interview_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'notes' => '');

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No interview found.";
            return $ret_value;
        }

        $sql = "SELECT `interview`.`notes`";
        $sql = $sql." FROM `interview`";
        $sql = $sql." WHERE `interview`.`id`=".$this->db->escape($interview_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0)
        {
            $row = $query->result()[0];
            $ret_value['notes'] = $row->notes;
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Load Full Interview
     * 
     * @param int $interview_id
     * @return array
     */
    function load_full_interview($interview_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'interview' => array());

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No interview found.";
            return $ret_value;
        }

        $sql = "SELECT `interview`.`id`, `interview`.`notes`, `interview`.`dob`, `interview`.`location`, `interview`.`user_id`, `user`.email, `interview`.`name`, `interview`.`description`,";
        $sql = $sql." `interview`.`title`, `interview`.`employer`, `interview`.`street`, `interview`.`city`, `interview`.`state`, `interview`.`zip`, `interview`.`phone`,";
        $sql = $sql." `interview`.`date_occured`, `interview`.`modified`, `is_approved`, `supervisor_id`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `interview`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `interview`.`user_id`";
        $sql = $sql." LEFT JOIN `user` ON `user`.`id` = `interview`.`user_id`";
        $sql = $sql." WHERE `interview`.`id`=".$this->db->escape($interview_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0)
        {
            $int = array('id' => 0, 'user_id' => '', 'notes' => '', 'dob' => '', 'location' => '', 'name' => '', 'title' => '', 'emp' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'phone' => '', 'description' => '', 'date_occured' => '', 'modified' => '', 'is_approved' => 0, 'supervisor_id' => '', 'user_name' => '', 'email' => '', 'attachments' => array());

            $row = $query->result()[0];

            $int['id'] = $row->id;
            $int['user_id'] = $row->user_id;
            $int['name'] = $row->name;
            $int['description'] = $row->description;
            $int['date_occured'] = date('m/d/Y', strtotime($row->date_occured));
            $int['modified'] = date('m/d/Y', strtotime($row->modified));
            $int['is_approved'] = $row->is_approved;
            $int['supervisor_id'] = $row->supervisor_id;
            $int['user_name'] = $row->user_name;
            $int['email'] = $row->email;
            $int['phone'] = $row->phone;
            $int['title'] = $row->title;
            $int['emp'] = $row->employer;
            $int['street'] = $row->street;
            $int['city'] = $row->city;
            $int['state'] = $row->state;
            $int['zip'] = $row->zip;
            if(isset($row->notes)) {
                $int['notes'] = $row->notes;
            }
            if(isset($row->dob)) {
                $int['dob'] = $row->dob;
            }
            if(isset($row->location)) {
                $int['location'] = $row->location;
            }
            
            // load attachments
            $att_result = $this->load_interview_attachments($interview_id);
            if($att_result['result']) {
                $int['attachments'] = $att_result['docs'];
            }

            // add to array
            $ret_value['result'] = TRUE;
            $ret_value['interview'] = $int;

        }

        return $ret_value;
    }

    /**
     * Load Interviews By User
     * 
     * @param int $case_id
     * @return array
     */
    function load_interviews_user($user_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'interviews' => array());

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        $sql = "SELECT `interview`.`id`, `interview`.`case_id`, `interview`.`notes`, `interview`.`dob`, `interview`.`location`, `interview`.`user_id`, `interview`.`lead_entry_id`, `interview`.`name`, `interview`.`description`,";
        $sql = $sql." `interview`.`title`, `interview`.`employer`, `interview`.`street`, `interview`.`city`, `interview`.`state`, `interview`.`zip`, `interview`.`phone`,";
        $sql = $sql." `date_occured`, `interview`.`modified`, `is_approved`, `supervisor_id`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `interview`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `interview`.`user_id`";
        $sql = $sql." WHERE `interview`.`user_id`=".$this->db->escape($user_id);

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {

                $int = array('id' => 0, 'user_id' => '', 'notes' => '', 'dob' => '', 'name' => '', 'description' => '', 'date_occured' => '', 'modified' => '', 'is_approved' => 0, 'supervisor_id' => '', 'user_name' => '', 'title' => '', 'emp' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'phone' => '', 'description' => '', 'date_occured' => '', 'modified' => '', 'is_approved' => 0, 'supervisor_id' => '', 'user_name' => '');
                
                $int['id'] = $row->id;
                $int['user_id'] = $row->user_id;
                $int['name'] = $row->name;
                $int['description'] = $row->description;
                $int['date_occured'] = date('m/d/Y', strtotime($row->date_occured));
                $int['modified'] = date('m/d/Y', strtotime($row->modified));
                $int['is_approved'] = $row->is_approved;
                $int['supervisor_id'] = $row->supervisor_id;
                $int['user_name'] = $row->user_name;
                $int['phone'] = $row->phone;
                $int['title'] = $row->title;
                $int['emp'] = $row->employer;
                $int['street'] = $row->street;
                $int['city'] = $row->city;
                $int['state'] = $row->state;
                $int['zip'] = $row->zip;
                if(isset($row->notes)) {
                    $int['notes'] = $row->notes;
                }
                if(isset($row->dob)) {
                    $int['dob'] = $row->dob;
                }
                if(isset($row->location)) {
                    $int['location'] = $row->location;
                }

                $ret_value['interviews'][] = $int;
            }

        }

        return $ret_value;
    }

    /**
     * Load Interviews
     * 
     * @param int $case_id
     * @return array
     */
    function load_interviews($case_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'interviews' => array());

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        $sql = "SELECT `interview`.`id`, `interview`.`case_id`, `interview`.`user_id`, `interview`.`lead_entry_id`, `interview`.`name`, `interview`.`description`,";
        $sql = $sql." `date_occured`, `interview`.`modified`, `is_approved`, `supervisor_id`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `interview`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `interview`.`user_id`";
        $sql = $sql." WHERE LOWER(MD5(`interview`.`case_id`))=".strtolower($this->db->escape($case_id));

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {

                $int = array('id' => 0, 'user_id' => '', 'name' => '', 'description' => '', 'date_occured' => '', 'modified' => '', 'is_approved' => 0, 'supervisor_id' => '', 'user_name' => '');
                
                $int['id'] = $row->id;
                $int['user_id'] = $row->user_id;
                $int['name'] = $row->name;
                $int['description'] = $row->description;
                $int['date_occured'] = date('m/d/Y', strtotime($row->date_occured));
                $int['modified'] = date('m/d/Y', strtotime($row->modified));
                $int['is_approved'] = $row->is_approved;
                $int['supervisor_id'] = $row->supervisor_id;
                $int['user_name'] = $row->user_name;

                $ret_value['interviews'][] = $int;
            }

        }

        return $ret_value;
    }

    /**
     * Add a new interview
     * 
     * @param int $case_id
     * @param int $user_id
     * @param string $name
     * @param string $description
     * @param string $date_occured
     * @return array
     */
    function add_new_interview($case_id, $user_id, $name, $description, $date_occured, $title="", $employer="", $street="", $city="", $state="", $zip="", $phone="",$dob="",$location="")
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if(!isset($case_id) || $case_id == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        if(!isset($name) || strlen($name) == 0) {
            $ret_value['message'] = "Interview name required.";
            return $ret_value;
        }

        if(!isset($date_occured) || strlen($date_occured) == 0) {
            $date_occured = date('Y-m-d');
        }else{
            $date_occured = date('Y-m-d', strtotime($date_occured));
        }

        $sql = "INSERT INTO `interview` (`case_id`, `user_id`, `name`, `description`, `date_occured`, `title`, `employer`, `street`, `city`, `state`, `zip`, `phone`, `dob`, `location`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($name).",".$this->db->escape($description).",";
        $sql = $sql.$this->db->escape($date_occured).",".$this->db->escape($title).",".$this->db->escape($employer).",".$this->db->escape($street).",".$this->db->escape($city).",".$this->db->escape($state).",".$this->db->escape($zip).",".$this->db->escape($phone).",".$this->db->escape($dob).",".$this->db->escape($location);
        $sql = $sql." );";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $id = $this->db->insert_id();
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id;
        }

        return $ret_value;
    }

    /**
     * Approve Interview
     * 
     * @param int $interview_id
     * @param int $supervisor_id
     * @return array
     */
    function approve_interview($interview_id, $supervisor_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        $sql = "UPDATE `interview` SET `is_approved`=1,`supervisor_id`=".$this->db->escape($supervisor_id)." WHERE `id` =".$this->db->escape($interview_id).";";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    function edit_interview_notes($interview_id, $notes)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        $sql = "UPDATE `interview` SET";
        $sql = $sql."`notes`=".$this->db->escape($notes);
        $sql = $sql." WHERE `id` =".$this->db->escape($interview_id).";";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Edit Interview
     * 
     * @param int $interview_id
     * @param string $name
     * @param string $description
     * @return array
     */
    function edit_interview($interview_id, $name, $description, $date_occured, $title="", $employer="", $street="", $city="", $state="", $zip="", $phone="",$dob="",$location="")
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($date_occured) || strlen($date_occured) == 0) {
            $date_occured = date('Y-m-d');
        }else{
            $date_occured = date('Y-m-d', strtotime($date_occured));
        }

        $sql = "UPDATE `interview` SET";
        $sql = $sql."`name`=".$this->db->escape($name);
        $sql = $sql.",`description`=".$this->db->escape($description);
        $sql = $sql.",`date_occured`=".$this->db->escape($date_occured);

        $sql = $sql.",`title`=".$this->db->escape($title);
        $sql = $sql.",`employer`=".$this->db->escape($employer);
        $sql = $sql.",`street`=".$this->db->escape($street);
        $sql = $sql.",`city`=".$this->db->escape($city);
        $sql = $sql.",`state`=".$this->db->escape($state);
        $sql = $sql.",`zip`=".$this->db->escape($zip);
        $sql = $sql.",`phone`=".$this->db->escape($phone);
        $sql = $sql.",`dob`=".$this->db->escape($dob);
        $sql = $sql.",`location`=".$this->db->escape($location);

        $sql = $sql." WHERE `id` =".$this->db->escape($interview_id).";";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Delete Interview
     * 
     * @param int $interview_id
     * @param int $supervisor_id
     * @return array
     */
    function delete_interview($interview_id, $supervisor_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($supervisor_id) || $supervisor_id == 0) {
            $ret_value['message'] = "No supervisor found.";
            return $ret_value;
        }

        $sql = "DELETE FROM `interview`";
        $sql = $sql." WHERE `id` =".$this->db->escape($interview_id);
        $sql = $sql." AND `supervisor_id`=".$this->db->escape($supervisor_id).";";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    // private load attachments by interview
    private function load_interview_attachments($interview_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array());

        if(!isset($interview_id) || $interview_id == 0) {
            $ret_value['message'] = "No interview found.";
            return $ret_value;
        }

        $sql = "SELECT `attachments`.`id`, `attachments`.`name` AS att_name, `type`, `size`, `location`, `attachments`.`created`, `attachments`.`title`, `tags`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `attachments`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `attachments`.`user_id`";
        $sql = $sql." WHERE `attachments`.`interview_id` = ".$this->db->escape($interview_id);
        $sql = $sql." ORDER BY `attachments`.`name` ASC;";
        
        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row_count = 1;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'number' => '', 'icon' => '', 'postfix' => '', 'url' => '', 'name' => '', 'type' => '', 'size' => '', 'location' => '', 'title' => '', 'tags' => '', 'username' => '', 'created' => '');

                $acronym = "";
                $user_words = preg_split("/\s+/", $row->user_name);
                if (count($user_words) > 0) {
                    foreach ($user_words as $w) {
                      $acronym .= $w[0];
                    }
                }

                $number = $row_count;
                if($row_count < 10) {
                    $number = "0".$row_count;
                }
                
                $doc['id'] = $row->id;
                $doc['number'] = $acronym."-".$number;
                $doc['name'] = $row->att_name;
                $doc['type'] = $row->type;
                $doc['size'] = $row->size;
                $doc['location'] = $row->location;
                $doc['title'] = $row->title;
                $doc['tags'] = $row->tags;
                $doc['username'] = $row->user_name;
                $doc['created'] = date('m/d/Y', strtotime($row->created));

                // get url
                $url = '';
                $path_parts = explode('/', $row->location);
                if (count($path_parts) > 0) {
                    // TODO: pull from S3
                    $url = base_url('docs')."/".$path_parts[(count($path_parts)-1)];
                }
                $doc['url'] = $url;

                // get icon image
                $icon = '';
                $postfix = '';
                switch ($row->type) {
                    case 1: $icon = base_url("img/icons/powerpoint.png"); $postfix="PowerPoint"; break;
                    case 2: $icon = base_url("img/icons/word.png"); $postfix="Word"; break;
                    case 3: $icon = base_url("img/icons/excel.png"); $postfix="Excel"; break;
                    case 4: $icon = base_url("img/icons/pdf.png"); $postfix="PDF"; break;
                    case 5: $icon = base_url("img/icons/video.png"); $postfix="MP4"; break;
                    case 6: $icon = base_url("img/icons/video.png"); $postfix="Webm"; break;
                    case 7: $icon = base_url("img/icons/video.png"); $postfix="OGV"; break;
                    case 8: $icon = base_url("img/icons/video.png"); $postfix="MP3"; break;
                    case 9: $icon = base_url("img/icons/image.png"); $postfix="JPG"; break;
                    case 10: $icon = base_url("img/icons/image.png"); $postfix="PNG"; break;
                    case 11: $icon = base_url("img/icons/text.png"); $postfix="TXT"; break;
                }

                $doc['icon'] = $icon;
                $doc['postfix'] = $postfix;

                $ret_value['docs'][] = $doc;

                $row_count = $row_count + 1;
            }

        }

        return $ret_value;
    }

}