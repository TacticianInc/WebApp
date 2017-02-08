<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myreports extends CI_Controller {

	public function index()
	{
		// ensure user is signed in
		$email_session = $this->session->userdata('email');
		if (!isset($email_session) || (isset($email_session) && strlen($email_session) == 0)) {
			redirect(site_url(""), 'refresh');
		}

		$this->load->helper('report');
		$this->load->model('Company');
		$this->load->model('Cases');

		// get case id
		$case_id = $this->uri->segment(2);
		if(!isset($case_id) || strlen($case_id) == 0){
			redirect(site_url('cases'), 'refresh');
		}

		$user_id = $this->session->userdata('id');
		$name = $this->session->userdata('name');
		$title = $this->session->userdata('title');
		$email = $this->session->userdata('email');
		$is_admin = $this->session->userdata('is_admin');
		$image = $this->session->userdata('image');
		$team = array();
		$case_name = "";

		$case_response = $this->Cases->open_case($case_id, $user_id);
		if ($case_response['result']) {
			$case_name = $case_response['case']['name'];
		}

		$company_respose = $this->Company->load_company_by_user_id($user_id);

		if ($company_respose['result']) {
			$company_id = $company_respose['id'];
			
			$user_response = $this->Company->load_company_users($company_id);
			if ($user_response['result']) {
				$team = $user_response['team'];
			}
		}

		$pdf_url = site_url("pdf");

		$data['case_id'] = $case_id;
		$data['case_name'] = $case_name;
		$data['script'] = report_script($case_id,$user_id,$company_id,$is_admin,$team,$pdf_url);
		$data['is_admin'] = $is_admin;
		$data['user_name'] = $name;
		$data['page_title'] = 'My Reports | Tactician';
		$data['menu_item'] = 6;
		
		$this->load->view('header', $data);
		$this->load->view('reports_page');
		$this->load->view('footer');
	}

}