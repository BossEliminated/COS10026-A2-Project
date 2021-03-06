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

function fallback_count($array) {
	$count = 0;
	foreach ($array as $value) {
		if ($value == 'fallback') {
			$count++;
		}
	}
	return $count;
}

function id_data_validation($post_id_values_array, $post_id_inputs){
	// Check arrays are the same length
	if (count($post_id_values_array) == count($post_id_inputs)) {
		$name_char_issues = false;
		$name_length_issues = false;
		$names_number_issues = false;
		$names_empty_issues = false;
		foreach ($post_id_values_array as $key => $value) {
			$error = 0;
			if ($post_id_inputs[$key] == "ID") {
				if (empty($value)) { // Empty check
					$error = 1;
					print "<p class='id-validation'>ID field empty</p>";
				} elseif (!is_numeric($value)) { // Check value is a number
					$error = 1;
					print "<p class='id-validation'>ID not a number</p>";
				}
				if (strlen($value) < 7 or strlen($value) > 10) { // Check number not between 7 to 10
					$error = 1;
					print "<p class='id-validation'>ID must be between 7 to 10</p>";
				}
			} elseif ($post_id_inputs[$key] == "given_name" or $post_id_inputs[$key] == "family_name") {

				// Empty check
				if (empty($value)) {
					if ($names_empty_issues == false) {
						$error = 1;
						$names_empty_issues = "<p class='id-validation'>$post_id_inputs[$key]";
					} else {
						$error = 1;
						$names_empty_issues .= " and $post_id_inputs[$key]";
					}
				}

				// Check number is less then 30
				if (strlen($value) > 30) {
					if ($name_length_issues == false) {
						$error = 1;
						$name_length_issues = "<p class='id-validation'>$post_id_inputs[$key]";
					} else {
						$error = 1;
						$name_length_issues .= " and $post_id_inputs[$key]";
					}
				}

				// Check names have no numbers
				if (preg_match("/\d/", $value)) {
					if ($names_number_issues == false) {
						$error = 1;
						$names_number_issues = "<p class='id-validation'>$post_id_inputs[$key]";
					} else {
						$error = 1;
						$names_number_issues .= " and $post_id_inputs[$key]";
					}
				}

				// Check names for hyphen or space
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
			// If there is an error set the input value to fallback
			if ($error == 1) { $post_id_values_array[$key] = 'fallback'; }
		}

		// Print out recorded issues for both first or last name
		if ($names_empty_issues) {
			print $names_empty_issues." field empty</p>";
		}
		if ($name_char_issues) {
			print "<p class='id-validation'>Spaces or hyphens are not allowed in your ".$name_char_issues."</P>";
		}
		if ($names_number_issues) {
			print $names_number_issues." cannot include numbers</p>";
		}
		if ($name_length_issues) {
			print $name_length_issues." must be 30 or less characters</p>";
		}

	} else {
	 print "<h2>Error: ID data validation invalid array length</h2>";
	}
	return $post_id_values_array;
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
		// Check if student number exists
		$sql = "SELECT COUNT(*) FROM id WHERE student_number = '$id[0]'";
		$student_number_exists = $conn->query($sql)->fetch_assoc()["COUNT(*)"];

		// Create User
		if ($student_number_exists == 0) {
			$unique_id = rand().rand();
			$sql = "INSERT INTO `id`(`unique_id`, `first_name`, `last_name`, `student_number`) VALUES ('$unique_id', '$id[1]', '$id[2]', '$id[0]')";
			$conn->query($sql);
			$sql = "INSERT INTO `attempts`(`unique_id`, `attempt`, `score`) VALUES ('$unique_id', '1', '$score')";
			$conn->query($sql);

			print("<h2>Score submitted</h2>");
		}

		// For existing user update attempt details
		if ($student_number_exists >= 1) {
			// Check first and last name match the student ID
			$sql = "SELECT COUNT(*) FROM id WHERE first_name = '$id[1]' AND last_name = '$id[2]' AND student_number = '$id[0]'";
			$name_check = $conn->query($sql)->fetch_assoc()["COUNT(*)"];

			if ($name_check) {
				// Get unique_id for attempts table
				$sql = "SELECT `unique_id` FROM `id` WHERE first_name = '$id[1]' AND last_name = '$id[2]' AND student_number = '$id[0]'";
				$unique_id = $conn->query($sql)->fetch_array()[0];
				// Get number of attempts
				$sql = "SELECT `attempt` FROM `attempts` WHERE `unique_id` = '$unique_id'";
				$attempts = $conn->query($sql)->fetch_assoc()["attempt"];
				// Update attempts
				if ($attempts < 2) {
					$sql_update_attempts = "UPDATE `attempts` SET `attempt` ='".($attempts+1)."', `score_2` = '$score' WHERE `unique_id` = '$unique_id'";
					$conn->query($sql_update_attempts);
					print("<h2>Second attempt submitted</h2>");
				} else {
					print ("<h2>Maximum Attempts Reached</h2>");
				}
			} else {
				print("<h2>The student ID belongs to another person</h2>");
			}
		}
		$conn->close();
	}
}

function print_wrong_answers($results) {
	$incorrect = 0;
	foreach ($results as $key => $value) {
		if($value[0] == 0) {
			$incorrect += 1;
		}
	}

	if ($incorrect > 0) {
		print "<br />";
		print "<h2>Incorrect:</h2>";
	}

	foreach ($results as $key => $value) {
		if($value[0] == 0) {
			if (!is_array($value[1])) {
				print "<p>Q".($key+1).") ";
				if ($value[1] == 'fallback') {
					print("No input</p>");
				} else {
					print($value[1]."</p>");
				}
			} else {
				$multi_choice = "";
				$count_wrong = 0; // Used to work out when to print commas or and.
				foreach ($value[1] as $v2) {
					if($v2[0] == 0) {
						$count_wrong += 1;
					}
				}
				$i = 1;
				foreach ($value[1] as $v2) {
					// $missing_correct = 0;
					if($v2[0] == 0) {
						$multi_choice .= $v2[1];
						if ($i < $count_wrong-1) {
							$multi_choice .= ", ";
						} elseif ($i == $count_wrong-1) {
							$multi_choice .= " and ";
						}
						$i += 1;
					}
				}
				if ($count_wrong == 0) { // Missing correct value check
					$multi_choice .= "Missing an answer";
				}
				print "<p>Q".($key+1).") ".$multi_choice;
			}
		}
	}
}

function print_attempts($validated_post_id_values_array) {
	$conn = db_connect();
	if ($conn == true) {
		$sql = "SELECT `unique_id` FROM `id` WHERE first_name = '$validated_post_id_values_array[1]' AND last_name = '$validated_post_id_values_array[2]' AND student_number = '$validated_post_id_values_array[0]'";
		$unique_id = $conn->query($sql)->fetch_array();
		if ($unique_id != NULL) {
			$sql = "SELECT `attempt` FROM `attempts` WHERE `unique_id` = '$unique_id[0]'";
			print "<p>Attempts: ".$conn->query($sql)->fetch_assoc()["attempt"]."</p>";
		}
		$conn->close();
	}
}

$post_id_inputs = ['ID','given_name','family_name'];
$post_question_inputs = ['quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$answers = ['NGINX',['Windows','Linux', 'MacOS'],['Ipv6','SSL_Secure_Server', 'Root_SSH_Access'],'1995','1996'];
$post_id_values_array = get_post_values($post_id_inputs);

if (!fallback_count($post_id_values_array) == count($post_id_values_array)) {
	print ('<div id="results" class="quiz-content quiz-results">');
	$validated_post_id_values_array = id_data_validation($post_id_values_array, $post_id_inputs);
	$post_questions_values_array = get_post_values($post_question_inputs); // Read Questions Values
	$results = marking($post_questions_values_array, $answers); // Calulate results - Input arrays must be same size

	if (!fallback_count($validated_post_id_values_array) > 0) {
		$score = score($results);
		if ($score == 0) {
			print ("<h2>You scored 0, try again</h2>");
		} else {
			save_db_data($validated_post_id_values_array, $score);
			print "<p>ID: $validated_post_id_values_array[0]</p>";
			print "<p>Name: $validated_post_id_values_array[1] $validated_post_id_values_array[2]</p>";
			print_attempts($validated_post_id_values_array);
			print "<p>Score: ".$score."/5</p>";
		}
		print_wrong_answers($results);
	}
	print ('</div>');
}

?>
