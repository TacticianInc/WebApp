<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Captcha extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    }

    /**
	 * Determines if a captcha was shown
	 * 
	 * @return boolean
	 */
    public function has_captcha()
    {
    	$ci = get_instance();
    	if ($ci->session->flashdata('word')) {
    		return TRUE;
    	}

    	return FALSE;
    }

    /**
	 * Creates a captcha
	 * 
	 * @return base64 image
	 */
	public function load_captcha()
	{
		$ret_value = '';
		$ci = get_instance();

		try {

			$sql = "SELECT word, image FROM captcha ORDER BY RAND() LIMIT 1";
			$query = $this->db->query($sql);

			if ($query !== FALSE) {
				$image = $query->result()[0]->image;
				$word = $query->result()[0]->word;

				if (isset($word) && strlen($word) > 0) {
					$ci->session->set_flashdata('word',$word);
					$ret_value = base64_encode($image);
				}
			}

			return $ret_value;

		} catch (Exception $e) {
			return $ret_value;
		}

	}

	/**
	 * Validate a captcha response
	 * 
	 * @param string $word
	 * @return boolean
	 */
	public function validate_captcha($word)
	{
		$ret_value = FALSE;
		$ci = get_instance();

		if (!isset($word)) {
			return $ret_value;
		}

		try {

			$word = trim(strtolower($word));

			$stored_word = $ci->session->flashdata('word');
			$stored_word = trim(strtolower($stored_word));

			if ($stored_word == $word) {
				$ret_value = TRUE;
			}

			return $ret_value;

		} catch (Exception $e) {
			return $ret_value;
		}

	}

}