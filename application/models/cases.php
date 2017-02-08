<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cases extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    public function get_client_email($case_id)
    {
        $ret_value = '';

        if(!isset($case_id) || $case_id == 0) {
            $ret_value['message'] = "No case.";
            return $ret_value;
        }

        $sql = "SELECT client.`email` FROM `case`";
        $sql = $sql." LEFT JOIN client ON client.id = `case`.client_id";
        $sql = $sql." WHERE LOWER(MD5(`case`.id)) = ".$this->db->escape($case_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = $query->result()[0]->email;
        }

        return $ret_value;
    }

    public function get_case_client($case_id)
    {
        $ret_value = array('result' => FALSE, 'client' => array());

        if(!isset($case_id) || $case_id == 0) {
            $ret_value['message'] = "No case.";
            return $ret_value;
        }

        $sql = "SELECT `client`.`id`, `client`.`name`, `client`.`street`, city, state, zip";
        $sql = $sql." FROM `case`";
        $sql = $sql." LEFT JOIN client ON client.id = `case`.client_id";
        $sql = $sql." LEFT JOIN geo ON geo.id = `client`.geo_id";
        $sql = $sql." WHERE LOWER(MD5(`case`.id)) = ".$this->db->escape($case_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;
            $row = $query->result()[0];

            $clt = array('id' => 0, 'name' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '');
            $clt['id'] = $row->id;
            $clt['name'] = $row->name;
            $clt['street'] = $row->street;
            $clt['city'] = $row->city;
            $clt['state'] = $row->state;
            $clt['zip'] = $row->zip;

            $ret_value['client'] = $clt;
        }

        return $ret_value;
    }

    /**
     * Edit Client
     * 
     * @param int $client_id
     * @param string $name
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $zip
     * @return array
     */
    public function edit_client($client_id, $name, $street, $city, $state, $zip, $email='', $phone='')
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($client_id) || $client_id == 0) {
            $ret_value['message'] = "No client id given.";
            return $ret_value;
        }

        // get geo id
        $geo_id = $this->get_geo_id($city, $state, $zip);
        if ($geo_id == 0) {
            $ret_value['message'] = "Address is invalid. Please check and try again.";
            $ret_value['result'] = FALSE;
            return $ret_value;
        }

        $sql = "UPDATE `client` SET";
        $sql = $sql." `name`=".$this->db->escape($name).",`street`=".$this->db->escape($street);
        $sql = $sql." ,`geo_id`=".$this->db->escape($geo_id);
        $sql = $sql." ,`email`=".$this->db->escape($email);
        $sql = $sql." ,`phone`=".$this->db->escape($phone);
        $sql = $sql." WHERE `id`=".$this->db->escape($client_id).";";

        $query = $this->db->query($sql);

        if ($query !== FALSE)
        {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Add Synopsis File
     * 
     * @param int $role_id
     * @param string $name
     * @param string $title
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $zip
     * @return array
     */
    public function edit_supporting($role_id, $name, $title, $street, $city, $state, $zip, $email='', $phone='')
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($role_id) || $role_id == 0) {
            $ret_value['message'] = "No role id given.";
            return $ret_value;
        }

        // get geo id
        $geo_id = $this->get_geo_id($city, $state, $zip);
        if ($geo_id == 0) {
            $ret_value['message'] = "Address is invalid. Please check and try again.";
            $ret_value['result'] = FALSE;
            return $ret_value;
        }

        $sql = "UPDATE `supporting` SET";
        $sql = $sql." `name`=".$this->db->escape($name).",`title`=".$this->db->escape($title).",`street`=".$this->db->escape($street);
        $sql = $sql." ,`geo_id`=".$this->db->escape($geo_id);
        $sql = $sql." ,`email`=".$this->db->escape($email);
        $sql = $sql." ,`phone`=".$this->db->escape($phone);
        $sql = $sql." WHERE `id`=".$this->db->escape($role_id).";";

        $query = $this->db->query($sql);

        if ($query !== FALSE)
        {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Add Synopsis File
     * 
     * @param string $case_id (md5)
     * @param int $user_id
     * @param string $path_to_save
     * @param base64 $file_contents
     * @param int $size
     * @param string $type
     * @param string $name
     * @return array
     */
    public function add_synopsis_file($case_id, $user_id, $path_to_save, $file_contents, $size, $type, $name)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'location' => '', 'name' => '', 'id' => 0);

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Case not found.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
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

            // set case id to lower
            $case_id = strtolower($case_id);

            try {

                // run in transaction
                $this->db->trans_start();

                $sql = "INSERT INTO `synopsis` (`name`, `size`, `location`, `att_type`, `user_id`)";
                $sql = $sql." VALUES (";
                $sql = $sql.$this->db->escape($name).",".$this->db->escape($size).",".$this->db->escape($path_to_save).",".$this->db->escape($type_id).",";
                $sql = $sql.$this->db->escape($user_id);
                $sql = $sql." );";

                $this->db->query($sql);
                $synopsis_id = $this->db->insert_id();

                $sql = "UPDATE `case` SET";
                $sql = $sql." `synopsis_id`=".$this->db->escape($synopsis_id);
                $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($case_id);

                $this->db->query($sql);

                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    throw new Exception();
                }
                else
                {
                    $this->db->trans_commit();
                    $ret_value['result'] = TRUE;
                    $ret_value['location'] = $path_to_save;
                    $ret_value['id'] = $synopsis_id;
                    $ret_value['name'] = $unique_name;
                }

                return $ret_value;

            } catch (Exception $e) {
                $ret_value['message'] = "Unable to process request. Please try again later.";
                return $ret_value;
            }

        }else{
            $ret_value['message'] = "Unable to save file.";
        }

        return $ret_value;
    }

    /**
     * Add Synopsis Contents
     * 
     * @param string $case_id (md5)
     * @param string $contents
     * @param int $user_id
     * @return array
     */
    public function add_synopsis_contents($case_id, $contents, $user_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Case not found.";
            return $ret_value;
        }

        if (!isset($contents) || strlen($contents) == 0) {
            $ret_value['message'] = "Contents required.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        // set case id to lower
        $case_id = strtolower($case_id);

        try {

            // run in transaction
            $this->db->trans_start();

            $sql = "INSERT INTO `synopsis` (`contents`, `user_id`)";
            $sql = $sql." VALUES (";
            $sql = $sql.$this->db->escape($contents).",".$this->db->escape($user_id);
            $sql = $sql." );";

            $this->db->query($sql);
            $synopsis_id = $this->db->insert_id();

            $sql = "UPDATE `case` SET";
            $sql = $sql." `synopsis_id`=".$this->db->escape($synopsis_id);
            $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($case_id);

            $this->db->query($sql);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                throw new Exception();
            }
            else
            {
                $this->db->trans_commit();
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $synopsis_id;
            }

            return $ret_value;

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }
    }

    /**
     * Edit case name
     * 
     * @param string $case_id (md5)
     * @param string $case_name
     * @return array
     */
    public function edit_case_name($case_id, $case_name)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Case not found.";
            return $ret_value;
        }

        if (!isset($case_name) || strlen($case_name) == 0) {
            $ret_value['message'] = "Invalid case name.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        $sql = "UPDATE `case` SET";
        $sql = $sql." `name`=".$this->db->escape($case_name);
        $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($case_id).";";

        $query = $this->db->query($sql);

        if ($query !== FALSE)
        {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Open close cases
     * 
     * @param string $case_id (md5)
     * @param boolean $is_closed
     * @return boolean
     */
    public function open_close_case($case_id, $is_closed)
    {
        $ret_value = FALSE;

        if (!isset($case_id) || strlen($case_id) == 0) {
            return $ret_value;
        }

        $case_id = strtolower($case_id);
        $iscl_int = 0;

        if($is_closed === TRUE || $is_closed == 'true') {
            $iscl_int = 1;
        }

        $sql = "UPDATE `case` SET";
        $sql = $sql." `is_closed`=".$this->db->escape($iscl_int);
        $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($case_id).";";

        $query = $this->db->query($sql);

        if ($query !== FALSE)
        {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    /**
     * Get synopsis
     * 
     * @param string $case_id (md5)
     * @return array
     */
    public function get_synopsis($case_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'synopsis' => array());

        if (!isset($case_id) || strlen($case_id) == 0) {
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        //SELECT `id`, `name`, `size`, `contents`, `location`, `att_type`, `user_id`, `modified` FROM `synopsis` WHERE 1
        $sql = "SELECT `synopsis`.`id`, `synopsis`.`name`, `size`, `contents`, `location`, `att_type`, `synopsis`.`user_id`, `synopsis`.`modified`,";
        $sql = $sql." `user_info`.`name` as user_name, `user_info`.`title` as user_title, `user_info`.`image` as user_image";
        $sql = $sql." FROM `synopsis`";
        $sql = $sql." LEFT JOIN `case` ON `case`.synopsis_id = `synopsis`.`id`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.user_id = `synopsis`.`user_id`";
        $sql = $sql." WHERE LOWER(MD5(`case`.`id`))=".$this->db->escape($case_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $synposis = array('id' => 0, 'url' => '', 'icon' => '', 'name' => '', 'size' => '', 'contents' => '', 'location' => '', 'type' => '', 'user_id' => '', 'user_name' => '', 'user_title' => '', 'user_image' => '', 'modified' => '');
            
            $row = $query->result()[0];

            $synposis['id'] = $row->id;
            $synposis['name'] = $row->name;
            $synposis['size'] = $row->size;
            $synposis['contents'] = $row->contents;
            $synposis['location'] = $row->location;
            $synposis['type'] = $row->att_type;
            $synposis['user_id'] = $row->user_id;
            $synposis['user_name'] = $row->user_name;
            $synposis['user_title'] = $row->user_title;
            $synposis['user_image'] = $row->user_image;
            $synposis['modified'] = $row->modified;

            /*
            $url = '';
            $path_parts = explode('/', $row->location);
            if (count($path_parts) > 0) {
                // TODO: pull from S3
                $url = base_url('docs')."/".$path_parts[(count($path_parts)-1)];
            }
            $synposis['url'] = $url;
            */

            $synposis['url'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;

            // get icon image
            $icon = '';
            switch ($row->att_type) {
                case 1: $icon = base_url("img/icons/powerpoint.png"); break;
                case 2: $icon = base_url("img/icons/word.png"); break;
                case 3: $icon = base_url("img/icons/excel.png"); break;
                case 4: $icon = base_url("img/icons/pdf.png"); break;
                case 5:
                case 6:
                case 7:
                case 8: $icon = base_url("img/icons/video.png"); break;
                case 9:
                case 10: $icon = base_url("img/icons/image.png"); break;
                case 11: $icon = base_url("img/icons/text.png"); break;
            }

            $synopsis['icon'] = $icon;

            $ret_value['synopsis'] = $synposis;

        }

        return $ret_value;
    }

    /**
     * Get cases
     * 
     * @param int $company_id
     * @param int $user_id
     * @param boolean $include_closed
     * @return array
     */
    public function get_cases($company_id, $user_id, $include_closed=TRUE)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'cases' => array());

        if (!isset($company_id) || $company_id == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if (!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "Invalid user.";
            return $ret_value;
        }

        $sql = "SELECT `case`.`id`, `is_closed`, `client_id`, `attorney_id`, `cpa_id`, `le_agent_id`,";
        $sql = $sql." `district_attorney_id`, `name`, `synopsis_id`, `predication`, `created`, `modified`, `is_case_admin`";
        $sql = $sql." FROM `case`";
        $sql = $sql." INNER JOIN team ON team.case_id = case.id";
        $sql = $sql." WHERE `company_id`=".$this->db->escape(intval($company_id));
        $sql = $sql." AND `user_id`=".$this->db->escape($user_id);

        if ($include_closed == FALSE) {
            $sql = $sql." AND `is_closed`=0";
        }

        $sql = $sql." ORDER BY `modified` DESC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $case = array('id' => 0, 'name' => '', 'synopsis_id' => '', 'predication' => '', 'created' => '', 'modified' => '', 'is_case_admin' => '', 'is_closed' => '', 'client_id' => 0, 'attorney_id' => 0, 'cpa_id' => 0, 'le_agent_id' => 0, 'district_attorney_id' => 0);
                
                $case['id'] = $row->id;
                $case['name'] = $row->name;
                $case['synopsis_id'] = $row->synopsis_id;
                $case['predication'] = $row->predication;
                $case['created'] = $row->created;
                $case['modified'] = $row->modified;
                $case['is_case_admin'] = $row->is_case_admin;
                $case['is_closed'] = $row->is_closed;
                $case['client_id'] = $row->client_id;
                $case['attorney_id'] = $row->attorney_id;
                $case['cpa_id'] = $row->cpa_id;
                $case['le_agent_id'] = $row->le_agent_id;
                $case['district_attorney_id'] = $row->district_attorney_id;

                $ret_value['cases'][] = $case;
            }
        }

        return $ret_value;
    }

    /**
     * Load case details
     * 
     * @param string $case_id (md5)
     * @param int $client_id
     * @param int $cpa_id
     * @param int $attorney_id
     * @param int $le_agent_id
     * @param int $da_id
     * @return array
     */
    public function load_case_details($client_id, $cpa_id, $attorney_id, $le_agent_id, $da_id)
    {
        $ret_value = array('result' => FALSE, 'client' => array(), 'supporting' => array());

        // get client data
        if (isset($client_id) && $client_id > 0) {

            $sql = "SELECT `client`.`id`, `name`, `street`, city, state, zip, phone, email";
            $sql = $sql." FROM `client`";
            $sql = $sql." INNER JOIN geo ON geo.id = `client`.geo_id";
            $sql = $sql." WHERE `client`.`id` = ".$this->db->escape($client_id);
            $sql = $sql." LIMIT 1;";

            $query = $this->db->query($sql);

            if (($query !== FALSE) && ($query->num_rows() > 0))
            {
                $ret_value['result'] = TRUE;
                $row = $query->result()[0];

                $clt = array('id' => 0, 'name' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'phone' => '', 'email' => '');
                $clt['id'] = $row->id;
                $clt['name'] = $row->name;
                $clt['street'] = $row->street;
                $clt['city'] = $row->city;
                $clt['state'] = $row->state;
                $clt['zip'] = $row->zip;
                $clt['phone'] = $row->phone;
                $clt['email'] = $row->email;

                $ret_value['client'] = $clt;
            }
        }

        // get supporting data
        $sql = "SELECT `supporting`.`id`, `type`, `supporting`.`name`, `title`, `street`, city, state, zip, email, phone, `supporting_type`.name AS profession";
        $sql = $sql." FROM `supporting`";
        $sql = $sql." INNER JOIN `supporting_type` ON `supporting_type`.id = `supporting`.`type`";
        $sql = $sql." INNER JOIN geo ON geo.id = `supporting`.geo_id";
        $sql = $sql." WHERE `supporting`.`id` IN (".$client_id.",".$cpa_id.",".$attorney_id.",".$le_agent_id.",".$da_id.")";
        $sql = $sql." ORDER BY `supporting_type`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = true;

            foreach ($query->result() as $row)
            {
                $spt = array('id' => 0, 'type' => 0, 'name' => '', 'email' => '', 'phone' => '', 'title' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'profession' => '');
                $spt['id'] = $row->id;
                $spt['type'] = $row->type;
                $spt['name'] = $row->name;
                $spt['title'] = $row->title;
                $spt['street'] = $row->street;
                $spt['city'] = $row->city;
                $spt['state'] = $row->state;
                $spt['zip'] = $row->zip;
                $spt['email'] = $row->email;
                $spt['phone'] = $row->phone;
                $spt['profession'] = $row->profession;

                $ret_value['supporting'][] = $spt;
            }
        }

        return $ret_value;

    }

    public function get_case_admin_intid($int_id)
    {
        $ret_value = array('result' => FALSE, 'name' => '', 'email' => '', 'case_id' => '', 'int_name' => '');

        if (!isset($int_id) || $int_id == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        // get case id from interview id
        $sql = "SELECT `case_id`,`name`";
        $sql = $sql." FROM `interview`";
        $sql = $sql." WHERE `interview`.`id`=".$this->db->escape($int_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);
        $case_id = "";

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];
            $case_id = $row->case_id;
            $ret_value['int_name'] = $row->name;
        }else{
            return $ret_value;
        }

        $case_id = strtolower(md5($case_id));
        $ret_value['case_id'] = $case_id;

        $sql = "SELECT `user_info`.`name`, `user`.`email`";
        $sql = $sql." FROM `team`";
        $sql = $sql." INNER JOIN user ON user.id = team.user_id";
        $sql = $sql." INNER JOIN user_info ON user_info.user_id = team.user_id";
        $sql = $sql." WHERE LOWER(MD5(`team`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row = $query->result()[0];

            $ret_value['name'] = $row->name;
            $ret_value['email'] = $row->email;
        }

        return $ret_value;
    }

    public function get_case_admin($case_id)
    {
        $ret_value = array('result' => FALSE, 'name' => '', 'email' => '');

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        $sql = "SELECT `user_info`.`name`, `user`.`email`";
        $sql = $sql." FROM `team`";
        $sql = $sql." INNER JOIN user ON user.id = team.user_id";
        $sql = $sql." INNER JOIN user_info ON user_info.user_id = team.user_id";
        $sql = $sql." WHERE LOWER(MD5(`team`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row = $query->result()[0];

            $ret_value['name'] = $row->name;
            $ret_value['email'] = $row->email;
        }

        return $ret_value;
    }

    /**
     * Open case
     * 
     * @param string $case_id (md5)
     * @param int $user_id
     * @return array
     */
    public function open_case($case_id, $user_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'case' => array());

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if (!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "Invalid user.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        $sql = "SELECT `case`.`id`, `is_closed`, `client_id`, `attorney_id`, `cpa_id`, `le_agent_id`,";
        $sql = $sql." `district_attorney_id`, `name`, `synopsis_id`, `predication`, `created`, `modified`, `is_case_admin`";
        $sql = $sql." FROM `case`";
        $sql = $sql." INNER JOIN team ON team.case_id = case.id";
        $sql = $sql." WHERE LOWER(MD5(`case`.`id`))=".$this->db->escape($case_id);
        $sql = $sql." AND `user_id`=".$this->db->escape($user_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $case = array('id' => 0, 'name' => '', 'synopsis_id' => '', 'predication' => '', 'created' => '', 'modified' => '', 'is_case_admin' => '', 'is_closed' => '', 'client_id' => 0, 'attorney_id' => 0, 'cpa_id' => 0, 'le_agent_id' => 0, 'district_attorney_id' => 0);
            
            $row = $query->result()[0];

            $case['id'] = $row->id;
            $case['name'] = $row->name;
            $case['synopsis_id'] = $row->synopsis_id;
            $case['predication'] = $row->predication;
            $case['created'] = $row->created;
            $case['modified'] = $row->modified;
            $case['is_case_admin'] = $row->is_case_admin;
            $case['is_closed'] = $row->is_closed;
            $case['client_id'] = $row->client_id;
            $case['attorney_id'] = $row->attorney_id;
            $case['cpa_id'] = $row->cpa_id;
            $case['le_agent_id'] = $row->le_agent_id;
            $case['district_attorney_id'] = $row->district_attorney_id;

            $ret_value['case'] = $case;

        }

        return $ret_value;
    }

    /**
     * Is team lead
     * 
     * @param string $case_id
     * @param int $user_id
     * @return boolean
     */
    public function is_team_lead($case_id, $user_id)
    {
        $ret_value = FALSE;

        if (!isset($case_id) || strlen($case_id) == 0) {
            return $ret_value;
        }

        if (!isset($user_id) || $user_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `team`.`is_case_admin`";
        $sql = $sql." FROM `team`";
        $sql = $sql." WHERE LOWER(MD5(`team`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." AND `team`.`user_id`=".$this->db->escape($user_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $is_case_admin = $query->result()[0]->is_case_admin;
            if (intval($is_case_admin) == 1) {
                $ret_value = TRUE;
            }
        }

        return $ret_value;
    }

    /**
     * Remove User Team
     * 
     * @param string $case_id
     * @param int $user_id
     * @return array
     */
    public function remove_user_team($case_id,$user_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "Invalid user.";
            return $ret_value;
        }

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Invalid case.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        $sql = "DELETE FROM `team`";
        $sql = $sql." WHERE LOWER(MD5(`team`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." AND `team`.`user_id`=".$this->db->escape($user_id).";";

        $query = $this->db->query($sql);

        if ($query !== FALSE) {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Get case team with available company users
     * 
     * @param string $case_id
     * @param string $company_id
     * @return array
     */
    public function get_team_with_available($case_id, $company_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'team' => array(), 'users' => array());

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Invalid case.";
            return $ret_value;
        }

        if (!isset($company_id) || strlen($company_id) == 0) {
            $ret_value['message'] = "Invalid company.";
            return $ret_value;
        }

        $team_ids = "";

        $case_id = strtolower($case_id);

        $sql = "SELECT `team`.`user_id`, `user_info`.`name`, `user_info`.`title`, `user_info`.`image`, team.is_case_admin";
        $sql = $sql." FROM `team`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.user_id = `team`.user_id";
        $sql = $sql." WHERE LOWER(MD5(`team`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." ORDER BY `team`.`user_id` ASC;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0)
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $mem = array('user_id' => 0, 'name' => '', 'title' => '', 'image' => '', 'is_case_admin' => FALSE);

                $mem['user_id'] = $row->user_id;
                $mem['name'] = $row->name;
                $mem['title'] = $row->title;
                $mem['image'] = $row->image;
                if (intval($row->is_case_admin) == 1) {
                    $mem['is_case_admin'] = TRUE;
                }

                $team_ids = $team_ids.",".$row->user_id;
                
                $ret_value['team'][] = $mem;

            }
        }

        // remove comma from team ids
        $team_ids = trim($team_ids, ",");

        $company_id = strtolower($company_id);

        $sql = "SELECT `user`.`id`, `is_admin`, `user_info`.`name`, `user_info`.`title`, `user_info`.`image`";
        $sql = $sql." FROM `user`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.user_id = `user`.id";
        $sql = $sql." WHERE LOWER(MD5(`user_info`.company_id)) = ".$this->db->escape($company_id);
        $sql = $sql." AND `user`.`id` NOT IN (".$team_ids.")";
        $sql = $sql." ORDER BY `user_info`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = true;

            foreach ($query->result() as $row)
            {
                $user = array('id' => 0, 'is_admin' => 0, 'name' => '', 'title' => '', 'image' => '');
                $user['id'] = $row->id;
                $user['is_admin'] = $row->is_admin;
                $user['name'] = $row->name;
                $user['title'] = $row->title;
                $user['image'] = $row->image;
                $ret_value['users'][] = $user;
            }
        }

        return $ret_value;
    }

    /**
     * Get case team
     * 
     * @param string $case_id
     * @return array
     */
    public function get_team($case_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'team' => array());

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        $sql = "SELECT `team`.`user_id`, `user_info`.`name`, `user_info`.`title`, `user_info`.`image`, team.is_case_admin";
        $sql = $sql." FROM `team`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.user_id = `team`.user_id";
        $sql = $sql." WHERE LOWER(MD5(`team`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." ORDER BY `team`.`user_id` ASC;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0)
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $mem = array('user_id' => 0, 'name' => '', 'title' => '', 'image' => '', 'is_case_admin' => FALSE);

                $mem['user_id'] = $row->user_id;
                $mem['name'] = $row->name;
                $mem['title'] = $row->title;
                $mem['image'] = $row->image;
                if (intval($row->is_case_admin) == 1) {
                    $mem['is_case_admin'] = TRUE;
                }
                
                $ret_value['team'][] = $mem;

            }
        }

        return $ret_value;
    }

    /**
     * Add new case
     * 
     * @param int $company_id
     * @param int $client_id
     * @param string $name
     * @param string $predication
     * @param string $created
     * @param int $attorney_id
     * @param int $cpa_id
     * @param int $le_agency_id
     * @param int $district_attorney_id
     * @return array
     */
    public function add_new_case($company_id, $client_id, $name, $predication, $created, $attorney_id=0, $cpa_id=0, $le_agent_id=0, $district_attorney_id=0)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if (!isset($company_id) || $company_id == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if (!isset($client_id) || $client_id == 0) {
            $ret_value['message'] = "Invalid client.";
            return $ret_value;
        }

        if ((!isset($name) || strlen($name) == 0)) {
            $ret_value['message'] = "Invalid parameters.";
            return $ret_value;
        }

        if (!isset($created) || strlen($created) == 0) {
            $created = date('Y-m-d');
        }else{
            // format date
            $created = date('Y-m-d', strtotime($created));
        }

        // does case exist
        $case_response = $this->does_case_exist($company_id, $client_id, $name);
        if ($case_response['exists']) {
            // return support id since it exists, no need to add it again
            if ($case_response['id'] > 0) {
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $case_response['id'];
                return $ret_value;
            }
        }

        $sql = "INSERT INTO `case` (`company_id`, `client_id`, `attorney_id`, `cpa_id`, `le_agent_id`, `district_attorney_id`, `name`, `predication`, `created`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($company_id).",".$this->db->escape($client_id).",".$this->db->escape($attorney_id).",".$this->db->escape($cpa_id).",";
        $sql = $sql.$this->db->escape($le_agent_id).",".$this->db->escape($district_attorney_id).",".$this->db->escape($name).",".$this->db->escape($predication).",".$this->db->escape($created);
        $sql = $sql.");";

        try {

            $query = $this->db->query($sql);

            if ($query !== FALSE) {
                $id = $this->db->insert_id();
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $id;
            } else {
                throw new Exception();
            }

        } catch (Exception $e) {
            $ret_value['result'] = FALSE;
            $ret_value['message'] = 'Unable to process request. Please try again later.';
        }

        return $ret_value;
    }

    /**
     * Edit team member
     * 
     * @param string $case_id (md5)
     * @param int $user_id
     * @param boolean $is_admin
     * @return array
     */
    public function edit_team_member($case_id, $user_id, $is_admin)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if (!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "Team user not found.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);

        $sql = "UPDATE `team`";
        $sql = $sql." SET `is_case_admin`=".$this->db->escape($is_admin);
        $sql = $sql." WHERE LOWER(MD5(`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." AND `user_id`=".$this->db->escape($user_id);
        $sql = $sql.";";

        try {

            $query = $this->db->query($sql);

            if ($query !== FALSE) {
                $ret_value['result'] = TRUE;
            } else {
                throw new Exception();
            }

        } catch (Exception $e) {
            $ret_value['result'] = FALSE;
            $ret_value['message'] = 'Unable to process request.';
        }

        return $ret_value;
    }

    /**
     * Add new team member
     * 
     * @param int $case_id
     * @param int $user_id
     * @param boolean $is_admin
     * @param string $joined
     * @return array
     */
    public function add_new_team_member($case_id, $user_id, $is_admin, $joined)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($case_id) || $case_id == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if (!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "Team user not found.";
            return $ret_value;
        }

        if (!isset($joined) || strlen($joined) == 0) {
            $joined = date('Y-m-d');
        }else{
            // format date
            $joined = date('Y-m-d', strtotime($joined));
        }

        // does team member already exist
        if ($this->does_team_member_exist($case_id, $user_id)){
            $ret_value['result'] = TRUE;
            return $ret_value;
        }

        $sql = "INSERT INTO `team` (`case_id`, `user_id`, `is_case_admin`, `joined`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($is_admin).",".$this->db->escape($joined);
        $sql = $sql.");";

        try {

            $query = $this->db->query($sql);

            if ($query !== FALSE) {
                $ret_value['result'] = TRUE;
            } else {
                throw new Exception();
            }

        } catch (Exception $e) {
            $ret_value['result'] = FALSE;
            $ret_value['message'] = 'Unable to process request. Please try again later.';
        }

        return $ret_value;
    }

    /**
     * Add new client
     * 
     * @param int $company_id
     * @param string $name
     * @param string $title
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $image (location)
     * @param string $created
     * @return array
     */
    public function add_new_client($company_id, $name, $street, $city, $state, $zip, $image, $created, $email='')
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if (!isset($company_id) || $company_id == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if ((!isset($name) || strlen($name) == 0) || (!isset($street) || strlen($street) == 0) || (!isset($city) || strlen($city) == 0) || (!isset($state) || strlen($state) == 0) || (!isset($zip) || strlen($zip) == 0)) {
            $ret_value['message'] = "Invalid parameters.";
            return $ret_value;
        }

        if (!isset($created) || strlen($created) == 0) {
            $created = date('Y-m-d');
        }else{
            // format date
            $created = date('Y-m-d', strtotime($created));
        }

        // ensure client does not exist
        $client_response = $this->does_client_exist($company_id, $name, $street, $city, $state, $zip);
        if ($client_response['exists']) {
            // return client id since it exists, no need to add it again
            if ($client_response['id'] > 0) {
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $client_response['id'];
                return $ret_value;
            }
        }

        // get geo id
        $geo_id = $this->get_geo_id($city, $state, $zip);
        if ($geo_id == 0) {
            $ret_value['message'] = "Address is invalid. Please check and try again.";
            $ret_value['result'] = FALSE;
            return $ret_value;
        }

        $sql = "INSERT INTO `client` (`company_id`, `name`, `street`, `geo_id`, `image`, `created`, `email`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($company_id).",".$this->db->escape($name).",".$this->db->escape($street).",".$this->db->escape($geo_id).",";
        $sql = $sql.$this->db->escape($image).",".$this->db->escape($created).",".$this->db->escape($email);
        $sql = $sql.");";

        try {

            $query = $this->db->query($sql);

            if ($query !== FALSE) {
                $id = $this->db->insert_id();
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $id;
            } else {
                throw new Exception();
            }

        } catch (Exception $e) {
            $ret_value['result'] = FALSE;
            $ret_value['message'] = 'Unable to process request. Please try again later.';
        }

        return $ret_value;
    }

    /**
     * Add new supporting role (attorney, cpa, etc...)
     * 
     * @param int $company_id
     * @param string $name
     * @param string $title
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param int $type
     * @param string $created
     * @param string $phone
     * @param string $email
     * @return array
     */
    public function add_new_supporting($company_id, $name, $title, $street, $city, $state, $zip, $type, $created, $phone="", $email="")
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if (!isset($company_id) || $company_id == 0) {
            $ret_value['message'] = "Invalid id.";
            return $ret_value;
        }

        if ((!isset($name) || strlen($name) == 0) || (!isset($title) || strlen($title) == 0) || (!isset($street) || strlen($street) == 0) || (!isset($city) || strlen($city) == 0) || (!isset($state) || strlen($state) == 0) || (!isset($zip) || strlen($zip) == 0)) {
            $ret_value['message'] = "Invalid parameters.";
            return $ret_value;
        }

        if (!isset($created) || strlen($created) == 0) {
            $created = date('Y-m-d');
        }else{
            // format date
            $created = date('Y-m-d', strtotime($created));
        }

        // ensure supporting does not exist
        $support_response = $this->does_support_exist($company_id, $name, $title, $street, $city, $state, $zip);
        if ($support_response['exists']) {
            // return support id since it exists, no need to add it again
            if ($support_response['id'] > 0) {
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $support_response['id'];
                return $ret_value;
            }
        }

        // get geo id
        $geo_id = $this->get_geo_id($city, $state, $zip);
        if ($geo_id == 0) {
            $ret_value['message'] = "Address is invalid. Please check and try again.";
            $ret_value['result'] = FALSE;
            return $ret_value;
        }

        $sql = "INSERT INTO `supporting` (`company_id`, `name`, `title`, `street`, `geo_id`, `type`, `created`, `phone`, `email`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($company_id).",".$this->db->escape($name).",".$this->db->escape($title).",".$this->db->escape($street).",".$this->db->escape($geo_id).",";
        $sql = $sql.$this->db->escape($type).",".$this->db->escape($created).",".$this->db->escape($phone).",".$this->db->escape($email);
        $sql = $sql.");";

        try {

            $query = $this->db->query($sql);

            if ($query !== FALSE) {
                $id = $this->db->insert_id();
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $id;
            } else {
                throw new Exception();
            }

        } catch (Exception $e) {
            $ret_value['result'] = FALSE;
            $ret_value['message'] = 'Unable to process request. Please try again later.';
        }

        return $ret_value;
    }

    /**
     * Get supporting id from md5 id
     * 
     * @param string $md5
     * @return int
     */
    public function get_solution_id_from_md5($md5)
    {
        $ret_value = 0;
        
        if (!isset($md5) || strlen($md5) == 0) {
            return $ret_value;
        }

        $md5 = strtolower($md5);

        $sql = "SELECT `id` FROM `supporting`";
        $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($md5);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = $query->result()[0]->id;
        }

        return $ret_value;
    }

    public function get_casename($md5)
    {
        $ret_value = '';
        
        if (!isset($md5) || strlen($md5) == 0) {
            return $ret_value;
        }

        $md5 = strtolower($md5);

        $sql = "SELECT `name` FROM `case`";
        $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($md5);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = $query->result()[0]->name;
        }

        return $ret_value;
    }

    /**
     * Get case id from md5 id
     * 
     * @param string $md5
     * @return int
     */
    public function get_caseid_by_md5($md5)
    {
        $ret_value = 0;
        
        if (!isset($md5) || strlen($md5) == 0) {
            return $ret_value;
        }

        $md5 = strtolower($md5);

        $sql = "SELECT `id` FROM `case`";
        $sql = $sql." WHERE LOWER(MD5(`id`))=".$this->db->escape($md5);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = $query->result()[0]->id;
        }

        return $ret_value;
    }

    /**
     * Does case exist
     * 
     * @param string $company_id
     * @param string $client_id
     * @param string $name
     * @return array
     */
    private function does_case_exist($company_id, $client_id, $name)
    {
        $ret_value = array('exists' => FALSE, 'id' => 0);

        if (!isset($company_id) || $company_id == 0) {
            return $ret_value;
        }

        if (!isset($client_id) || $client_id == 0) {
            return $ret_value;
        }

        if (!isset($name) || strlen($name) == 0) {
            return $ret_value;
        }
        
        $name = strtolower($name);
        $name = trim($name);

        $sql = "SELECT `id` FROM `case`";
        $sql = $sql." WHERE `company_id`=".$this->db->escape($company_id);
        $sql = $sql." AND client_id=".$this->db->escape($client_id);
        $sql = $sql." AND LOWER(name)=".$this->db->escape($name);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value['id'] = $query->result()[0]->id;
            $ret_value['exists'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Does team member exist
     * 
     * @param int $case_id
     * @param int $user_id
     * @return boolean
     */
    private function does_team_member_exist($case_id, $user_id)
    {
        $ret_value = FALSE;

        if (!isset($case_id) || $case_id == 0) {
            return $ret_value;
        }

        if (!isset($user_id) || strlen($user_id) == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id` FROM `team`";
        $sql = $sql." WHERE `case_id`=".$this->db->escape($case_id);
        $sql = $sql." AND `user_id`=".$this->db->escape($user_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    /**
     * Does client exist
     * 
     * @param int $company_id
     * @param string $name
     * @param string $title
     * @param string $street
     * @param string $city
     * @param string $state
     * @param stirng $zip
     * @return array
     */
    private function does_client_exist($company_id, $name, $street, $city, $state, $zip)
    {
        $ret_value = array('exists' => FALSE, 'id' => 0);

        if (!isset($company_id) || $company_id == 0) {
            return $ret_value;
        }

        if (!isset($name) || strlen($name) == 0) {
            return $ret_value;
        }

        if (!isset($street) || strlen($street) == 0) {
            return $ret_value;
        }

        $geo_id = $this->get_geo_id($city, $state, $zip);
        
        $name = strtolower($name);
        $name = trim($name);
        $street = strtolower($street);
        $street = trim($street);

        $sql = "SELECT `id` FROM `client`";
        $sql = $sql." WHERE `company_id`=".$this->db->escape($company_id);
        $sql = $sql." AND LOWER(name)=".$this->db->escape($name);
        $sql = $sql." AND (LOWER(street)=".$this->db->escape($street);
        if ($geo_id > 0) {
            $sql = $sql." OR geo_id=".$this->db->escape($geo_id);
        }
        $sql = $sql.") LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value['id'] = $query->result()[0]->id;
            $ret_value['exists'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Does support role exist
     * 
     * @param int $company_id
     * @param string $name
     * @param string $title
     * @param string $street
     * @param string $city
     * @param string $state
     * @param stirng $zip
     * @return array
     */
    private function does_support_exist($company_id, $name, $title, $street, $city, $state, $zip)
    {
        $ret_value = array('exists' => FALSE, 'id' => 0);

        if (!isset($company_id) || $company_id == 0) {
            return $ret_value;
        }

        if (!isset($name) || strlen($name) == 0) {
            return $ret_value;
        }

        if (!isset($title) || strlen($title) == 0) {
            return $ret_value;
        }

        if (!isset($street) || strlen($street) == 0) {
            return $ret_value;
        }

        $geo_id = $this->get_geo_id($city, $state, $zip);
        
        $name = strtolower($name);
        $name = trim($name);
        $title = strtolower($title);
        $title = trim($title);
        $street = strtolower($street);
        $street = trim($street);

        $sql = "SELECT `id` FROM `supporting`";
        $sql = $sql." WHERE `company_id`=".$this->db->escape($company_id);
        $sql = $sql." AND LOWER(name)=".$this->db->escape($name);
        $sql = $sql." AND LOWER(title)=".$this->db->escape($title);
        $sql = $sql." AND (LOWER(street)=".$this->db->escape($street);
        if ($geo_id > 0) {
            $sql = $sql." OR geo_id=".$this->db->escape($geo_id);
        }
        $sql = $sql.") LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value['id'] = $query->result()[0]->id;
            $ret_value['exists'] = TRUE;
        }

        return $ret_value;
    }

    /**
     * Get geo id for city, state, zip in Table Lookup
     * 
     * @param string $city
     * @param string $state
     * @param integer $zip
     * @return boolean
     */
    private function get_geo_id($city, $state, $zip)
    {
        $ret_value = 0;

        if ((!isset($city) || strlen($city) == 0) || (!isset($state) || strlen($state) == 0) || (!isset($zip) || strlen($zip) == 0)) {
            return $ret_value;
        }

        $sql = "SELECT id FROM geo WHERE LOWER(state)=".$this->db->escape(strtolower($state));
        $sql = $sql." AND (LOWER(city)=".$this->db->escape(strtolower($city));
        $sql = $sql." OR zip=".$this->db->escape($zip).") LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = $query->result()[0]->id;
        }

        return $ret_value;
    }

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