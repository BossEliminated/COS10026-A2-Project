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

function validate_accordingly($inputvalue, $validation_preference, $validation_index) {
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
function create_post_values_array($post_value_ids, $validation_preference){
	$error_messages = [];
	$input_array = [];
	foreach ($post_value_ids as $i => $value) {
		$base_value = ($_POST[$post_value_ids[$i]]);
		if (gettype($base_value) != "array") { // If not array, do basic pass.
			$input_value = sanitise_inputs($base_value); // Sanitise values to prevent code injection.
			$validation_output = (validate_accordingly($base_value, $validation_preference, $i)); // Validate accordingly
			if ($validation_output != "no_error") { // If error, add to error array.
				$format_error = ("$post_value_ids[$i] :" . $validation_output);
				array_push($error_messages, $format_error);
			}
		}
		else {
			$input_value = deconstruct_array_sanitisation($base_value); // Sanitise an array
		}
		array_push($input_array, $input_value ?? 'fallback'); // Useing Null Coalescing Operator
		$input_value != 'fallback' ?: print "<h2 style='display: inline;'>Error Missing Value </h2><p style='display: inline;'>".$post_value_ids[$i]."</p><br><br>"; // Temporary Ternary Operator error warning, I will add data checks later

		// // Non Null Coalescing Operator Version
		// if (isset($_POST[$post_value_ids[$i]])) {
		// 	array_push($input_array, $_POST[$post_value_ids[$i]]);
		// } else {
		// 	array_push($input_array, 0);
		// 	print "<h2 style='display: inline;'>Error Missing Value </h2><p style='display: inline;'>".$post_value_ids[$i]."</p><br><br>";
		// }
	}
	show_error_debug($error_messages);
	return $input_array;
}




function check_post_values_array($post_values_array, $answers, $post_value_ids){
	$exclude = 3;
	$i = $exclude;
	$count = count($post_values_array);
	
	for ($u=0;$u < $exclude;$u++) {
		if ($post_values_array[$u] == "NOT_ENTERED") {
			echo "<p>$post_value_ids[$u]: INCORRECT_INPUT</p>";
		}
		else {
			echo "<p>$post_value_ids[$u]: $post_values_array[$u]</p>"; // Basic Display
		}
	}

	while($i < $count) {
		if (!is_array($post_values_array[$i])){
			if ($post_values_array[$i] == $answers[$i-$exclude]) {
				print "Correct: ".$post_values_array[$i];
			} else {
				print "Wrong: ".$post_values_array[$i];
			}
		} else {
			$result = array_diff($post_values_array[$i], $answers[$i-$exclude]);
			if (count($result) == 0 && count($post_values_array[$i]) == count($answers[$i-$exclude])) {
				print "Correct: " . implode(", ", $post_values_array[$i]);
			} else {
				print "Wrong: " . implode(", ", $post_values_array[$i]);
			}
		}
		echo "<br>";
	  $i++;
	}
}

$post_value_ids = ['ID','given_name','family_name','quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$validation_preference = ['number_only', 'character_only'];
$answers = ['slowloris',['process-based_mode','hybrid_mode'],'','2004','1994'];

$post_values_array = create_post_values_array($post_value_ids, $validation_preference);
check_post_values_array($post_values_array, $answers, $post_value_ids);

?>
