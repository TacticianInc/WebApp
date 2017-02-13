<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->helper('script');
		$this->load->model('Captcha');
		$this->load->model('User');

		$email_session = $this->session->userdata('email');
		if (isset($email_session) && strlen($email_session) > 0) {
			redirect(site_url("mycases"), 'refresh');
		}

		$is_valid_captcha = FALSE;
		$captcha = "";
		$script = signin_script();
		$error = "";

		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$captcha_response = $this->input->post('captcha');

			// check for captcha
			if ($this->Captcha->has_captcha()) {
				if ($captcha_response != FALSE) {
					// validate captcha
					if ($this->Captcha->validate_captcha($captcha_response)) {
						$is_valid_captcha = TRUE;
					}
				}
			} else {
				$is_valid_captcha = TRUE;
			}
			
			if ($is_valid_captcha === TRUE) {
				$validate = $this->User->validate_user($email, $password);

				if ($validate['result'] === TRUE) {

					$id = 0;
					$name = "";
					$title = "";
					$image = "";
					$is_admin = "";

					// load userdata
					$user_response = $this->User->load_user($email);
					if ($user_response['result']) {

						$id = $user_response['id'];
						$name = $user_response['name'];
						$title = $user_response['title'];
						$image = $user_response['image'];
						$is_admin = $user_response['is_admin'];

						// create session and goto account page
						$this->session->set_userdata('email', $email);
						$this->session->set_userdata('name', $name);
						$this->session->set_userdata('title', $title);
						$this->session->set_userdata('is_admin', $is_admin);
						$this->session->set_userdata('image', $image);
						$this->session->set_userdata('id', $id);

						redirect(site_url("mycases"), 'refresh');

					} else {
						// user does not have a validated email address
						$error = $user_response['message'];
						$captcha = $this->Captcha->load_captcha();
					}

				} else {
					// invlaid sign in attempt
					$error = $validate['message'];
					$captcha = $this->Captcha->load_captcha();
				}
			}else{
				$error = "Are you human? Enter the name you see on the image.";
				$captcha = $this->Captcha->load_captcha();
			}
			
		}

		$data['page_title'] = 'Tactician | Sign In';
		$data['script'] = $script;
		$data['error'] = $error;
		$data['captcha'] = $captcha;

		$this->load->view('header', $data);
		$this->load->view('home');
		$this->load->view('footer');
	}
}
