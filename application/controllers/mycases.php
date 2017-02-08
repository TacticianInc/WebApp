<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mycases extends CI_Controller {

	public function index()
	{
		// ensure user is signed in
		$email_session = $this->session->userdata('email');
		if (!isset($email_session) || (isset($email_session) && strlen($email_session) == 0)) {
			redirect(site_url(""), 'refresh');
		}

		$this->load->helper('case');
		$this->load->model('Company');
		$this->load->model('Cases');

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

		$cases = array();

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

			$image_comp_data = "<img id=\"imgAgency\" src=\"".base_url('img/company/noimage.png')."\" style=\"height:128px;\" class=\"img-thumbnail\">";
			if (isset($company_image) && strlen($company_image) > 0) {
				$c_image = base_url("img/company")."/".$company_image;
			    $image_comp_data = "<img id=\"imgAgency\" src=\"".$c_image."\" style=\"height:128px;\" class=\"img-thumbnail\">";
			}

			$case_response = $this->Cases->get_cases($company_id, $user_id, TRUE);
			if ($case_response['result']) {
				$cases = $case_response['cases'];
			}

		}

		$data['script'] = cases_script($cases,$is_admin);
		$data['is_admin'] = $is_admin;

		$data['page_title'] = 'My Cases | Tactician';
		$data['menu_item'] = 5;
		
		$this->load->view('header', $data);
		$this->load->view('cases_page');
		$this->load->view('footer');
	}

	public function view_case()
	{
		// ensure user is signed in
		$email_session = $this->session->userdata('email');
		if (!isset($email_session) || (isset($email_session) && strlen($email_session) == 0)) {
			redirect(site_url(""), 'refresh');
		}

		// get case id
		$case_id = $this->uri->segment(3);
		if(!isset($case_id) || strlen($case_id) == 0){
			redirect(site_url('cases'), 'refresh');
		}

		$this->load->helper('case');
		$this->load->model('Cases');
		$this->load->model('Attachment');
		$this->load->model('Document');
		$this->load->model('Company');
		$this->load->model('Interview');

		$user_id = $this->session->userdata('id');
		$name = $this->session->userdata('name');
		$title = $this->session->userdata('title');
		$email = $this->session->userdata('email');
		$is_admin = $this->session->userdata('is_admin');
		$image = $this->session->userdata('image');

		$case_name = "";
		$case_pred = "";
		$case_created = "";
		$case_modified = "";

		$company_id = "";
		$company_name = "";
		$street = "";
		$city = "";
		$state = "";
		$zip = "";
		$company_image = "";
		$plan = "";

		$interviews = array();
		$attachments = array();
		$team = array();
		$admin_docs = array();
		$available_docs = array();
		$admin_docs_cats = array();
		$synopsis = array();
		$is_team_lead = FALSE;
		$case_closed = FALSE;
		$client = array();
		$supporting = array();

		// ensure user has permission to view case
		$case_response = $this->Cases->open_case($case_id, $user_id);

		if ($case_response['result']) {

			$case_name = $case_response['case']['name'];
			$case_pred = $case_response['case']['predication'];
			$case_created = $case_response['case']['created'];
			$case_modified = $case_response['case']['modified'];
			$case_closed = $case_response['case']['is_closed'];
			$client_id = $case_response['case']['client_id'];
            $attorney_id = $case_response['case']['attorney_id'];
            $cpa_id = $case_response['case']['cpa_id'];
            $le_agent_id = $case_response['case']['le_agent_id'];
            $da_id = $case_response['case']['district_attorney_id'];

			// load case details
			$case_response = $this->Cases->load_case_details($client_id, $cpa_id, $attorney_id, $le_agent_id, $da_id);
			if ($case_response['result']) {
				$client = $case_response['client'];
				$supporting = $case_response['supporting'];
			}

			// load interviews
			$int_response = $this->Interview->load_interviews($case_id);
			if ($int_response['result']) {
				$interviews = $int_response['interviews'];
			}

			// load attachments
			$att_response = $this->Attachment->load_documents($case_id);
			if ($att_response['result']) {
				$attachments = $att_response['docs'];
			}

			// load team
			$team_response = $this->Cases->get_team($case_id);
			if ($team_response['result']) {
				$team = $team_response['team'];
			}

			// load synopsis
			$synopsis_response = $this->Cases->get_synopsis($case_id);
			if ($synopsis_response['result']) {
				$synopsis = $synopsis_response['synopsis'];
			}

			// load company
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

				$image_comp_data = "<img id=\"imgAgency\" src=\"".base_url('img/company/noimage.png')."\" style=\"height:128px;\" class=\"img-thumbnail\">";
				if (isset($company_image) && strlen($company_image) > 0) {
					$c_image = base_url("img/company")."/".$company_image;
				    $image_comp_data = "<img id=\"imgAgency\" src=\"".$c_image."\" style=\"height:128px;\" class=\"img-thumbnail\">";
				}

				// load conf and admin docs
				$admin_doc_response = $this->Document->load_documents(md5($company_id), $case_id);
				if ($admin_doc_response['result']) {
					$admin_docs = $admin_doc_response['docs'];
				}

				// load conf and admin docs available
				$available_doc_response = $this->Document->load_available_documents(md5($company_id));
				if ($available_doc_response['result']) {
					$available_docs = $available_doc_response['docs'];
				}

				// load conf and admin doc categories
				$admin_cats_response = $this->Document->load_doc_categories();
				if ($admin_cats_response['result']) {
					$admin_docs_cats = $admin_cats_response['categories'];
				}

			}

			// determine if this user is team lead
			$is_team_lead = $this->Cases->is_team_lead($case_id, $user_id);

		} else {
			// user does not have permission or invalid case id
			redirect(site_url('cases'), 'refresh');
		}

		$data['script'] = view_case_script($team, base_url(),$company_id, $case_id, $user_id, site_url('mycases'), $case_name, $is_admin, $is_team_lead);
		$data['case_name'] = $case_name;
		$data['case_pred'] = $case_pred;
		$data['case_created'] = $case_created;
		$data['case_modified'] = $case_modified;
		$data['is_admin'] = $is_admin;
		$data['attachments'] = $attachments;
		$data['interviews'] = $interviews;
		$data['admin_docs'] = $admin_docs;
		$data['available_docs'] = $available_docs;
		$data['admin_docs_cats'] = $admin_docs_cats;
		$data['team'] = $team;
		$data['user_id'] = $user_id;
		$data['is_team_lead'] = $is_team_lead;
		$data['case_closed'] = $case_closed;
		$data['synopsis'] = $synopsis;
		$data['client'] = $client;
		$data['supporting'] = $supporting;
		$data['case_id'] = $case_id;

		$data['page_title'] = $case_name." | Tactician";
		$data['menu_item'] = 5;

		$this->load->view('header', $data);
		$this->load->view('cases_view_page');
		$this->load->view('footer');
	}

	public function new_case()
	{
		// ensure user is signed in
		$email_session = $this->session->userdata('email');
		if (!isset($email_session) || (isset($email_session) && strlen($email_session) == 0)) {
			redirect(site_url(""), 'refresh');
		}

		$this->load->helper('case');
		$this->load->model('Company');

		$user_id = $this->session->userdata('id');
		$name = $this->session->userdata('name');
		$title = $this->session->userdata('title');
		$email = $this->session->userdata('email');
		$is_admin = $this->session->userdata('is_admin');
		$image = $this->session->userdata('image');

		// ensure is admin
		if (!isset($is_admin) || ($is_admin == 0 || $is_admin == FALSE)){
			redirect(site_url('cases'), 'refresh');
		}

		$company_id = "";
		$company_name = "";
		$street = "";
		$state = "";
		$city = "";
		$zip = "";
		$company_image = "";
		$team = array();
		$supporting = array();
		$clients = array();

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

			$image_comp_data = "<img id=\"imgAgency\" src=\"".base_url('img/company/noimage.png')."\" style=\"height:128px;\" class=\"img-thumbnail\">";
			if (isset($company_image) && strlen($company_image) > 0) {
				$c_image = base_url("img/company")."/".$company_image;
			    $image_comp_data = "<img id=\"imgAgency\" src=\"".$c_image."\" style=\"height:128px;\" class=\"img-thumbnail\">";
			}

			// get all users for company
			$team_response = $this->Company->load_company_users($company_id);
			if ($team_response['result']) {
				$team = $team_response['team'];
			}

			// get all clients for company
			$client_response = $this->Company->load_company_clients($company_id);
			if ($client_response['result']) {
				$clients = $client_response['clients'];
			}

			// load supporting - attorney,cpa, etc...
			$supporting_response = $this->Company->load_company_supporting($company_id);
			if ($supporting_response['result']) {
				$supporting = $supporting_response['supporting'];
			}

		}else{
			// needs agency first
			redirect('/account', 'refresh');
		}

		$data['script'] = new_case_script($company_id, $user_id, $team);

		$data['user_id'] = $user_id;
		$data['user_image'] = $image;
		$data['name'] = $name;
		$data['title'] = $title;
		$data['email'] = $email;
		$data['company_name'] = $company_name;
		$data['company_id'] = $company_id;
		$data['street'] = $street;
		$data['city'] = $city;
		$data['state'] = $state;
		$data['zip'] = $zip;
		$data['company_image'] = $image_comp_data;
		$data['team'] = $team;
		$data['supporting'] = $supporting;
		$data['clients'] = $clients;

		$data['page_title'] = 'New Case | Tactician';
		$data['menu_item'] = 5;
		
		$this->load->view('header', $data);
		$this->load->view('case_new_page');
		$this->load->view('footer');
	}

}