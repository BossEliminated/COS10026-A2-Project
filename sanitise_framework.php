<?php 
function sanitise_input($input) {
	$input = trim($input);
	$input = htmlspecialchars($input);
	return $input;
}




?>