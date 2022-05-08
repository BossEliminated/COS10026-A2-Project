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
	if (preg_match("|^([0-9]{1,10})$|", $input)) {  // If Number and 1-10
		return (int)$input;
	}
	else {
		print ("Error: Number Issue");
		return 'fallback';
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

function get_post_values($post_value_ids){
	$input_array = [];
	foreach ($post_value_ids as $key => $value) {
		array_push($input_array, sanitization_and_type_check($_POST[$post_value_ids[$key]] ?? 'fallback')); // Useing Null Coalescing Operator
	}
	return $input_array;
}

function sanitization_and_type_check($input) {
	if (!is_array($input)) {
		$input = sanitization_and_type_processing($input);
	} else {
		foreach ($input as $input2) {
			$input2 = sanitization_and_type_processing($input2);
		}
	}
	return $input;
}

function sanitization_and_type_processing($input) { // Sanitization type confirmation ----------- Broken Needs Fixing
	$input = sanitise_inputs($input);
	print ($input."<br>");
	if (is_numeric($input)) {
		$input = has_only_numbers($input);
	}
	print (gettype($input)."<br>");
	return $input;
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

function debug_dump($results) {
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

function db_connect() {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "a_patchy_team";
	try {
		$conn = new mysqli($servername, $username, $password, $dbname);
	} catch(Exception $e) {
	  echo '<p>MySQLi Conneciton Error: ' .$e->getMessage().'</p>';
		return false;
	}

	// Check Table Exists
	try {
		$conn->query("SELECT 1 FROM attempts WHERE 1");
		return $conn;
	} catch(Exception $e) {
		echo '<p>MySQLi Created Table: ' .$e->getMessage().'</p>';
		$create_table = "CREATE TABLE IF NOT EXISTS attempts (
			id int(11) NOT NULL AUTO_INCREMENT,
			created datetime NOT NULL DEFAULT current_timestamp(),
			first_name varchar(255) NOT NULL,
			last_name varchar(255) NOT NULL,
			student_number int(11) NOT NULL,
			attempt int(11) NOT NULL,
			score int(11) NOT NULL,
			PRIMARY KEY (id)
		)";
		$conn->query($create_table);
		return $conn;
	}
}

function save_db_data($id, $score){
	$conn = db_connect();
	if ($conn == true) {
		// Count of recors with input details
		$sql_check = "SELECT COUNT(*) FROM attempts WHERE first_name = '$id[0]' AND last_name = '$id[1]' AND student_number = '$id[2]'";
		$check_db = $conn->query($sql_check);
		$check_db = $check_db->fetch_assoc()["COUNT(*)"];
		// print $check_db;
		if ($check_db == 1) {
			$sql_attempts = "SELECT attempt FROM attempts WHERE first_name = '$id[0]' AND last_name = '$id[1]' AND student_number = '$id[2]'";
			$attempts = $conn->query($sql_attempts);
			$attempts = $attempts->fetch_assoc()["attempt"]+1;

			$sql_update_attempts = "UPDATE attempts SET attempt ='$attempts', score = '$score' WHERE first_name = '$id[0]' AND last_name = '$id[1]' AND student_number = '$id[2]'";
			$conn->query($sql_update_attempts);
			print ("<p>Attempt: ".$attempts."</p>");
		} else {
			// Create user
			if ($check_db == false) {
				$sql_insert = "INSERT INTO `attempts`(`first_name`, `last_name`, `student_number`, `attempt`, `score`) VALUES ('$id[0]','$id[1]','$id[2]','1','$score')";
				$conn->query($sql_insert);
				print("<h1>User Added</h1>");
			}
		}

		$conn->close();
	}
}

$post_id_inputs = ['given_name','family_name','ID'];
$post_question_inputs = ['quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$answers = ['slowloris',['process-based_mode','hybrid_mode'],['bob','sky'],'2004','1994'];

$post_questions_values_array = get_post_values($post_question_inputs);
$post_ids_values_array = get_post_values($post_id_inputs);

$results = marking($post_questions_values_array, $answers); // Input arrays must be same size will ad in check later
$score = score($results);

print ("<p>Score: ".$score."</p>");
save_db_data($post_ids_values_array, $score);
debug_dump($results);

?>
