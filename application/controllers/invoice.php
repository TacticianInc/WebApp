<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends CI_Controller {

	public function index()
	{
		setlocale(LC_MONETARY, 'en_US');

		// get case id
		$case_id = $this->uri->segment(2);
		if(!isset($case_id) || strlen($case_id) == 0){
			redirect(site_url(''), 'refresh');
		}

		$month = $this->uri->segment(3);
		if(!isset($month) || strlen($month) == 0) {
			$month = date("m");
		}

		$year = $this->uri->segment(4);
		if(!isset($year) || strlen($year) == 0) {
			$year = date("Y");
		}

		$this->load->model('Billing');
		$this->load->model('Company');
		$this->load->model('Cases');

		// get case name
		$case_name = $this->Cases->get_casename($case_id);

		// get company information
		$company_respose = $this->Company->load_company_by_case_id($case_id);
		if (!$company_respose['result']) {
			$this->output->set_status_header('400');
			exit('No company data found');
		}

		$company_name = $company_respose['name'];
        $company_street = $company_respose['street'];
        $company_phone = $company_respose['phone'];
        $city = $company_respose['city'];
        $state = $company_respose['state'];
        $zip = $company_respose['zip'];
        $company_address = $city.", ".$state." ".$zip;
        $image = $company_respose['image'];
        $image_url = base_url("img/company")."/".$image;

        // get client information
        $client_response = $this->Cases->get_case_client($case_id);
        if (!$client_response['result']) {
			$this->output->set_status_header('400');
			exit('No client data found');
		}

        $client_name = $client_response['client']['name'];
        $client_street = $client_response['client']['street'];
        $client_city = $client_response['client']['city'];
        $client_state = $client_response['client']['state'];
        $client_zip = $client_response['client']['zip'];
        $client_address = $client_city.", ".$client_state." ".$client_zip;

        $db_result = $this->Billing->view_expenses(0,$case_id,$month,$year);
		if ($db_result['result']) {

			$this->load->library('Fpdf');

			$fpdf = $this->fpdf;

			$fpdf->AddPage();

			$width = $fpdf->GetPageWidth()-20;

			// title
			$fpdf->SetFont('Arial','B',14);
			$fpdf->Cell($width,5,'Invoice:',0,2,'L',FALSE);

			// company
			$fpdf->SetFont('Arial','B',10);
			$fpdf->Cell($width,5,$company_name,0,2,'C',FALSE);
			$fpdf->Cell($width,5,$company_street,0,2,'C',FALSE);
			$fpdf->Cell($width,5,$company_address,0,2,'C',FALSE);
			$fpdf->Cell($width,5,$company_phone,0,2,'C',FALSE);

			// line
			$fpdf->Line(10, 37, $fpdf->GetPageWidth()-10, 37);

			// client
			$fpdf->SetY(39);
			$fpdf->SetFont('Arial','',10);
			$fpdf->Cell($width,5,$client_name,0,2,'L',FALSE);
			$fpdf->Cell($width,5,$client_street,0,2,'L',FALSE);
			$fpdf->Cell($width,5,$client_address,0,2,'L',FALSE);

			// line
			$fpdf->Line(10, 55, $fpdf->GetPageWidth()-10, 55);

			// case id / name
			$fpdf->SetY(57);
			$fpdf->SetFont('Arial','B',10);
			$fpdf->Cell(15,5,"Case ID:",0,0,'L',FALSE);
			$fpdf->SetFont('Arial','',10);
			$fpdf->Cell(50,5,$case_name,0,2,'L',FALSE);

			// line
			$fpdf->Line(10, 64, $fpdf->GetPageWidth()-10, 64);

			// table
			$fpdf->SetY(66);
			$fpdf->SetFont('Arial','B',10);
			$fpdf->Cell(25,5,"Date",0,0,'L',FALSE);
			$fpdf->Cell(40,5,"Engagment",0,0,'L',FALSE);
			$fpdf->Cell(50,5,"Activity/Expense",0,0,'L',FALSE);
			$fpdf->Cell(35,5,"Time/Amount",0,0,'L',FALSE);
			$fpdf->Cell(25,5,"Rate",0,0,'L',FALSE);
			$fpdf->Cell(20,5,"Total",0,0,'L',FALSE);

			$space = 73;
			$grand_total = 0.00;

			foreach ($db_result['expenses'] as $exp) {

				$date = $exp['date_occured'];
				$eng = $exp['attachment_name'];
				$act = $exp['item_name'];
				$desc = $exp['desc'];
				$amt = $exp['amount'];
				$rate = $exp['rate'];

				// calc total
				$total = $amt;
				if($exp['need_calc'] == 'true' || $exp['need_calc'] == TRUE) {
					$total = floatval($exp['amount']) * floatval($exp['rate']);
				}

				$grand_total = $grand_total + $total;
				
				$fpdf->SetY($space);
				$fpdf->SetFont('Arial','',9);
				$fpdf->Cell(25,5,$date,0,0,'L',FALSE);
				$fpdf->Cell(40,5,$eng,0,0,'L',FALSE);
				$fpdf->Cell(50,5,$act,0,0,'L',FALSE);
				$fpdf->Cell(35,5,$amt,0,0,'L',FALSE);
				$fpdf->Cell(25,5,$rate,0,0,'L',FALSE);
				$fpdf->Cell(20,5,money_format('%.2n', $total),0,0,'L',FALSE);
				
				$space = $space + 7;
			}
			// end table

			// show grand total
			$fpdf->Line(10, $space, $fpdf->GetPageWidth()-10, $space);

			$space = $space + 5;

			$fpdf->SetY($space);
			$fpdf->SetFont('Arial','B',10);
			$fpdf->Cell(15,5,"Total:",0,0,'L',FALSE);
			$fpdf->SetFont('Arial','',10);
			$fpdf->Cell(50,5,money_format('%.2n', $grand_total),0,2,'L',FALSE);
			
			$this->fpdf->SetAutoPageBreak(true, 0);
			$this->fpdf->AliasNbPages();
			$this->fpdf->SetCreator('Tactician');
			$this->fpdf->SetSubject('Invoice');
			$this->fpdf->SetTitle('Tactician Invoice');
			$this->fpdf->SetAuthor('Tactician');
			$this->fpdf->Output();

		}else{
			if (strlen($db_result['message']) > 0) {
				$this->output->set_status_header('400');
				exit($db_result['message']);
			} else {
				$this->output->set_status_header('500');
				exit('Unable to load invoice.');
			}
		}
	}

}