<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Legal extends CI_Controller {

	public function index()
	{
		$data['page_title'] = 'Legal | Tactician';
		
		$this->load->view('header', $data);
		$this->load->view('legal_page');
		$this->load->view('footer');
	}
}