<?php



function sanitise_inputs($input) {
	$input = trim($input);
	$input = htmlspecialchars($input);
	return $input;
}


function has_characters($input) {
	if (!preg_match("#^(([a-zA-Z]|-| ){1,30})$#", $input)) {  // If Character and between 1 to 50
		return false;
	}
	else {
		return true;
	}
}

function has_only_numbers($input) {
	if (!preg_match("|^([0-9]{7,10})$|", $input)) {  // If Number and 9 only
		return false;
	}
	else {
		return true;
	}
}

function validate_accordingly($inputvalue, $validation_index) {
	$validation_preference = ['number_only', 'character_only'];
	$errorfound = false;
	if ($inputvalue != "") {
		if (count($validation_preference) > $validation_index) {
			if ($validation_preference[$validation_index] == 'number_only') {
				if (has_only_numbers($inputvalue) == false) {
					$errorfound = true;
					//echo"<p>issue</p>";
					return "Input other than numbers found!";
				}
			}
			elseif ($validation_preference[$validation_index] == 'character_only') {
				if (has_characters($inputvalue) == false) {
					$errorfound = true;
					return "Input other than characters found!";
				};
			}
		}
	}
	if ($errorfound == false) {
		return "no_error";
	}
}

function deconstruct_array_sanitisation($array) {
	$sanitised_array = [];
	for ($counter=0;$counter<count($array);$counter++) {
		$sanitised_array[$counter] = sanitise_inputs($array[$counter]); // Rebuild sanitised array.
	}
	return $sanitised_array;
}


function show_error_debug($array_of_errors) {
	for ($counter=0;$counter<count($array_of_errors);$counter++) {
		echo("<h2>$array_of_errors[$counter]</h2>");
	}
}


# Get post values
function create_post_values_array($post_value_ids){
	$error_messages = [];
	$input_array = [];
	foreach ($post_value_ids as $i => $value) {
		$base_value = ($_POST[$post_value_ids[$i]]);
		if (gettype($base_value) != "array") { // If not array, do basic pass.
			$input_value = sanitise_inputs($base_value); // Sanitise values to prevent code injection.
			$validation_output = (validate_accordingly($base_value, $i)); // Validate accordingly
			if ($validation_output != "no_error") { // If error, add to error array.
				$format_error = ("$post_value_ids[$i] :" . $validation_output);
				array_push($error_messages, $format_error);
			}
		}
		else {
			$input_value = deconstruct_array_sanitisation($base_value); // Sanitise an array
		}
		array_push($input_array, $input_value ?? 'fallback'); // Useing Null Coalescing Operator
		// $input_value != 'fallback' ?: print "<h2 style='display: inline;'>Error Missing Value </h2><p style='display: inline;'>".$post_value_ids[$i]."</p><br><br>"; // Temporary Null Ternary Operator error warning
	}
	show_error_debug($error_messages);
	return $input_array;
}

function marking($post_values_array, $answers){ //Inputs must be pre sanitised & type checked
	foreach ($post_values_array as $key => $value) {
		$correct = 0;
		if (!is_array($value)){
			if ($value == $answers[$key]) {
				mark_submission(1, $value);
				// print "Correct: ".$value;
			} else {
				mark_submission(0, $value);
				// print "Wrong: ".$value;
			}
		} else {
			$result = array_diff($value, $answers[$key]);
			if (count($result) == 0 && count($value) == count($answers[$key])) {
				mark_submission(1, $value);
				// print "Correct: ".implode(", ", $value); //How is this working TF why is there no first comma
			} else {
				mark_submission(0, $value);
				// print "Wrong: ".implode(", ", $value);
			}
		}
		echo "<br>";
	}
}

function mark_submission($correct, $value) {
	if ($correct == 1) {
		print("Correct<br>");
	} else {
		print("Incorrect<br>");
	}
	// array_push($checked_items_array, $value);
}

$post_value_id = ['ID','given_name','family_name'];
$post_value_questions = ['quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$answers = ['slowloris',['process-based_mode','hybrid_mode'],'','2004','1994'];

$post_values_array = create_post_values_array($post_value_questions);
marking($post_values_array, $answers); // Input arrays must be same size will ad in check later

?>
