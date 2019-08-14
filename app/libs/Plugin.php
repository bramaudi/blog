<?php

class Plugin
{
	function slugify($string)
	{
		$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
		return strtolower($slug);
	}
	

	function redir($url) {
		return header('location: '.$url);
	}
	

	function formatTime($time, $format = 'd-m-Y H:i:s')
	{
		date_default_timezone_set('Asia/Jakarta');
		$var = strtotime($time) + (60*60*7);
		return gmdate($format, $var);
	}


	function summarize($text, $maxchar, $end = '...') {
		if (strlen($text) > $maxchar || $text == '') {
			$words = preg_split('/\s/', $text);
			$output = '';
			$i = 0;
			while (1) {
				$length = strlen($output) + strlen($words[$i]);
				if ($length > $maxchar) {
					break;
				} else {
					$output .= ' '. $words[$i];
					++$i;
				}
			}
			$output .= $end;
		}
		else {
			$output = $text;
		}
		return $output;
	}


	public function online($time)
	{
		// 2 minutes expired
		return $time > time()-(60*2) ? true : false;
	}
}