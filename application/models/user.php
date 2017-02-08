<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Send an invitation
     * 
     * @param string $md5_company_id
     * @param string $name
     * @param string $email
     * @return boolean
     */
    public function store_invitation($md5_company_id, $name, $email)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'num_tries' => 0);;

        if (!isset($md5_company_id) || strlen($md5_company_id) == 0) {
            return $ret_value;
        }

        if (!isset($name) || strlen($name) == 0) {
            $ret_value['message'] = "Name is required.";
            return $ret_value;
        }

        if (!isset($email) || strlen($email) == 0) {
            $ret_value['message'] = "Email is required.";
            return $ret_value;
        }

        if ($this->does_user_exist($email) === TRUE) {
            $ret_value['message'] = "User already exists in system.";
            return $ret_value;
        }

        $test_name = strtolower($name);
        $test_email = strtolower($email);
        $md5_company_id = strtolower($md5_company_id);

        // determine if we already have a previous invitation
        $sql = "SELECT `id`, `name`, `email`, `last_date_sent`, `num_tries`";
        $sql = $sql." FROM `user_invite`";
        $sql = $sql." WHERE LOWER(MD5(`company_id`)) = ".$this->db->escape($md5_company_id);
        $sql = $sql." AND LOWER(`name`) = ".$this->db->escape($test_name);
        $sql = $sql." AND LOWER(`email`) = ".$this->db->escape($test_email);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $id = $query->result()[0]->id;
            $num_tries = $query->result()[0]->num_tries;

            $num_tries = intval($num_tries) + 1;

            // update
            $sql = "UPDATE `user_invite`";
            $sql = $sql." SET `num_tries` = ".$this->db->escape($num_tries);
            $sql = $sql." WHERE `id` = ".$this->db->escape($id);

            $query = $this->db->query($sql);

            if ($query !== FALSE) {
                $ret_value['result'] = TRUE;
                $ret_value['num_tries'] = $num_tries;
            }

        }
        else
        {
            $num_tries = 1;

            $company_id = 0;

            // load company id from md5
            $sql = "SELECT id FROM company WHERE LOWER(MD5(`id`))=".$this->db->escape($md5_company_id)." LIMIT 1;";
            
            $query = $this->db->query($sql);

            if ($query !== FALSE && $query->num_rows() > 0) {
                $company_id = $query->result()[0]->id;

                // new entry
                $sql = "INSERT INTO `user_invite` (`company_id`, `name`, `email`, `num_tries`)";
                $sql = $sql." VALUES (";
                $sql = $sql.$this->db->escape($company_id).",".$this->db->escape($name).",".$this->db->escape($email).",".$this->db->escape($num_tries);
                $sql = $sql." );";

                $query = $this->db->query($sql);
                
                if ($query !== FALSE) {
                    $ret_value['result'] = TRUE;
                    $ret_value['num_tries'] = $num_tries;
                }
            }
            
        }

        return $ret_value;
    }

    /**
     * Validate a user email address on replying to an email
     * 
     * @param string $md5
     * @return boolean
     */
    public function validate_user_email($md5)
    {
        $ret_value = FALSE;

        if (!isset($md5) || strlen($md5) == 0) {
            return $ret_value;
        }

        $md5 = strtolower($md5);

        $sql = "UPDATE `user` SET `is_valid` = 1";
        $sql = $sql." WHERE MD5(id) = ".$this->db->escape($md5).";";

        $query = $this->db->query($sql);
        if ($query !== FALSE) {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    /**
     * Validate a user sign in attempt
     * 
     * @param string $email
     * @param string $password
     * @return array
     */
    public function validate_user($email, $password)
    {
    	$ret_value = array('result' => FALSE, 'message' => '');

    	if (!isset($email) || !isset($password)) {
            $ret_value['message'] = "Valid email and password required.";
			return $ret_value;
		}

    	$ci = get_instance();
		$ci->load->helper('password');

        // validate email
        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $ret_value['message'] = "Email is invalid. Please check and try again.";
            return $ret_value;
        }

		try {

			$email = trim(strtolower($email));

			$sql = "SELECT password FROM user WHERE LOWER(email)=".$this->db->escape($email)." LIMIT 1";
			$query = $this->db->query($sql);

			if ($query !== FALSE && $query->num_rows() > 0) {
				$hash = $query->result()[0]->password;
				$ret_value['result'] = verify_password($password, $hash);
                if ($ret_value['result'] === FALSE) {
                    $ret_value['message'] = "Invalid email and/or password.";
                }
			} else {
                $ret_value['message'] = "We do not have a record of that email address.";
            }

			return $ret_value;

		} catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
			return $ret_value;
		}
    }

    /**
     * Remove a user from the system
     * 
     * @param string $id
     * @return array
     */
    public function remove_user($id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($id) || strlen($id) == 0) {
            $ret_value['message'] = "Id not found";
            return $ret_value;
        }

        try {

            // run in transaction
            $this->db->trans_start();

            $sql = "DELETE FROM `user`";
            $sql = $sql." WHERE `id`=".$this->db->escape($id);

            $this->db->query($sql);

            $sql = "DELETE FROM `user_info`";
            $sql = $sql." WHERE `user_id`=".$this->db->escape($id);

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
            }

            return $ret_value;

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Load a complete user from the database
     * 
     * @param string $email
     * @return array
     */
    public function load_user($email)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0, 'is_admin' => 0, 'company_id' => 0, 'company_name' => 0, 'name' => '', 'title' => '', 'image' => '');

        if (!isset($email) || strlen($email) == 0) {
            $ret_value['message'] = "Email not given";
            return $ret_value;
        }

        try {

            $email = trim($email);
            $email = strtolower($email);

            $sql = "SELECT `user`.`id`, `company_id`, `company`.`name` as company_name, `is_admin`, `user_info`.`name`, `user_info`.`title`, `user_info`.`image`";
            $sql = $sql." FROM `user`";
            $sql = $sql." LEFT JOIN `user_info` ON `user_info`.user_id = `user`.id";
            $sql = $sql." LEFT JOIN `company` ON `company`.id = `user_info`.`company_id`";
            $sql = $sql." WHERE `is_valid` = 1 AND LOWER(`email`) = ".$this->db->escape($email)." LIMIT 1;";

            $query = $this->db->query($sql);

            if ($query !== FALSE && $query->num_rows() > 0)
            {
                $id = intval($query->result()[0]->id);
                $is_admin = $query->result()[0]->is_admin;
                $name = trim($query->result()[0]->name);
                $company_id = $query->result()[0]->company_id;
                $company_name = $query->result()[0]->company_name;
                $title = trim($query->result()[0]->title);
                $image = "";
                if(isset($query->result()[0]->image) && strlen($query->result()[0]->image) > 0) {
                    $image = $query->result()[0]->image;
                }
                
                $ret_value['result'] = TRUE;
                $ret_value['id'] = $id;
                $ret_value['is_admin'] = $is_admin;
                $ret_value['company_id'] = $company_id;
                $ret_value['company_name'] = $company_name;
                $ret_value['name'] = $name;
                $ret_value['title'] = $title;
                $ret_value['image'] = $image;

            } else {
                $ret_value['message'] = "You must First validate your email address.";
            }

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Store a new user to the database
     * 
     * @param integer $company_id
     * @param string $name
     * @param string $title
     * @param string $email
     * @param string $password
     * @param boolean $is_admin
     * @param blob $image
     * @return array
     */
    public function store_new_user($company_id, $name, $title, $email, $password, $is_admin=0, $image="NULL")
    {
    	$ret_value = array('result' => FALSE, 'id' => '', 'message' => '', 'elem' => '');

        $ci = get_instance();
        $ci->load->helper('password');

        if (!isset($company_id)) {
            $ret_value['message'] = "Unable to create account.";
            $ret_value['elem'] = "company";
            return $ret_value;
        }

        // validate email
    	if (!isset($email) || strlen($email) == 0) {
            $ret_value['message'] = "Email is required";
            $ret_value['elem'] = "email";
			return $ret_value;
		}else if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $ret_value['message'] = "Email is invalid. Please check and try again.";
            $ret_value['elem'] = "email";
            return $ret_value;
        }

        // validate password
        if (!isset($password) || strlen($password) == 0) {
            $ret_value['message'] = "Password is required";
            $ret_value['elem'] = "password";
            return $ret_value;
        }else if (strlen($password) < 8 || strlen($password) > 50) {
            $ret_value['message'] = "Password must be 8 to 50 characters.";
            $ret_value['elem'] = "password";
            return $ret_value;
        }

        // validate name
        if (!isset($name) || strlen($name) == 0) {
            $ret_value['message'] = "Name is required";
            $ret_value['elem'] = "name";
            return $ret_value;
        }else if (strlen($name) < 3 || strlen($name) > 50) {
            $ret_value['message'] = "Name must be 3 to 50 characters.";
            $ret_value['elem'] = "name";
            return $ret_value;
        }

        // validate title
        if (!isset($title) || strlen($title) == 0) {
            $ret_value['message'] = "Title is required";
            $ret_value['elem'] = "title";
            return $ret_value;
        }else if (strlen($title) < 3 && strlen($title) > 50) {
            $ret_value['message'] = "Title must be 3 to 50 characters.";
            $ret_value['elem'] = "title";
            return $ret_value;
        }

        if (!isset($image) || strlen($image) == 0) {
            $image = "NULL";
        }

    	try {

            // ensure user does not already exist
            if ($this->does_user_exist($email)) {
                $ret_value['message'] = "Email already exists in our system. Please Sign In.";
                return $ret_value;
            }

            $hash = create_hash($password);

            $today = date('Y-m-d');
            $email = strtolower($email);

            // run in transaction
            $this->db->trans_start();

            $sql = "INSERT INTO `user` (`email`, `password`, `is_admin`, `created`)";
            $sql = $sql." VALUES (";
            $sql = $sql.$this->db->escape($email).",".$this->db->escape($hash).",".$this->db->escape($is_admin).",".$this->db->escape($today);
            $sql = $sql." );";

            $this->db->query($sql);

            $user_id = $this->db->insert_id();

            $sql = "INSERT INTO `user_info`(`user_id`, `company_id`, `name`, `title`, `image`, `created`)";
            $sql = $sql." VALUES (";
            $sql = $sql.$this->db->escape($user_id).",".$this->db->escape($company_id).",".$this->db->escape($name).",";
            $sql = $sql.$this->db->escape($title).",".$this->db->escape($image).",".$this->db->escape($today);
            $sql = $sql." );";

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
                $ret_value['id'] = $user_id;

                // now update invitation
                $this->accept_invitation($email,$company_id);
            }

            return $ret_value;

    	} catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
    		return $ret_value;
    	}
    }

    /**
     * Edit an existing user role (admin or other)
     * 
     * @param string $id
     * @param string $role (0 or 1)
     * @return array
     */
    public function edit_user_role($id, $role)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        try {

            if (!isset($id) || strlen($id) == 0) {
                $ret_value['message'] = "User not found";
                return $ret_value;
            }

            if (!isset($role) || strlen($role) == 0) {
                $ret_value['message'] = "No role given";
                return $ret_value;
            }

            $sql = "UPDATE `user` SET";
            $sql = $sql." `is_admin` = ".$this->db->escape($role);
            $sql = $sql." WHERE `id` = ".$this->db->escape($id).";";
            $query = $this->db->query($sql);

            if ($query !== FALSE)
            {
                $ret_value['result'] = TRUE;
                return $ret_value;
            }

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Edit an existing user image
     * 
     * @param string $md5
     * @param string $image_path
     * @return array
     */
    public function edit_user_image($md5, $image_path)
    {
        $ret_value = array('result' => FALSE);

        try {

            if (!isset($md5) || strlen($md5) == 0) {
                return $ret_value;
            }

            if (!isset($image_path) || count($image_path) == 0) {
                return $ret_value;
            }

            $sql = "UPDATE `user_info` SET";
            $sql = $sql." `image` = ".$this->db->escape($image_path);
            $sql = $sql." WHERE LOWER(md5(`user_id`)) = ".strtolower($this->db->escape($md5)).";";
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
     * Edit an existing user
     * 
     * @param string $md5
     * @param string $name
     * @param string $title
     * @param string $company_id
     * @return array
     */
    public function edit_user($md5, $name, $title, $company_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        try {

            if (!isset($md5) || strlen($md5) == 0) {
                $ret_value['message'] = "User not found";
                return $ret_value;
            }

            $md5 = strtolower($md5);

            $sql = "UPDATE `user_info` SET";
            $inner_sql = "";

            if (isset($name) && strlen($name) > 0) {
                $inner_sql = " `name` = ".$this->db->escape($name).",";
            }
            
            if (isset($title) && strlen($title) > 0) {
                $inner_sql = $inner_sql." `title` = ".$this->db->escape($title).",";
            }

            if (isset($company_id) && $company_id > 0) {
                $inner_sql = $inner_sql." `company_id` = ".$this->db->escape($company_id).",";
            }

            // remove last comma
            $inner_sql = trim($inner_sql, ",");

            $sql = $sql.$inner_sql;
            $sql = $sql." WHERE LOWER(MD5(`user_id`)) = ".$this->db->escape($md5).";";

            $query = $this->db->query($sql);
            if ($query !== FALSE) {
                $ret_value['result'] = TRUE;
            }

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Change email
     * 
     * @param string $md5
     * @param string $new_email
     * @param boolean $ignore_same
     * @return array
     */
    public function change_email_md5($md5, $new_email, $ignore_same=FALSE)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($md5) || strlen($md5) == 0) {
            $ret_value['message'] = "Missing account identifier.";
            return $ret_value;
        }

        if (!isset($new_email) || strlen($new_email) == 0) {
            $ret_value['message'] = "New email is required.";
            return $ret_value;
        }

        try {

            // if ignore same then do nothing if old email is presented
            if ($ignore_same) {
                $sql = "SELECT `email` FROM `user`";
                $sql = $sql." WHERE LOWER(MD5(`id`)) = ".$this->db->escape($md5).";";
                $query = $this->db->query($sql);
                if ($query !== FALSE && $query->num_rows() > 0) {
                    $email = $query->result()[0]->email;
                    if (strtolower($email) == strtolower($new_email)) {
                        $ret_value['result'] = TRUE;
                        return $ret_value;
                    }
                }
            }

            // ensure email does not exist
            if ($this->does_user_exist($new_email)) {
                $ret_value['message'] = "This email is already in use.";
                return $ret_value;
            }

            $new_email = strtolower($new_email);
            $md5 = strtolower($md5);

            // update email
            $sql = "UPDATE `user` SET `email`=".$this->db->escape($new_email);
            $sql = $sql." WHERE LOWER(MD5(`id`)) = ".$this->db->escape($md5).";";

            $query = $this->db->query($sql);
            if ($query !== FALSE) {
                $ret_value['result'] = TRUE;
            }

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Change email
     * 
     * @param string $old_email
     * @param string $new_email
     * @return array
     */
    public function change_email($old_email, $new_email)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if (!isset($old_email) || strlen($old_email) == 0) {
            $ret_value['message'] = "Old email is required.";
            return $ret_value;
        }

        if (!isset($new_email) || strlen($new_email) == 0) {
            $ret_value['message'] = "New email is required.";
            return $ret_value;
        }

        try {

            // get user id by email
            $user_id = $this->get_userid_by_email($old_email);

            if ($user_id == 0) {
                $ret_value['message'] = "User not found";
                return $ret_value;
            }

            // ensure email does not exist
            if ($this->does_user_exist($new_email)) {
                $ret_value['message'] = "This email is already in use.";
                return $ret_value;
            }

            $new_email = strtolower($new_email);

            // update email
            $sql = "UPDATE `user` SET `email`=".$this->db->escape($new_email);
            $sql = $sql." WHERE `id` = ".$this->db->escape($user_id).";";

            $query = $this->db->query($sql);
            if ($query !== FALSE) {
                $ret_value['result'] = TRUE;
            }

        } catch (Exception $e) {
            $ret_value['message'] = "Unable to process request. Please try again later.";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Change a password insecure - used for internal changing
     * 
     * @param string $md5
     * @param string $new_password
     * @return array
     */
    public function change_password_insecure($md5, $new_password)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        $this->load->helper('password');

        if (!isset($md5) || strlen($md5) == 0) {
            $ret_value['message'] = "Missing account identifier.";
            return $ret_value;
        }

        if (!isset($new_password) || strlen($new_password) == 0) {
            $ret_value['message'] = "New password is required.";
            return $ret_value;
        }

        // get email from user id hash
        $sql = "SELECT `email` FROM `user` WHERE `is_valid` = 1";
        $sql = $sql." AND LOWER(MD5(`id`))=".$this->db->escape($md5);
        
        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $email = $query->result()[0]->email;

            // update password
            $new_password_hash = create_hash($new_password);
            $ret_value['result'] = $this->update_password($email, $new_password_hash);

        } else {
            $ret_value['message'] = "Account not found.";
        }

        return $ret_value;
    }

    /**
     * Change a password
     * 
     * @param string $email
     * @param string $old_password
     * @param string $new_password
     * @return array
     */
    public function change_password($email, $old_password, $new_password)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        $this->load->helper('password');

        if (!isset($email) || strlen($email) == 0) {
            $ret_value['message'] = "Email is required.";
            return $ret_value;
        }

        if (!isset($old_password) || strlen($old_password) == 0) {
            $ret_value['message'] = "Old password is required.";
            return $ret_value;
        }

        if (!isset($new_password) || strlen($new_password) == 0) {
            $ret_value['message'] = "New password is required.";
            return $ret_value;
        }else if (strlen($password) < 8 || strlen($password) > 50) {
            $ret_value['message'] = "New must be 8 to 50 characters.";
            return $ret_value;
        }

        // check existing password
        $email = strtolower($email);

        $sql = "SELECT `password` FROM `user` WHERE `is_valid` = 1";
        $sql = $sql." AND LOWER(`email`)=".$this->db->escape($email);
        
        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $hash = $query->result()[0]->password;

            // verify old password is correct
            if(verify_password($password, $hash)) {

                // ensure old and new password are not same
                if ($new_password == $old_password) {
                    $ret_value['message'] = "New password must NOT be the same as Old.";
                    return $ret_value;
                }

                // update password
                $new_password_hash = create_hash($new_password);
                $ret_value['result'] = $this->update_password($email, $new_password_hash);

            } else {
                // invalid old password
                $ret_value['message'] = "Old password is not correct. Please check and try again.";
                return $ret_value;
            }

        } else {
            $ret_value['message'] = "No Account Found for email: ".$email.".";
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Reset a password
     * 
     * @param string $email
     * @return string
     */
    public function reset_password($email)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'new_password' => '');

        $ci = get_instance();
        $ci->load->helper('password');

        if (!isset($email) || strlen($email) == 0) {
            $ret_value['message'] = "Email is required.";
            return $ret_value;
        }

        // generate new random password 10 characters long
        $new_password = gen_random_password();

        if (isset($new_password) && strlen($new_password) == 10) {

            $new_password_hash = create_hash($new_password);

            if (!$this->update_password($email, $new_password_hash)) {
                $ret_value['message'] = "Unable to process request. Please try again later.";
            }

            $ret_value['result'] = TRUE;
            $ret_value['new_password'] = $new_password;

        } else {
            $ret_value['message'] = "Unable to process request. Please try again later.";
        }

        return $ret_value;
    }

    public function get_email_by_id($user_id)
    {
        $ret_value = "";

        try {

            if (!isset($md5) || strlen($md5) == 0) {
                return $ret_value;
            }

            $sql = "SELECT `email` FROM `user`";
            $sql = $sql." WHERE `id` = ".$this->db->escape($user_id)." LIMIT 1;";
            $query = $this->db->query($sql);

            if ($query !== FALSE && $query->num_rows() > 0) {
                $ret_value = $query->result()[0]->id;
            }

        } catch (Exception $e) {
            return $ret_value;
        }

        return $ret_value;
    }

    public function get_userid_by_md5($md5)
    {
        $ret_value = 0;

        try {

            if (!isset($md5) || strlen($md5) == 0) {
                return $ret_value;
            }

            $sql = "SELECT `id` FROM `user`";
            $sql = $sql." WHERE LOWER(md5(`id`)) = ".strtolower($this->db->escape($md5))." LIMIT 1;";
            $query = $this->db->query($sql);

            if ($query !== FALSE && $query->num_rows() > 0) {
                $ret_value = $query->result()[0]->id;
            }

        } catch (Exception $e) {
            return $ret_value;
        }

        return $ret_value;
    }

    /**
     * Update a user password with new hash
     * 
     * @param string $email
     * @return boolean
     */
    private function update_password($email, $new_password_hash)
    {
        $ret_value = FALSE;

        if (!isset($email) || strlen($email) == 0) {
            return $ret_value;
        }

        if (!isset($new_password_hash) || strlen($new_password_hash) == 0) {
            return $ret_value;
        }

        try {

            $email = strtolower($email);

            $sql = "UPDATE `user` SET";
            $sql = $sql." `password` = ".$this->db->escape($new_password_hash);
            $sql = $sql." WHERE LOWER(`email`) = ".$this->db->escape($email);

            $query = $this->db->query($sql);
            if ($query !== FALSE) {
                $ret_value = TRUE;
            }

        }  catch (Exception $e) {
            $ret_value = FALSE;
        }

        return $ret_value;
    }

    /**
     * Get user id by email
     * 
     * @param string $email
     * @return integer
     */
    private function get_userid_by_email($email)
    {
        $ret_value = 0;

        if (!isset($email) || strlen($email) == 0) {
            return $ret_value;
        }

        $email = strtolower($email);

        $sql = "SELECT id FROM user WHERE LOWER(email)=".$this->db->escape($email)." LIMIT 1";
        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $id = $query->result()[0]->id;
            return intval($id);
        }

        return $ret_value;
    }

    /**
     * Does user exist
     * 
     * @param string $email
     * @return boolean
     */
    private function does_user_exist($email)
    {
        $ret_value = FALSE;

        if (!isset($email) || strlen($email) == 0) {
            return $ret_value;
        }

        $email = strtolower($email);

        $sql = "SELECT id FROM user WHERE LOWER(email)=".$this->db->escape($email)." LIMIT 1";
        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    /**
     * Accept an invitation: remove from invitation list once succesful registered
     * 
     * @param string $email
     * @param string $company_id
     * @return boolean
     */
    private function accept_invitation($email, $company_id)
    {
        $ret_value = FALSE;

        try {

            if (!isset($email) || strlen($email) == 0) {
                return $ret_value;
            }

            if (!isset($company_id) || strlen($company_id) == 0) {
                return $ret_value;
            }

            $email = strtolower($email);

            $sql = "DELETE FROM `user_invite`";
            $sql = $sql." WHERE LOWER(`email`) = ".$this->db->escape($email);
            $sql = $sql." AND `company_id` = ".$this->db->escape($company_id);
            $query = $this->db->query($sql);

            if ($query !== FALSE)
            {
                $ret_value = TRUE;
            }

        } catch (Exception $e) {
            return $ret_value;
        }

        return $ret_value;
    }

}