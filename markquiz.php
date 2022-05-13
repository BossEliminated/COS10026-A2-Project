<?php

include 'db_connect.php';

function get_post_values($post_value_ids){
	$input_array = [];
	foreach ($post_value_ids as $key => $value) {
		if (isset($_POST[$post_value_ids[$key]])) {
			array_push($input_array, sanitization($_POST[$post_value_ids[$key]]));
		}	else {
			array_push($input_array, "fallback");
		}
	}
	return $input_array;
}

function sanitization($post_value) {
	if ($post_value != 'fallback') {
		if (!is_array($post_value)) {
			$post_value = sanitization_processing($post_value);
		} else {
			$replacement_array = [];
			foreach ($post_value as $post_value_2) {
				$post_value_2 = sanitization_processing($post_value_2);
				array_push($replacement_array, $post_value_2);
			}
			$post_value = $replacement_array;
		}
	}
	return $post_value;
}

function sanitization_processing($post_values) {
	$post_values = trim($post_values);
	$post_values = htmlspecialchars($post_values);
	return $post_values;
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

function score($results) {
	$score = 0;
	foreach ($results as $value) {
		if ($value[0] == 1) {
			$score++;
		}
	}
	return $score;
}

function save_db_data($id, $score){
	$conn = db_connect();
	if ($conn == true) {
		// Check if user exists
		$sql = "SELECT COUNT(*) FROM attempts WHERE first_name = '$id[0]' AND last_name = '$id[1]' AND student_number = '$id[2]'";
		$user_exists = $conn->query($sql)->fetch_assoc()["COUNT(*)"];

		// Create User
		if ($user_exists == 0) {
			$sql_insert = "INSERT INTO `attempts`(`first_name`, `last_name`, `student_number`, `attempt`, `score`) VALUES ('$id[0]','$id[1]','$id[2]','1','$score')";
			$conn->query($sql_insert);
			print("<h2>User Added</h2>");
		}

		// For existing user update attempt details
		if ($user_exists >= 1) {
			$sql = "SELECT attempt FROM attempts WHERE first_name = '$id[0]' AND last_name = '$id[1]' AND student_number = '$id[2]'";
			$attempts = $conn->query($sql)->fetch_assoc()["attempt"]+1;

			if ($attempts <= 2) {
				$sql_update_attempts = "UPDATE attempts SET attempt ='$attempts', score = '$score' WHERE first_name = '$id[0]' AND last_name = '$id[1]' AND student_number = '$id[2]'";
				$conn->query($sql_update_attempts);
			} else {
				print ("<h2>Maximum Attempts Reached</h2>");
			}
		}
		$conn->close();
	}
}

function submission_check($results){
	$count = 0;
	foreach ($results as $value) {
		if ($value == 'fallback') {
			$count++;
		}
	}
	if ($count == count($results)) {
		return false;
	} else {
		return true;
	}
}

function id_data_validation($post_id_values_array, $post_id_inputs){
	// Check arrays are the same length
	if (count($post_id_values_array) == count($post_id_inputs)) {
		$name_char_issues = false;
		$name_length_issues = false;
		foreach ($post_id_values_array as $key => $value) {
			$error = 0;
			if ($post_id_inputs[$key] == "ID") {
				if (!is_numeric($value)) { // Check value is a number
					$error = 1;
					print "<p>ID not a number</p>";
				}
				if (strlen($value) < 7 or strlen($value) > 10) {  // Check number not between 7 to 10
					$error = 1;
					print "<p>ID must be between 7 to 10</p>";
				}
			} elseif ($post_id_inputs[$key] == "given_name" or $post_id_inputs[$key] == "family_name") {
				if (strlen($value) > 30) {  // Check number not between 7 to 10
					if ($name_length_issues == false) {
						$error = 1;
						$name_length_issues = "<p>$post_id_inputs[$key]";
					} else {
						$error = 1;
						$name_length_issues .= " and $post_id_inputs[$key]";
					}
				}
				if (preg_match("/[\- ]+/", $value)) {
					if ($name_char_issues == false) {
						$error = 1;
						$name_char_issues = "$post_id_inputs[$key]";
					} else {
						$error = 1;
						$name_char_issues .= " and $post_id_inputs[$key]";
					}
				}
			}
			if ($error == 1) { $post_id_values_array[$key] = 'fallback'; }
		}
		if ($name_char_issues) {
			print "Spaces or hyphens not allowed in your ".$name_char_issues."</P>";
		}
		if ($name_length_issues) {
			print $name_length_issues." must be 30 or less characters</p>";
		}
	} else {
	 print "Error: ID data validation invalid array length";
	}
	return $post_id_values_array;
}

function quiz_data_validation($post_questions_values_array){
	// Not done yet
	return $post_questions_values_array;
}

$post_id_inputs = ['ID','given_name','family_name'];
$post_question_inputs = ['quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$answers = ['slowloris',['process-based_mode','hybrid_mode'],['bob','sky'],'2004','1994'];
$post_id_values_array = get_post_values($post_id_inputs);

if (submission_check($post_id_values_array) == true) {
	print ('<div id="results" class="quiz-content quiz-results">');
	$validated_post_id_values_array = id_data_validation($post_id_values_array, $post_id_inputs);
	$post_questions_values_array = get_post_values($post_question_inputs); // Read Questions Values
	$validated_post_questions_values_array = quiz_data_validation($post_questions_values_array);
	$results = marking($validated_post_questions_values_array, $answers); // Calulate results - Input arrays must be same size
	$score = score($results);
	save_db_data($validated_post_id_values_array, $score);
	print ("<p>Score: ".$score."/5</p>");
	print ('</div>');
}

?>
