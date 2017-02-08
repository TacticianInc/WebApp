<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends CI_Controller {

	public function index()
	{
		$data['page_title'] = 'About | Tactician';
		$data['menu_item'] = 1;
		
		$this->load->view('header', $data);
		$this->load->view('about_page');
		$this->load->view('footer');
	}

}