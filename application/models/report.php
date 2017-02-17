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

    private function convert_cp1252_to_ascii($input) {
        if ($input === null || $input == '') {
            return $input;
        }

        // special case
        $quotes = array(
            "\xC2\xAB"     => '"', // « (U+00AB) in UTF-8
            "\xC2\xBB"     => '"', // » (U+00BB) in UTF-8
            "\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
            "\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
            "\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
            "\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
        );
        $input = strtr($input, $quotes);

        // https://en.wikipedia.org/wiki/UTF-8
        // https://en.wikipedia.org/wiki/ISO/IEC_8859-1
        // https://en.wikipedia.org/wiki/Windows-1252
        // http://www.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/WINDOWS/CP1252.TXT
        $encoding = mb_detect_encoding($input, array('Windows-1252', 'ISO-8859-1'), true);
        if ($encoding == 'ISO-8859-1' || $encoding == 'Windows-1252') {
            /*
             * Use the search/replace arrays if a character needs to be replaced with
             * something other than its Unicode equivalent.
             */ 

            $replace = array(
                128 => "E",     // http://www.fileformat.info/info/unicode/char/20AC/index.htm EURO SIGN
                129 => "",              // UNDEFINED
                130 => ",",     // http://www.fileformat.info/info/unicode/char/201A/index.htm SINGLE LOW-9 QUOTATION MARK
                131 => "f",     // http://www.fileformat.info/info/unicode/char/0192/index.htm LATIN SMALL LETTER F WITH HOOK
                132 => ",,",        // http://www.fileformat.info/info/unicode/char/201e/index.htm DOUBLE LOW-9 QUOTATION MARK
                133 => "...",       // http://www.fileformat.info/info/unicode/char/2026/index.htm HORIZONTAL ELLIPSIS
                134 => "t",     // http://www.fileformat.info/info/unicode/char/2020/index.htm DAGGER
                135 => "T",     // http://www.fileformat.info/info/unicode/char/2021/index.htm DOUBLE DAGGER
                136 => "^",     // http://www.fileformat.info/info/unicode/char/02c6/index.htm MODIFIER LETTER CIRCUMFLEX ACCENT
                137 => "%",     // http://www.fileformat.info/info/unicode/char/2030/index.htm PER MILLE SIGN
                138 => "S",     // http://www.fileformat.info/info/unicode/char/0160/index.htm LATIN CAPITAL LETTER S WITH CARON
                139 => "<",     // http://www.fileformat.info/info/unicode/char/2039/index.htm SINGLE LEFT-POINTING ANGLE QUOTATION MARK
                140 => "OE",        // http://www.fileformat.info/info/unicode/char/0152/index.htm LATIN CAPITAL LIGATURE OE
                141 => "",              // UNDEFINED
                142 => "Z",     // http://www.fileformat.info/info/unicode/char/017d/index.htm LATIN CAPITAL LETTER Z WITH CARON 
                143 => "",              // UNDEFINED
                144 => "",              // UNDEFINED
                145 => "'",     // http://www.fileformat.info/info/unicode/char/2018/index.htm LEFT SINGLE QUOTATION MARK 
                146 => "'",     // http://www.fileformat.info/info/unicode/char/2019/index.htm RIGHT SINGLE QUOTATION MARK
                147 => "\"",        // http://www.fileformat.info/info/unicode/char/201c/index.htm LEFT DOUBLE QUOTATION MARK
                148 => "\"",        // http://www.fileformat.info/info/unicode/char/201d/index.htm RIGHT DOUBLE QUOTATION MARK
                149 => "*",     // http://www.fileformat.info/info/unicode/char/2022/index.htm BULLET
                150 => "-",     // http://www.fileformat.info/info/unicode/char/2013/index.htm EN DASH
                151 => "--",        // http://www.fileformat.info/info/unicode/char/2014/index.htm EM DASH
                152 => "~",     // http://www.fileformat.info/info/unicode/char/02DC/index.htm SMALL TILDE
                153 => "TM",        // http://www.fileformat.info/info/unicode/char/2122/index.htm TRADE MARK SIGN
                154 => "s",     // http://www.fileformat.info/info/unicode/char/0161/index.htm LATIN SMALL LETTER S WITH CARON
                155 => ">",     // http://www.fileformat.info/info/unicode/char/203A/index.htm SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
                156 => "oe",        // http://www.fileformat.info/info/unicode/char/0153/index.htm LATIN SMALL LIGATURE OE
                157 => "",              // UNDEFINED
                158 => "z",     // http://www.fileformat.info/info/unicode/char/017E/index.htm LATIN SMALL LETTER Z WITH CARON
                159 => "Y",     // http://www.fileformat.info/info/unicode/char/0178/index.htm LATIN CAPITAL LETTER Y WITH DIAERESIS
            );

            $find = array();
            foreach (array_keys($replace) as $key) {
                $find[] = chr($key);
            }

            $input = str_replace($find, array_values($replace), $input);

            /*
             * Because ISO-8859-1 and CP1252 are identical except for 0x80 through 0x9F
             * and control characters, always convert from Windows-1252 to UTF-8.
             */
            $input = iconv('Windows-1252', 'UTF-8//IGNORE', $input);
        }

        return $input;
    }

    public function gen_report_data($report_id, $user_id=0)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'report' => array(), 'case' => array(), 'company' => array(), 'client' => array(), 'documents' => array(), 'interviews' => array(), 'attachments' => array());

        if(!isset($report_id) || $report_id == 0) {
            $ret_value['message'] = "No report found.";
            return $ret_value;
        }

        $sql = "SELECT `report`.`id`, `report`.`case_id`, `report`.`author_id`, `report`.`name`, `report`.`is_redacted`, `report`.`redact_text`, `report`.`created`, `case`.`company_id`, `case`.`client_id`, `case`.`name` AS case_name, `case`.`synopsis_id`, `case`.`created` AS case_created, `user_info`.`name` AS author_name, `user_info`.`image` AS author_image FROM `report`";
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
            $report['redact_text'] = $row->redact_text;
            $report['is_redacted'] = $row->is_redacted;

            $case['author'] = $row->author_name;
            $case['case_id'] = $row->case_id;
            $case['name'] = $row->case_name;
            $case['created'] = $row->case_created;

            $case['synopsis'] = $this->get_synopsis_report($report_id,$row->redact_text); // redact
            
            $company = $this->get_company($row->company_id);

            $client = $this->get_client($row->client_id,$row->redact_text); // redact

            $documents = $this->get_document_report($report_id,$row->redact_text); // redact

            $attachments = $this->get_attachment_list($row->case_id);

            $interviews = $this->get_interviews_report($report_id,$row->case_id,$row->redact_text); // redact

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

    private function get_interviews_report($report_id, $case_id, $redacted_text="")
    {
        $ret_value = array();

        if(!isset($report_id) || $report_id == 0) {
            return $ret_value;
        }

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

                $doc['id'] = $row->id;
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
                $doc['attachments'] = $this->get_int_attachment_list($row->id,TRUE);

                if(strlen($redacted_text) > 0) {
                    $doc['name'] = $this->redact_text($row->name,$redacted_text);
                    $doc['author_name'] = $this->redact_text($row->author_name,$redacted_text);
                    $doc['street'] = $this->redact_text($row->street,$redacted_text);
                }

                // get text
                $sql = "SELECT `interview_text`";
                $sql = $sql." FROM `report_interview_text`";
                $sql = $sql." WHERE `interview_id`=".$this->db->escape($row->id);
                $sql = $sql." AND `report_id` = ".$this->db->escape($report_id);

                $query = $this->db->query($sql);
                if (($query !== FALSE) && ($query->num_rows() > 0))
                {
                    $row = $query->result()[0];
                    $doc['notes'] = $row->interview_text;

                    if(strlen($redacted_text) > 0) {
                        $doc['notes'] = $this->redact_text($row->interview_text,$redacted_text);
                    }

                }

                $ret_value[] = $doc;
            }
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

                $doc['id'] = $row->id;
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

    private function get_int_attachment_list($int_id,$av_only=FALSE)
    {
        $ret_value = array();

        if(!isset($int_id) || $int_id == 0) {
            return $ret_value;
        }

        //SELECT `id`, `case_id`, `user_id`, `interview_id`, `title`, `type`, `size`, `location`, `created`, `is_approved` FROM `attachments`

        $sql = "SELECT `attachments`.`id`,`interview_id`, `title`, `size`, `location`, `attachments`.`name` AS attachment_name  FROM `attachments`";
        $sql = $sql." LEFT JOIN `attachment_type` ON `attachments`.`type` = `attachment_type`.`id`";
        $sql = $sql." WHERE `interview_id`=".$this->db->escape($int_id);
        $sql = $sql." AND `interview_id` > 0";
        if($av_only == TRUE) {
            $sql = $sql." AND `attachment_type`.`id` IN (5,6,7,8,9,10);";
        } else {
            $sql = $sql.";";
        }

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

    private function get_document_report($report_id,$redacted_text="")
    {
        $ret_value = array();

        if(!isset($report_id) || $report_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `document_text` FROM `report_documents_text`";
        $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id);
        $sql = $sql." ORDER BY `id` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            foreach ($query->result() as $row)
            {
                $doc = array();

                $doc['text'] = $row->document_text;

                if(strlen($redacted_text) > 0) {
                    $doc['text'] = $this->redact_text($row->document_text,$redacted_text);
                }

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

        $sql = "SELECT `documents`.`id`, `document_type`.`name`, `documents`.`location`, `attachment_type`.`name` AS attachment_name, `attachment_type`.`mime`  FROM `documents_included`";
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

                $doc['id'] = $row->id;
                $doc['name'] = $row->name;
                $doc['location'] = "https://s3.amazonaws.com/tacticiandocs/".$row->location;
                $doc['attachment_name'] = $row->attachment_name;
                $doc['mime'] = $row->mime;

                $ret_value[] = $doc;
            }
        }

        return $ret_value;
    }

    private function get_client($client_id,$redacted_text="")
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

            if(strlen($redacted_text) > 0) {
                $ret_value['name'] = $this->redact_text($row->name,$redacted_text);
            }

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

    private function redact_text($text,$redacted_text="")
    {
        if(strlen($text) == 0) {
            return $text;
        }

        // split redacted text into array
        $redacted_parts = explode(",", $redacted_text);

        // build replacment redaction
        // ASCII 254 is black block &squf;
        // ASCII 178 chr(0x2588)

        $redacted = "";
        $num_chars = strlen($redacted_text);
        for($i=0;$i<$num_chars;$i++) {
            $redacted = $redacted.chr(150);
        }

        foreach ($redacted_parts as $redtxt) {
            // mixed str_ireplace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
            $text = str_ireplace(trim($redtxt), $redacted, $text);
        }

        return $text;
    }

    private function get_synopsis_report($report_id,$redacted_text="")
    {
        $ret_value = array();

        if(!isset($report_id) || $report_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `synopsis_text`";
        $sql = $sql." FROM `report_synopsis_text`";
        $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];

            $ret_value['synopsis_text'] = $row->synopsis_text;

            if(strlen($redacted_text) > 0) {
                $ret_value['synopsis_text'] = $this->redact_text($row->synopsis_text,$redacted_text);
            }
            
        }

        return $ret_value;
    }

    private function get_synopsis($synopsis_id)
    {
        $ret_value = array();

        if(!isset($synopsis_id) || $synopsis_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id`,`name`, `size`, `contents`, `location`, `att_type`";
        $sql = $sql." FROM `synopsis`";
        $sql = $sql." WHERE `id`=".$this->db->escape($synopsis_id)." LIMIT 1;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $row = $query->result()[0];

            $ret_value['id'] = $row->id;
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

    public function add_new_report($user_id,$case_id,$name,$redact_text="")
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

        $is_redacted = FALSE;

        if(isset($redact_text) && strlen($redact_text) > 0) {
            $is_redacted = TRUE;
        } else {
            $redact_text = "";
        }

        $date_occured = date('Y-m-d');

        $sql = "INSERT INTO `report`(`case_id`, `author_id`, `name`, `created`, `is_redacted`, `redact_text`) VALUES (";
        $sql = $sql.$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($name).",".$this->db->escape($date_occured).",".$this->db->escape($is_redacted).",".$this->db->escape($redact_text);
        $sql = $sql.");";

        $query = $this->db->query($sql);

        if ($query !== FALSE) {
            $id = $this->db->insert_id();

            // convert attachments to text
            $res = $this->convert_attachments_to_text($id, $case_id);

            $ret_value['result'] = TRUE;
            $ret_value['id'] = $id;
        }

        return $ret_value;
    }

    private function convert_attachments_to_text($report_id, $case_id)
    {
        $ret_value = FALSE;

        if(!isset($case_id) || $case_id == 0) {
            return $ret_value;
        }

        if(!isset($report_id) || $report_id == 0) {
            return $ret_value;
        }

        // Documents
        $docs = $this->get_document_list($case_id);
        foreach ($docs as $doc) {
            $doc_id = $doc['id'];
            $location = $doc['location'];
            $text = $this->convert_doc_to_text($location);
            if($text != FALSE && strlen($text) > 0){
                // save to database
                $text = $this->convert_cp1252_to_ascii($text); // clean out special quotes
                $res = $this->save_doc_text($text, $doc_id, $report_id);
            }
        }

        // Synopsis
        // get synopsis id
        $sql = "SELECT `case`.`synopsis_id` FROM `case`";
        $sql = $sql." WHERE `case`.`id`=".$this->db->escape($case_id);

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0)) {

            $synopsis_id = $query->result()[0]->synopsis_id;

            $synopsis = $this->get_synopsis($synopsis_id);
            
            $contents = $synopsis['contents'];
            $location = $synopsis['location'];
            $att_type = $synopsis['att_type'];

            $text = "";

            if ($att_type > 0 || strlen($contents) == 0) {
                $text = $this->convert_doc_to_text($location);
            } else if (strlen($contents) > 0) {
                $breaks = array("<br />","<br>","<br/>");
                $text = str_ireplace($breaks, "\r\n", $contents);
                $contents = strip_tags($text);
                $text = $contents;
                $text = $this->convert_cp1252_to_ascii($text);  // clean out special quotes
            }

            if (strlen($text) > 0 && $text !== FALSE) {
                // save to database
                $res = $this->save_synopsis_text($text, $report_id);
            }
        }

        // Interviews
        $interviews = $this->get_interviews($case_id);

        if (count($interviews) > 0) {

            foreach ($interviews as $int) {
                $int_id = $int['id'];
                $int_atts = $int['attachments'];
                $int_notes = $int['notes'];

                $text = "";
                $new_line = chr(0x0D).chr(0x0A);

                if(strlen($int_notes) > 0) {
                    // convert to text
                    $note_json = json_decode($int_notes);
                    if(isset($note_json->ops) && count($note_json) > 0) {
                        $text = $note_json->ops[0]->insert.$new_line;
                    }
                }

                foreach ($int_atts as $att) {
                    $att_id = $att['id'];
                    $location = $att['location'];

                    $att_text = $this->convert_doc_to_text($location);
                    if ($att_text !== FALSE) {
                        // save to text
                        $text = $text.$new_line.$att_text.$new_line;
                    }
                }

                $text = $this->convert_cp1252_to_ascii($text);  // clean out special quotes
                $res = $this->save_interview_text($text, $report_id, $int_id);
            }
        }

        // set to true since no error occured
        $ret_value = TRUE;

        return $ret_value;
    }

    private function convert_doc_to_text($file)
    {
        $ret_value = FALSE;

        $this->load->library('DocxConversion');
        $this->load->library('PdfParser');

        $fileArray = pathinfo($file);
        $file_name  = $fileArray['basename'];

        $file_path = "/tmp/".$file_name;
        $file_ext  = $fileArray['extension'];

        // load file to temp directory
        file_put_contents($file_path, fopen($file, 'r'));

        if ($file_ext == "pdf") {
            try{
                $ret_value = $this->pdfparser->parseFile($file_path);
            } catch (Exception $e) {
                $ret_value = FALSE;
            }
        } else {
            $ret_value = $this->docxconversion->convertToText($file_path);
        }

        return $ret_value;
    }

    private function save_interview_text($text, $report_id, $interview_id)
    {
        $ret_value = FALSE;

        if (strlen($text) == 0 || $report_id == 0 || $interview_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT  `id` FROM `report_interview_text`";
        $sql = $sql." WHERE `interview_id`=".$this->db->escape($interview_id);
        $sql = $sql." AND `report_id`=".$this->db->escape($report_id).";";

        $query = $this->db->query($sql);

        if (($query === FALSE) || ($query->num_rows() == 0)) {

            $sql = "INSERT INTO `report_interview_text`(`interview_text`, `report_id`, `interview_id`) VALUES (";
            $sql = $sql.$this->db->escape($text).",".$this->db->escape($report_id).",".$this->db->escape($interview_id);
            $sql = $sql.");";

            $query = $this->db->query($sql);
            $ret_value = TRUE;

        } else {

            $sql = "UPDATE `report_interview_text`";
            $sql = $sql." SET `interview_text`=".$this->db->escape($text);
            $sql = $sql." WHERE `interview_id`=".$this->db->escape($interview_id);
            $sql = $sql." AND `report_id`=".$this->db->escape($report_id).";";

            $query = $this->db->query($sql);
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    private function save_synopsis_text($text, $report_id)
    {
        $ret_value = FALSE;

        if (strlen($text) == 0 || $report_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id` FROM `report_synopsis_text`";
        $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id).";";

        $query = $this->db->query($sql);

        if (($query === FALSE) || ($query->num_rows() == 0))
        {
            $sql = "INSERT INTO `report_synopsis_text`(`synopsis_text`, `report_id`) VALUES (";
            $sql = $sql.$this->db->escape($text).",".$this->db->escape($report_id);
            $sql = $sql.");";

            $query = $this->db->query($sql);
            $ret_value = TRUE;
        } else {
            $sql = "UPDATE `report_synopsis_text`";
            $sql = $sql." SET `synopsis_text`=".$this->db->escape($text);
            $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id).";";

            $query = $this->db->query($sql);
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    private function save_doc_text($text, $doc_id, $report_id)
    {
        $ret_value = FALSE;

        if (strlen($text) == 0 || $report_id == 0) {
            return $ret_value;
        }

        $sql = "SELECT `id` FROM `report_documents_text`";
        $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id);
        $sql = $sql." AND `document_id`=".$this->db->escape($doc_id).";";

        $query = $this->db->query($sql);

        if (($query === FALSE) || ($query->num_rows() == 0))
        {
            $sql = "INSERT INTO `report_documents_text`(`document_text`, `report_id`, `document_id`) VALUES (";
            $sql = $sql.$this->db->escape($text).",".$this->db->escape($report_id).",".$this->db->escape($doc_id);
            $sql = $sql.");";

            $query = $this->db->query($sql);
            $ret_value = TRUE;
        } else {
            $sql = "UPDATE `report_documents_text`";
            $sql = $sql." SET `document_text`=".$this->db->escape($text);
            $sql = $sql." WHERE `report_id`=".$this->db->escape($report_id);
            $sql = $sql." AND `document_id`=".$this->db->escape($doc_id).";";

            $query = $this->db->query($sql);
            $ret_value = TRUE;
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

            $sql = "DELETE FROM `report_documents_text` WHERE `report_id`=".$this->db->escape($report_id).";";

            $query = $this->db->query($sql);

            $sql = "DELETE FROM `report_interview_text` WHERE `report_id`=".$this->db->escape($report_id).";";

            $query = $this->db->query($sql);

            $sql = "DELETE FROM `report_synopsis_text` WHERE `report_id`=".$this->db->escape($report_id).";";

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