<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit extends CI_Controller {

	public function index()
	{
		$this->output->set_status_header('401');
		exit('Access not allowed');
	}

	public function delete_report()
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

		$report_id = $this->input->post('report_id');

		if (!isset($report_id) || $report_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Report not found.');
		}

		$this->load->model('Report');

		$db_result = $this->Report->delete_report($report_id);
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
				exit('Unable to remove report.');
			}
		}
	}

	public function edit_expense()
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

		$exp_id = $this->input->post('exp_id');
		$item = $this->input->post('item');
		$description = $this->input->post('desc');
		$date_occured = $this->input->post('dte_occured');
		$amount = $this->input->post('amount');
		$interview_id = $this->input->post('intid');
		$att_id = $this->input->post('attid');

		if (!isset($item) || $item == FALSE) {
			$this->output->set_status_header('400');
			exit('item not found');
		}

		if (!isset($exp_id) || $exp_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Expense not found.');
		}

		$this->load->model('Billing');

		//edit_expense($expense_id,$date_occured,$item,$amount,$desc,$interview_id,$att_id)
		$db_result = $this->Billing->edit_expense($exp_id,$date_occured,$item,$amount,$description,$interview_id,$att_id);
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
				exit('Unable to remove expense.');
			}
		}
	}

	public function delete_expense()
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

		$exp_id = $this->input->post('exp_id');

		if (!isset($exp_id) || $exp_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Expense not found.');
		}

		$this->load->model('Billing');

		//delete_expense($expense_id)
		$db_result = $this->Billing->delete_expense($exp_id);
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
				exit('Unable to remove expense.');
			}
		}
	}

	public function interview_notes_edit()
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

		$int_id = $this->input->post('iid');
		$notes = $this->input->post('notes');

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		$this->load->model('Interview');
		$this->load->model('Cases');

		$db_result = $this->Interview->edit_interview_notes($int_id, $notes);
		if ($db_result['result']) {

			// Send Approval Email to Supervisor
			$this->load->library('SendMail');

			$new_line = chr(0x0D).chr(0x0A);

			// get email for admin by case_id
			$ad_result = $this->Cases->get_case_admin_intid($int_id);
			if ($ad_result['result']) {

				$admin_name = $ad_result['name'];
				$admin_email = $ad_result['email'];
				$case_id = $ad_result['case_id'];
				$int_name = $ad_result['int_name'];

				$email_url = site_url("mycases/view_case")."/".$case_id."#interviews";
				$message = "Attention ".$admin_name.$new_line.$new_line;
				$message = $message."A Tactician Interview [".$int_name."] has been edited and is in need of review. Please click on or copy the paste the following into your web browser:".$new_line.$new_line;
				$message = $message.$email_url.$new_line.$new_line;
				$message = $message."Sincerely,".$new_line;
				$message = $message."Tactician,".$new_line;
				$subject = "Tactician - New Interview";

				$this->sendmail->send_email_ses($message, $subject, $admin_email);
			}

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to edit interview.');
			}
		}

	}

	public function interview_edit()
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

		$int_id = $this->input->post('iid');
		$name = $this->input->post('name');
		$description = $this->input->post('desc');
		$date_occured = $this->input->post('dte_occured');

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

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		$this->load->model('Interview');
		$this->load->model('Cases');

		$db_result = $this->Interview->edit_interview($int_id, $name, $description, $date_occured, $title, $emp, $street, $city, $state, $zip, $phone, $dob, $location);
		if ($db_result['result']) {

			// Send Approval Email to Supervisor
			$this->load->library('SendMail');

			$new_line = chr(0x0D).chr(0x0A);

			// get email for admin by case_id
			$ad_result = $this->Cases->get_case_admin_intid($int_id);
			if ($ad_result['result']) {

				$admin_name = $ad_result['name'];
				$admin_email = $ad_result['email'];
				$case_id = $ad_result['case_id'];

				$email_url = site_url("mycases/view_case")."/".$case_id."#interviews";
				$message = "Attention ".$admin_name.$new_line.$new_line;
				$message = $message."A Tactician Interview [".$name."] has been edited and is in need of review. Please click on or copy the paste the following into your web browser:".$new_line.$new_line;
				$message = $message.$email_url.$new_line.$new_line;
				$message = $message."Sincerely,".$new_line;
				$message = $message."Tactician,".$new_line;
				$subject = "Tactician - New Interview";

				$this->sendmail->send_email_ses($message, $subject, $admin_email);
			}

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to edit interview.');
			}
		}
	}

	public function interview_return()
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
		$this->load->library('SendMail');

		$int_id = $this->input->post('iid');
		$comments = $this->input->post('comments');
		$supervisor_id = $this->input->post('supid');

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		$int_result = $this->Interview->load_full_interview($int_id);

		if($int_result['result']) {
			$interview_name = $int_result['interview']['name'];
			$email = $int_result['interview']['email'];

			$new_line = chr(0x0D).chr(0x0A);

			// process email
			$url = site_url("account/validate")."/".md5($int_result['interview']['id']);
			$message = "The Interview ".$interview_name." has NOT been Approved.".$new_line;

			$message = $message."Comments:".$new_line;

			if (isset($comments) && $comments !== FALSE) {
				if (strlen($comments) > 0) {
					$message = $message.$comments.$new_line.$new_line;
				}
			}

			$subject = "Tactician Interview Review";

			$this->sendmail->send_email_ses($message, $subject, $email);
		}

		$this->output->set_status_header('200');
		echo "{\"result\":".$int_result['result']."}";
	}

	public function interview_approve()
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

		$int_id = $this->input->post('iid');
		$supervisor_id = $this->input->post('supid');
		$comments = $this->input->post('comments');

		if (!isset($int_id) || $int_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Interview not found.');
		}

		if (!isset($supervisor_id) || $supervisor_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Supervisor not found.');
		}

		$this->load->model('Interview');
		$this->load->library('SendMail');

		$db_result = $this->Interview->approve_interview($int_id, $supervisor_id);
		if ($db_result['result']) {

			$int_result = $this->Interview->load_full_interview($int_id);

			if($int_result['result']) {
				$interview_name = $int_result['interview']['name'];
				$email = $int_result['interview']['email'];

				$new_line = chr(0x0D).chr(0x0A);

				// process email
				$url = site_url("account/validate")."/".md5($int_result['interview']['id']);
				$message = "The Interview ".$interview_name." has been Approved.".$new_line;

				$message = $message."Comments:".$new_line;

				if (isset($comments) && $comments !== FALSE) {
					if (strlen($comments) > 0) {
						$message = $message.$comments.$new_line.$new_line;
					}
				}

				$subject = "Tactician Interview Approval";

				$this->sendmail->send_email_ses($message, $subject, $email);
			}

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to approve interview.');
			}
		}
	}

	public function mark_lead_complete()
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

		$lead_id = $this->input->post('leadid');
		$is_complete = $this->input->post('complete');

		if (!isset($lead_id) || $lead_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Lead not found.');
		}

		$this->load->model('Leadsheet');

		$db_result = $this->Leadsheet->mark_complete($lead_id, $is_complete);
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
				exit('Unable to mark lead.');
			}
		}

	}

	public function edit_client()
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

		$client_id = $this->input->post('clientid');
		$name = $this->input->post('name');
		$street = $this->input->post('street');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zip = $this->input->post('zip');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');

		if (!isset($client_id) || $client_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid client');
		}

		if (!isset($name) || strlen($name) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing name');
		}

		if (!isset($street) || strlen($street) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing street');
		}

		if (!isset($city) || strlen($city) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing city');
		}

		if (!isset($state) || strlen($state) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing state');
		}

		if (!isset($zip) || strlen($zip) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing zip');
		}

		if (!isset($phone) || strlen($phone) == FALSE) {
			$phone = "";
		}

		if (!isset($email) || strlen($email) == FALSE) {
			$email = "";
		}

		// edit client
		$db_result = $this->Cases->edit_client($client_id, $name, $street, $city, $state, $zip, $email, $phone);
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
				exit('Unable to edit client.');
			}
		}
	}

	public function edit_supporting_role()
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

		$role_id = $this->input->post('roleid');
		$name = $this->input->post('name');
		$title = $this->input->post('title');
		$street = $this->input->post('street');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zip = $this->input->post('zip');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');

		if (!isset($role_id) || $role_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid role');
		}

		if (!isset($name) || strlen($name) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing name');
		}

		if (!isset($title) || strlen($title) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing title');
		}

		if (!isset($street) || strlen($street) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing street');
		}

		if (!isset($city) || strlen($city) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing city');
		}

		if (!isset($state) || strlen($state) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing state');
		}

		if (!isset($zip) || strlen($zip) == FALSE) {
			$this->output->set_status_header('400');
			exit('Missing zip');
		}

		if (!isset($phone) || strlen($phone) == FALSE) {
			$phone = "";
		}

		if (!isset($email) || strlen($email) == FALSE) {
			$email = "";
		}

		// edit support role
		$db_result = $this->Cases->edit_supporting($role_id, $name, $title, $street, $city, $state, $zip, $email, $phone);
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
				exit('Unable to edit supporting role.');
			}
		}
	}

	public function change_user_case_role()
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

		$user_id = $this->input->post('userid');
		$case_id = $this->input->post('caseid');
		$is_admin = $this->input->post('admin');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid user');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$db_result = $this->Cases->edit_team_member($case_id, $user_id, $is_admin);
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
				exit('Unable to change role.');
			}
		}
	}

	public function remove_user_team()
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

		$user_id = $this->input->post('userid');
		$case_id = $this->input->post('caseid');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid user');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		$db_result = $this->Cases->remove_user_team($case_id,$user_id);
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
				exit('Unable to remove user from team.');
			}
		}

	}

	public function case_name()
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
		$case_name = $this->input->post('casename');

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Case not found.');
		}

		if (!isset($case_id) || $case_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Name required.');
		}

		// edit case name
		$db_result = $this->Cases->edit_case_name($case_id, $case_name);
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
				exit('Unable to edit case name.');
			}
		}

	}

	public function remove_document()
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

		$doc_id = $this->input->post('docid');

		if (!isset($doc_id) || $doc_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Document not found.');
		}

		// explode commma seperated values
		$doc_ids = explode(",", $doc_id);
		if($doc_ids !== FALSE) {
			foreach ($doc_ids as $did) {
				$did = intval($did);
				$db_result = $this->Document->remove_document($did);
				if (!$db_result['result']) {
					if (strlen($db_result['message']) > 0) {
						$this->output->set_status_header('400');
						exit($db_result['message']);
					} else {
						$this->output->set_status_header('500');
						exit('Unable to remove document.');
					}
				}
			}
		}else{

			$db_result = $this->Document->remove_document($doc_id);
			if (!$db_result['result']) {
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to remove document.');
				}
			}

		}
		
		$this->output->set_status_header('200');
		echo "{\"result\":1}";

	}

	public function remove_attachment()
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

		$attachment_id = $this->input->post('attid');

		if (!isset($attachment_id) || $attachment_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Attachment not found.');
		}

		$db_result = $this->Attachment->remove_document($attachment_id);
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
				exit('Unable to remove attachment.');
			}
		}
	}

	public function open_close_case()
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

		$case_id = $this->input->post('cid');
		$is_closed = $this->input->post('isclosed');

		if ($this->Cases->open_close_case($case_id,$is_closed) == FALSE) {
			$this->output->set_status_header('500');
			exit('Update failed');
		}

		$this->output->set_status_header('200');
		echo "{\"result\":true}";

	}

	public function edit_user_role()
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

		$this->load->model('User');

		$user_id = $this->input->post('user_id');
		$role = $this->input->post('is_admin');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		$db_result = $this->User->edit_user_role($user_id, $role);
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
				exit('Unable to edit user role.');
			}
		}

	}

	public function remove_user()
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

		$this->load->model('User');

		$user_id = $this->input->post('user_id');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		$db_result = $this->User->remove_user($user_id);
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
				exit('Unable to remove user.');
			}
		}

	}

	public function edit_account()
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

		$this->load->model('User');

		$user_id = $this->input->post('user_id');
		$email = $this->input->post('email');
		$password = $this->input->post('pass');

		if (!isset($user_id) || $user_id == FALSE) {
			$this->output->set_status_header('400');
			exit('Invalid request');
		}

		if ((!isset($email) || $email == FALSE) && (!isset($password) || $password == FALSE)) {
			$this->output->set_status_header('400');
			exit('Valid email and/or password required.');
		}

		$email_changed = FALSE;
		$password_changed = FALSE;

		// change_email_md5($md5, $new_email)
		if (isset($email) && strlen($email) > 0) {
			$db_result = $this->User->change_email_md5($user_id, $email, TRUE);
			if ($db_result['result']) {
				$email_changed = TRUE;
			} else {
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to save email.');
				}
			}
		}

		// change_password_insecure($md5, $new_password)
		if (isset($password) && strlen($password) > 0) {
			$db_result = $this->User->change_password_insecure($user_id, $password);
			if ($db_result['result']) {
				$password_changed = TRUE;
			} else {
				if (strlen($db_result['message']) > 0) {
					$this->output->set_status_header('400');
					exit($db_result['message']);
				} else {
					$this->output->set_status_header('500');
					exit('Unable to change password.');
				}
			}
		}

		$result_msg = "";
		if ($email_changed) {
			$result_msg = "email updated ";
		}
		if ($password_changed) {
			$result_msg = $result_msg."password_changed";
		}

		$this->output->set_status_header('200');
		echo "{\"result\":".$db_result['result'].",\"message\":\"".trim($result_msg)."\"}";
 
	}

	// edit profile (name and title)
	public function edit_profile()
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

		$this->load->model('User');

		$user_id = $this->input->post('user_id');
		$name = $this->input->post('name');
		$title = $this->input->post('title');

		if ((!isset($name) || $name == FALSE) || (!isset($title) || $title == FALSE) || (!isset($user_id) || $user_id == FALSE)) {
			$this->output->set_status_header('400');
			exit('All fields required');
		}

		$db_result = $this->User->edit_user($user_id, $name, $title, 0);

		if ($db_result['result']) {

			// change name and title in session
			$this->session->set_userdata('name',$name);
			$this->session->set_userdata('title',$title);

			$this->output->set_status_header('200');
			echo "{\"result\":".$db_result['result']."}";

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to save changes. Please contact support.');
			}
		}

	}

	// edit agency
	public function edit_agency()
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

		$this->load->model('Company');

		$comp_id = $this->input->post('comp_id');
		$name = $this->input->post('name');
		$street = $this->input->post('street');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zip = $this->input->post('zip');

		if ((!isset($name) || $name == FALSE) || (!isset($street) || $street == FALSE) || (!isset($city) || $city == FALSE) || (!isset($state) || $state == FALSE) || (!isset($zip) || $zip == FALSE) || (!isset($comp_id) || $comp_id == FALSE)) {
			$this->output->set_status_header('400');
			exit('All fields required');
		}

		$db_result = $this->Company->edit_company($comp_id, $name, $street, $city, $state, $zip);
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
				exit('Unable to save changes. Please contact support.');
			}
		}

	}

	// invite a collaborator
	public function invite_user()
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

		$this->load->library('SendMail');
		$this->load->model('User');
		$this->load->model('Company');

		$comp_id = $this->input->post('comp_id');
		$name = $this->input->post('name');
		$email = $this->input->post('email');

		if ((!isset($name) || $name == FALSE) || (!isset($email) || $email == FALSE) || (!isset($comp_id) || $comp_id == FALSE)) {
			$this->output->set_status_header('400');
			exit('All fields required');
		}

		$db_result = $this->User->store_invitation($comp_id, $name, $email);
		if ($db_result['result']) {

			$this->load->helper('message');

			$url = site_url("account/register")."/".$comp_id;

			$company_response = $this->Company->load_company($comp_id, TRUE);
			if ($company_response['result'] == TRUE) {
				
				$company_name = $company_response['name'];
				
				// send email
				$message_response = build_guest_invitation_email_message($name, $url, $company_name);
				$subject = "Tactician Invitation from ".$company_name;
				$to_item = array('name' => $name, 'email' => $email);
				$to = array($to_item);

				$from_name = "Tactician Software";
				$from_email = "noreply@tacticianinc.com";

				//$this->sendmail->send_email($message_response['message'], $subject, $from_name, $from_email, $to, FALSE, 'mtmosestn@gmail.com');
				$this->sendmail->send_email_ses($message_response['message'], $subject, $email);
				
				$this->output->set_status_header('200');
				echo "{\"result\":".$db_result['result'].", \"num_tries\":".$db_result['num_tries']."}";

			} else {
				$this->output->set_status_header('500');
				exit('Unable to send invitation at this time. Please try again later');
			}

		}else{
			// determine if user error or server error
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to save invitation. Please contact support.');
			}
		}

	}

	// profile image upload
	public function profile_image()
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

		$config['upload_path'] = FCPATH."img/user/";
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 1024 * 8;
        $config['encrypt_name'] = TRUE;
 
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('fleProfile'))
        {
            $this->output->set_status_header('400');
            exit('File must be valid image in .png or .jpg format.');
        }
        else
        {
        	$user_id_md5 = $this->input->post('profimgid');

        	if (!isset($user_id_md5) || $user_id_md5 === FALSE) {
        		$this->output->set_status_header('500');
            	exit('File may be corrupted. Please try again.');
        	}

        	$data = $this->upload->data();

        	if (!isset($data)) {
        		$this->output->set_status_header('500');
            	exit('File may be corrupted. Please try again.');
        	}

        	$this->load->model('User');

        	$db_result = $this->User->edit_user_image($user_id_md5, $data['file_name']);
        	if ($db_result['result']) {
        		$this->output->set_status_header('200');
        		// set session image
        		$this->session->set_userdata('image', $data['file_name']);
				echo "{\"result\":true, \"url\":\"".base_url('img/user')."/".$data['file_name']."\"}";
        	} else {
        		$this->output->set_status_header('500');
            	exit('File must be valid image.');
        	}
        }

	}

	// agency image upload
	public function agency_image()
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

		$config['upload_path'] = FCPATH."img/company/";
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 1024 * 8;
        $config['encrypt_name'] = TRUE;
 
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('fleAgency'))
        {
            $this->output->set_status_header('400');
            exit('File must be valid image in .png or .jpg format.');
        }
        else
        {
        	$agency_id_md5 = $this->input->post('agencyimgid');

        	if (!isset($agency_id_md5) || $agency_id_md5 === FALSE) {
        		$this->output->set_status_header('500');
            	exit('File may be corrupted. Please try again.');
        	}

        	$data = $this->upload->data();

        	if (!isset($data)) {
        		$this->output->set_status_header('500');
            	exit('File may be corrupted. Please try again.');
        	}

        	$this->load->model('Company');

        	$db_result = $this->Company->edit_company_image($agency_id_md5, $data['file_name']);
        	if ($db_result['result']) {
        		$this->output->set_status_header('200');
				echo "{\"result\":true, \"url\":\"".base_url('img/company')."/".$data['file_name']."\"}";
        	} else {
        		$this->output->set_status_header('500');
            	exit('File must be valid image.');
        	}
        }
	}

}