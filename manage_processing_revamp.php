<?php
include 'db_connect.php';
include 'sanitise_framework.php';
$notsearched = true;
$attemptstable = "id";
$scoring_table = "attempts";
$filter_fields = ["student_id", "student_name", "mark_filter"];
//$attempts_filter = ["id=id", "score=5 AND attempt=1", "score<=3 AND attempt=2"];
$id_refer = ["student_number", "first_nameORlast_name"];

// Correlate radio with filter.
$index_of_radio_buttons = 2;
$second_query_produced = "";

function get_recent_click() {
  if (isset($_POST["action"])) {
	$action_val = sanitise_input($_POST["action"]);
    if (is_numeric($action_val)) {
      $_SESSION["prev_page"] = $action_val;
      return($action_val);
    }
  }	elseif (isset($_SESSION["prev_page"])) {
		if (isset($_SESSION["prev_page"])) {
		return($_SESSION["prev_page"]);
	  }
  }
  return false;
}


function sanitise_and_combine($post_value) {
	if (isset($_POST[$post_value])) {
		if (($_POST[$post_value]) != "") {
		$sanitised_output = sanitise_input($_POST[$post_value]);
		return ($sanitised_output);
		}
		return false;
	}
	else {
		return false;
	}
}


function query_build($filter_fields, $modifier_bool) {
	// Initialize to prevent errors.
	$student_id_input = false;
	$student_name_input = false;
	$mark_filter_selected = false;
	$modifier_id = false;
	$search_via_attempt = false;
	
	$base_query = "";
	if ($modifier_bool == false) {
		$student_id_input = sanitise_and_combine($filter_fields[0]);
		$student_name_input = sanitise_and_combine($filter_fields[1]);
		$mark_filter_selected = sanitise_and_combine($filter_fields[2]);
	}
	else {
		$modifier_id = sanitise_and_combine("manual_change_id");
		$search_via_attempt = sanitise_and_combine("attempt_search");
	}
	//
	if ($student_id_input) {
		$base_query = ($base_query . " AND " . "student_number='$student_id_input'");
	}
	if ($student_name_input) {
		echo"<p>hi</p>";
		$base_query = ($base_query . " AND " . "(first_name='$student_name_input' OR last_name='$student_name_input')");
	}
	echo"<p>$mark_filter_selected</p>";
	if ($mark_filter_selected != 0 and $mark_filter_selected) {
		echo"<p>hi</p>";
		if ($mark_filter_selected == 1) {
			$base_query = ($base_query . " AND " . "(score=5 AND attempt=1)");
		}
		elseif ($mark_filter_selected == 2) {
			$base_query = ($base_query . " AND " . "(score < 3 AND attempt=2)");
		}
		else {
			echo"<p>Error detected in mark fltering selection</p>";
		}
	}
	// Specific Search Request
	if ($modifier_id) {
		$base_query = ($base_query . " AND " . "student_number='$modifier_id'");
	}
	if ($search_via_attempt) {
		$base_query = ($base_query . " AND " . "attempt='$search_via_attempt'");
	}
	
	
	
	
	//
		
	return $base_query;
}

// --------------- Login / Log out Section ------------------------

// Login DB Check
$sql = "CREATE TABLE IF NOT EXISTS login (login_id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(30), password VARCHAR(30));";
$conn = db_connect();
if ($conn) {
  mysqli_query($conn, $sql);
	mysqli_close($conn);
}

// Login message - Must be before set
if (isset($_SESSION["login_msg"])) {
  if ($_SESSION["login_msg"]) {
    $_SESSION["login_msg"] = false;
    print "<h2 class='log_in_notif'>Logged in as, ".$_SESSION["username"]."</h2>";
  }
}

if (get_recent_click() == 5) {
  // Login Validation
  if (isset($_POST["username"]) && isset($_POST["password"])) {
    if (!$_POST["username"] == "" && !$_POST["password"] == "") {
      $username = sanitise_input($_POST["username"]);
      $password = sanitise_input($_POST["password"]);
    } else {
      echo"<h2 class='fail_log'>Username/Password input left blank!</h2>";
    }
  } else {
    echo"<h2 class='fail_log'>Username/Password input left blank!</h2>";
  }

  // Login
  if (isset($username) && isset($password)) {
    if (attempt_log_in($username, $password)) {
      $logged_in = true;
      $_SESSION["username"] = $username;
      $_SESSION["password"] = $password;
      $_SESSION["login_msg"] = true;
      unset($_SESSION["prev_page"]);
      unset($_POST['action']);
      header("refresh:0");
    } else {
      echo"<h2 class='fail_log'>Failed to log in, username or password is incorrect</h2>";
    }
  }
}

// Check login details against database
function attempt_log_in($username, $password) {
	$conn = db_connect();
	$sql = "SELECT COUNT(*) FROM `login` WHERE `username` = '$username' AND `password` = '$password'";
	return mysqli_fetch_array(mysqli_query($conn, $sql))['COUNT(*)'];
}

// Check Login
if (isset($_SESSION["username"])) {
  $logged_in = true;
} else {
  $logged_in = false;
}

// Log out message
if (isset($_SESSION["logout_msg"])) {
  if ($_SESSION["logout_msg"]) {
    $_SESSION["logout_msg"] = false;
    print "<h2 class='log_in_notif'>You've been logged out.</h2>";
  }
}

// Log out message - Must be before set
if (isset($_SESSION["logout_msg"])) {
  if ($_SESSION["logout_msg"]) {
    $_SESSION["logout_msg"] = false;
    print "<h2 class='log_in_notif'>You've been logged out.</h2>";
  }
}

// Log out
if (get_recent_click() == 6) {
  unset($_SESSION['username']);
  unset($_SESSION['password']);
  $_SESSION["logout_msg"] = true;
  unset($_SESSION["prev_page"]);
  unset($_POST['action']);
  header("refresh:0");
}

// ---------------- END ----------------

//Input fields
function load_filter_inputs($logged_in) {
	if ($logged_in == true) {
			  echo'<form method="post" action="manage.php">
				<label for="student_id">Student ID </label>
				<input name="student_id" id="student_id" type="text" placeholder="Student ID" />
				<label for="student_name">Student Name </label>
				<input name="student_name" id="student_name" type="text" placeholder="Name" />
				<br />
				<input type="radio" name="mark_filter" id="no_filter" value="0" />
				<label for="no_filter">No Filtering</label>
				<input type="radio" name="mark_filter" id="mark_filtering_hundred" value="1" />
				<label for="mark_filtering_hundred">Scored 100% on first Attempt</label>
				<input type="radio" name="mark_filter" id="mark_filtering_less_than" value="2"/>
				<label for="mark_filtering_less_than">Scored less than 50% on second Attempt </label>
				<br />
				<input type="submit" name="filter_all" value="Submit" />
			  </form>
			  <hr />';
	}
	else {
		echo"<section>";
		echo"</br>";
		echo"<h2>Log in, to view results</h2>";
		echo"<p>If you believe this is an error, please contact your server administrator</p>";
		echo"</section>";
	}
}

function filter_considerations($filter_fields, $attempts_filter, $index_of_radio_buttons) {
	$filter_provided_array = [];
	//print_r($filter_fields);
	$debug = count($filter_fields);
	for ($counter=0;$counter<count($filter_fields);$counter++) {
		//echo"<p>loop $counter</p>";
		if (isset($_POST[$filter_fields[$counter]])) {
				//$debug = $_POST[$filter_fields[$counter]];
				if (trim($_POST[$filter_fields[$counter]]) != "") {
					//echo"<p>$counter<p>";
					if ($counter == $index_of_radio_buttons) {
						//$result = $attempts_filter[$_POST[$filter_fields[$counter]]];
						//echo $result;
						//echo"<p> sent on </p>";
						//$debug = $filter_fields[2];
						//echo$debug;
						//echo"<p>$counter</p>";
						//echo"<p>add on</p>";
						array_push($filter_provided_array, sanitise_input($_POST[$filter_fields[$counter]]));
					}
					else {
						array_push($filter_provided_array, sanitise_input($_POST[$filter_fields[$counter]]));
					}
				}
				else {
					array_push($filter_provided_array, "NO_FILT");
				}
		}
		else {
			array_push($filter_provided_array, "NO_FILT");
		}
	}
	return $filter_provided_array;
}


function manual_change_display($mode, $page_num) {
	if ($mode == "delete") {
		$button_text = "Delete";
	}
	elseif ($mode == "modify") {
		$button_text = "Change Score";
	}
	echo"<h2>Specific Change Request</h2>";
	echo"<form method='POST' action='manage.php'>";
	echo"<label>Student ID: </label><input type='text' name='manual_change_id' placeholder='Student Id'/>";
	if ($mode == "modify") {
		echo"<label> Attempt: </label><input type='number' name='attempt_search' size='10' min='1' max='2' placeholder='Attempt'/>";
		echo"</br>";
		echo"<label> New Score: </label><input type='number' name='desired_score' min='0' max='5' size='6' placeholder='Score'/>";
	}
	echo"<button type='submit' name='modify_request' value='true'>$mode</button>";
	echo"<input type='hidden' name='action' value='$page_num'/>";
	echo"<hr>";
	echo"</form>";
}



function display_results_in_table($main_data, $mode, $page_num) {
	$rows_available = mysqli_num_rows($main_data);

	$all_fields = mysqli_fetch_fields($main_data); // This has Name
	$starter = 0;
	if ($mode == "half") {
		$starter = round($rows_available / 2);
	}
	echo"<table class='manage_table'>"; // Create Headers
	echo"<thead>";
  echo"<tr>";
  for ($t = 0; $t < count($all_fields); $t++) {
    // Secondary
    if ($t < (count($all_fields))) {
    	$local_name = $all_fields[$t]->name;
    		echo"<th>$local_name</th>";
    }
  }
  	echo"</thead>";
  if ($rows_available != 0) { // Go through rows
	  for ($i=$starter;$i<$rows_available;$i++) {
		echo"<tr>";
		$associative_return = mysqli_fetch_assoc($main_data);
		//$first_table_id = $associative_return["unique_id"];
		$found_second_id = false;
		// Compare Unique IDS // stop if hit
				for ($t = 0; $t < count($all_fields); $t++) {
					$return_data = "";
						$local_name = $all_fields[$t]->name;
						$return_data = $associative_return[$local_name];
						//echo"<p>$local_name</p>";
						$temporary_student_number = $associative_return["student_number"];
						//echo"<p>$temporary_student_number</p>";
				  if (($mode == "delete" or $mode == "manage") and $t == 0 and $return_data != "") {
					  echo"<form method='POST' action='manage.php'>";
					  echo"<td><button type='submit' name='which_selected' value='$return_data'>$return_data</button>";
					  if ($mode == "manage") {
						  echo"<input type='number' placeholder='Score' name='desired_score' min='1' max='5'></input></td>";
					  }
					  echo"<input type='hidden' name='manual_change_id' value='$temporary_student_number'>";
					  echo"<input type='hidden' name='action' value='$page_num'>";
					  echo"</form>";
				  } else {
					  if ($return_data != "") {
              echo"<td class='manage_table_info'>$return_data</td>";
					  }
				  }
				}
				echo"</tr>";
	 }
  }
	else {
		echo"<h3>No results found! Filter may be too strict!</h3>";
	}
  echo"</table>";
	mysqli_free_result($main_data);
}



function modify_attempt($id_table, $query_produced, $desired_score) {
	$database = db_connect(); // Attempts table gives student number.
	//$sql_query = "UPDATE $scoring_table SET score=$score_desired WHERE $manual";
	$sql_query = "UPDATE id, attempts SET attempts.score=$desired_score WHERE id.unique_id = attempts.unique_id";
	 //$sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
   $sql_query = ($sql_query . $query_produced);
   echo"<p>final modifiy quiery $sql_query</p>";
	$attemptmodify = mysqli_query($database, $sql_query);
		if ($attemptmodify) {
			echo"<h2>Dataset successfully modified!</h2>";
		} else {
			echo"<h2>Dataset could not be modified, may not exist!</h2>";
		}
}

function delete_attempt($query_produced) {
	if ($query_produced) {
		$database = db_connect();
		//$sql_query = "DELETE FROM $attemptstable WHERE unique_id=$unique_id";
		$sql_query = "DELETE id,attempts FROM id, attempts WHERE id.unique_id = attempts.unique_id";
		$sql_query = ($sql_query . $query_produced);
		echo"<p>whole query delete: $sql_query</p>";
		$attemptdelete = mysqli_query($database, $sql_query);
		$affected_row_num = mysqli_affected_rows($database);
		if ($affected_row_num > 0) {
			echo"<h3>Data was deleted!</h2>";
		}
		else {
			echo"<h3>No data deleted, found no corresponding results!</h2>";
		}
	}
	else {
		echo"<p>Improper search</p>";
	}
}




function list_all_attempts($query_produced) {
  $sql_connection = db_connect();
  $sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
  $sql_query = ($sql_query . $query_produced);
  $returned_data = mysqli_query($sql_connection, $sql_query);
  if ($returned_data) {
    display_results_in_table($returned_data, "all", 1);
  }
  else {
    $query_failure = true;
  }
}

function list_half_attempts($query_produced) {
  $sql_connection = db_connect();
  echo"<p>$query_produced</p>";
  $sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
   $sql_query = ($sql_query . $query_produced);
  $returned_data = mysqli_query($sql_connection, $sql_query);
  if ($returned_data) {
	 if (!$query_produced) {
		display_results_in_table($returned_data, "half", 2);
	 }
	 else {
		display_results_in_table($returned_data, "all", 1);
	 }
  }
  else {
    $query_failure = true;
  }
}

function manage_score($query_produced) {
  $sql_connection = db_connect();
  $sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
   $sql_query = ($sql_query . $query_produced);
  $returned_data = mysqli_query($sql_connection, $sql_query);
  if ($returned_data) {
    display_results_in_table($returned_data, "manage", 4);
  }
  else {
    $query_failure = true;
  }
}

function delete_attempts($query_produced) {
  $sql_connection = db_connect();
  $sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
  $sql_query = ($sql_query . $query_produced);
  $returned_data = mysqli_query($sql_connection, $sql_query);
  if ($returned_data) {
    display_results_in_table($returned_data, "delete", 3);
  }
  else {
    $query_failure = true;
  }
}

// Start of Main Sequence
// Check if a deletion was prompted.

$query_produced = query_build($filter_fields, false);


if (isset($_POST["manual_change_id"]) and isset($_POST["action"])) {
	$query_secondary_produce = query_build($filter_fields, true);
	$type_of_action = sanitise_input($_POST["action"]);
	if ($type_of_action == 3) {
		delete_attempt($query_secondary_produce);
	}
	elseif ($type_of_action == 4 and isset($_POST["desired_score"])) {
		$desired_score = sanitise_input($_POST["desired_score"]);
		modify_attempt($query_secondary_produce, $desired_score);
	}
}	// A request for deletion made

// Check which page.
$Page_Accessed_Properly = get_recent_click();
if (!isset($main_page)) {
	header("location: manage.php");
}

if (get_recent_click()) {
	$_SESSION["prev_page"] = get_recent_click();
	$temp_prev_page_num = $_SESSION["prev_page"];
}

if ($logged_in == true) {
	if (get_recent_click() == 1) {
	  echo"<h2>Show all attempts page</h2>";
	  load_filter_inputs($logged_in);
	  list_all_attempts($query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 2) {
	  echo"<h2>Show half attempts page</h2>";;
	  load_filter_inputs($logged_in);
	  list_half_attempts($query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 3) {
	  echo"<h2>Delete page</h2>";
	  load_filter_inputs($logged_in);
	  manual_change_display("delete", 3);
	  delete_attempts($query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 4) {
	  echo"<h2>Modify page</h2>";
	  load_filter_inputs($logged_in);
	  manual_change_display("modify", 4);
	  manage_score($query_produced);
	  $notsearched = false;
	}
} else {
		echo"<p>$logged_in</p>";
		echo"<section>";
		echo"</br>";
		echo"<h2>Log in, to view results</h2>";
		echo"<p>If you believe this is an error, please contact your server administrator</p>";
		echo"</section>";
}

if ($notsearched == true and $logged_in == true) {
  echo"<h2>No search has occured</h2>";
}

function debug_check() {
  if (!isset($_POST) or !count($_POST)) {
    print "<h2>No post set</h2>";
  } else {
    foreach ($_POST as $param_name => $param_val) {
      print "<p>Name: $param_name, Value: $param_val</p>";
    }
  }
}

// debug_check();

?>
