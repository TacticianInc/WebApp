<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mybilling extends CI_Controller {

	public function index()
	{
		// ensure user is signed in
		$email_session = $this->session->userdata('email');
		if (!isset($email_session) || (isset($email_session) && strlen($email_session) == 0)) {
			redirect(site_url(""), 'refresh');
		}

		$this->load->helper('billing');
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
		$company_id = "";
		$case_name = "";

		$this->load->model('Cases');
		$this->load->model('Company');

		$case_response = $this->Cases->open_case($case_id, $user_id);

		if ($case_response['result']) {
			$case_name = $case_response['case']['name'];
		}

		// build agents
		$agents = "";
		if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
			// load_company_users($id)
			$company_respose = $this->Company->load_company_by_user_id($user_id);

			$agents .= "<option value=\"0\" selected>All Agents</option>";

			if ($company_respose['result']) {
				$comp_users_response = $this->Company->load_company_users($company_respose['id']);
				$company_id = $company_respose['id'];
				if ($comp_users_response['result']) {
					$team = $comp_users_response['team'];
					foreach ($team as $mem) {
						$agents .= "<option value=\"".$mem['id']."\">".$mem['name']."</option>";
					}
				}
			}
		}

		// build months
		$current_month = date("m");
		$months = "";
		for($i=0;$i<12;$i++) {
			$month = "";
			$value = "";
			switch ($i) {
				case 0:
					$month = "Jan";
					$value = "01";
					break;
				case 1:
					$month = "Feb";
					$value = "02";
					break;
				case 2:
					$month = "Mar";
					$value = "03";
					break;
				case 3:
					$month = "Apr";
					$value = "04";
					break;
				case 4:
					$month = "May";
					$value = "05";
					break;
				case 5:
					$month = "June";
					$value = "06";
					break;
				case 6:
					$month = "July";
					$value = "07";
					break;
				case 7:
					$month = "Aug";
					$value = "08";
					break;
				case 8:
					$month = "Sep";
					$value = "09";
					break;
				case 9:
					$month = "Oct";
					$value = "10";
					break;
				case 10:
					$month = "Nov";
					$value = "11";
					break;
				case 11:
					$month = "Dec";
					$value = "12";
					break;
			}
			if ($value == $current_month) {
				$months .= "<option value=\"".$value."\" selected>".$month."</option>";
			}else{
				$months .= "<option value=\"".$value."\">".$month."</option>";
			}
			
		}

		// build years
		$current_year = date("Y");
		$last_year = $current_year - 1;
		$years = "<option value=".$current_year." selected>".$current_year."</option>";
		$years .= "<option  value=".$last_year.">".$last_year."</option>";

		$data['script'] = billing_script($case_id,$user_id,$company_id,$is_admin);
		$data['is_admin'] = $is_admin;

		$data['agents'] = $agents;
		$data['years'] = $years;
		$data['months'] = $months;

		$data['user_name'] = $name;
		$data['page_title'] = $case_name.' Billing | Tactician';
		$data['menu_item'] = 5;

		$data['case_id'] = $case_id;
		$data['case_name'] = $case_name;
		
		$this->load->view('header', $data);
		$this->load->view('billing_page');
		$this->load->view('footer');
	}

}