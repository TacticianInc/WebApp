<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Add extends CI_Controller {

	public function index()
	{
		$this->output->set_status_header('401');
		exit('Access not allowed');
	}

	public function share_report()
	{
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

		$this->load->library('SendMail');
		$this->load->model('Report');
		$this->load->model('User');

		$user_ids = $this->input->post('userids');
		$report_id = $this->input->post('reportid');
		$email = $this->input->post('email');

		$email_list = array();

		if (!isset($user_ids) || $user_ids == FALSE) {
			$user_ids = [0];
		}

		if (!isset($report_id) || $report_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Report not found.');
		}

		// send email to non-team member
		if(isset($email) && $email !== FALSE) {
			
			$new_line = chr(0x0D).chr(0x0A);

			$email_url = site_url("pdf")."/".md5($report_id);
			$message = "A Tactician Report is available for you. Please click on or copy the paste the following into your web browser:".$new_line.$new_line;
			$message = $message.$email_url.$new_line.$new_line;
			$message = $message."Sincerely,".$new_line;
			$message = $message."Tactician,".$new_line;
			$subject = "Tactician Report";

			$this->sendmail->send_email_ses($message, $subject, $email);
		}

		if (count($user_ids) > 0) {
			foreach ($user_ids as $uid) {
				$db_result = $this->Report->share_report($report_id,$uid);
				if (!$db_result['result']) {
					$email_list[] = $this->User->get_email_by_id($uid);
					// determine if user error or server error
					if (strlen($db_result['message']) > 0) {
						$this->output->set_status_header('400');
						exit($db_result['message']);
					} else {
						$this->output->set_status_header('500');
						exit('Unable to share report.');
					}
				}
			}
		}else{
			$db_result = $this->Report->share_report($report_id,$user_ids);
			if (!$db_result['result']) {
				$email_list[] = $this->User->get_email_by_id($user_ids);
				// determine if user error or server error
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to share report.');
				}
			}
		}

		// send email to team
		if(isset($email_list) && count($email_list) > 0) {
			
			$new_line = chr(0x0D).chr(0x0A);
			$email_url = site_url("pdf")."/".md5($report_id);
			$message = "A Tactician Report has been shared with you. Please click on or copy the paste the following into your web browser:".$new_line.$new_line;
			$message = $message.$email_url.$new_line.$new_line;
			$message = $message."Sincerely,".$new_line;
			$message = $message."Tactician".$new_line;
			$subject = "Tactician Report";

			foreach ($email_list as $eml) {
				$this->sendmail->send_email_ses($message, $subject, $eml);
			}
		}

		$this->output->set_status_header('200');
		echo "{\"result\":1}";
	}

	public function add_report()
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

		$this->load->model('Report');
		$this->load->model('Cases');

		$user_id = $this->input->post('userid');
		$case_id = $this->input->post('caseid');
		$name = $this->input->post('name');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($name) || $name == FALSE) {
			$this->output->set_status_header('400');
			exit('Report not named.');
		}

		// get int for md5 of case id
		$cid = $this->Cases->get_caseid_by_md5($case_id);

		$db_result = $this->Report->add_new_report($user_id,$cid,$name);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add report.');
			}
		}

	}

	public function add_edit_rate()
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

		$this->load->model('Billing');

		$cat_id = $this->input->post('catid');
		$user_id = $this->input->post('userid');
		$amount = $this->input->post('amount');

		//add_edit_rate($user_id,$cat_id,$amount)
		$db_result = $this->Billing->add_edit_rate($user_id,$cat_id,$amount);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to update rate.');
			}
		}
	}

	public function add_expense()
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

		$this->load->model('Billing');
		$this->load->model('Cases');

		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('userid');
		$item = $this->input->post('item');
		$description = $this->input->post('desc');
		$date_occured = $this->input->post('dte_occured');
		$amount = $this->input->post('amount');
		$interview_id = $this->input->post('intid');
		$att_id = $this->input->post('attid');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($item) || $item == FALSE) {
			$this->output->set_status_header('400');
			exit('item not found');
		}

		// convert case_id to numeric
		$cid = $this->Cases->get_caseid_by_md5($case_id);
		if ($cid <= 0) {
			$this->output->set_status_header('400');
			exit('invalid case');
		}

		$db_result = $this->Billing->add_new_expense($cid,$user_id,$date_occured,$item,$description,$amount,$interview_id,$att_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add expense.');
			}
		}

	}

	public function add_interview()
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

		$this->load->model('Interview');
		$this->load->model('Cases');
		$this->load->model('User');

		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('userid');
		$name = $this->input->post('name');
		$title = $this->input->post('title');
		$emp = $this->input->post('emp');
		$street = $this->input->post('street');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zip = $this->input->post('zip');
		$phone = $this->input->post('phone');
		$description = $this->input->post('desc');
		$date_occured = $this->input->post('dte_occured');

		$dob = $this->input->post('dob');
		$location = $this->input->post('location');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		// format data
		$cid = $this->Cases->get_caseid_by_md5($case_id);
		$uid = $this->User->get_userid_by_md5($user_id);

		$db_result = $this->Interview->add_new_interview($cid, $uid, $name, $description, $date_occured, $title, $emp, $street, $city, $state, $zip, $phone, $dob, $location);
		if ($db_result['result']) {

			// Send Approval Email to Supervisor
			$this->load->library('SendMail');

			$new_line = chr(0x0D).chr(0x0A);

			// get email for admin by case_id
			$ad_result = $this->Cases->get_case_admin($case_id);
			if ($ad_result['result']) {

				$admin_name = $ad_result['name'];
				$admin_email = $ad_result['email'];

				$email_url = site_url("mycases/view_case")."/".$case_id."#interviews";
				$message = "Attention ".$admin_name.$new_line.$new_line;
				$message = $message."A Tactician Interview [".$name."] has been added and is in need of review. Please click on or copy the paste the following into your web browser:".$new_line.$new_line;
				$message = $message.$email_url.$new_line.$new_line;
				$message = $message."Sincerely,".$new_line;
				$message = $message."Tactician,".$new_line;
				$subject = "Tactician - New Interview";

				$this->sendmail->send_email_ses($message, $subject, $admin_email);
			}

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add interview.');
			}
		}

	}

	public function add_team_member()
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

		$this->load->model('Cases');

		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('userid');
		$is_admin = $this->input->post('admin');
		$joined = date('Y-m-d');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		// format data
		$cid = $this->Cases->get_caseid_by_md5($case_id);

		$db_result = $this->Cases->add_new_team_member($cid, $user_id, $is_admin, $joined);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add team member.');
			}
		}
	}

	public function add_synopsis_text()
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

		$this->load->model('Cases');
		$this->load->model('User');

		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('uid');
		$text = $this->input->post('text');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($text) || $text == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing content.');
		}

		// format data
		$uid = $this->User->get_userid_by_md5($user_id);

		$db_result = $this->Cases->add_synopsis_contents($case_id, $text, $uid);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to save synopsis.');
			}
		}

	}

	public function add_synopsis_doc()
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

		$this->load->model('Cases');
		$this->load->model('User');

		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('uid');
		$file = $this->input->post('file');
		$size = $this->input->post('size');
		$type = $this->input->post('type');
		$name = $this->input->post('name');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($file) || $file == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing file.');
		}

		if (!isset($size) || $size == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing file attributes.');
		}

		if (!isset($type) || $type == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing file attributes.');
		}

		if (!isset($name) || $name == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing file attributes.');
		}

		// format data
		$file_data = ltrim($file,'[removed]');
		$file_contents = base64_decode($file_data);
		$uid = $this->User->get_userid_by_md5($user_id);
		$path_to_save = FCPATH."docs/";

		// pdf, word, txt
		$db_result = $this->Cases->add_synopsis_file($case_id, $uid, $path_to_save, $file_contents, $size, $type, $name);
		if ($db_result['result']) {

			$url = base_url("docs")."/".$db_result['name'];

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id'].",\"url\":\"".$url."\"}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add synopsis.');
			}
		}

	}

	public function add_lead_entries()
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

		$this->load->model('Leadsheet');
		$this->load->model('User');
		$this->load->model('Cases');

		$case_id = $this->input->post('caseid');
		$entries = $this->input->post('leads');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		// check for md5
		if (preg_match('/^[a-f0-9]{32}$/', $case_id) == TRUE) {
			$case_id = $this->Cases->get_caseid_by_md5($case_id);
		}
		
		// if not array - no lead sheet entries to save so return back positive
		if(!is_array($entries)) {
			$this->output->set_status_header('200');
			exit("{\"result\":1}");
		}

		foreach ($entries as $lead_entry) {

			$uid = $lead_entry['astoid'];
			$name = $lead_entry['name'];
			$source = $lead_entry['source'];
			$comments = $lead_entry['notes'];
			$is_complete = $lead_entry['iscomp'];
			$date_assigned = $lead_entry['asdt'];

			$user_id = $this->User->get_userid_by_md5($uid);
			
			$lead_result = $this->Leadsheet->add_new_entry($case_id, $user_id, $name, $source, $comments, $is_complete, $date_assigned);
			
			if ($lead_result['result'] == FALSE) {
				// determine if user error or server error
				if (strlen($lead_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($lead_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add document.');
				}
			}
		}

		//success
		$this->output->set_status_header('200');
		echo "{\"result\":1}";
	}

	public function add_admin_doc()
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
		$this->load->model('User');

		$company_id = $this->input->post('compid');
		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('uid');
		$document = $this->input->post('file');

		if (!isset($document) || $document == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid file.');
		}

		if (!isset($company_id) || $company_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		// convert user_id from md5
		$uid = $this->User->get_userid_by_md5($user_id);

		// set path (root/docs)
		$path_to_save = FCPATH."docs/";

		$file_data = ltrim($document['data'],'[removed]');

		$file_contents = base64_decode($file_data);
		$size = $document['size'];
		$att_type = $document['att_type'];
		$doc_type = $document['doc_type'];
		$title = $document['title'];
		$name = $document['name'];

		$doc_result = $this->Document->add_new_document($doc_type, $company_id, $uid, $path_to_save, $file_contents, $size, $att_type, $title, $name);
		if ($doc_result['result'] == FALSE) {
			// determine if user error or server error
			if (strlen($doc_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($doc_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to document.');
			}
		}

		$document_id = $doc_result['id'];

		$add_case_result = $this->Document->attach_document_to_case($case_id, $document_id, $user_id);
		if ($add_case_result['result'] == FALSE) {
			// determine if user error or server error
			if (strlen($add_case_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($add_case_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add document to case.');
			}
		}

		//success
		$this->output->set_status_header('200');
		echo "{\"result\":1,\"id\":".$document_id."}";
	}

	public function attach_doc_to_case()
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
		$this->load->model('User');

		$case_id = $this->input->post('caseid');
		$doc_id = $this->input->post('docid');
		$user_id = $this->input->post('uid');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($doc_id) || $doc_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Document not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		// convert user_id from md5
		$uid = $this->User->get_userid_by_md5($user_id);

		$doc_ids = explode(",", $doc_id);
		if($doc_ids !== FALSE) {
			foreach ($doc_ids as $did) {
				$did = intval($did);
				$db_result = $this->Document->attach_document_to_case($case_id, $did, $user_id);
				if (!$db_result['result']) {
					if (strlen($db_result['message']) > 0) {
						$this->output->set_status_header('400');
						exit($db_result['message']);
					} else {
						$this->output->set_status_header('500');
						exit('Unable to add document.');
					}
				}
			}
		}else{
		
			$add_case_result = $this->Document->attach_document_to_case($case_id, $doc_id, $user_id);
			if ($add_case_result['result'] == FALSE) {
				// determine if user error or server error
				if (strlen($add_case_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($add_case_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add document to case.');
				}
			}

		}

		//success
		$this->output->set_status_header('200');
		echo "{\"result\":1}";
	}

	public function add_supporting_docs()
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
		$this->load->model('User');

		$case_id = $this->input->post('caseid');
		$user_id = $this->input->post('uid');
		$int_id = $this->input->post('iid'); //interview id
		$documents = $this->input->post('fls');

		if (!isset($documents) || count($documents) == 0) {
			$this->output->set_status_header('400');
			exit('Invalid file.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('User not found.');
		}

		if (!isset($int_id) || $int_id == FALSE) {
			$int_id = 0;
		}

		// convert user_id from md5
		$uid = $this->User->get_userid_by_md5($user_id);

		// set path (root/docs)
		$path_to_save = FCPATH."docs/";

		// if not array - no documents to save so return back positive
		if(!is_array($documents)) {
			$this->output->set_status_header('200');
			exit("{\"result\":1}");
		}

		// data,size,type,name,tags,title
		foreach ($documents as $doc) {

			//[removed]
			$file_data = ltrim($doc['data'],'[removed]');

			// data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,UEsD
			$file_contents = base64_decode($file_data);
			$size = $doc['size'];
			$type = $doc['type'];
			$name = $doc['name'];
			$tags = $doc['tags'];
			$title = $doc['title'];

			$doc_result = $this->Attachment->add_new_document($case_id, $uid, $int_id, $path_to_save, $file_contents, $size, $type, $name, $tags, $title);
			if ($doc_result['result'] == FALSE) {
				// determine if user error or server error
				if (strlen($doc_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($doc_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add attachments.');
				}
			}
		}

		//success
		$this->output->set_status_header('200');
		echo "{\"result\":1}";
	}

	public function add_case_team()
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

		$this->load->model('Cases');
		$this->load->model('User');

		$case_id = $this->input->post('caseid');
		$admin_id = $this->input->post('adminid');
		$team = $this->input->post('team_mems'); // array of md5 ids

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($admin_id) || $admin_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case Admin required.');
		}

		$joined = date('Y-m-d');

		foreach ($team as $mem) {
			
			$uid = $this->User->get_userid_by_md5($mem);
			
			$is_admin = FALSE;
			if($mem == $admin_id) {
				$is_admin = TRUE;
			}

			$team_result = $this->Cases->add_new_team_member($case_id, $uid, $is_admin, $joined);
			if ($team_result['result'] == FALSE) {
				// determine if user error or server error
				if (strlen($team_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($team_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add team.');
				}
			}
		}

		//success
		$this->output->set_status_header('200');
		echo "{\"result\":1}";
	}

	public function add_case()
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

		$this->load->model('Cases');
		$this->load->model('Company');

		$company_id = 0;
		$md5 = $this->input->post('comp_id');
		$name = $this->input->post('name');
		$predication = $this->input->post('pred');
		$client_id = $this->input->post('cl_id');
		$att_id = $this->input->post('att_id');
		$act_id = $this->input->post('cpa_id');
		$lea_id = $this->input->post('lea_id');
		$da_id = $this->input->post('da_id');

		if (!isset($md5) || $md5 == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		// must get $company_id from md5
		$company_response = $this->Company->load_company($md5, true);
		if ($company_response['result']) {
			$company_id = $company_response['id'];
		} else {
			$this->output->set_status_header('400');
			exit('Invalid company id');
		}

		// convert md5 ids for supporting roles to ints
		$attorney_id = 0;
		$cpa_id = 0;
		$le_agent_id = 0;
		$district_attorney_id = 0;

		if (isset($att_id) && strlen($att_id) > 10){
			$attorney_id = $this->Cases->get_solution_id_from_md5($att_id);
		}

		if (isset($act_id) && strlen($act_id) > 10){
			$cpa_id = $this->Cases->get_solution_id_from_md5($act_id);
		}

		if (isset($lea_id) && strlen($lea_id) > 10){
			$le_agent_id = $this->Cases->get_solution_id_from_md5($lea_id);
		}

		if (isset($da_id) && strlen($da_id) > 10){
			$district_attorney_id = $this->Cases->get_solution_id_from_md5($da_id);
		}

		$created = date('Y-m-d');

		// create new case
		$db_result = $this->Cases->add_new_case($company_id, $client_id, $name, $predication, $created, $attorney_id, $cpa_id, $le_agent_id, $district_attorney_id);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add case.');
			}
		}

	}

	public function add_client()
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

		$this->load->model('Cases');
		$this->load->model('Company');

		$company_id = 0;
		$md5 = $this->input->post('comp_id');
		$image = $this->input->post('image');
		$name = $this->input->post('name');
		$street = $this->input->post('street');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zip = $this->input->post('zip');

		if (!isset($md5) || $md5 == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		// must get $company_id from md5
		$company_response = $this->Company->load_company($md5, true);
		if ($company_response['result']) {
			$company_id = $company_response['id'];
		} else {
			$this->output->set_status_header('400');
			exit('Invalid company id');
		}

		$created = date('Y-m-d');

		// create new client
		$db_result = $this->Cases->add_new_client($company_id, $name, $street, $city, $state, $zip, $image, $created);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add client.');
			}
		}

	}

	public function add_multiple_supporting_roles()
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

		$this->load->model('Cases');
		$this->load->model('Company');

		$company_id = 0;
		$md5 = $this->input->post('comp_id');
		$attorney = $this->input->post('att');
		$cpa = $this->input->post('cpa');
		$leagency = $this->input->post('leag');
		$distattorney = $this->input->post('datt');

		$created = date('Y-m-d');

		if (!isset($md5) || $md5 == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		// must get $company_id from md5
		$company_response = $this->Company->load_company($md5, true);
		if ($company_response['result']) {
			$company_id = $company_response['id'];
		} else {
			$this->output->set_status_header('400');
			exit('Invalid company id');
		}

		// set result to true incase nothing is needed to be added
		$ret_value = array('result' => TRUE, 'attorney_id' => 0, 'cpa_id' => 0, 'leagency_id' => 0, 'distatt_id' => 0);

		if(isset($attorney) && $attorney != FALSE && $attorney != 'false') {

			$att_obj = json_decode($attorney);

			$type = 1;
			$name = $att_obj->{'name'};
			$title = $att_obj->{'title'};
			$street = $att_obj->{'street'};
			$city = $att_obj->{'city'};
			$state = $att_obj->{'state'};
			$zip = $att_obj->{'zip'};
			$phone = $att_obj->{'phone'};
			$email = $att_obj->{'email'};

			// create new support role
			$db_result = $this->Cases->add_new_supporting($company_id, $name, $title, $street, $city, $state, $zip, $type, $created, $phone, $email);
			if ($db_result['result']) {

				$ret_value['result'] = true;
				$ret_value['attorney_id'] = md5($db_result['id']);

			}else{
				// determine if user error or server error
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add supporting role.');
				}
			}
		}

		if(isset($cpa) && $cpa != FALSE && $cpa != 'false') {

			$cpa_obj = json_decode($cpa);

			$type = 2;
			$name = $cpa_obj->{'name'};
			$title = $cpa_obj->{'title'};
			$street = $cpa_obj->{'street'};
			$city = $cpa_obj->{'city'};
			$state = $cpa_obj->{'state'};
			$zip = $cpa_obj->{'zip'};
			$phone = $cpa_obj->{'phone'};
			$email = $cpa_obj->{'email'};

			// create new support role
			$db_result = $this->Cases->add_new_supporting($company_id, $name, $title, $street, $city, $state, $zip, $type, $created, $phone, $email);
			if ($db_result['result']) {

				$ret_value['result'] = true;
				$ret_value['cpa_id'] = md5($db_result['id']);

			}else{
				// determine if user error or server error
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add supporting role.');
				}
			}
		}

		if(isset($leagency) && $leagency != FALSE && $leagency != 'false') {

			$leagency_obj = json_decode($leagency);

			$type = 3;
			$name = $leagency_obj->{'name'};
			$title = $leagency_obj->{'title'};
			$street = $leagency_obj->{'street'};
			$city = $leagency_obj->{'city'};
			$state = $leagency_obj->{'state'};
			$zip = $leagency_obj->{'zip'};
			$phone = $leagency_obj->{'phone'};
			$email = $leagency_obj->{'email'};

			// create new support role
			$db_result = $this->Cases->add_new_supporting($company_id, $name, $title, $street, $city, $state, $zip, $type, $created, $phone, $email);
			if ($db_result['result']) {

				$ret_value['result'] = true;
				$ret_value['leagency_id'] = md5($db_result['id']);

			}else{
				// determine if user error or server error
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add supporting role.');
				}
			}
		}

		if(isset($distattorney) && $distattorney != FALSE && $distattorney != 'false') {
			
			$distattorney_obj = json_decode($distattorney);

			$type = 4;
			$name = $distattorney_obj->{'name'};
			$title = $distattorney_obj->{'title'};
			$street = $distattorney_obj->{'street'};
			$city = $distattorney_obj->{'city'};
			$state = $distattorney_obj->{'state'};
			$zip = $distattorney_obj->{'zip'};
			$phone = $distattorney_obj->{'phone'};
			$email = $distattorney_obj->{'email'};

			// create new support role
			$db_result = $this->Cases->add_new_supporting($company_id, $name, $title, $street, $city, $state, $zip, $type, $created, $phone, $email);
			if ($db_result['result']) {

				$ret_value['result'] = true;
				$ret_value['distattorney_id'] = md5($db_result['id']);

			}else{
				// determine if user error or server error
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to add supporting role.');
				}
			}
		}

		// display the josn
		$this->output->set_status_header('200');
		echo json_encode($ret_value);
		
	}

	public function add_supporting_role()
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

		$this->load->model('Cases');
		$this->load->model('Company');

		$company_id = 0;
		$md5 = $this->input->post('comp_id');
		$type = $this->input->post('type');
		$name = $this->input->post('name');
		$title = $this->input->post('title');
		$street = $this->input->post('street');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zip = $this->input->post('zip');

		if (!isset($md5) || $md5 == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		// must get $company_id from md5
		$company_response = $this->Company->load_company($md5, true);
		if ($company_response['result']) {
			$company_id = $company_response['id'];
		} else {
			$this->output->set_status_header('400');
			exit('Invalid company id');
		}

		$created = date('Y-m-d');

		// create new support role
		$db_result = $this->Cases->add_new_supporting($company_id, $name, $title, $street, $city, $state, $zip, $type, $created);
		if ($db_result['result']) {

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result'].",\"id\":".$db_result['id']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to add supporting role.');
			}
		}
	}

}