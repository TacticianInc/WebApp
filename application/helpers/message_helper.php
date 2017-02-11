<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// build forgot message
if (!function_exists('build_forgot_success_message')) {

        function build_forgot_success_message()
        {
                $ret_value = "";

                $new_line = chr(0x0D).chr(0x0A);

                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Your password has been changed.".$new_line;
                $ret_value = $ret_value."</p>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Please check your inbox for an important email. Then follow the instructions in the email to change your password.";
                $ret_value = $ret_value." If you have any questions, please contact support.".$new_line;
                $ret_value = $ret_value."</p>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Sincerely,<br>The Tactician Team".$new_line;
                $ret_value = $ret_value."</p>".$new_line;

                return $ret_value;
        }
}

// build success message
if (!function_exists('build_register_success_message')) {

        function build_register_success_message($cc_last_four, $cc_type_name, $total)
        {
                $ret_value = "";

                $new_line = chr(0x0D).chr(0x0A);

                $ret_value = "<h3>Thank You</h3>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Your ".$cc_type_name." ending in ".$cc_last_four." has been charged in the amount of $".$total." ".$new_line;
                $ret_value = $ret_value."</p>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Please check your inbox for an important email. Then follow the instructions in the email to complete your registration.";
                $ret_value = $ret_value." If you have any questions, please contact support.".$new_line;
                $ret_value = $ret_value."</p>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Sincerely,<br>The Tactician Team".$new_line;
                $ret_value = $ret_value."</p>".$new_line;

                return $ret_value;
        }
}

// build success message
if (!function_exists('build_register_guest_success_message')) {

        function build_register_guest_success_message()
        {
                $ret_value = "";

                $new_line = chr(0x0D).chr(0x0A);

                $ret_value = "<h3>Thank You</h3>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Please check your inbox for an important email. Then follow the instructions in the email to complete your registration.";
                $ret_value = $ret_value." If you have any questions, please contact support.".$new_line;
                $ret_value = $ret_value."</p>".$new_line;
                $ret_value = $ret_value."<p>".$new_line;
                $ret_value = $ret_value."Sincerely,<br>The Tactician Team".$new_line;
                $ret_value = $ret_value."</p>".$new_line;

                return $ret_value;
        }
}

// build registration message
if (!function_exists('build_forgot_email_message')) {

        function build_forgot_email_message($new_password)
        {
                $ret_value = array('result' => FALSE, 'message' => '');

                if (!isset($new_password) || strlen($new_password) == 0) {
                        return $ret_value;
                }

                $new_line = chr(0x0D).chr(0x0A);

                $title = "Your Tactician Account";

                $message = "Please note that your password has been changed to: ".$new_password." ".$new_line.$new_line;
                $message = $message."You can change this once you sign in at: ".site_url("")." ".$new_line;
                $message = $message."If you did not do this, please contact support. ".$new_line.$new_line;
                $message = $message."Sincerly.".$new_line;
                $message = $message."The Tactician Team";

                $ret_value['result'] = TRUE;
                $ret_value['message'] = $message;

                return $ret_value;
        }
}

// build registration message
if (!function_exists('build_register_email_message')) {

        function build_register_email_message($name, $url)
        {
                $ret_value = array('result' => FALSE, 'message' => '');

                if (!isset($name) || strlen($name) == 0) {
                        return $ret_value;
                }

                if (!isset($url) || strlen($url) == 0) {
                        return $ret_value;
                }

                $new_line = chr(0x0D).chr(0x0A);

                $title = "Your Tactician Account";

                $message = "Welcome ".trim($name).$new_line.$new_line;
                $message = $message."Thank you for registering on Tactician.com. Before you can begin, you must verify your email address.".$new_line;
                $message = $message."To do so, please click on or paste the following into a web browser: ".$url." ".$new_line.$new_line;
                $message = $message."Then just sign in as normal.".$new_line.$new_line;
                
                $message = $message.$new_line;
                $message = $message."Sincerely,".$new_line;
                $message = $message."The Tactician Team".$new_line;
                $message = $message."http://tacticianinc.com".$new_line;

                $ret_value['result'] = TRUE;
                $ret_value['message'] = $message;

                return $ret_value;
        }
}

// build guest invitation message
if (!function_exists('build_guest_invitation_email_message'))
{
    function build_guest_invitation_email_message($name, $url, $company_name) {

        $ret_value = array('result' => FALSE, 'message' => '');

                if (!isset($name) || strlen($name) == 0) {
                        return $ret_value;
                }

                if (!isset($url) || strlen($url) == 0) {
                        return $ret_value;
                }

                if (!isset($company_name) || strlen($company_name) == 0) {
                        return $ret_value;
                }

                $new_line = chr(0x0D).chr(0x0A);

                $title = "Tactician Invitation";

                $message = "Hello ".trim($name).$new_line.$new_line;
                $message = $message."You have been invited to collaborate with ".$company_name.".".$new_line;
                $message = $message."To accept, click on or paste the following into a web browser: ".$url." ".$new_line.$new_line;
                $message = $message."Then just complete the short form.".$new_line.$new_line;
                
                $message = $message.$new_line;
                $message = $message."Sincerely,".$new_line;
                $message = $message."The Tactician Team".$new_line;
                $message = $message."http://tacticianinc.com".$new_line;

                $ret_value['result'] = TRUE;
                $ret_value['message'] = $message;

                return $ret_value;
    }
}

// build guest registration message
if (!function_exists('build_guest_register_email_message'))
{
    function build_guest_register_email_message($name, $url) {

        $ret_value = array('result' => FALSE, 'message' => '');

                if (!isset($name) || strlen($name) == 0) {
                        return $ret_value;
                }

                if (!isset($url) || strlen($url) == 0) {
                        return $ret_value;
                }

                $new_line = chr(0x0D).chr(0x0A);

                $title = "Your Tactician Account";

                $message = "Welcome ".trim($name).$new_line.$new_line;
                $message = $message."Thank you for accepting an invitation to Tactician. There is just one last step.".$new_line;
                $message = $message."Please click on or paste the following into a web browser: ".$url." ".$new_line.$new_line;
                $message = $message."Then just sign in as normal.".$new_line.$new_line;
                
                $message = $message.$new_line;
                $message = $message."Sincerely,".$new_line;
                $message = $message."The Tactician Team".$new_line;
                $message = $message."http://tacticianinc.com".$new_line;

                $ret_value['result'] = TRUE;
                $ret_value['message'] = $message;

                return $ret_value;
    }
}

if (!function_exists('build_html_message'))
{
        function build_html_message($title, $message, $signature, $logo_url) {
                $ret_value = "";

                $message = replace_nr_br($message);

                $html = "<html><head><title>".$title."</title><meta http-equiv=Content-Type content=text/html; charset=UTF-8>";
                $html = $html."</head><body><table width=\"800\" border=\"0\"><tr><td><p align=\"justify\" style=\"color:#000000;\">";
                $html = $html.$message;
                $html = $html."</p></td></tr></table></body></html>";

                $html = "<html>";
                $html = $html."<head>";
                $html = $html."<title>".$title."</title>";
                $html = $html."<meta http-equiv=Content-Type content=text/html; charset=UTF-8>";
                $html = $html."</head>";
                $html = $html."<body>";
                $html = $html."<table width=\"800\" border=\"0\">";

                if (isset($logo_url) && strlen($logo_url) > 0) {
                    $html = $html."<tr>";
                    $html = $html."</td>";
                    $html = $html."<img src=\"".$logo_url."\">";
                    $html = $html."</td>";
                    $html = $html."</tr>";
                }

                $html = $html."<tr>";
                $html = $html."<td>";
                $html = $html."<p align=\"justify\" style=\"margin-top:10px;color:#000000;\">";
                $html = $html.$message;
                $html = $html."</p>";
                $html = $html."</td>";
                $html = $html."</tr>";

                if (isset($signature) && strlen($signature) > 0) {
                    $signature = replace_nr_br($signature);
                    $html = $html."<tr>";
                    $html = $html."</td>";
                    $html = $html."<p align=\"justify\" style=\"margin-top:10px;color:#333333;\">";
                    $html = $html.$signature;
                    $html = $html."</p>";
                    $html = $html."</td>";
                    $html = $html."</tr>";
                }

                $html = $html."</table>";
                $html = $html."</body>";
                $html = $html."</html>";

                return $ret_value;
        }
}

if (!function_exists('replace_nr_br'))
{
        function replace_nr_br($message)
        {
                $ret_value = str_replace("\r\n", "<br>", $message);
        }
}
