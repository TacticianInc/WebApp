<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data extends CI_Controller {

	public function index()
	{
		$this->output->set_status_header('401');
		exit('Access not allowed');
	}

	public function send_invoice()
	{
		$this->load->library('SendMail');

		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$user_id = 0; // to get all
		$case_id = $this->input->post('caseid');
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		if (!isset($month) || $month == FALSE) {
			$month = date("m");
		}

		if (!isset($year) || $year == FALSE) {
			$year = date("Y");
		}

		$this->load->model('Billing');
		$this->load->model('Cases');
		$this->load->model('Company');

		// get email address of client
		$email = $this->Cases->get_client_email($case_id);
		if (strlen($email) == 0) {
			$this->output->set_status_header('400');
			exit('No email on file for client');
		}

		// get company information
		$company_respose = $this->Company->load_company_by_case_id($case_id);
		if (!$company_respose['result']) {
			$this->output->set_status_header('400');
			exit('No company data found');
		}

		$comp_name = $company_respose['name'];
        $street = $company_respose['street'];
        $phone = $company_respose['phone'];
        $city = $company_respose['city'];
        $state = $company_respose['state'];
        $zip = $company_respose['zip'];
        $image = $company_respose['image'];
        $image_url = base_url("img/company")."/".$image;

        $new_line = chr(0x0D).chr(0x0A);
        $message = "";
        $message = $message."Invoice From:".$new_line.$new_line;
        $message = $message.$comp_name.$new_line;
        $message = $message.$street.$new_line;
        $message = $message.$city.", ".$state." ".$zip.$new_line;
        $message = $message.$phone.$new_line.$new_line;

        $url = site_url("invoice")."/".$case_id."/".$month."/".$year;

        $message = $message."Your invoice is ready to view. Please click on or copy ond paste the following link into your web browser:".$new_line;
        $message = $message.$url.$new_line.$new_line;

        $message = $message."Sincerely,".$new_line;
        $message = $message.$comp_name.$new_line;

		// send email to client
		$subject = "Your Invoice From ".$comp_name;
		$this->sendmail->send_email_ses($message, $subject, $email);

		$this->output->set_status_header('200');
		echo "{\"result\":1}";
	}

	public function billing_csv()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$user_id = 0; //to get all
		$case_id = $this->input->post('caseid');
		$month = $this->input->post('month');
		$year = $this->input->post('year');


		if (!isset($month) || $month == FALSE) {
			$month = date("m");
		}

		if (!isset($year) || $year == FALSE) {
			$year = date("Y");
		}

		$this->load->model('Billing');

		// load available team
		$db_result = $this->Billing->view_expenses($user_id,$case_id,$month,$year);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			$this->output->set_content_type('text/csv; charset=utf-8');
			$this->output->set_header('Content-Disposition: attachment; filename=billing.csv');

			$headers = array('Date', 'Case', 'Engagment', 'Activity/Expense', 'Comments', 'Time/Amount', 'Rate', 'Total');

			$rows = array();

			$f = fopen('php://memory', 'w');

			fputcsv($f, $headers, ",");

			foreach ($db_result['expenses'] as $exp) {
				$row = array();
				$row[] = $exp['date_occured'];
				$row[] = $exp['case_name'];
				$row[] = $exp['attachment_name'];
				$row[] = $exp['item_name'];
				$row[] = $exp['desc'];
				$row[] = $exp['amount'];
				$row[] = $exp['rate'];
				
				// calc total
				if($exp['need_calc'] == 'true' || $exp['need_calc'] == TRUE) {
					$row[] = floatval($exp['amount']) * floatval($exp['rate']);
				} else {
					$row[] = 0;
				}

				//fputcsv($output, $exp);
				$rows[] = $row;

				fputcsv($f, $row, ",");
			}

			fseek($f, 0);
			fpassthru($f);

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load billing csv.');
			}
		}
	}

	private function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
	    // open raw memory as file so no temp files needed, you might run out of memory though
	    $f = fopen('php://memory', 'w');
	    // loop over the input array
	    foreach ($array as $line) {
	        // generate csv lines from the inner arrays
	        fputcsv($f, $line, $delimiter);
	    }
	    // reset the file pointer to the start of the file
	    fseek($f, 0);
	    // tell the browser it's going to be a csv file
	    header('Content-Type: application/csv');
	    // tell the browser we want to save it instead of displaying it
	    header('Content-Disposition: attachment; filename="'.$filename.'";');
	    // make php send the generated csv lines to the browser
	    fpassthru($f);
	}

	public function reports()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$user_id = $this->input->post('userid');
		$case_id = $this->input->post('caseid');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$this->load->model('Report');

		// load available team
		$db_result = $this->Report->get_reports($user_id,$case_id);

		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"reports\":".json_encode($db_result['reports'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load reports.');
			}
		}
	}

	public function expense_single()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$exp_id = $this->input->post('expid');

		if (!isset($exp_id) || $exp_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing expense id');
		}

		$this->load->model('Billing');

		// load available team
		$db_result = $this->Billing->view_single_expense($exp_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"expenses\":".json_encode($db_result['expenses'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load expenses.');
			}
		}
	}

	public function expenses()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$user_id = $this->input->post('userid'); //must be int
		$case_id = $this->input->post('caseid');
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		if (!isset($month) || $month == FALSE) {
			$month = date("m");
		}

		if (!isset($year) || $year == FALSE) {
			$year = date("Y");
		}

		$this->load->model('Billing');

		// load available team
		$db_result = $this->Billing->view_expenses($user_id,$case_id,$month,$year);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"expenses\":".json_encode($db_result['expenses'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load expenses.');
			}
		}
	}

	public function cases_cats_atts_ints()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$user_id = $this->input->post('userid'); //must be int
		//$company_id = $this->input->post('compid'); //must be int
		$case_id = $this->input->post('caseid');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$this->load->model('Cases');
		$this->load->model('Billing');
		$this->load->model('Attachment');
		$this->load->model('Interview');

		// convert case id to int
		$cid = $this->Cases->get_caseid_by_md5($case_id);

		// load available team
		$cats = array();
		$atts = array();
		$ints = array();

		$db_bill_result = $this->Billing->view_categories();

		if ($db_bill_result['result']) {
			$cats = $db_bill_result['cats'];
		}

		$db_atts_result = $this->Attachment->load_docs_by_user($user_id,$case_id);

		if ($db_atts_result['result']) {
			$atts = $db_atts_result['docs'];
		}

		$db_int_result = $this->Interview->load_interviews_user($user_id,$case_id);
		if ($db_int_result['result']) {
			$ints = $db_int_result['interviews'];
		}

		$this->output->set_status_header('200');
		echo "{\"result\":1,\"cats\":".json_encode($cats).",\"atts\":".json_encode($atts).",\"interviews\":".json_encode($ints)."}";
	}

	public function interviews()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$case_id = $this->input->post('caseid');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$this->load->model('Interview');

		// load available team
		$db_result = $this->Interview->load_interviews($case_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"interviews\":".json_encode($db_result['interviews'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load interviews.');
			}
		}
	}

	public function interview_notes()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$int_id = $this->input->post('intid');

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		$this->load->model('Interview');

		// load available team
		$db_result = $this->Interview->load_interview_notes($int_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			if(isset($db_result['notes'])){
				echo "{\"result\":".$db_result['result'].",\"notes\":".json_encode($db_result['notes'])."}";
			}else{
				echo "{\"result\":1}";
			}

		}else{
			// since no row error just continue
			$this->output->set_status_header('200');
			echo "{\"result\":1}";
		}
	}

	public function interview()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$int_id = $this->input->post('intid');

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		$this->load->model('Interview');

		// load available team
		$db_result = $this->Interview->load_full_interview($int_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"interview\":".json_encode($db_result['interview'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load interview.');
			}
		}

	}

	public function leadsheet()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$case_id = $this->input->post('caseid');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$this->load->model('Leadsheet');

		// load available team
		$db_result = $this->Leadsheet->load_leads($case_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"leads\":".json_encode($db_result['leads'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load leadsheet.');
			}
		}
	}

	public function case_details()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$client_id = $this->input->post('clientid');
		$att_id = $this->input->post('attorneyid');
		$cpa_id = $this->input->post('cpaid');
		$lea_id = $this->input->post('leagentid');
		$da_id = $this->input->post('daid');

		if (!isset($client_id) || $client_id == FALSE) {
			$client_id = 0;
		}

		if (!isset($att_id) || $att_id == FALSE) {
			$att_id = 0;
		}

		if (!isset($cpa_id) || $cpa_id == FALSE) {
			$cpa_id = 0;
		}

		if (!isset($lea_id) || $lea_id == FALSE) {
			$lea_id = 0;
		}

		if (!isset($da_id) || $da_id == FALSE) {
			$da_id = 0;
		}

		$this->load->model('Cases');

		// load available team
		$db_result = $this->Cases->load_case_details($client_id, $cpa_id, $att_id, $lea_id, $da_id);
		if ($db_result['result']) {
			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"client\":".json_encode($db_result['client']).",\"supporting\":".json_encode($db_result['supporting'])."}";
		}else{
			$this->output->set_status_header('500');
			exit('Unable to load details.');
		}
	}

	public function team_and_users()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$company_id = $this->input->post('compid');
		$case_id = $this->input->post('caseid');

		if (!isset($company_id) || $company_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Company not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$this->load->model('Cases');

		// load available team
		$db_result = $this->Cases->get_team_with_available($case_id, $company_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"team\":".json_encode($db_result['team']).",\"users\":".json_encode($db_result['users'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load team.');
			}
		}
	}

	public function view_docs()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$this->load->model('Document');

		$doc_ids = $this->input->post('docids');

		if (!isset($doc_ids) || $doc_ids == FALSE) {
			$this->output->set_status_header('400');
			exit('Document not found.');
		}

		$db_result = $this->Document->load_included_documents($doc_ids);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"docs\":".json_encode($db_result['docs'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load documents.');
			}
		}

	}

	public function documents()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$company_id = $this->input->post('compid');
		$case_id = $this->input->post('caseid');

		if (!isset($company_id) || $company_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Company not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$this->load->model('Document');

		// load conf and admin docs
		$admin_doc_response = $this->Document->load_documents($company_id, $case_id);
		if (!$admin_doc_response['result']) {
			$this->output->set_status_header('500');
			exit('Unable to load documents.');
		}

		// load conf and admin docs available
		$available_doc_response = $this->Document->load_available_documents($company_id);
		if (!$available_doc_response['result']) {
			$this->output->set_status_header('500');
			exit('Unable to load available documents.');
		}

		// load conf and admin doc categories
		$admin_cats_response = $this->Document->load_doc_categories();
		if (!$admin_cats_response['result']) {
			$this->output->set_status_header('500');
			exit('Unable to load categories.');
		}

		$admin_docs = $admin_doc_response['docs'];
		$available_docs = $available_doc_response['docs'];
		$admin_docs_cats = $admin_cats_response['categories'];

		$conf_html_docs = array();
		$admin_html_docs = array();
		if (isset($admin_docs_cats) && count($admin_docs_cats) > 0) {

		    foreach ($admin_docs_cats as $doc) {
		        
		        $row = "";
		        $id = intval($doc['id']);
		        $name = $doc['name'];
		        $cat = $doc['category'];

		        $chk_name = "";

		        if ($cat == 1) {
		            $chk_name = "chkConfDocs";
		        } else if ($cat == 2) {
		            $chk_name = "chkAdminDocs";
		        }

		        $glph_class = "glyphicon glyphicon-plus text-warning";
		        $file_url = "";
		        $file_userid = "";
		        $doc_count = 0;
		        $disp_text = "";
		        $name_disp = $name;
		        $checked = "";
		        $doc_ids = "";
		        $av_doc_ids = "";
		        $show_row = TRUE;
		        $btnClass = "btnupload";
		        $btnValue = "New";

		        // see if checked
		        if (isset($admin_docs[$id][0]) && is_array($admin_docs[$id][0])) {
		            $doc_count = count($admin_docs[$id]);

		            // get all admin doc ids
		            foreach ($admin_docs[$id] as $ad) {
		                $doc_ids = $doc_ids.",".$ad['id'];
		            }

		            // remove extra commas
		            $doc_ids = trim($doc_ids, ",");

		            $name_other = $name;
		            // handle display text if other
		            if($id == 6 || $id>=16) {
		                $title = $admin_docs[$id][0]['title'];
		                $name_other = $title;
		            }

					$icon = $admin_docs[$id][0]['icon'];
		            $filename = $admin_docs[$id][0]['filename'];
		            $date_added = $admin_docs[$id][0]['date_added'];

		            $glph_class = "glyphicon glyphicon-ok text-success";
		            if($cat == 1) {
		                $name_disp = "<a href=\"#\" data=\"".$id."\" added=\"".$date_added."\" icon=\"".$icon."\" fname=\"".$filename."\" docs=\"".$doc_ids."\" class=\"ancDocView\">".$name_other."</a>";
		            } else if ($cat == 2) {
		            	$name_disp = "<a href=\"#\" data=\"".$id."\" added=\"".$date_added."\" icon=\"".$icon."\" fname=\"".$filename."\" docs=\"".$doc_ids."\" class=\"ancDocViewAdmin\">".$name_other."</a>";
		            }
		            $checked = "checked=true";
		        }

		        if (isset($available_docs[$id]) && is_array($available_docs[$id]) ){

		            // get all admin doc ids
		            foreach ($available_docs[$id] as $ad) {
		                $av_doc_ids = $av_doc_ids.",".$ad['id'];
		            }

		            // remove extra commas
		            $av_doc_ids = trim($av_doc_ids, ",");

		            $glph_class = "glyphicon glyphicon-ok text-success";
		        }

		        if ($doc_count > 1 && $cat == 2) {
		            $glph_class = "badge";
		            $disp_text = $doc_count;
		        }

		        if ($cat == 2) {
		        	$btnClass = "btnuploadadmin";
		        	$btnValue = "Add";
		        }

		        $row = $row."<tr>";
		        $row = $row."<td><input type=\"checkbox\" id=\"chk_".$id."\" avdocs=\"".$av_doc_ids."\" docs=\"".$doc_ids."\" name=\"".$chk_name."\" data=\"".$id."\" value=\"".$name."\" ".$checked."></td>";
		        $row = $row."<td title=\"".$name."\">".$name_disp."</td>";
		        $row = $row."<td><span class=\"".$glph_class."\" id=\"spn_".$id."\">".$disp_text."</span></td>";
		        $row = $row."<td><button type=\"button\" class=\"btn btn-info btn-xs ".$btnClass."\" cat=\"".$cat."\" data=\"".$id."\">".$btnValue."</button></td>";
		        $row = $row."</tr>";

		        if($name_disp == 'Other' && $id >= 17) {
		            $show_row = FALSE;
		        }

		        if ($show_row) {
		        	if ($cat == 1) {
		            	$conf_html_docs[] = $row;
			        } else if ($cat == 2) {
			            $admin_html_docs[] = $row;
			        }
		        }

		    }

		    $this->output->set_status_header('200');
			echo "{\"result\":1,\"conf_docs\":".json_encode($conf_html_docs).",\"admin_docs\":".json_encode($admin_html_docs)."}";

		}else{
			// final safety to avoid empty rows
			$this->output->set_status_header('500');
			exit('Unable to load documents.');
		}

	}

	public function available_docs()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$this->load->model('Document');

		$doc_type_id = $this->input->post('doctypeid');
		$company_id = $this->input->post('compid');

		if (!isset($company_id) || $company_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Company not found.');
		}

		if (!isset($doc_type_id) || $doc_type_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Doc type not found.');
		}

		$db_result = $this->Document->load_available_documents($company_id, $doc_type_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"docs\":".json_encode($db_result['doc_array'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load documents.');
			}
		}

	}

	public function interview_attachments()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$this->load->model('Attachment');

		$int_id = $this->input->post('int_id');

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		$db_result = $this->Attachment->load_documents_interview($int_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"docs\":".json_encode($db_result['docs'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load attachments.');
			}
		}

	}

	public function attachments()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$this->load->model('Attachment');

		$case_id = $this->input->post('caseid');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$db_result = $this->Attachment->load_documents($case_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"docs\":".json_encode($db_result['docs'])."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load attachments.');
			}
		}

	}

	// if I have a city and state then I need a list of zip codes to validate with
	public function zipcode()
	{
		// ensure request is from website
		$base_url = site_url();

		if (!isset($_SERVER['HTTP_REFERER']) || (strripos($_SERVER['HTTP_REFERER'], $base_url) === FALSE))
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST')
		{
			$this->output->set_status_header('401');
			exit('Access not allowed');
		}

		$city = $this->input->post('c');
		$state = $this->input->post('s');

		if ($city == FALSE || $state == FALSE) {
			$this->output->set_status_header('401');
			exit('Invalid parameters');
		}

		$city = trim(strtolower($city));
		$state = trim(strtolower($state));

		$response = "";

		$sql = "SELECT zip FROM geo WHERE LOWER(state)=".$this->db->escape($state);
		$sql = $sql." AND LOWER(city)=".$this->db->escape($city).";";

		$query = $this->db->query($sql);

		if ($query !== FALSE && $query->num_rows() > 0) {
			
			foreach ($query->result() as $row)
			{
				$zip = floatval($row->zip);

				if (isset($zip) && strlen($zip) > 0) {
					$zip = trim($zip);
					$response = $response.",".$zip;
				}
			}

			// remove extra commas
			$response = trim($response, ",");

			$response = "[".$response."]";

		}else{
			$this->output->set_status_header('404');
			exit('No data found');
		}

		$this->output->set_status_header('200');
		echo $response;
	}

}