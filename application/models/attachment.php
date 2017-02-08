<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attachment extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a new document
     * 
     * @param int $case_id
     * @param int $user_id
     * @param int $interview_id
     * @param string $path_to_save
     * @param base64 $file_contents
     * @param int $size
     * @param string $type
     * @param string $name
     * @param string $tags
     * @param string $title
     * @return array
     */
    public function add_new_document($case_id, $user_id, $interview_id, $path_to_save, $file_contents, $size, $type, $name, $tags, $title)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if (strlen($case_id) > 10) {
            $case_response = $this->get_id_md5($case_id);
            if($case_response['result']) {
                $case_id = $case_response['id'];
            } else {
                $ret_value['message'] = "No case found.";
                return $ret_value;
            }
        } elseif(!isset($case_id) || $case_id == 0) {

            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        if(!isset($interview_id)) {
            $interview_id = 0;
        }

        if(!isset($path_to_save) || strlen($path_to_save) == 0) {
            $ret_value['message'] = "Unable to save document.";
            return $ret_value;
        }

        if(!isset($file_contents) || strlen($file_contents) == 0) {
            $ret_value['message'] = "File contents missing.";
            return $ret_value;
        }

        if(!isset($size) || $size == 0) {
            $ret_value['message'] = "File corrupted: 0 bytes.";
            return $ret_value;
        }

        if(!isset($type) || strlen($type) == 0) {
            $ret_value['message'] = "File corrupted: unknown type.";
            return $ret_value;
        }

        if(!isset($name) || strlen($name) == 0) {
            $ret_value['message'] = "File corrupted: missing name.";
            return $ret_value;
        }

        // gen unique name
        $unique_name = '';
        $type_id = 0;
        $unique_name_result = $this->gen_unique_filename($type,$name);
        if($unique_name_result['result']) {
            $unique_name = $unique_name_result['name'];
            $type_id = $unique_name_result['type_id'];
            $path_to_save = $path_to_save.$unique_name;
        }else{
            $ret_value['message'] = "File corrupted: unknown type.";
            return $ret_value;
        }

        // store to s3
        $path_to_save = $this->store_to_s3($type, $file_contents);

        if (strlen($path_to_save) > 0) {

            // store info to database
            $created = date('Y-m-d');

            $sql = "INSERT INTO `attachments` (`case_id`, `user_id`, `interview_id`, `tags`, `title`, `name`, `type`, `size`, `location`, `created`)";
            $sql = $sql." VALUES (";
            $sql = $sql.$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($interview_id).",".$this->db->escape($tags).",";
            $sql = $sql.$this->db->escape($title).",".$this->db->escape($name).",".$this->db->escape($type_id).",".$this->db->escape($size).",";
            $sql = $sql.$this->db->escape($path_to_save).",".$this->db->escape($created);
            $sql = $sql." );";

            $query = $this->db->query($sql);
            
            if ($query !== FALSE) {
                $id = $this->db->insert_id();
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $id;
            }

        }else{
            $ret_value['message'] = "Unable to save file.";
        }

        return $ret_value;
    }

    public function load_docs_by_user($user_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array());

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        $sql = "SELECT `attachments`.`id`, `interview_id`, `attachments`.`name` AS att_name, `type`, `size`, `location`, `attachments`.`created`, `attachments`.`title`, `tags`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `attachments`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `attachments`.`user_id`";
        $sql = $sql." WHERE `attachments`.`user_id`=".$this->db->escape($user_id);
        $sql = $sql." AND `interview_id` = 0";
        $sql = $sql." ORDER BY `attachments`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row_count = 1;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'number' => '', 'icon' => '', 'postfix' => '', 'url' => '', 'name' => '', 'type' => '', 'size' => '', 'location' => '', 'title' => '', 'tags' => '', 'username' => '', 'created' => '', 'interview_id' => '');

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
                $doc['intid'] = $row->interview_id;

                // get url
                /*
                $url = '';
                $path_parts = explode('/', $row->location);
                if (count($path_parts) > 0) {
                    // TODO: pull from S3
                    $url = base_url('docs')."/".$path_parts[(count($path_parts)-1)];
                }
                $doc['url'] = $url;
                */

                $doc['url'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;

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

    public function load_documents_interview($int_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array());

        if(!isset($int_id) || strlen($int_id) == 0) {
            $ret_value['message'] = "No interview found.";
            return $ret_value;
        }

        $sql = "SELECT `attachments`.`id`, `interview_id`, `attachments`.`name` AS att_name, `type`, `size`, `location`, `attachments`.`created`, `attachments`.`title`, `tags`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `attachments`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `attachments`.`user_id`";
        $sql = $sql." WHERE `interview_id`=".$this->db->escape($int_id);
        $sql = $sql." AND `interview_id` > 0";
        $sql = $sql." ORDER BY `attachments`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row_count = 1;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'number' => '', 'icon' => '', 'postfix' => '', 'url' => '', 'name' => '', 'type' => '', 'size' => '', 'location' => '', 'title' => '', 'tags' => '', 'username' => '', 'created' => '', 'interview_id' => '');

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
                $doc['intid'] = $row->interview_id;

                // get url
                /*
                $url = '';
                $path_parts = explode('/', $row->location);
                if (count($path_parts) > 0) {
                    // TODO: pull from S3
                    $url = base_url('docs')."/".$path_parts[(count($path_parts)-1)];
                }
                $doc['url'] = $url;
                */

                $doc['url'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;

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

    /**
     * Load Documents
     * 
     * @param string $case_id (md5)
     * @return array
     */
    public function load_documents($case_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array());

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        $sql = "SELECT `attachments`.`id`, `interview_id`, `attachments`.`name` AS att_name, `type`, `size`, `location`, `attachments`.`created`, `attachments`.`title`, `tags`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `attachments`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `attachments`.`user_id`";
        $sql = $sql." WHERE LOWER(MD5(`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." AND `interview_id` = 0";
        $sql = $sql." ORDER BY `attachments`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row_count = 1;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'number' => '', 'icon' => '', 'postfix' => '', 'url' => '', 'name' => '', 'type' => '', 'size' => '', 'location' => '', 'title' => '', 'tags' => '', 'username' => '', 'created' => '', 'interview_id' => '');

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
                $doc['intid'] = $row->interview_id;

                // get url
                /*
                $url = '';
                $path_parts = explode('/', $row->location);
                if (count($path_parts) > 0) {
                    // TODO: pull from S3
                    $url = base_url('docs')."/".$path_parts[(count($path_parts)-1)];
                }
                $doc['url'] = $url;
                */

                $doc['url'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;

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

    /**
     * remove document
     *
     * @param int $attachment_id
     * @return array
     */
    public function remove_document($attachment_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($attachment_id) || $attachment_id == 0) {
            $ret_value['message'] = "No attachment found.";
            return $ret_value;
        }

        $sql = "DELETE FROM `attachments`";
        $sql = $sql." WHERE `id`=".$this->db->escape($attachment_id).";";

        $query = $this->db->query($sql);
        if ($query !== FALSE){
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Load case id from md5
     * 
     * @param string $md5
     * @return array
     */
    private function get_id_md5($md5)
    {
        $ret_value = array('result' => FALSE, 'id' => 0);

        if (!isset($md5) || strlen($md5) == 0) {
            return $ret_value;
        }

        $md5 = strtolower($md5);
        $sql = "SELECT `id` FROM `case` WHERE LOWER(MD5(`id`))=".$this->db->escape($md5)." LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $query->result()[0]->id;
        }

        return $ret_value;
    }

    // returns the file name in s3
    private function store_to_s3($file_type, $file_contents)
    {
        $ret_value = FALSE;

        if(!isset($file_type) || strlen($file_type) == 0) {
            return $ret_value;
        }

        if(!isset($file_contents) || strlen($file_contents) == 0) {
            return $ret_value;
        }

        $url = 'http://sample-env-2.hyw6bxz77m.us-west-2.elasticbeanstalk.com/doc/save/';

        $data = array("type" => $file_type, "data" => base64_encode($file_contents));

        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Save document to file system
     * 
     * @param string $path_to_save
     * @param string $file_contents
     * @return boolean
     */
    private function store_to_file_system($path_to_save, $file_contents)
    {
        $ret_value = FALSE;

        if(!isset($path_to_save) || strlen($path_to_save) == 0) {
            return $ret_value;
        }

        if(!isset($file_contents) || strlen($file_contents) == 0) {
            return $ret_value;
        }

        $bytes = file_put_contents($path_to_save, $file_contents);

        if($bytes !== FALSE && $bytes > 0) {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    /**
     * Generate a unique name
     * 
     * @param string $type
     * @param string $name
     * @return array
     */
    private function gen_unique_filename($type, $name)
    {
        $ret_value = array('result' => FALSE, 'name' => '', 'type_id' => '');

        if(!isset($type) || strlen($type) == 0) {
            return $ret_value;
        }

        if(!isset($name) || strlen($name) == 0) {
            return $ret_value;
        }

        // get prefix from name
        $prefix = '';
        $name_parts = explode(".", $name);
        if(count($name_parts) > 0){
            $prefix = $name_parts[1];
        }

        // handle jpeg / jpg problem
        if($prefix=='jpeg'){
            $prefix='jpg';
        }

        $type = strtolower($type);

        $pfix = '';
        $type_id = 0;

        // get prefix and id by file type
        $sql = "SELECT `id`, `pfix` FROM `attachment_type`";
        $sql = $sql." WHERE `mime`=".$this->db->escape($type);
        $sql = $sql." OR `pfix`=".$this->db->escape($prefix);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['type_id'] = $query->result()[0]->id;
            $pfix = $query->result()[0]->pfix;
        }else{
            return $ret_value;
        }

        // generate unique name
        $ret_value['name'] = md5(time()."-".$name).".".$pfix;
        $ret_value['result'] = TRUE;

        return $ret_value;
    }

}