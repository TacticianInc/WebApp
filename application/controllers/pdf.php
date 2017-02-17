<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pdf extends CI_Controller {

	public function index()
	{
		//one can post or send values via url

		$user_id = 0;
		if(!isset($user_id) || strlen($user_id) == 0){
			$user_id = $this->input->post('uid');
		}

		$this->load->library('Fpdf');
		$this->load->model('Report');

		$report_id = $this->uri->segment(2);
		if(isset($report_id) && strlen($report_id) > 5){
			$report_id = $this->Report->get_report_id_md5($report_id);
		}else{
			$report_id = $this->input->post('rid');
		}

		if(!isset($report_id) || strlen($report_id) == 0){
			redirect(site_url(''), 'refresh');
		}
		
		$report_response = $this->Report->gen_report_data($report_id, $user_id);
		if($report_response['result'] == FALSE) {
			exit("report is no longer available");
		}

		//var_dump($report_response);

		$report = $report_response['report'];
		$case = $report_response['case'];
		$company = $report_response['company'];
		$client = $report_response['client'];
		$documents = $report_response['documents'];
		$interviews = $report_response['interviews'];
		$attachments = $report_response['attachments'];
		$supporting = $report_response['supporting'];

		// warning page
		$this->build_warning_page($this->fpdf,$documents);

		// title page
		$this->build_title_page($this->fpdf,$report,$case,$company,$client);

		// supporting page
		$this->build_supporting_page($this->fpdf,$supporting);

		// synopsis page
		$this->build_synopsis_page($this->fpdf,$case);

		// listing page
		$this->build_index_page($this->fpdf,$documents,$interviews,$attachments);

		// interviews
		foreach ($interviews as $intv) {

			$int_name = $intv["name"];
			$int_title = $intv["title"];
			$int_emp = $intv["employer"];
			$int_street = $intv["street"];
			$int_city = $intv["city"];
			$int_state = $intv["state"];
			$int_zip = $intv["zip"];
			$int_dob = $intv["dob"];
			$int_notes = $intv["notes"];
			$int_agent = $intv["author_name"];
			$int_date = date('d/m/Y', strtotime($intv["date_occured"]));
			$int_desc = $intv["description"];
			$int_location = $intv["location"];
			$int_attachments = $intv["attachments"];

			$this->build_interview_page($this->fpdf,$int_name,$int_agent,$int_date,$int_desc,$int_title,$int_emp,$int_street,$int_city,$int_state,$int_zip,$int_dob,$int_notes,$int_location,$int_attachments);
		}

		$this->fpdf->SetAutoPageBreak(true, 0);
		$this->fpdf->AliasNbPages();
		$this->fpdf->SetCreator('Tactician');
		$this->fpdf->SetSubject($report["name"]);
		$this->fpdf->SetTitle($case["name"]);
		$this->fpdf->SetAuthor($case["author"]);
		$this->fpdf->Output();
		
	}

	private function build_warning_page(&$fpdf,$documents)
	{
		$fpdf->AddPage();

		$width = $fpdf->GetPageWidth()-20;

		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,'Warning:',0,2,'L',FALSE);

		$fpdf->SetY(20);
		$fpdf->SetFont('Arial','',12);
		$fpdf->Cell($width,5,'This document contains sensitive information. Please read the following warnings before continuing.',0,2,'L',FALSE);
		//Before continuing, be sure to read each document listed below.

		$fpdf->SetFont('Arial','',12);
		$fpdf->SetY(30);

		if(count($documents) > 0) {

			$count = 0;

			foreach ($documents as $doc) {
				$count = $count+1;

				$doc_text = $doc['text'];

				$fpdf->MultiCell($width,4,$doc_text,0,'L', FALSE);
				//$fpdf->MultiCell($width,3,$doc_text,0,2,'L',FALSE);
				$fpdf->Cell($width,5,"",0,2,'L',FALSE); // space between

				if ($count < count($documents)) {
					$fpdf->SetFont('Arial','B',9);
					$fpdf->SetXY(190, 270);
					// Now we display our page number using the Cell function.
					$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
				}

			}
			
		}

		$fpdf->SetFont('Arial','B',9);
		$fpdf->SetXY(190, 270);
		// Now we display our page number using the Cell function.
		$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
	}

	private function build_title_page(&$fpdf,$report,$case,$company,$client)
	{
		$fpdf->AddPage();
		
		$report_created = $report["created"];

		$company_phone = "";
		$company_name = $company["name"];
		$company_street = $company["street"];
		$company_address = $company["city"].", ".$company["state"]." ".$company["zip"];
		if(isset($company['phone'])) {
			$company_phone = $company['phone'];
		}

		$case_agent = $case["author"];

		$company_image = $company["image"];

		$client_phone = "";
		$client_name = $client["name"];
		$client_street = $client["street"];
		$client_address = $client["city"].", ".$client["state"]." ".$client["zip"];
		if(isset($client['phone'])) {
			$client_phone = $client['phone'];
		}

		$case_name = $case["name"];
		$case_created = $case["created"];
		$date_created = date('m/d/Y', strtotime($case_created));

		$width = $fpdf->GetPageWidth()-20;

		// company
		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,$company_name,0,2,'C',FALSE);
		$fpdf->Cell($width,5,$company_street,0,2,'C',FALSE);
		$fpdf->Cell($width,5,$company_address,0,2,'C',FALSE);
		$fpdf->Cell($width,5,$company_phone,0,2,'C',FALSE);

		// agent
		$fpdf->SetY(30);
		$fpdf->SetFont('Arial','B',11);
		$fpdf->Cell($width,5,'Agent: '.$case_agent,0,2,'C',FALSE);

		// image
		$c_image = base_url('img/company').'/'.$company_image;
		if(strpos($company_image, '.jpg') !== false){
			$fpdf->Image($c_image,60,45,90,0,'JPG');
		} else {
			$fpdf->Image($c_image,60,45,90,0,'PNG');
		}

		// client
		$fpdf->SetY(150);
		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,$client_name,0,2,'C',FALSE);
		$fpdf->Cell($width,5,$client_street,0,2,'C',FALSE);
		$fpdf->Cell($width,5,$client_address,0,2,'C',FALSE);
		$fpdf->Cell($width,5,$client_phone,0,2,'C',FALSE);

		// case
		$fpdf->SetY(175);
		$fpdf->SetFont('Arial','B',12);
		$fpdf->Cell($width,5,$case_name,0,2,'C',FALSE);
		$fpdf->Cell($width,5,'Engagment Date: '.$date_created,0,2,'C',FALSE);

		$fpdf->SetFont('Arial','B',9);
		$fpdf->SetXY(190, 270);
		// Now we display our page number using the Cell function.
		$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
	}

	private function build_supporting_page(&$fpdf,$supporting)
	{
		$fpdf->AddPage();

		$width = $fpdf->GetPageWidth();

		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,'Supporting:',0,2,'L',FALSE);

		$fpdf->SetY(20);

		if(count($supporting) > 0) {
			$fpdf->SetFont('Arial','',10);

			foreach ($supporting as $sup) {
				
				$sup_name = $sup['name'];
				$sup_title = $sup['title'];
				$sup_street = $sup['street'];
				$sup_city = $sup['city'];
				$sup_state = $sup['state'];
				$sup_zip = $sup['zip'];
				$sup_role = $sup['profession'];
				$sup_email = $sup['email'];
				$sup_phone = $sup['phone'];

				$sup_addr = $sup_city.", ".$sup_state." ".$sup_zip;

				$fpdf->SetFont('Arial','B',10);
				$fpdf->Cell($width,5,$sup_role,0,2,'L',FALSE);
				$fpdf->SetFont('Arial','',10);
				$fpdf->Cell($width,5,$sup_name." ".$sup_title,0,2,'L',FALSE);
				$fpdf->Cell($width,5,$sup_street,0,2,'L',FALSE);
				$fpdf->Cell($width,5,$sup_addr,0,2,'L',FALSE);
				$fpdf->Cell($width,5,$sup_email,0,2,'L',FALSE);
				$fpdf->Cell($width,7,$sup_phone,0,2,'L',FALSE);
				$fpdf->Cell($width,5,"",0,2,'L',FALSE); // space between
			}
		}

		$fpdf->SetFont('Arial','B',9);
		$fpdf->SetXY(190, 270);
		// Now we display our page number using the Cell function.
		$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
	}

	private function build_synopsis_page(&$fpdf,$case)
	{
		$fpdf->AddPage();

		$width = $fpdf->GetPageWidth();

		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,'Synopsis:',0,2,'L',FALSE);

		$contents = "";
		$name = "";

		//$synopsis = $case["synopsis"];
		//if(isset($synopsis["contents"])){
		//	$contents = $synopsis["contents"];
		//	$name = $synopsis["name"];
		//}
		
		$fpdf->SetFont('Arial','',11);

		$synopsis = $case["synopsis"];

		if (isset($synopsis["synopsis_text"]) > 0) {
			$fpdf->SetY(20);
			$fpdf->MultiCell($width,4,$synopsis["synopsis_text"],0,'L', FALSE);
		}

		// TODO: Format Synopsis
		//$breaks = array("<br />","<br>","<br/>");
    	//$text = str_ireplace($breaks, "\r\n", $contents);
		//$contents = strip_tags($text);

		/*
		if (strlen($contents) > 0) {
			$fpdf->SetY(20);
			$fpdf->MultiCell($width,4,$contents,0,'L', FALSE);
		} else if (strlen($name) > 0) {

			// build link
			$url = $synopsis["location"];
            $fpdf->Cell($width,5,'Attached File: '.$name,0,2,'L',FALSE,$url);
		}
		*/

		$fpdf->SetFont('Arial','B',9);
		$fpdf->SetXY(190, 270);
		// Now we display our page number using the Cell function.
		$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
	}

	private function build_index_page(&$fpdf,$documents,$interviews,$attachments)
	{
		$fpdf->AddPage();
		
		$width = $fpdf->GetPageWidth();

		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,'Index:',0,2,'L',FALSE);

		$fpdf->SetFont('Arial','B',10);
		$fpdf->SetY(20);

		if(count($interviews) > 0) {
			$fpdf->SetFont('Arial','B',10);
			$fpdf->Cell($width,10,"Interviews:",0,2,'c',FALSE);
			foreach ($interviews as $doc) {
				$fpdf->SetFont('Arial','',10);
				$doc_name = $doc["name"];
				$date_created = date('d/m/Y', strtotime($doc["date_occured"]));
				$fpdf->Cell($width,5,$doc_name." [".$date_created."]",0,2,'L',FALSE);
			}
		}

		/*
		if(count($documents) > 0) {
			$fpdf->Cell($width,10,"Documents:",0,2,'c',FALSE);
			foreach ($documents as $doc) {
				$doc_name = $doc["name"];
				$mime = $doc["mime"];
				$url = $doc["location"];
				$fpdf->Cell($width,5,$doc_name." [".$mime."]",0,2,'L',FALSE,$url);
			}
		}
		*/

		if(count($attachments) > 0) {
			$fpdf->SetFont('Arial','B',10);
			$fpdf->Cell($width,10,"Attachments:",0,2,'c',FALSE);
			foreach ($attachments as $att) {
				$fpdf->SetFont('Arial','',10);
				$doc_name = $att['attachment_name'];
				$id = $att['id'];
				$url = $att["location"];
				$fpdf->Cell($width,5,$doc_name." [".$id."]",0,2,'L',FALSE,$url);
			}
		}

		$fpdf->SetFont('Arial','B',9);
		$fpdf->SetXY(190, 270);
		// Now we display our page number using the Cell function.
		$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
	}

	private function build_interview_page(&$fpdf,$name,$agent,$date,$desc,$title,$emp,$street,$city,$state,$zip,$dob,$notes,$location,$attachments)
	{
		$fpdf->AddPage();
		
		$width = $fpdf->GetPageWidth();

		$fpdf->SetFont('Arial','B',14);
		$fpdf->Cell($width,5,'Interview:',0,2,'L',FALSE);

		$fpdf->SetFont('Arial','',11);
		$fpdf->SetY(20);

		$address = "";
		if(strlen($street) > 0){
			$address = $street." ".$city.", ".$state." ".$zip;
		}

		$fpdf->Cell($width,5,"Interviewee: ".$name,0,2,'L',FALSE);

		//Warning Docs

		$fpdf->Cell($width,5,"DOB: ".$dob,0,2,'L',FALSE);
		$fpdf->Cell($width,5,"Title: ".$title,0,2,'L',FALSE);
		$fpdf->Cell($width,5,"Employer: ".$emp,0,2,'L',FALSE);
		$fpdf->Cell($width,5,"Address: ".$address,0,2,'L',FALSE);

		$fpdf->Line(10, 50, $width-10, 50);

		$fpdf->SetY(53);
		$fpdf->Cell($width,5,"Agent: ".$agent,0,2,'L',FALSE);
		$fpdf->Cell($width,5,"Date: ".$date,0,2,'L',FALSE);
		$fpdf->Cell($width,5,"Location: ".$location,0,2,'L',FALSE);

		$fpdf->Line(10, 71, $width-10, 71);

		//Format Notes
		//$breaks = array("<br />","<br>","<br/>");
    	//$text = str_ireplace($breaks, "\r\n", $notes);
		//$contents = strip_tags($text);

		$contents = "";

		/*
		if(isset($notes) && strlen($notes) > 0) {
			$note_json = json_decode($notes);
			//var_dump($note_json->ops[0]->insert);
			if(isset($note_json->ops) && count($note_json) > 0) {
				$contents = $note_json->ops[0]->insert;
			}
		}
		*/

		$fpdf->SetFont('Arial','',10);
		$fpdf->SetY(74);
		$fpdf->MultiCell($width-20,4,$notes,0,'L', FALSE);

		if(count($attachments) > 0) {
			$fpdf->Cell($width,5,"",0,2,'L',FALSE); // space
			$fpdf->SetFont('Arial','B',12);
			$fpdf->Cell($width,10,"Included Files:",0,2,'c',FALSE);
			$fpdf->SetFont('Arial','',12);
			foreach ($attachments as $att) {
				$doc_name = $att['attachment_name'];
				$url = $att["location"];
				$fpdf->Cell($width,5,$doc_name,0,2,'L',FALSE,$url);
			}
		}

		$fpdf->SetFont('Arial','B',9);
		$fpdf->SetXY(190, 270);
		// Now we display our page number using the Cell function.
		$fpdf->Cell($width, 4, 'Page ' . $fpdf->PageNo(), 0, 1);
	}

}