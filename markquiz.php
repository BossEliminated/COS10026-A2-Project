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
function get_post_values($post_value_ids){ // Super Broken
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
		array_push($input_array, $input_value ?? 'fallback'); // Useing Null Coalescing Operator + Broken NULL check needs fixing
	}
	show_error_debug($error_messages);
	return $input_array;
}

function marking($post_values_array, $answers){ //Inputs must be pre sanitised & type checked
	$results = [];
	foreach ($post_values_array as $key => $value) {
		if (!is_array($value)){
			if ($value == $answers[$key]) {
				array_push($results, [1, $value]);
			} else {
				array_push($results, [0, $value]);
			}
		} else {
			$correctness_and_input_array = [];
			foreach ($value as $v2) {
				if (in_array($v2, $answers[$key], true)) {
					array_push($correctness_and_input_array, [1, $v2]);
				} else {
					array_push($correctness_and_input_array, [0, $v2]);
				}
			}
			$count_correct = 0;
			foreach ($correctness_and_input_array as $v2) {
				if ($v2[0] == 1) {
					$count_correct++;
				}
			}
			if ($count_correct == count($answers[$key]) && count($correctness_and_input_array) == count($answers[$key])) {
				$correct = 1;
			} else {
				$correct = 0;
			}
			array_push($results, [$correct, $correctness_and_input_array]);
		}
	}
	return $results;
}

function fat_dump($results) {
	print_r($results);
	print("<br><br>");

	foreach ($results as $value) {
		foreach ($value as $v2) {
			if (!is_array($v2)) {
				print($v2);
			} else {
				foreach ($v2 as $v3) {
					print_r($v3);
				}
			}
		}
		print("<br>");
	}

	// // Temporary Null Ternary Operator error warning
	// $input_value != 'fallback' ?: print "<h2 style='display: inline;'>Error Missing Value </h2><p style='display: inline;'>".$post_value_ids[$i]."</p><br><br>";

}

function save($a, $b){
	$conn = mysqli_connect("localhost", "root", "", "a_patchy_team");
	$sql = "INSERT INTO `attempts`(`first_name`, `last_name`, `student_number`, `attempt`, `score`) VALUES ('$a[0]','$a[1]','$a[2]','','')";

	if (mysqli_query($conn, $sql)) {
	  echo "New record created successfully";
	} else {
	  // echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
}

$post_id_inputs = ['given_name','family_name','ID'];
$post_question_inputs = ['quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$answers = ['slowloris',['process-based_mode','hybrid_mode'],['bob','sky'],'2004','1994'];

$post_questions_values_array = get_post_values($post_question_inputs);
$post_ids_values_array = get_post_values($post_id_inputs);
$results = marking($post_questions_values_array, $answers); // Input arrays must be same size will ad in check later

save($post_ids_values_array, $results);

// fat_dump($results);

?>
