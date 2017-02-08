<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SendMail {
    
    public function send_email_ses($message, $subject, $to)
    {
        $ret_value = FALSE;

        $url = 'http://sample-env-1.yp5ckcgnyg.us-west-2.elasticbeanstalk.com/email/send/';

        $data = array( "from"=> "brian@tacticianinc.com", "to" => $to, "subject" => $subject, "body" => $message);

        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);

        if ($response == 1) {
            $ret_value = TRUE;
        }

        return $ret_value;
    }

    /**
     * Sends an Email
     * 
     * @param string $message
     * @param string $subject
     * @param string $from_name
     * @param string $from_email
     * @param array $to
     * @param boolean $is_html
     * @param string $cc
     * @param string $bcc
     * @return boolean
     */
    public function send_email($message, $subject, $from_name, $from_email, $to, $is_html=FALSE, $cc="", $bcc="")
    {
        $ret_value = FALSE;

        if (!isset($message) || strlen($message) == 0) {
            return $ret_value;
        }

        // get headers
        $header_result = $this->build_header($subject, $from_name, $from_email, $to, $is_html, $cc, $bcc);

        if ($header_result['result'] == FALSE) {
            return $ret_value;
        }

        $headers = $header_result['header'];
        $to_list = $header_result['to'];

        // make message nice looking if is text
        if ($is_html === FALSE) {
            $message = wordwrap($message, 70, "\r\n");
        }
        
        //return mail( string $to , string $subject , string $message [, string $additional_headers [, string $additional_parameters ]] );
        return mail($to_list, trim($subject), $message, $headers);
    }

    /**
     * Builds the headers
     * 
     * @param string $subject
     * @param string $from_name
     * @param string $from_email
     * @param array $to
     * @param boolean $is_html
     * @param string $cc
     * @param string $bcc
     * @return array
     */
    private function build_header($subject, $from_name, $from_email, $to, $is_html, $cc="", $bcc="")
    {
        $ret_value = array('result' => FALSE, 'header' => '', 'to' => '');

        if (!isset($subject) || strlen($subject) == 0) {
            return ret_value;
        }

        if ((!isset($from_name) || strlen($from_name) == 0) || (!isset($from_email) || strlen($from_email) == 0)) {
            return $ret_value;
        }

        if (!isset($to) || count($to) == 0) {
            return $ret_value;
        }

        $new_line = chr(0x0D).chr(0x0A);

        $headers = "MIME-Version: 1.0".$new_line;

        if ($is_html) {
            $headers = $headers."Content-type: text/html; charset=iso-8859-1".$new_line;
        } else {
            $headers = $headers."Content-type: text/plain; charset=iso-8859-1".$new_line;
        }

        $to_list = "";
        $to_send_list = "";

        // build to list
        foreach ($to as $to_record) {
            $name = $to_record['name'];
            $email = $to_record['email'];
            $name = trim($name);
            $email = trim($email);

            $to_send_list = $to_send_list.$name.",";
            $to_list = $to_list.$name." <".$email.">,";
        }

        // remove extra comma
        $to_list = trim($to_list, ",");
        $to_send_list = trim($to_send_list, ",");

        $headers = $headers."To: ".$to_list.$new_line;
        $headers = $headers."From:".$from_name."<".$from_email.">".$new_line;
        
        if (isset($cc) && strlen($cc) > 0) {
            $headers = $headers."Cc:".$cc.$new_line;
        }
        
        if (isset($bcc) && strlen($bcc) > 0) {
            $headers = $headers."Bcc:".$bcc.$new_line;
        }

        $headers = $headers."Reply-To: ".$from_name."<".$from_email.">".$new_line;

        $headers = $headers."Date: ".date("r").$new_line;

        $headers = $headers."Subject: {".trim($subject)."}".$new_line;

        $ret_value['result'] = TRUE;
        $ret_value['header'] = $headers;
        $ret_value['to'] = $to_send_list;

        return $ret_value;
    }
}