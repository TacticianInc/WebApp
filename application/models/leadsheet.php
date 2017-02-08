<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leadsheet extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Store a new lead entry
     * 
     * @param int $case_id
     * @param int $user_id
     * @param string $name
     * @param string $source
     * @param string $comments
     * @param bool $is_complete
     * @param string $date_assigned
     * @return array
     */
    public function add_new_entry($case_id, $user_id, $name, $source, $comments, $is_complete, $date_assigned)
    {
    	$ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

    	if (!isset($case_id) || $case_id == 0) {
    		$ret_value['message'] = "Invalid case";
            return $ret_value;
        }

        if (!isset($user_id) || $user_id == 0) {
        	$ret_value['message'] = "Invalid user";
            return $ret_value;
        }

        if ((!isset($name) || strlen($name) == 0) || (!isset($source) || strlen($source) == 0)) {
        	$ret_value['message'] = "Invalid parameters";
            return $ret_value;
        }

        if (!isset($date_assigned) || strlen($date_assigned) == 0) {
            $date_assigned = date('Y-m-d');
        }else{
            // format date
            $date_assigned = date('Y-m-d', strtotime($date_assigned));
        }

        // insert lead entry
        $sql = "INSERT INTO `lead_sheet`(`case_id`, `user_id`, `name`, `source`, `comments`, `is_complete`, `date_assigned`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($name).",";
        $sql = $sql.$this->db->escape($source).",".$this->db->escape($comments).",".$this->db->escape($is_complete).",";
        $sql = $sql.$this->db->escape($date_assigned);
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

    public function load_leads($case_id)
    {
    	$ret_value = array('result' => FALSE, 'message' => '', 'leads' => array());

    	if (!isset($case_id) || strlen($case_id) == 0) {
    		$ret_value['message'] = "Invalid case";
            return $ret_value;
        }

        $sql = "SELECT `lead_sheet`.`id`, `lead_sheet`.`user_id`, `lead_sheet`.`name`, `source`, `comments`, `is_complete`, `date_assigned`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `lead_sheet`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `lead_sheet`.`user_id`";
        $sql = $sql." WHERE LOWER(MD5(`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." ORDER BY `lead_sheet`.`date_assigned` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $lead_count = 1;

            foreach ($query->result() as $row)
            {
            	$lead = array('number' => 0, 'user_id' => '', 'id' => 0, 'title' => '', 'source' => '', 'comments' => '', 'date_assigned' => '', 'assigned_to' => '', 'is_complete' => '');

            	$lead['number'] = $lead_count;
            	$lead['user_id'] = $row->user_id;
            	$lead['id'] = $row->id;
            	$lead['title'] = $row->name;
            	$lead['source'] = $row->source;
            	$lead['comments'] = $row->comments;
            	$lead['date_assigned'] = date('m/d/Y', strtotime($row->date_assigned));
            	$lead['assigned_to'] = $row->user_name;
            	$lead['is_complete'] = $row->is_complete;

            	$ret_value['leads'][] = $lead;

            	$lead_count = $lead_count+1;
            }
        }

        return $ret_value;
    }

    public function mark_complete($lead_id, $is_complete)
    {
    	$ret_value = array('result' => FALSE, 'message' => '');

    	if (!isset($lead_id) || strlen($lead_id) == 0) {
    		$ret_value['message'] = "Invalid lead";
            return $ret_value;
        }

        $completed = 0;
        if($is_complete === TRUE || strtolower($is_complete) == "true") {
        	$completed = 1;
        }

        $sql = "UPDATE `lead_sheet`";
        $sql = $sql." SET `is_complete`=".$this->db->escape($completed);
        $sql = $sql." WHERE `id`=".$this->db->escape($lead_id);

        $query = $this->db->query($sql);
        if ($query !== FALSE){
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

}