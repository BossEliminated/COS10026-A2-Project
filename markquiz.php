<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="authors" content="Nicholas, Syed, Christopher, Yash">
	<meta name="keywords" content="Verifing the server">
	<meta name="description" content="Marking the quiz on the server side allowing to sending to the database">
</head>
<body>
<?php

# Sanitisation Function
function sanitise_inputs(input) {
	input = trim(input);
	input = htmlspecialchars(input);
	return input;
}



$_POST["name"]

# Variables defined to intake from quiz.html
$id = $_POST["ID"];
$first_name = $_POST["g_name"];
$last_name = $_POST["family_name"];
$radio_question_one = $_POST["opt"];
# Checkbox set of variables
$checkbox_question_two_process = $_POST["C1"];
$checkbox_question_two_hybrid = $_POST["C2"];
$checkbox_question_two_event = $_POST["C3"];
$checkbox_question_two_professional_wrong = $_POST["C4"];
$checkbox_question_two_key_wrong = $_POST["C5"];
# Checkbox end of variables
$text_question_three = $_POST["answer3"];
$dropdown_question_four = $_POST["year"];
$dropdown_question_five = $_POST["year_used"];
# Variable intake end.

#Sanitisation
$id = sanitise_inputs($id);
$first_name = sanitise_inputs($first_name);
$last_name = sanitise_inputs($last_name);
$radio_question_one = sanitise_inputs($radio_question_one);
$checkbox_question_two_process = sanitise_inputs($checkbox_question_two_process);
$checkbox_question_two_hybrid = sanitise_inputs($checkbox_question_two_hybrid);
$checkbox_question_two_event = sanitise_inputs($checkbox_question_two_event);
$checkbox_question_two_professional_wrong = sanitise_inputs($checkbox_question_two_professional_wrong);
$checkbox_question_two_key_wrong = sanitise_inputs($checkbox_question_two_key_wrong);
$text_question_three = sanitise_inputs($answer3);
$dropdown_question_four = sanitise_inputs($year);
$dropdown_question_five = sanitise_inputs($year_used);

#Santisation End

# Verify Inputs
if isset($id) {
	if preg_match("^(\d{1,10})$", $id) {
	## If digits, range from 1 to 10.
	}
}




?>

</body>
</html>
