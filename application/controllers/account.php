<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	public function index()
	{
		// ensure user is signed in
		$email_session = $this->session->userdata('email');
		if (!isset($email_session) || (isset($email_session) && strlen($email_session) == 0)) {
			redirect(site_url(""), 'refresh');
		}

		$this->load->helper('account');
		$this->load->model('Company');
		$this->load->model('Payment');

		$user_id = $this->session->userdata('id');
		$name = $this->session->userdata('name');
		$title = $this->session->userdata('title');
		$email = $this->session->userdata('email');
		$is_admin = $this->session->userdata('is_admin');
		$image = $this->session->userdata('image');

		$company_id = "";
		$company_name = "";
		$street = "";
		$state = "";
		$city = "";
		$zip = "";
		$company_image = "";
		$cc_type = 0;
		$cc_last_four = "";
		$cc_exp_date = "";
		$plan = "";
		$image_comp_data = "";
		$team = array();
		$invites = array();
		$plans = array();
		$cc_script = FALSE;

		if($is_admin == 1 || $is_admin == TRUE) {
			$cc_script = TRUE;
		}

		$team = array();

		$image_data = "<img id=\"imgProfile\" src=\"".base_url('img/user/profile.png')."\" style=\"width:48px;height:48px;\" class=\"img-thumbnail\">";
		if (isset($image) && strlen($image) > 0 && $image !== 'NULL') {
		    $image_data = "<img id=\"imgProfile\" src=\"".base_url('img/user')."/".$image."\" style=\"width:48px;height:48px;;\" class=\"img-thumbnail\">";
		}

		$company_respose = $this->Company->load_company_by_user_id($user_id);

		if ($company_respose['result']) {

			$company_id = $company_respose['id'];
			$company_name = $company_respose['name'];
			$street = $company_respose['street'];
			$city = $company_respose['city'];
			$state = $company_respose['state'];
			$zip = $company_respose['zip'];
			$company_image = $company_respose['image'];

			$plan = $company_respose['plan'];

			$cc_type = $company_respose['cc_type'];
			$cc_last_four = $company_respose['last_four'];
			$cc_exp_date = $company_respose['exp_date'];

			$image_comp_data = "<img id=\"imgAgency\" src=\"".base_url('img/company/noimage.png')."\" style=\"height:128px;\" class=\"img-thumbnail\">";
			if (isset($company_image) && strlen($company_image) > 0) {
				$c_image = base_url("img/company")."/".$company_image;
			    $image_comp_data = "<img id=\"imgAgency\" src=\"".$c_image."\" style=\"height:128px;\" class=\"img-thumbnail\">";
			}

			$team_response = $this->Company->load_company_users($company_id);
			if ($team_response['result']) {
				$team = $team_response['team'];
				$invites = $team_response['invites'];
			}

			$plans = $this->Payment->get_plans();

		}

		// main account / profile page
		$data['is_admin'] = $is_admin;
		$data['user_id'] = $user_id;
		$data['name'] = $name;
		$data['title'] = $title;
		$data['email'] = $email;
		$data['image'] = $image_data;
		$data['team'] = $team;
		$data['invites'] = $invites;
		$data['cc_type'] = $cc_type;
		$data['cc_last_four'] = $cc_last_four;
		$data['cc_exp_date'] = $cc_exp_date;
		$data['company_name'] = $company_name;
		$data['company_id'] = $company_id;
		$data['street'] = $street;
		$data['city'] = $city;
		$data['state'] = $state;
		$data['zip'] = $zip;
		$data['company_image'] = $image_comp_data;
		$data['plan'] = $plan;
		$data['cc_script'] = $cc_script;
		$data['script'] = edit_script($plans, $user_id, $company_id);
 
		$data['menu_item'] = 3;
		$data['page_title'] = 'Account | Tactician';
		
		$this->load->view('header', $data);
		$this->load->view('account_page');
		$this->load->view('footer');
	}

	public function logout()
	{
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
    	$this->output->set_header("Pragma: no-cache");

    	$this->session->unset_userdata('email');
		$this->session->unset_userdata('name');
		$this->session->unset_userdata('title');
		$this->session->unset_userdata('is_admin');
		$this->session->unset_userdata('image');
		$this->session->unset_userdata('id');

		$this->session->sess_destroy();

		redirect(site_url(""), 'refresh');
	}

	public function forgot()
	{
		$email_session = $this->session->userdata('email');
		if (isset($email_session) && strlen($email_session) > 0) {
			redirect(site_url("account"), 'refresh');
		}

		$this->load->library('SendMail');

		$this->load->model('Captcha');
		$this->load->model('User');

		$this->load->helper('script');
		$this->load->helper('message');

		$captcha = $this->Captcha->load_captcha();

		$error_msg = "";
		$success_text = "";
		$email = "";

		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$email = $this->input->post('email');
			$captcha_response = $this->input->post('captcha');

			if ($email != FALSE && strlen($email) > 0) {

				if ($captcha_response != FALSE) {
					// validate captcha
					if ($this->Captcha->validate_captcha($captcha_response)) {
						
						$pass_response = $this->User->reset_password($email);

						if ($pass_response['result']) {

							$new_password = $pass_response['new_password'];

							$name = "Tactician User";

							$message_response = build_forgot_email_message($new_password);
							$subject = "Your Tactician Account";
							$to_item = array('name' => $name, 'email' => $email);
							$to = array($to_item);

							$from_name = "Tactician Software";
							$from_email = "noreply@tacticianinc.com";

							$this->sendmail->send_email_ses($message_response['message'], $subject, $email);
							//$this->sendmail->send_email($message_response['message'], $subject, $from_name, $from_email, $to, FALSE);
							
							$success_text = build_forgot_success_message();

						} else {
							$error_msg = $pass_response['message'];
						}

					}else{
						$error_msg = "Are you human? Please enter the name you see on the image.";
					}
				}

			} else {
				$error_msg = "A valid email is required.";
			}
		}

		$data['email'] = $email;
		$data['script'] = forgot_script();
		$data['success_text'] = $success_text;
		$data['error'] = $error_msg;
		$data['page_title'] = 'Forgot Password | Tactician';
		$data['captcha'] = $captcha;
		
		$this->load->view('header', $data);
		$this->load->view('forgot_page');
		$this->load->view('footer');
	}

	public function validate()
	{
		$this->load->helper('url');
		$this->load->model('User');

		// validate email address link
		$query_string = $this->uri->segment(3);

		if ($query_string !== FALSE && strlen($query_string) > 0) {
			// validate_user_email($md5)
			$result = $this->User->validate_user_email($query_string);
		}

		// redirect to sign in
		redirect(site_url(""), 'refresh');
	}

	public function register()
	{
		$email_session = $this->session->userdata('email');
		if (isset($email_session) && strlen($email_session) > 0) {
			redirect(site_url("account"), 'refresh');
		}

		$this->load->library('SendMail');

		$this->load->model('User');
		$this->load->model('Company');
		$this->load->model('Payment');

		$this->load->helper('script');
		$this->load->helper('message');

		$posturl = site_url('account/register');

		// see if it is guest or full registration
		$query_string = $this->uri->segment(3);
		$plan_id = $this->input->get('plan');

		$success_text = "";
		$error = "";
		$is_guest = FALSE;
		$cc_script = TRUE;
		$script = register_script();

		$name = "";
		$title = "";
		$email = "";
		$company = "";
		$street = "";
		$city = "";
		$state = "";
		$zip = "";
		$password = "";
		$cc_name = "";
		$cc_num = "";
		$cc_month = "";
		$cc_year = "";
		$cc_type = 0;
		$cvv = "";
		$phone = "";
		$plans = array();

		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$name = $this->input->post('name');
			$title = $this->input->post('title');
			$email = $this->input->post('email');
			$company = $this->input->post('company');
			$street = $this->input->post('street');
			$city = $this->input->post('city');
			$state = $this->input->post('state');
			$zip = $this->input->post('zip');
			$phpne = $this->input->post('phone');
			$password = $this->input->post('password');
			$cc_name = $this->input->post('cardname');
			$cc_num = $this->input->post('cardnumber');
			$cc_month = $this->input->post('expmonth');
			$cc_year = $this->input->post('expyear');
			$cvv = $this->input->post('cvv');
			$cc_type = $this->input->post('cc_type');
			$plan_id = $this->input->post('rdoPlan');
		}

		// for guest registration we expect an MD5 hash of the company id to be passed
		if ($query_string !== FALSE && strlen($query_string) > 0) {

			$posturl = site_url('account/register')."/".$query_string;

			// register as guest
			$is_guest = TRUE;
			$cc_script = FALSE;
			$script = register_quest_script();

			// get company id from md5
			$company_response = $this->Company->load_company($query_string, TRUE);
			$company_id = $company_response['id'];

			if ($this->input->server('REQUEST_METHOD') == 'POST')
			{
				// load company from query string data
				$user_response = $this->User->store_new_user($company_id, $name, $title, $email, $password);

				if ($user_response['result'] == TRUE) {

					// process email
					$email_url = site_url("account/validate")."/".md5($user_response['id']);
					$message_response = build_guest_register_email_message($name, $email_url);
					$subject = "Your Tactician Account";

					$this->sendmail->send_email_ses($message_response['message'], $subject, $email);

					// set success response
					$success_text = build_register_guest_success_message();

				} else {
					$error = $user_response['message'];
				}
			}

		}else{

			$plans = $this->Payment->get_plans();

			if ($this->input->server('REQUEST_METHOD') == 'POST')
			{
				// store company
				$company_respose = $this->Company->store_new_company($company, $street, $city, $state, $zip, "");

				if ($company_respose['result'] == TRUE) {

					// register full new user with payment
					$company_id = $company_respose['id'];
					$is_admin=1;
					$user_response = $this->User->store_new_user($company_id, $name, $title, $email, $password, $is_admin);
					
					if ($user_response['result'] == TRUE) {

						if($plan_id == FALSE || strlen($plan_id) == "" || $plan_id == 0) {
							$plan_id = 1;
						}

						// store payment
						$payment_response = $this->Payment->store_new_cc($company_id, $cc_name, $cc_num, $cc_month, $cc_year, $cvv, $cc_type, $plan_id);
						if ($payment_response['result']) {
							
							// make payment
							// TODO: create_new_subscription($company_id, $amount, $frequency)

							// process email
							$email_url = site_url("account/validate")."/".md5($user_response['id']);
							$message_response = build_register_email_message($name, $email_url);
							$subject = "Your Tactician Account";
							$to_item = array('name' => $name, 'email' => $email);
							$to = array($to_item);

							$from_name = "Tactician Software";
							$from_email = "noreply@tacticianinc.com";

							$this->sendmail->send_email_ses($message_response['message'], $subject, $email);
							//$this->sendmail->send_email($message_response['message'], $subject, $from_name, $from_email, $to, TRUE);

							// get total
							$total = $this->Payment->get_total_from_plan_type($plan_id);

							$cc_type_name = "Credit Card";
							$cc_last_four = substr($cc_num, -4);
							switch ($cc_type) {
								case 1: $cc_type_name = "Visa"; break;
								case 2: $cc_type_name = "Mastercard"; break;
								case 3: $cc_type_name = "AMEX"; break;
								case 4: $cc_type_name = "Discover"; break;
							}

							// set success response
							$success_text = build_register_success_message($cc_last_four, $cc_type_name, $total);

						} else {
							$error = $payment_response['message'];
						}

					} else {
						$error = $user_response['message'];
					}

				} else {
					$error = $company_respose['message'];
				}

			}
		}

		$data['posturl'] = $posturl;
		$data['name'] = $name;
		$data['title'] = $title;
		$data['email'] = $email;
		$data['phone'] = $phone;
		$data['company'] = $company;
		$data['street'] = $street;
		$data['city'] = $city;
		$data['state'] = $state;
		$data['zip'] = $zip;
		$data['cc_name'] = $cc_name;
		$data['cc_num'] = $cc_num;
		$data['cc_month'] = $cc_month;
		$data['cc_year'] = $cc_year;
		$data['cvv'] = $cvv;
		$data['cc_type'] = $cc_type;

		$data['plan_id'] = $plan_id;
		$data['plans'] = $plans;
		$data['success_text'] = $success_text;
		$data['error'] = $error;
		$data['guest'] = $is_guest;
		$data['cc_script'] = $cc_script;
		$data['script'] = $script;
		$data['page_title'] = 'Register | Tactician';
		$data['menu_item'] = 2;
		
		$this->load->view('header', $data);
		$this->load->view('register_page');
		$this->load->view('footer');
	}

}