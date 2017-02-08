<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load company clients
     * 
     * @param string $id
     * @return array
     */
    public function load_company_clients($id)
    {
        $ret_value = array('result' => FALSE, 'clients' => array());

        if (!isset($id) || $id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `client`.`id`, `name`, `street`, city, state, zip";
        $sql = $sql." FROM `client`";
        $sql = $sql." INNER JOIN geo ON geo.id = `client`.geo_id";
        $sql = $sql." WHERE `company_id` = ".$this->db->escape($id);
        $sql = $sql." ORDER BY `name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $clt = array('id' => 0, 'name' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '');
                $clt['id'] = $row->id;
                $clt['name'] = $row->name;
                $clt['street'] = $row->street;
                $clt['city'] = $row->city;
                $clt['state'] = $row->state;
                $clt['zip'] = $row->zip;

                $ret_value['clients'][] = $clt;
            }
        }

        return $ret_value;
    }

    /**
     * Load company supporting
     * 
     * @param string $id
     * @return array
     */
    public function load_company_supporting($id)
    {
        $ret_value = array('result' => FALSE, 'supporting' => array());

        if (!isset($id) || $id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `supporting`.`id`, `type`, `supporting`.`name`, `title`, `street`, city, state, zip, `supporting_type`.name AS profession";
        $sql = $sql." FROM `supporting`";
        $sql = $sql." INNER JOIN `supporting_type` ON `supporting_type`.id = `supporting`.`type`";
        $sql = $sql." INNER JOIN geo ON geo.id = `supporting`.geo_id";
        $sql = $sql." WHERE `company_id` = ".$this->db->escape($id).";";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = true;

            foreach ($query->result() as $row)
            {
                $spt = array('id' => 0, 'type' => 0, 'name' => '', 'title' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'profession' => '');
                $spt['id'] = $row->id;
                $spt['type'] = $row->type;
                $spt['name'] = $row->name;
                $spt['title'] = $row->title;
                $spt['street'] = $row->street;
                $spt['city'] = $row->city;
                $spt['state'] = $row->state;
                $spt['zip'] = $row->zip;
                $spt['profession'] = $row->profession;

                $ret_value['supporting'][] = $spt;
            }
        }

        return $ret_value;
    }

    /**
     * Load company team
     * 
     * @param int $id
     * @return array
     */
    public function load_company_users($id)
    {
        $ret_value = array('result' => FALSE, 'team' => array(), 'invites' => array());

        if (!isset($id) || $id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `user`.`id`, `is_admin`, `user_info`.`name`, `user_info`.`title`, `user_info`.`image`";
        $sql = $sql." FROM `user`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.user_id = `user`.id";
        $sql = $sql." WHERE `user_info`.company_id = ".$this->db->escape($id);
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
                $ret_value['team'][] = $user;
            }
        }

        $sql = "SELECT `id`, `name`, `email`, `last_date_sent`, `num_tries`";
        $sql = $sql." FROM `user_invite`";
        $sql = $sql." WHERE `company_id` = ".$this->db->escape($id);
        $sql = $sql." ORDER BY `last_date_sent` DESC;";
        
        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = true;

            foreach ($query->result() as $row)
            {
                $user = array('id' => '', 'name' => '', 'email' => '', 'last_sent' => '', 'num_tries' => 0);
                $user['id'] = $row->id;
                $user['name'] = $row->name;
                $user['email'] = $row->email;
                $user['last_sent'] = $row->last_date_sent;
                $user['num_tries'] = $row->num_tries;
                $ret_value['invites'][] = $user;
            }
        }

        return $ret_value;
    }

    /**
     * Store a new company to the database
     * 
     * @param string $name
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $zip
     * @return array
     */
    public function store_new_company($name, $street, $city, $state, $zip, $image="NULL", $phone="")
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if ((!isset($name) || strlen($name) == 0) || (!isset($street) || strlen($street) == 0) || (!isset($city) || strlen($city) == 0) || (!isset($state) || strlen($state) == 0) || (!isset($zip) || strlen($zip) == 0)) {
            return $ret_value;
        }

        if (!isset($image) || strlen($image) == 0) {
            $image = "NULL";
        }

        if (!isset($phone) || strlen($phone) == 0) {
            $phone = "na";
        }

        // ensure company does not exist
        $company_response = $this->does_company_exist($name, $street, $city, $state, $zip);
        if ($company_response['exists']) {
            // return company id since it exists, no need to add it again
            if ($company_response['id'] > 0) {
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $company_response['id'];
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

        $today = date('Y-m-d');

        // store company
        $sql = "INSERT INTO `company` (`name`, `street`, `geo_id`, `image`, `created`, `phone`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($name).",".$this->db->escape($street).",".$this->db->escape($geo_id).",";
        $sql = $sql.$this->db->escape($image).",".$this->db->escape($today).",".$this->db->escape($phone);
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
     * Edit a company in the database
     * 
     * @param string $md5
     * @param string $name
     * @param string $street
     * @param string $city
     * @param string $state
     * @param string $zip
     * @return array
     */
    public function edit_company($md5, $name, $street, $city, $state, $zip, $phone="")
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($md5) || strlen($md5) == 0) {
            $ret_value['message'] = 'Invalid agency identifier';
            return $ret_value;
        }

        if ((!isset($name) || strlen($name) == 0) || (!isset($street) || strlen($street) == 0) || (!isset($city) || strlen($city) == 0) || (!isset($state) || strlen($state) == 0) || (!isset($zip) || strlen($zip) == 0)) {
            $ret_value['message'] = 'All fields required';
            return $ret_value;
        }

        try {

            // get geo id
            $geo_id = $this->get_geo_id($city, $state, $zip);
            if ($geo_id == 0) {
                $ret_value['message'] = 'Unable to verify address.';
                return $ret_value;
            }

            // get company id
            $company_response = $this->load_company($md5, TRUE);
            if ($company_response['result'] === FALSE) {
                $ret_value['message'] = 'Agency not found. Contact support.';
                return $ret_value;
            }

            $comp_id = $company_response['id'];

            $sql = "UPDATE `company` SET";
            $sql = $sql." `name`=".$this->db->escape($name);
            $sql = $sql." ,`street`=".$this->db->escape($street);
            $sql = $sql." ,`geo_id`=".$this->db->escape($geo_id);
            $sql = $sql." ,`phone`=".$this->db->escape($phone);
            $sql = $sql." WHERE `id`=".$this->db->escape($comp_id).";";

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
     * Edit an existing company image
     * 
     * @param string $md5
     * @param string $image_path
     * @return boolean
     */
    public function edit_company_image($md5, $image_path)
    {
        $ret_value = array('result' => FALSE);

        try {

            if (!isset($md5) || strlen($md5) == 0) {
                return $ret_value;
            }

            if (!isset($image_path) || count($image_path) == 0) {
                return $ret_value;
            }

            $sql = "UPDATE `company` SET";
            $sql = $sql." `image` = ".$this->db->escape($image_path);
            $sql = $sql." WHERE LOWER(md5(`id`)) = ".strtolower($this->db->escape($md5)).";";
            $query = $this->db->query($sql);

            if ($query !== FALSE)
            {
                $ret_value['result'] = TRUE;
                return $ret_value;
            }

        } catch (Exception $e) {
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Load company from case id
     * 
     * @param string $id
     * @return boolean
     */
    public function load_company_by_case_id($id)
    {
        $ret_value = array('result' => FALSE, 'id' => 0, 'name' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'image' => '', 'cc_type' => '', 'last_four' => '', 'exp_date' => '', 'plan' => '');

        if (!isset($id) || strlen($id) == 0) {
            return $ret_value;
        }

        // SELECT `id`, `name`, `description`, `image`, `total`, `modified` FROM `plans` WHERE 1

        $sql = "SELECT company.id, company.name, street, company.image, company.phone, city, state, zip, payment.type, payment.exp_date, payment.last_four, plans.name as plan_name, plans.total FROM company";
        $sql = $sql." INNER JOIN geo ON geo.id = company.geo_id";
        $sql = $sql." INNER JOIN user_info ON user_info.company_id = company.id";
        $sql = $sql." INNER JOIN payment ON payment.company_id = company.id";
        $sql = $sql." INNER JOIN plans ON plans.id = payment.plan_id";
        $sql = $sql." INNER JOIN `case` ON `case`.company_id = company.id";
        $sql = $sql." WHERE LOWER(MD5(`case`.id))=".$this->db->escape($id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query !== FALSE && $query->num_rows() > 0) {

            $id_stored = $query->result()[0]->id;
            $name = $query->result()[0]->name;
            $street = $query->result()[0]->street;
            $city = $query->result()[0]->city;
            $state = $query->result()[0]->state;
            $zip = $query->result()[0]->zip;
            $image = $query->result()[0]->image;
            $phone = $query->result()[0]->phone;

            $cc_type = $query->result()[0]->type;
            $last_four = $query->result()[0]->last_four;
            $exp_date = $query->result()[0]->exp_date;

            $plan = $query->result()[0]->plan_name.": $".$query->result()[0]->total." monthly";
            
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id_stored;
            $ret_value['name'] = $name;
            $ret_value['street'] = $street;
            $ret_value['city'] = $city;
            $ret_value['state'] = $state;
            $ret_value['zip'] = $zip;
            $ret_value['image'] = $image;
            $ret_value['plan'] = $plan;
            $ret_value['phone'] = $phone;

            $ret_value['cc_type'] = $cc_type;
            $ret_value['last_four'] = $last_four;
            $ret_value['exp_date'] = $exp_date;
        }

        return $ret_value;
    }

     /**
     * Load company from user id
     * 
     * @param string $id
     * @return boolean
     */
    public function load_company_by_user_id($id)
    {
        $ret_value = array('result' => FALSE, 'id' => 0, 'name' => '', 'street' => '', 'city' => '', 'state' => '', 'zip' => '', 'image' => '', 'cc_type' => '', 'last_four' => '', 'exp_date' => '', 'plan' => '');

        if (!isset($id) || strlen($id) == 0) {
            return $ret_value;
        }

        // SELECT `id`, `name`, `description`, `image`, `total`, `modified` FROM `plans` WHERE 1

        $sql = "SELECT company.id, company.name, street, company.image, company.phone, city, state, zip, payment.type, payment.exp_date, payment.last_four, plans.name as plan_name, plans.total FROM company";
        $sql = $sql." INNER JOIN geo ON geo.id = company.geo_id";
        $sql = $sql." INNER JOIN user_info ON user_info.company_id = company.id";
        $sql = $sql." INNER JOIN payment ON payment.company_id = company.id";
        $sql = $sql." INNER JOIN plans ON plans.id = payment.plan_id";
        $sql = $sql." WHERE user_info.user_id=".$this->db->escape($id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query !== FALSE && $query->num_rows() > 0) {

            $id_stored = $query->result()[0]->id;
            $name = $query->result()[0]->name;
            $street = $query->result()[0]->street;
            $city = $query->result()[0]->city;
            $state = $query->result()[0]->state;
            $zip = $query->result()[0]->zip;
            $image = $query->result()[0]->image;
            $phone = $query->result()[0]->phone;

            $cc_type = $query->result()[0]->type;
            $last_four = $query->result()[0]->last_four;
            $exp_date = $query->result()[0]->exp_date;

            $plan = $query->result()[0]->plan_name.": $".$query->result()[0]->total." monthly";
            
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id_stored;
            $ret_value['name'] = $name;
            $ret_value['street'] = $street;
            $ret_value['city'] = $city;
            $ret_value['state'] = $state;
            $ret_value['zip'] = $zip;
            $ret_value['image'] = $image;
            $ret_value['plan'] = $plan;
            $ret_value['phone'] = $phone;

            $ret_value['cc_type'] = $cc_type;
            $ret_value['last_four'] = $last_four;
            $ret_value['exp_date'] = $exp_date;
        }

        return $ret_value;
    }

    /**
     * Load company from id as either plain or md5 hashed
     * 
     * @param string $id
     * @param boolean $is_md5
     * @return boolean
     */
    public function load_company($id, $is_md5)
    {
        $ret_value = array('result' => FALSE, 'id' => 0, 'name' => '');

        if (!isset($id) || strlen($id) == 0) {
            return $ret_value;
        }

        $sql = "SELECT id, name FROM company WHERE id=".$this->db->escape($id)." LIMIT 1;";
        if ($is_md5) {
            $id = strtolower($id);
            $sql = "SELECT id, name FROM company WHERE LOWER(MD5(id))=".$this->db->escape($id)." LIMIT 1;";
        }
        
        $query = $this->db->query($sql);
        if ($query !== FALSE && $query->num_rows() > 0) {
            $id_stored = $query->result()[0]->id;
            $name = $query->result()[0]->name;
            
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id_stored;
            $ret_value['name'] = $name;
        }

        return $ret_value;
    }

    /**
     * Does company exist
     * 
     * @param string $name
     * @param string $street
     * @param string $city
     * @param string $state
     * @param stirng $zip
     * @return array
     */
    private function does_company_exist($name, $street, $city, $state, $zip)
    {
        $ret_value = array('exists' => FALSE, 'id' => 0);

        if (!isset($name) || strlen($name) == 0) {
            return $ret_value;
        }

        if (!isset($street) || strlen($street) == 0) {
            return $ret_value;
        }

        //get_geo_id($city, $state, $zip)
        $geo_id = $this->get_geo_id($city, $state, $zip);
        
        $name = strtolower($name);
        $name = trim($name);
        $street = strtolower($street);
        $street = trim($street);

        $sql = "SELECT id FROM company WHERE LOWER(name)=".$this->db->escape($name);
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

}