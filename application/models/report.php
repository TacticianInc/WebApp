<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    public function get_report_id_md5($report_id)
    {
        $ret_value = 0;

        $sql = "SELECT `report`.`id` FROM `report`";
        $sql = $sql." WHERE LOWER(MD5(`report`.`id`))=".strtolower($this->db->escape($report_id));
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];
            $ret_value = $row->id;
        }

        return $ret_value;
    }

    public function gen_report_data($report_id, $user_id=0)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'report' => array(), 'case' => array(), 'company' => array(), 'client' => array(), 'documents' => array(), 'interviews' => array(), 'attachments' => array());

        if(!isset($report_id) || $report_id == 0) {
            $ret_value['message'] = "No report found.";
            return $ret_value;
        }

        $sql = "SELECT `report`.`id`, `report`.`case_id`, `report`.`author_id`, `report`.`name`, `report`.`created`, `case`.`company_id`, `case`.`client_id`, `case`.`name` AS case_name, `case`.`synopsis_id`, `case`.`created` AS case_created, `user_info`.`name` AS author_name, `user_info`.`image` AS author_image FROM `report`";
        $sql = $sql." LEFT JOIN `case` ON `report`.`case_id` = `case`.`id`";
        $sql = $sql." LEFT JOIN `report_share` ON `report`.`id` = `report_share`.`report_id`";
        $sql = $sql." LEFT JOIN `user_info` ON `report`.`author_id` = `user_info`.`user_id`";
        $sql = $sql." WHERE `report`.`id`=".$this->db->escape($report_id);
        if($user_id !== 0){
            $sql = $sql." AND (`report`.`author_id`=".$this->db->escape($user_id);
            $sql = $sql." OR `report_share`.`user_id`=".$this->db->escape($user_id).")";
        }
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $report = array('name' => '', 'created' => '');
            $case = array();
            $company = array();
            $client = array();
            $documents = array();
            $interviews = array();
            $attachments = array();

            $row = $query->result()[0];

            $report['name'] = $row->name;
            $report['created'] = $row->created;

            $case['author'] = $row->author_name;
            $case['case_id'] = $row->case_id;
            $case['name'] = $row->case_name;
            $case['created'] = $row->case_created;
            $case['synopsis'] = $this->get_synopsis($row->synopsis_id);
            
            $company = $this->get_company($row->company_id);

            $client = $this->get_client($row->client_id);

            $documents = $this->get_document_list($row->case_id);

            $attachments = $this->get_attachment_list($row->case_id);

            $interviews = $this->get_interviews($row->case_id);

            $supporting = $this->get_supporting_roles($row->case_id);

            $ret_value['report'] = $report;
            $ret_value['case'] = $case;
            $ret_value['company'] = $company;
            $ret_value['client'] = $client;
            $ret_value['documents'] = $documents;
            $ret_value['interviews'] = $interviews;
            $ret_value['attachments'] = $attachments;
            $ret_value['supporting'] = $supporting;
        }

        return $ret_value;
    }

    private function get_interviews($case_id)
    {
        $ret_value = array();

        if(!isset($case_id) || $case_id == 0) {
            return $ret_value;
        }

        // SELECT `id`, `case_id`, `user_id`, `lead_entry_id`, `name`, `description`, `date_occured`, `modified`, `is_approved`, `supervisor_id` FROM `interview` WHERE 1

        $sql = "SELECT `interview`.`id`, `interview`.`notes`, `interview`.`dob`, `interview`.`location`, `interview`.`name`, `interview`.`description`, `interview`.`date_occured`, `user_info`.`name` AS author_name,";
        $sql = $sql." `interview`.`title`, `interview`.`employer`, `interview`.`street`, `interview`.`city`, `interview`.`state`, `interview`.`zip`, `interview`.`phone`";
        $sql = $sql." FROM `interview`";
        $sql = $sql." LEFT JOIN `user_info` ON `interview`.`user_id` = `user_info`.`user_id`";
        $sql = $sql." WHERE `interview`.`case_id`=".$this->db->escape($case_id);
        $sql = $sql." AND `interview`.`is_approved` = 1";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $doc = array();

                $doc['location'] = $row->location;
                $doc['name'] = $row->name;
                $doc['title'] = $row->title;
                $doc['employer'] = $row->employer;
                $doc['street'] = $row->street;
                $doc['city'] = $row->city;
                $doc['state'] = $row->state;
                $doc['zip'] = $row->zip;
                $doc['dob'] = $row->dob;
                $doc['notes'] = $row->notes;
                $doc['description'] = $row->description;
                $doc['date_occured'] = $row->date_occured;
                $doc['author_name'] = $row->author_name;
                $doc['attachments'] = $this->get_int_attachment_list($row->id);

                $ret_value[] = $doc;
            }
        }

        return $ret_value;
    }

    private function get_roles_for_case($case_id)
    {
        $ret_value = array('attorney_id' => 0, 'cpa_id' => 0, 'le_agent_id' => 0, 'da_id' => 0);

        if(!isset($case_id) || $case_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `attorney_id`, `cpa_id`, `le_agent_id`, `district_attorney_id`";
        $sql = $sql." FROM `case`";
        $sql = $sql." WHERE `case`.`id`=".$this->db->escape($case_id);


        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];
            $ret_value['attorney_id'] = $row->attorney_id;
            $ret_value['cpa_id'] = $row->cpa_id;
            $ret_value['le_agent_id'] = $row->le_agent_id;
            $ret_value['da_id'] = $row->district_attorney_id;
        }

        return $ret_value;
    }

    private function get_supporting_roles($case_id)
    {
        $ret_value = array();

        $case_data = $this->get_roles_for_case($case_id);

        $attorney_id = $case_data['attorney_id'];
        $cpa_id = $case_data['cpa_id'];
        $le_agent_id = $case_data['le_agent_id'];
        $da_id = $case_data['da_id'];

        // get supporting data
        $sql = "SELECT `supporting`.`id`, `type`, `supporting`.`name`, `title`, `street`, city, state, zip, email, phone, `supporting_type`.name AS profession";
        $sql = $sql." FROM `supporting`";
        $sql = $sql." INNER JOIN `supporting_type` ON `supporting_type`.id = `supporting`.`type`";
        $sql = $sql." INNER JOIN geo ON geo.id = `supporting`.geo_id";
        $sql = $sql." WHERE `supporting`.`id` IN (".$cpa_id.",".$attorney_id.",".$le_agent_id.",".$da_id.")";
        $sql = $sql." AND `supporting`.`id` > 0";
        $sql = $sql." ORDER BY `supporting_type`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {

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

                $ret_value[] = $spt;
            }
        }

        return $ret_value;
    }

    private function get_int_attachment_list($int_id)
    {
        $ret_value = array();

        if(!isset($int_id) || $int_id == 0) {
            return $ret_value;
        }

        //SELECT `id`, `case_id`, `user_id`, `interview_id`, `title`, `type`, `size`, `location`, `created`, `is_approved` FROM `attachments`

        $sql = "SELECT `attachments`.`id`,`interview_id`, `title`, `size`, `location`, `attachments`.`name` AS attachment_name  FROM `attachments`";
        $sql = $sql." LEFT JOIN `attachment_type` ON `attachments`.`type` = `attachment_type`.`id`";
        $sql = $sql." WHERE `interview_id`=".$this->db->escape($int_id);
        $sql = $sql." AND `interview_id` > 0;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $doc = array();

                $doc['id'] = $row->id;
                $doc['title'] = $row->title;
                $doc['size'] = $row->size;
                $doc['location'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;
                $doc['attachment_name'] = $row->attachment_name;

                $ret_value[] = $doc;
            }
        }

        return $ret_value;
    }

    private function get_attachment_list($case_id)
    {
        $ret_value = array();

        if(!isset($case_id) || $case_id == 0) {
            return $ret_value;
        }

        //SELECT `id`, `case_id`, `user_id`, `interview_id`, `title`, `type`, `size`, `location`, `created`, `is_approved` FROM `attachments`

        $sql = "SELECT `attachments`.`id`,`interview_id`, `title`, `size`, `location`, `attachments`.`name` AS attachment_name  FROM `attachments`";
        $sql = $sql." LEFT JOIN `attachment_type` ON `attachments`.`type` = `attachment_type`.`id`";
        $sql = $sql." WHERE `attachments`.`case_id`=".$this->db->escape($case_id);
        $sql = $sql." AND `interview_id` = 0;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $doc = array();

                $doc['id'] = $row->id;
                $doc['title'] = $row->title;
                $doc['size'] = $row->size;
                $doc['location'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;
                $doc['attachment_name'] = $row->attachment_name;

                $ret_value[] = $doc;
            }
        }

        return $ret_value;
    }

    private function get_document_list($case_id)
    {
        $ret_value = array();

        if(!isset($case_id) || $case_id == 0) {
            return $ret_value;
        }

        // SELECT `id`, `doc_id`, `case_id`, `user_id`, `date_added` FROM `documents_included` WHERE 1
        // SELECT `id`, `company_id`, `user_id`, `name`, `title`, `location`, `size`, `document_type`, `attachment_type`, `date_added` FROM `documents` WHERE 1
        // SELECT `id`, `name`, `category` FROM `document_type` WHERE 1
        // SELECT `id`, `name`, `mime`, `pfix` FROM `attachment_type` WHERE 1

        $sql = "SELECT `document_type`.`name`, `documents`.`location`, `attachment_type`.`name` AS attachment_name, `attachment_type`.`mime`  FROM `documents_included`";
        $sql = $sql." LEFT JOIN `documents` ON `documents_included`.`doc_id` = `documents`.`id`";
        $sql = $sql." LEFT JOIN `document_type` ON `documents`.`document_type` = `document_type`.`id`";
        $sql = $sql." LEFT JOIN `attachment_type` ON `documents`.`attachment_type` = `attachment_type`.`id`";
        $sql = $sql." WHERE `documents_included`.`case_id`=".$this->db->escape($case_id).";";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $doc = array();

                $doc['name'] = $row->name;
                $doc['location'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;
                $doc['attachment_name'] = $row->attachment_name;
                $doc['mime'] = $row->mime;

                $ret_value[] = $doc;
            }
        }

        return $ret_value;
    }

    private function get_client($client_id)
    {
        $ret_value = array();

        if(!isset($client_id) || $client_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `client`.`id`, `client`.`name`, `client`.`street`, city, state, zip, `client`.`image`, phone FROM `client`";
        $sql = $sql." INNER JOIN geo ON geo.id = client.geo_id";
        $sql = $sql." WHERE `client`.`id`=".$this->db->escape($client_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];

            $ret_value['id'] = $row->id;
            $ret_value['name'] = $row->name;
            $ret_value['street'] = $row->street;
            $ret_value['city'] = $row->city;
            $ret_value['state'] = $row->state;
            $ret_value['zip'] = $row->zip;
            $ret_value['phone'] = $row->phone;
            $ret_value['image'] = "https://s3.amazonaws.com/tacticiandocs/".$row->image;
        }

        return $ret_value;
    }

    private function get_company($company_id)
    {
        $ret_value = array();

        if(!isset($company_id) || $company_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT company.id, company.name, street, company.image, city, state, zip, phone FROM company";
        $sql = $sql." INNER JOIN geo ON geo.id = company.geo_id";
        $sql = $sql." WHERE company.id=".$this->db->escape($company_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];

            $ret_value['id'] = $row->id;
            $ret_value['name'] = $row->name;
            $ret_value['image'] = $row->image;
            $ret_value['street'] = $row->street;
            $ret_value['city'] = $row->city;
            $ret_value['state'] = $row->state;
            $ret_value['zip'] = $row->zip;
            $ret_value['phone'] = $row->phone;
        }

        return $ret_value;
    }

    private function get_synopsis($synopsis_id)
    {
        $ret_value = array();

        if(!isset($synopsis_id) || $synopsis_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `name`, `size`, `contents`, `location`, `att_type`";
        $sql = $sql." FROM `synopsis`";
        $sql = $sql." WHERE `id`=".$this->db->escape($synopsis_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];

            $ret_value['name'] = $row->name;
            $ret_value['size'] = $row->size;
            $ret_value['contents'] = $row->contents;
            $ret_value['location'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;
            $ret_value['att_type'] = $row->att_type;
            
        }

        return $ret_value;
    }

    public function get_reports($user_id,$case_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'reports' => array());

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        $sql = "SELECT `report`.`id`, is_redacted, `report`.`case_id`, `report`.`author_id`, `report`.`name`, `report`.`created`, `case`.`name` AS case_name, `user_info`.`name` AS author_name, `user_info`.`image` AS author_image FROM `report`";
        $sql = $sql." LEFT JOIN `case` ON `report`.`case_id` = `case`.`id`";
        $sql = $sql." LEFT JOIN `report_share` ON `report`.`id` = `report_share`.`report_id`";
        $sql = $sql." LEFT JOIN `user_info` ON `report`.`author_id` = `user_info`.`user_id`";
        $sql = $sql." WHERE LOWER(MD5(`report`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." AND (`report`.`author_id`=".$this->db->escape($user_id);
        $sql = $sql." OR `report_share`.`user_id`=".$this->db->escape($user_id).");";
        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $rpt = array('id' => 0, 'user_id' => '', 'case_id' => '', 'case_name' => '', 'date_occured' => '', 'name' => '', 'author_name' => '', 'author_image' => '', 'shared' => array(), 'is_redacted' => 0);

                $rpt['id'] = $row->id;
                $rpt['case_id'] = $row->case_id;
                $rpt['case_name'] = $row->case_name;
                $rpt['date_occured'] = $row->created;
                $rpt['name'] = $row->name;
                $rpt['user_id'] = $user_id;
                $rpt['author_name'] = $row->author_name;
                $rpt['author_id'] = $row->author_id;
                $rpt['author_image'] = $row->author_image;
                $rpt['is_redacted'] = $row->is_redacted;

                $rpt['shared'] = $this->get_shared($row->id);

                $ret_value['reports'][] = $rpt;
            }
        }

        return $ret_value;
    }

    public function add_new_report($user_id,$case_id,$name)
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

        $date_occured = date('Y-m-d');

        $sql = "INSERT INTO `report`(`case_id`, `author_id`, `name`, `created`) VALUES (";
        $sql = $sql.$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($name).",".$this->db->escape($date_occured);
        $sql = $sql.");";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $id = $this->db->insert_id();
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id;
        }

        return $ret_value;
    }

    public function share_report($report_id,$user_id)
    {
        $ret_value = array('result' => FALSE);

        if(!isset($report_id) || $report_id == 0) {
            $ret_value['message'] = "No report found.";
            return $ret_value;
        }

        // NOTE: Do not check user_id so we can remove them

        $date_occured = date('Y-m-d');

        // First unshare entire report
        $this->db->trans_start();

        $sql = "DELETE FROM `report_share`";
        $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id);

        $this->db->query($sql);

        // Next add share so as to unshare unselected
        if (isset($user_id) && $user_id > 0) {
            $sql = "INSERT INTO `report_share`(`user_id`, `report_id`, `created`) VALUES (";
            $sql = $sql.$this->db->escape($user_id).",".$this->db->escape($report_id).",".$this->db->escape($date_occured);
            $sql = $sql.");";

            $this->db->query($sql);
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
        }
        else
        {
            $this->db->trans_commit();
            $ret_value['result'] = TRUE;
        }
        
        return $ret_value;
    }

    public function delete_report($report_id)
    {
        $ret_value = array('result' => FALSE);

        if(!isset($report_id) || $report_id == 0) {
            $ret_value['message'] = "No report found.";
            return $ret_value;
        }

        $sql = "DELETE FROM `report` WHERE `id`=".$this->db->escape($report_id).";";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {

            $sql = "DELETE FROM `report_share` WHERE `report_id`=".$this->db->escape($report_id).";";

            $query = $this->db->query($sql);

            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    private function get_shared($report_id)
    {
        $ret_value = array();

        if(!isset($report_id) || $report_id == 0) {
            $ret_value['message'] = "No report found.";
            return $ret_value;
        }

        $sql = "SELECT `report_share`.`user_id`, `user_info`.`name` AS user_name, `user_info`.`title`, `user_info`.`image` FROM `report_share`";
        $sql = $sql." LEFT JOIN `user_info` ON `report_share`.`user_id` = `user_info`.`user_id`";
        $sql = $sql." WHERE `report_share`.`report_id`=".$this->db->escape($report_id).";";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $rpt = array('user_id' => 0, 'user_name' => '', 'user_title' => '', 'image' => '');

                $rpt['user_id'] = $row->user_id;
                $rpt['user_name'] = $row->user_name;
                $rpt['user_title'] = $row->title;
                $rpt['user_image'] = $row->image;

                $ret_value[] = $rpt;
            }

        }

        return $ret_value;
    }

}