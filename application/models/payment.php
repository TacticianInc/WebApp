<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    private function add_card_to_processor($cc_name, $cc_num, $exp_month, $exp_year, $cvv, $cc_type)
    {
        // TODO: Add Card to Processor
        // return payment_id
        return "TEST";
    }

    public function create_new_transaction($company_id, $payment_id, $amount)
    {
        // TODO: Need Payment Processor
    }

    public function create_new_subscription($company_id, $payment_id, $amount, $frequency)
    {
        // TODO: Need Payment Processor
    }

    public function get_payment_history($company_id)
    {
        // TODO: Need Payment Processor
    }

    public function remove_cc($company_id)
    {
        // TODO: Need Payment Processor
    }

    /**
     * Store a new credit card to the database and processor
     * 
     * @param integer $company_id
     * @param string $cc_name
     * @param string $cc_num
     * @param string $cc_month
     * @param string $cc_year
     * @param string $cvv
     * @param integer $cc_type (1=Visa,2=MC,3=AMEX,4=Discover)
     * @param integer $plan_id
     * @return array
     */
    public function store_new_cc($company_id, $cc_name, $cc_num, $cc_month, $cc_year, $cvv, $cc_type, $plan_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'elem' => '');

        if ((!isset($company_id) || $company_id == 0)) {
            $ret_value['message'] = "Unable to create account.";
            $ret_value['elem'] = "company";
            return $ret_value;
        }

        if (!isset($cc_name) || strlen($cc_name) == 0) {
            $ret_value['message'] = "Name on card required.";
            $ret_value['elem'] = "cc_name";
            return $ret_value;
        }

        if (!isset($cc_num) || strlen($cc_num) == 0) {
            $ret_value['message'] = "Credit Card Number required.";
            $ret_value['elem'] = "cc_num";
            return $ret_value;
        }

        if (!isset($cc_month) || $cc_month == 0) {
            $ret_value['message'] = "Credit Card Expiration Date required.";
            $ret_value['elem'] = "cc_month";
            return $ret_value;
        }

        if (!isset($cc_year) || $cc_year == 0) {
            $ret_value['message'] = "Credit Card Expiration Date required.";
            $ret_value['elem'] = "cc_month";
            return $ret_value;
        }

        if (!isset($cvv) || strlen($cvv) == 0) {
            $ret_value['message'] = "Credit Card CVV required.";
            $ret_value['elem'] = "cvv";
            return $ret_value;
        }

        try {

            // make last four
            $last_four = substr($cc_num, -4);

            // make expiration date
            $exp_date = $this->format_exp_date($cc_month, $cc_year);

            if ($this->does_cc_exist($last_four, $exp_date)) {
                $ret_value['message'] = "Credit Card is already being used with another account.";
                $ret_value['elem'] = "card";
                return $ret_value;
            }

            // TODO: store payment to processor
            // add_card_to_processor($cc_name, $cc_num, $exp_month, $exp_year, $cvv, $cc_type)
            $payment_id = "TEST";

            // set is valid
            $is_valid = TRUE;

            $sql = "INSERT INTO `payment` (`company_id`, `plan_id`, `payment_id`, `last_four`, `exp_date`, `type`, `is_valid`)";
            $sql = $sql."VALUES (";
            $sql = $sql.$this->db->escape($company_id).",".$this->db->escape($plan_id).",".$this->db->escape($payment_id).",".$this->db->escape($last_four).",";
            $sql = $sql.$this->db->escape($exp_date).",".$this->db->escape($cc_type).",".$this->db->escape($is_valid);
            $sql = $sql.");";

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
     * Get total price from plan type
     * 
     * @param integer $id
     * @return float
     */
    public function get_total_from_plan_type($id)
    {
        $ret_value = 0.0;

        $sql = "SELECT `total` FROM `plans` WHERE id=".$this->db->escape($id)." LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query !== FALSE && $query->num_rows() > 0) {
            $total = $query->result()[0]->total;
            $ret_value = floatval($total);
        }

        return $ret_value;
    }

    /**
     * Get a list of available plans
     * 
     * @param integer $type
     * @return float
     */
    public function get_plans()
    {
        $ret_value = array();

        $sql = "SELECT `id`, `name`, `description`, `image`, `total`";
        $sql = $sql." FROM `plans`;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $id = intval($row->id);
                $name = trim($row->name);
                $description = trim($row->description);
                $image = "";
                if(isset($row->image) && strlen($row->image) > 0) {
                    $image = base64_encode($row->image);
                }
                $total = floatval($row->total);

                $ret_value[] = array('id' => $id, 'name' => $name, 'description' => $description, 'image' => $image, 'total' => $total);
            }
        }

        return $ret_value;
    }

    /**
     * Format a Credit Card Expiration Date
     * 
     * @param integer $month
     * @param integer $year
     * @return string
     */
    private function format_exp_date($month, $year)
    {
        $ret_value = "";

        if ((!isset($month) || $month == 0) || (!isset($year) || $year == 0)) {
            return $ret_value;
        }

        // format month and year
        $disp_month = $month;
        $year = intval($year);

        if (intval($month) <= 9) {
            $disp_month = "0".$disp_month;
        }

        $ret_value = $disp_month."/".$year;

        return $ret_value;
    }

    /**
     * Does credit card exist
     * 
     * @param string $last_four
     * @param string $exp_date
     * @return boolean
     */
    private function does_cc_exist($last_four, $exp_date)
    {
        $ret_value = FALSE;

        if (!isset($last_four) || strlen($last_four) == 0) {
            return $ret_value;
        }

        if (!isset($exp_date) || strlen($exp_date) == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id` FROM `payment`";
        $sql = $sql."WHERE last_four=".$this->db->escape($last_four)." AND exp_date=".$this->db->escape($exp_date);
        $sql = $sql." LIMIT 1;";
        $query = $this->db->query($sql);

        if ($query !== FALSE && $query->num_rows() > 0) {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

}