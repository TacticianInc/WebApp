<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load Included Documents - loads the documents with ids passed
     * 
     * @param string $doc_ids
     * @return array (associative array)
     */
    public function load_included_documents($doc_ids)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array());

        if(!isset($doc_ids) || strlen($doc_ids) == 0) {
            $ret_value['message'] = "No documents found.";
            return $ret_value;
        }

        $sql = "SELECT DISTINCT `documents`.`id`, `company_id`, `documents`.`name` as filename, `documents_included`.`user_id`, `title`, `location`,";
        $sql = $sql." `size`, `document_type`, `attachment_type`, `documents_included`.`date_added`, `document_type`.name,";
        $sql = $sql." `document_type`.category, `document_type`.id AS doc_type_id";
        $sql = $sql." FROM `documents`";
        $sql = $sql." LEFT JOIN `documents_included` ON `documents_included`.`doc_id` = `documents`.`id`";
        $sql = $sql." LEFT JOIN `document_type` ON `document_type`.id = `documents`.`document_type`";
        $sql = $sql." WHERE `documents`.`id` IN (".$doc_ids.")";
        $sql = $sql." ORDER BY `date_added` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'user_id' => '', 'filename' => '', 'icon' => '', 'url' => '', 'name' => '', 'title' => '', 'type' => '', 'doc_type' => '', 'att_type' => '', 'category' => '', 'size' => '', 'location' => '', 'date_added' => '');

                $doc['id'] = $row->id;
                $doc['user_id'] = $row->user_id;
                $doc['filename'] = $row->filename;
                $doc['name'] = $row->name;
                $doc['title'] = $row->title;
                $doc['type'] = $row->document_type;
                $doc['doc_type'] = $row->doc_type_id;
                $doc['att_type'] = $row->attachment_type;
                $doc['category'] = $row->category;
                $doc['size'] = $row->size;
                $doc['location'] = $row->location;
                $doc['date_added'] = date('m/d/Y', strtotime($row->date_added));

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
                switch ($row->attachment_type) {
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

                $doc['icon'] = $icon;

                // we need the shape of doc[type_id][...] = aray()
                $ret_value['docs'][] = $doc;
            }

        }

        return $ret_value;
    }

    /**
     * Add a new document
     * 
     * @param int $document_type
     * @param int $case_id
     * @param int $company_id
     * @param int $user_id
     * @param string $path_to_save
     * @param base64 $file_contents
     * @param int $size
     * @param string $type
     * @param string $title
     * @return array
     */
    public function add_new_document($document_type, $company_id, $user_id, $path_to_save, $file_contents, $size, $type, $title, $name)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if(!isset($document_type) || $document_type == 0) {
            $document_type = 6; // id for other
        }

        if(!isset($company_id) || $company_id == 0) {
            $ret_value['message'] = "No agency found.";
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

        if(!isset($title) || strlen($title) == 0) {
            $ret_value['message'] = "File corrupted: missing title.";
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

            $sql = "INSERT INTO `documents` (`document_type`, `name`, `company_id`, `user_id`, `title`, `location`, `size`, `attachment_type`, `date_added`)";
            $sql = $sql." VALUES (";
            $sql = $sql.$this->db->escape($document_type).",".$this->db->escape($name).",".$this->db->escape($company_id).",".$this->db->escape($user_id).",".$this->db->escape($title).",".$this->db->escape($path_to_save).",";
            $sql = $sql.$this->db->escape($size).",".$this->db->escape($type_id).",".$this->db->escape($created);
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

    /**
     * Attach a document to a case
     * 
     * @param string $case_id (md5)
     * @param int $document_id
     * @param int $user_id
     * @return array
     */
    public function attach_document_to_case($case_id, $document_id, $user_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'id' => 0);

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No case found.";
            return $ret_value;
        }

        if(!isset($document_id) || $document_id == 0) {
            $ret_value['message'] = "No document found.";
            return $ret_value;
        }

        if(!isset($user_id) || $user_id == 0) {
            $ret_value['message'] = "No user found.";
            return $ret_value;
        }

        // get case id from md5 id
        $case_response = $this->get_id_md5($case_id);
        if($case_response['result']) {
            $case_id = $case_response['id'];
        }

        $created = date('Y-m-d');

        $sql = "INSERT INTO `documents_included` (`doc_id`, `case_id`, `user_id`, `date_added`)";
        $sql = $sql." VALUES (";
        $sql = $sql.$this->db->escape($document_id).",".$this->db->escape($case_id).",".$this->db->escape($user_id).",".$this->db->escape($created);
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
     * Load Available Documents - loads all documents belonging to company/agency
     * 
     * @param string $company_id (md5)
     * @param int $doc_type (document type id, 0 for all)
     * @return array (docs[type_id][...] = associative array) and plain
     */
    public function load_available_documents($company_id, $doc_type_id=0)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array(), 'doc_array' => array());

        if(!isset($company_id) || strlen($company_id) == 0) {
            $ret_value['message'] = "No agency found.";
            return $ret_value;
        }

        $company_id = strtolower($company_id);

        $sql = "SELECT `documents`.`id`, `company_id`, `documents`.`name` as filename, `title`, `location`,";
        $sql = $sql." `size`, `document_type`, `attachment_type`, `document_type`.name,";
        $sql = $sql." `document_type`.category, `document_type`.id AS doc_type_id";
        $sql = $sql." FROM `documents`";
        $sql = $sql." LEFT JOIN `document_type` ON `document_type`.id = `documents`.`document_type`";
        $sql = $sql." WHERE LOWER(MD5(`company_id`))=".$this->db->escape($company_id);
        if (isset($doc_type_id) && intval($doc_type_id) > 0) {
             $sql = $sql." AND `document_type`.id=".$this->db->escape($doc_type_id);
        }
        $sql = $sql." ORDER BY `date_added` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'icon' => '', 'url' => '', 'filename' => '', 'name' => '', 'title' => '', 'type' => '', 'doc_type' => '', 'att_type' => '', 'category' => '', 'size' => '', 'location' => '');

                $doc['id'] = $row->id;
                $doc['filename'] = $row->filename;
                $doc['name'] = $row->name;
                $doc['title'] = $row->title;
                $doc['type'] = $row->document_type;
                $doc['doc_type'] = $row->doc_type_id;
                $doc['att_type'] = $row->attachment_type;
                $doc['category'] = $row->category;
                $doc['size'] = $row->size;
                $doc['location'] = $row->location;

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
                switch ($row->attachment_type) {
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

                $doc['icon'] = $icon;

                // we need the shape of doc[type_id][...] = aray()
                $ret_value['docs'][$row->doc_type_id][] = $doc;
                $ret_value['doc_array'][] = $doc;
            }

        }

        return $ret_value;
    }

    /**
     * Load Documents - loads all marks ones belonging to case
     * 
     * @param string $company_id (md5)
     * @param string $case_id (md5)
     * @return array (docs[type_id][...] = associative array)
     */
    public function load_documents($company_id, $case_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '', 'docs' => array());

        if(!isset($company_id) || strlen($company_id) == 0) {
            $ret_value['message'] = "No agency found.";
            return $ret_value;
        }

        if(!isset($case_id) || strlen($case_id) == 0) {
            $ret_value['message'] = "No agency found.";
            return $ret_value;
        }

        $case_id = strtolower($case_id);
        $company_id = strtolower($company_id);

        $sql = "SELECT `documents`.`id`, `company_id`, `documents`.`name` as filename, `documents_included`.`user_id`, `title`, `location`,";
        $sql = $sql." `size`, `document_type`, `attachment_type`, `documents_included`.`date_added`, `document_type`.name,";
        $sql = $sql." `document_type`.category, `document_type`.id AS doc_type_id";
        $sql = $sql." FROM `documents`";
        $sql = $sql." LEFT JOIN `documents_included` ON `documents_included`.`doc_id` = `documents`.`id`";
        $sql = $sql." LEFT JOIN `document_type` ON `document_type`.id = `documents`.`document_type`";
        $sql = $sql." WHERE LOWER(MD5(`case_id`)) =".$this->db->escape($case_id);
        $sql = $sql." AND LOWER(MD5(`company_id`))=".$this->db->escape($company_id);
        $sql = $sql." ORDER BY `date_added` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $doc = array('id' => 0, 'user_id' => '', 'filename' => '', 'icon' => '', 'url' => '', 'name' => '', 'title' => '', 'type' => '', 'doc_type' => '', 'att_type' => '', 'category' => '', 'size' => '', 'location' => '', 'date_added' => '');

                $doc['id'] = $row->id;
                $doc['user_id'] = $row->user_id;
                $doc['filename'] = $row->filename;
                $doc['name'] = $row->name;
                $doc['title'] = $row->title;
                $doc['type'] = $row->document_type;
                $doc['doc_type'] = $row->doc_type_id;
                $doc['att_type'] = $row->attachment_type;
                $doc['category'] = $row->category;
                $doc['size'] = $row->size;
                $doc['location'] = $row->location;
                $doc['date_added'] = $row->date_added;

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
                switch ($row->attachment_type) {
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

                $doc['icon'] = $icon;

                // we need the shape of doc[type_id][...] = aray()
                $ret_value['docs'][$row->doc_type_id][] = $doc;
            }

        }

        return $ret_value;
    }

    public function load_doc_categories()
    {
        $ret_value = array('result' => FALSE, 'categories' => array());

        $sql = "SELECT `id`, `name`, `category` FROM `document_type`";
        $sql = $sql." ORDER BY `id` ASC;";

        $query = $this->db->query($sql);

        if (($query !== FALSE) && ($query->num_rows() > 0))
        {
            $ret_value['result'] = TRUE;

            foreach ($query->result() as $row)
            {
                $cat = array('id' => 0, 'name' => '', 'category' => '');
                $cat['id'] = $row->id;
                $cat['name'] = $row->name;
                $cat['category'] = $row->category;

                $ret_value['categories'][] = $cat; 
            }
        }

        return $ret_value;
    }

    /**
     * remove document
     *
     * @param int $document_id
     * @return array
     */
    public function remove_document($document_id)
    {
        $ret_value = array('result' => FALSE, 'message' => '');

        if(!isset($document_id) || $document_id == 0) {
            $ret_value['message'] = "No document found.";
            return $ret_value;
        }

        $sql = "UPDATE `documents_included`";
        $sql = $sql." SET `case_id`=0";
        $sql = $sql." ,`interview_id`=0";
        $sql = $sql." WHERE `doc_id`=".$this->db->escape($document_id).";";

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