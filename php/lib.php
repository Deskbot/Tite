<?php
function redirect($url) {
	if (headers_sent()) {
		echo 'Trying to redirect to ' . $url . ', but headers have already been sent.';
		
	} else {
		$newHeader = 'Location: ' . $url;
		header($newHeader);
	}
	
	die;
}

function rand_number() {
    return rand(0, pow(2,32));
}

function rand_hex($length) {
    $output = '';
    while (strlen($output) < $length) {
        $output .= dechex(rand_number());
    }
    
    return substr($output, 0, $length);
}

function password_hash($pass, $salt) {
	return hash('sha512', $pass . $salt);
}

function format_sql_date($date) {
	$dateObj = new DateTime($date);
	return $dateObj->format('l j F Y');
}

function __($str) {
	return htmlspecialchars($str);
}