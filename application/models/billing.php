<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billing extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    public function add_new_expense($case_id,$user_id,$date_occured,$item,$desc,$amount=0,$interview_id=0,$att_id=0)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        if(!isset($item) || strlen($item) == 0) {
            $ret_value['message'] = "Item name required.";
            return $ret_value;
        }

        if(!isset($date_occured) || strlen($date_occured) == 0) {
            $date_occured = date('Y-m-d');
        }else{
            $date_occured = date('Y-m-d', strtotime($date_occured));
        }

        $sql = "INSERT INTO `billing`(`user_id`, `case_id`, `date_occured`, `item`,";
        $sql = $sql." `amount`, `desc`, `interview_id`, `attachment_id`) VALUES (";
        $sql = $sql.$this->db->escape($user_id).",".$this->db->escape($case_id).",".$this->db->escape($date_occured).",".$this->db->escape($item).",";
        $sql = $sql.$this->db->escape($amount).",".$this->db->escape($desc).",".$this->db->escape($interview_id).",".$this->db->escape($att_id);
        $sql = $sql.")";

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $id = $this->db->insert_id();
            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id;
        }

        return $ret_value;

    }

    public function edit_expense($expense_id,$date_occured,$item,$amount,$desc,$interview_id,$att_id)
    {
        $ret_value = array('result' => FALSE);

        if(!isset($expense_id) || $expense_id == 0) {
            return $ret_value;
        }

        if(!isset($date_occured) || strlen($date_occured) == 0) {
            $date_occured = date('Y-m-d');
        }else{
            $date_occured = date('Y-m-d', strtotime($date_occured));
        }

        $sql = "UPDATE `billing` SET";
        $sql = $sql." `date_occured`=".$this->db->escape($date_occured);
        $sql = $sql." ,`item`=".$this->db->escape($item);
        $sql = $sql." ,`amount`=".$this->db->escape($amount);
        $sql = $sql." ,`desc`=".$this->db->escape($desc);
        if($interview_id > 0) {
            $sql = $sql." ,`interview_id`=".$this->db->escape($interview_id);
        }
        $sql = $sql." ,`attachment_id`=".$this->db->escape($att_id);
        $sql = $sql." WHERE `id`=".$this->db->escape($expense_id);

        $query = $this->db->query($sql);
        
        if ($query !== FALSE) {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    public function add_edit_rate($user_id,$cat_id,$amount)
    {
        $ret_value = array('result' => FALSE);

        if(!isset($user_id) || $user_id == 0) {
            return $ret_value;
        }

        if(!isset($cat_id) || $cat_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id`, `user_id`, `billing_cat_id`, `amount` FROM `billing_rate`";
        $sql = $sql."  WHERE `user_id`=".$this->db->escape($user_id);
        $sql = $sql."  AND `billing_cat_id`=".$this->db->escape($cat_id).";";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            // edit
            $sql = "UPDATE `billing_rate` SET `amount`=".$this->db->escape($amount);
            $sql = $sql." WHERE `user_id`=".$this->db->escape($user_id);
            $sql = $sql." AND `billing_cat_id`=".$this->db->escape($cat_id).";";
        }
        else
        {
            // insert
            $sql = "INSERT INTO `billing_rate`(`user_id`, `billing_cat_id`, `amount`)";
            $sql = $sql." VALUES (".$this->db->escape($user_id).",".$this->db->escape($cat_id);
            $sql = $sql." ,".$this->db->escape($amount).");";
        }

        $query = $this->db->query($sql);

        if ($query !== FALSE)
        {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    public function delete_expense($expense_id)
    {
        $ret_value = array('result' => FALSE);

        if(!isset($expense_id) || $expense_id == 0) {
            $ret_value['message'] = "No expense given.";
            return $ret_value;
        }

        $sql = "DELETE FROM `billing`";
        $sql = $sql." WHERE `id`=".$this->db->escape($expense_id);

        $query = $this->db->query($sql);

        if ($query !== FALSE)
        {
            $ret_value['result'] = TRUE;
        }

        return $ret_value;
    }

    public function view_single_expense($exp_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'expenses' => array());

        if(!isset($exp_id) || $exp_id == 0) {
            $ret_value['message'] = "No expense id given.";
            return $ret_value;
        }

        $sql = "SELECT `billing`.`id`, `billing`.`user_id`, `billing`.`case_id`, `case`.`name` AS case_name, `billing`.`date_occured`, `billing`.`item`, `billing_category`.`name` AS item_name, `billing`.`amount`, `billing`.`desc`, `billing`.`interview_id`, `billing`.`attachment_id` FROM `billing`";
        $sql = $sql." LEFT JOIN `billing_category` ON `billing`.`item` = `billing_category`.`id`";
        $sql = $sql." LEFT JOIN `case` ON `billing`.`case_id` = `case`.`id`";
        $sql = $sql." WHERE `billing`.`id` = ".$this->db->escape($exp_id);
        $sql = $sql." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            $row = $query->result()[0];

            $exp = array('id' => 0, 'need_calc' => 'false', 'user_id' => '', 'case_id' => '', 'case_name' => '', 'date_occured' => '', 'item' => 0, 'item_name' => 0, 'amount' => 0, 'rate' => 0, 'desc' => '', 'interview_id' => 0, 'attachment_id' => 0, 'attachment_name' => '', 'interview_name' => '');

            $exp['id'] = $row->id;
            $exp['case_id'] = $row->case_id;
            $exp['case_name'] = $row->case_name;
            $exp['date_occured'] = $row->date_occured;
            $exp['item'] = $row->item;
            $exp['item_name'] = $row->item_name;
            $exp['amount'] = $row->amount;
            $exp['desc'] = $row->desc;
            $exp['interview_id'] = $row->interview_id;
            $exp['attachment_id'] = $row->attachment_id;
            $exp['user_id'] = $row->user_id;

            if ($row->item < 5 && $row->item > 0) {
                $exp['need_calc'] = 'true';
                $exp['rate'] = $this->getUserRate($row->user_id, $row->item);
            }
            
            // get interview or attachment name
            if($row->interview_id > 0) {
                $exp['interview_name'] = $this->loadInterviewName($row->interview_id);
            } else if ($row->attachment_id > 0) {
                $exp['attachment_name'] = $this->loadAttachmentName($row->case_id, $row->attachment_id);
            }
            
            $ret_value['expenses'] = $exp;
        }

        return $ret_value;
    }

    public function view_expenses($user_id,$case_id,$month,$year)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'expenses' => array());

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No case given.";
            return $ret_value;
        }

        if(!isset($month) || $month == 0) {
            $ret_value['message'] = "No month given.";
            return $ret_value;
        }

        if(!isset($year) || $year == 0) {
            $ret_value['message'] = "No year given.";
            return $ret_value;
        }

        $monthint = intval($month);
        $yearint = intval($year);

        $max_days = cal_days_in_month(1, $monthint, $yearint);
        $start_date = $yearint."-".$monthint."-01";
        $end_date = $yearint."-".$monthint."-".$max_days;

        $sql = "SELECT `billing`.`id`, `billing`.`user_id`, `billing`.`case_id`, `case`.`name` AS case_name, `billing`.`date_occured`, `billing`.`item`, `billing_category`.`name` AS item_name, `billing`.`amount`, `billing`.`desc`, `billing`.`interview_id`, `billing`.`attachment_id` FROM `billing`";
        $sql = $sql." LEFT JOIN `billing_category` ON `billing`.`item` = `billing_category`.`id`";
        $sql = $sql." LEFT JOIN `case` ON `billing`.`case_id` = `case`.`id`";
        $sql = $sql." WHERE `date_occured` BETWEEN ".$this->db->escape($start_date)." AND ".$this->db->escape($end_date);
        
        if(isset($user_id) && $user_id > 0) {
            $sql = $sql." AND `billing`.`user_id`=".$this->db->escape($user_id);
        }
        
        $sql = $sql." AND LOWER(MD5(`billing`.`case_id`))=".$this->db->escape($case_id);
        $sql = $sql." ORDER BY `date_occured` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $exp = array('id' => 0, 'need_calc' => 'false', 'user_id' => '', 'case_id' => '', 'case_name' => '', 'date_occured' => '', 'item' => 0, 'item_name' => 0, 'amount' => 0, 'rate' => 0, 'desc' => '', 'interview_id' => 0, 'attachment_id' => 0, 'attachment_name' => '', 'interview_name' => '');

                $exp['id'] = $row->id;
                $exp['case_id'] = $row->case_id;
                $exp['case_name'] = $row->case_name;
                $exp['date_occured'] = $row->date_occured;
                $exp['item'] = $row->item;
                $exp['item_name'] = $row->item_name;
                $exp['amount'] = $row->amount;
                $exp['desc'] = $row->desc;
                $exp['interview_id'] = $row->interview_id;
                $exp['attachment_id'] = $row->attachment_id;
                $exp['user_id'] = $row->user_id;

                if ($row->item < 5 && $row->item > 0) {
                    $exp['need_calc'] = 'true';
                    $exp['rate'] = $this->getUserRate($row->user_id, $row->item);
                }
                
                // get interview or attachment name
                if($row->interview_id > 0) {
                    $exp['interview_name'] = $this->loadInterviewName($row->interview_id);
                } else if ($row->attachment_id > 0) {
                    $exp['attachment_name'] = $this->loadAttachmentName($row->case_id, $row->attachment_id);
                }
                
                $ret_value['expenses'][] = $exp;
            }
        }

        return $ret_value;
    }

    public function view_categories()
    {
        $ret_value = array('result' => FALSE, 'cats' => array());

        $sql = "SELECT `id`, `name` FROM `billing_category`;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $cat = array('id' => 0, 'name' => '');

                $cat['id'] = $row->id;
                $cat['name'] = $row->name;

                $ret_value['cats'][] = $cat;
            }
        }

        return $ret_value;
    }

    private function getUserRate($user_id,$cat_id)
    {
        $ret_value = 0;

        if(!isset($user_id) || $user_id == 0) {
            return $ret_value;
        }

        if(!isset($cat_id) || $cat_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id`, `user_id`, `billing_cat_id`, `amount` FROM `billing_rate`";
        $sql = $sql."  WHERE `user_id`=".$this->db->escape($user_id);
        $sql = $sql."  AND `billing_cat_id`=".$this->db->escape($cat_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0)
        {
            $row = $query->result()[0];

            $ret_value = $row->amount;
        }

        return $ret_value;
    }

    private function loadInterviewName($int_id)
    {
        $ret_value = '';

        if(!isset($int_id) || $int_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `interview`.`name`";
        $sql = $sql." FROM `interview`";
        $sql = $sql." WHERE `interview`.`id`=".$this->db->escape($int_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0)
        {
            $row = $query->result()[0];

            $ret_value = $row->name." [Int]";
        }

        return $ret_value;
    }

    private function loadAttachmentName($case_id, $att_id)
    {
        $ret_value = '';

        if(!isset($att_id) || $att_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `attachments`.`id`, `interview_id`, `attachments`.`name` AS att_name, `type`, `size`, `location`, `attachments`.`created`, `attachments`.`title`, `tags`, `user_info`.`name` AS user_name";
        $sql = $sql." FROM `attachments`";
        $sql = $sql." LEFT JOIN `user_info` ON `user_info`.`user_id` = `attachments`.`user_id`";
        $sql = $sql." WHERE `case_id`=".$this->db->escape($case_id);
        $sql = $sql." AND `attachments`.`id`=".$this->db->escape($att_id);
        $sql = $sql." ORDER BY `attachments`.`name` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row_count = 1;

            foreach ($query->result() as $row)
            {
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

                if ($row->id == $att_id) {
                    $num = $acronym."-".$number;
                    $name = $row->title;
                    $ret_value = $name." [".$num."]";
                    return $ret_value;
                }

                $row_count = $row_count + 1;
            }

        }

        return $ret_value;
    }

}