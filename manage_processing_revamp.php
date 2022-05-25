<?php
include 'db_connect.php';
include 'sanitise_framework.php';
$notsearched = true;
$filter_fields = ["student_id", "student_name", "mark_filter"];

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
	$unique_id = false;
	$base_query = "";
	if ($modifier_bool == false) {
		$student_id_input = sanitise_and_combine($filter_fields[0]);
		$student_name_input = sanitise_and_combine($filter_fields[1]);
		$mark_filter_selected = sanitise_and_combine($filter_fields[2]);
	}
	else {
		$modifier_id = sanitise_and_combine("manual_change_id");
		$search_via_attempt = sanitise_and_combine("attempt_search");
		$unique_id = sanitise_and_combine("unique_id_search");
	}
	//
	if ($student_id_input) {
		$base_query = ($base_query . " AND " . "student_number='$student_id_input'");
	}
	if ($student_name_input) {
		$name_split_array = explode(" ", $student_name_input);
		if (count($name_split_array) > 1) {
			$first_name = $name_split_array[0];
			$second_name = $name_split_array[1];
			$base_query = ($base_query . " AND " . "(first_name LIKE '%$first_name%' AND last_name LIKE '%$second_name%')");
		}
		else {
			$base_query = ($base_query . " AND " . "(first_name LIKE '%$student_name_input%' OR last_name LIKE '%$student_name_input%')");
		}

	}
	if ($mark_filter_selected != 0 and $mark_filter_selected) {
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
	if ($modifier_id) {
		$base_query = ($base_query . " AND " . "student_number='$modifier_id'");
	}
	if ($unique_id) {
		$base_query = ($base_query . " AND " . "attempts.unique_id='$unique_id'");
		$base_query = ($base_query . " AND " . "id.unique_id='$unique_id'");
	}
	if ($search_via_attempt) {
		$base_query = ($base_query . " AND " . "attempt='$search_via_attempt'");
	}
	// echo"<h3>Debug: $base_query</h3>";
	return $base_query;
}

// --------------- Login / Log out Section ------------------------

// DB Login deafult password Check
$conn = db_connect();
if ($conn) {
	$sql = "SELECT COUNT(*) FROM `login` WHERE `username` = 'admin' AND `password` = 'pass'";
	$deafult_login_count = mysqli_fetch_array(mysqli_query($conn, $sql))[0];
	$sql = "SELECT COUNT(*) FROM `login` WHERE 1";
	$login_count = mysqli_fetch_array(mysqli_query($conn, $sql))[0];

	if (!$deafult_login_count && !$login_count) {
	  $sql = "INSERT INTO `login`(`username`, `password`) VALUES ('admin','pass')";
	  mysqli_query($conn, $sql);
	  $_SESSION["deafult_login_msg"] = true;
	  header("refresh:0");
	} elseif ($deafult_login_count && $login_count > 1) {
	  $sql = "DELETE FROM `login` WHERE `username` = 'admin' AND `password` = 'pass'";
	  mysqli_query($conn, $sql);
	  unset($_SESSION["deafult_login_msg"]);
	  header("refresh:0");
	} elseif ($deafult_login_count && $login_count && !isset($_SESSION["deafult_login_msg"])) {
	  $_SESSION["deafult_login_msg"] = true;
	  header("refresh:0");
	} elseif (isset($_SESSION["deafult_login_msg"]) && !$deafult_login_count) {
	  unset($_SESSION["deafult_login_msg"]);
	  header("refresh:0");
	}
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
      $_SESSION["prev_page"] = 1;
      unset($_POST['action']);
      header("refresh:0");
    } else {
      echo"<h2 class='fail_log'>Failed to log in, username or password is incorrect</h2>";
    }
  }
}

// Check login details against database
function attempt_log_in($username, $password) {
	$_SESSION["password_msg_change"] = false;
	$conn = db_connect();
	if ($conn) {
	$sql = "SELECT COUNT(*) FROM `login` WHERE `username` = '$username' AND `password` = '$password'";
	return mysqli_fetch_array(mysqli_query($conn, $sql))['COUNT(*)'];
	}
	else {
		return false;
	}
}


function log_out() {
  unset($_SESSION['username']);
  unset($_SESSION['password']);
  unset($_SESSION["prev_page"]);
  unset($_POST['action']);
  header("refresh:0");
  exit();
}

// Check Login
if (isset($_SESSION["username"]) and isset($_SESSION["password"])) {
  $username = sanitise_input($_SESSION["username"]);
  $password = sanitise_input($_SESSION["password"]);
  if (attempt_log_in($username, $password)) {
	    $logged_in = true;
  }
  else {
	  $_SESSION["password_msg_change"] = true;
	  log_out();
  }
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

// Password changed while logged in

if (isset($_SESSION["password_msg_change"])) {
  if ($_SESSION["password_msg_change"]) {
    print "<h2 class='fail_log'>Username or password issue, please login again.</h2>";
	$_SESSION["password_msg_change"] = false;
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
	log_out();
}

// ---------------- END ----------------

//Input fields
function load_filter_inputs($logged_in) { // Load filtering input boxes at start of page
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
	else { // Not logged in, view prompt to log in
		echo"<section>";
		echo"</br>";
		echo"<h2>Log in, to view results</h2>";
		echo"<p>If you believe this is an error, please contact your server administrator</p>";
		echo"</section>";
	}
}

function manual_change_display($mode, $page_num) { // Box for sites that require manual change.
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
		echo"<label>Unique Table ID: </label><input type='text' name='unique_id_search' placeholder='Unique Id'/>";
		echo"<label> Attempt: </label><input type='number' name='attempt_search' size='10' min='1' max='2' placeholder='Attempt'/>";
		echo"</br>";
		echo"<label> New Score: </label><input type='number' name='desired_score' min='0' max='5' size='6' placeholder='Score'/>";
	}
	echo"<button type='submit' name='modify_request' value='true'>$mode</button>";
	echo"<input type='hidden' name='action' value='$page_num'/>";
	echo"<hr>";
	echo"</form>";
}

function display_results_in_table($main_data, $mode, $page_num) { // Load all tables.
	$rows_available = mysqli_num_rows($main_data); // How many rows.
	$all_fields = mysqli_fetch_fields($main_data);
	$starter = 0;

  // For printing half results count rows and devide by two
  if ($mode == "half") {
    $rows_available = round($rows_available / 2);
  }

  if ($rows_available == 0) { // Go through rows
    echo"<h3>No results found! Filter may be too strict!</h3>";
  } else {
    // Print results table
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

    //
    for ($i=0;$i<$rows_available;$i++) {
		echo"<tr>";
		$associative_return = mysqli_fetch_assoc($main_data); // Fetch the row.
		for ($t = 0; $t < count($all_fields); $t++) {
      $return_data = "";
      $local_name = $all_fields[$t]->name;
      $return_data = $associative_return[$local_name];
      $temporary_student_number = $associative_return["student_number"]; // Store student id temporarily.
		  if (($mode == "delete" or $mode == "manage") and $t == 0 and $return_data != "") {
			  echo"<form method='POST' action='manage.php'>";
			  echo"<td><button type='submit' name='which_selected' value='$return_data'>$return_data</button>";
			  if ($mode == "manage") {
				  echo"<input type='number' placeholder='Score' name='desired_score' min='1' max='5'></input></td>";
			  }
			  echo"<input type='hidden' name='manual_change_id' value='$temporary_student_number'>"; // Send student id
			  echo"<input type='hidden' name='action' value='$page_num'>"; // Sent action type.
			  echo"</form>";
		  } else {
			  if ($return_data != "") {
          echo"<td class='manage_table_info'>$return_data</td>";
			  }
		  }
		}
		echo "</tr>";
    }
    echo"</table>";
	}
  mysqli_free_result($main_data);
}

function modify_attempt($query_produced, $desired_score) { // Attempt to modify attempt.
	$database = db_connect();
	if ($desired_score) {
		if ($query_produced)  {
			if ($database) {
			$sql_query = "UPDATE id, attempts SET attempts.score=$desired_score WHERE id.unique_id = attempts.unique_id"; // Set score.
		   $sql_query = ($sql_query . $query_produced); // Adds to WHERE query.
			$attemptmodify = mysqli_query($database, $sql_query);
			$affected = mysqli_affected_rows($database);
				if ($affected > 0) {
					echo"<h2>Dataset successfully modified!</h2>";
				} else {
					echo"<h2>Dataset could not be modified, may not exist!</h2>";
				}
			}
			else {
				  echo"<h3>Could not connect to database</h3>";
			}
		}
		else {
			echo"<h3>Required inputs not provided!</h3>";
		}
	}
	else {
		echo"<h3>Score value not provided</h3>";
	}
}

function delete_attempt($query_produced) { // Delete given info.
	if ($query_produced) {
		$database = db_connect();
		if ($database) {
		$sql_query = "DELETE id,attempts FROM id, attempts WHERE id.unique_id = attempts.unique_id";
		$sql_query = ($sql_query . $query_produced);
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
			  echo"<h3>Could not connect to database</h3>";
	}
	}
	else {
		echo"<p>Improper search</p>";
	}
}

function list_all_attempts($query_produced) { // Basic search all results.
  $sql_connection = db_connect();
  if ($sql_connection) {
	  $sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
	  $sql_query = ($sql_query . $query_produced);
	  // echo"<h3>Debug whole query: $sql_query</h3>";
	  $returned_data = mysqli_query($sql_connection, $sql_query);
	  if ($returned_data) {
		display_results_in_table($returned_data, "all", 1);
	  }
	  else {
		$query_failure = true;
	  }
  }
  else {
	  echo"<h3>Could not connect to database</h3>";
  }
}

function list_half_attempts($query_produced) { // Send to display all tables, that half search ONLY if there is no filter in place.
  $sql_connection = db_connect();
  if ($sql_connection) {
	  $sql_query = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";
	  $sql_query = ($sql_query . " -- " . $query_produced);
    // echo"<h3>Debug half query: $sql_query</h3>";
	  $returned_data = mysqli_query($sql_connection, $sql_query);
	  if ($returned_data) {
      if (!$query_produced) { // What is query_produced? XYZ
      display_results_in_table($returned_data, "half", 2);
      } else {
      display_results_in_table($returned_data, "all", 1); // Why would list_half_attempts print all attempts? XYZ
      }
	  } else {
      $query_failure = true;
	  }
  } else {
    echo"<h3>Could not connect to database</h3>";
  }
}

function manage_score($query_produced) { // Page for changing score (does not involve actual modification)
  $sql_connection = db_connect();
  if ($sql_connection) {
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
  else {
	  echo"<h3>Could not connect to database</h3>";
  }
}

function delete_attempts($query_produced) { // Page for deletion (does not involve deleting data)
  $sql_connection = db_connect();
  if ($sql_connection) {
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
  else {
	  echo"<h3>Could not connect to database</h3>";
  }
}

// Start of Main Sequence
// Check if a deletion was prompted.
$query_produced = query_build($filter_fields, false);
if (isset($_POST["manual_change_id"]) and isset($_POST["action"])) { // If a change input given, continue.
	$query_secondary_produce = query_build($filter_fields, true);
	$type_of_action = sanitise_input($_POST["action"]);
	if ($type_of_action == 3) { // If delete process given, load deletion
		delete_attempt($query_secondary_produce);
	}
	elseif ($type_of_action == 4 and isset($_POST["desired_score"])) { // If modification request and score given then modify.
		$desired_score = sanitise_input($_POST["desired_score"]);
		if ($desired_score <= 5) {
			modify_attempt($query_secondary_produce, $desired_score);
		}
		else {
			echo"<h3>Incorrect score desired value sent to server.</h3>";
		}
	}
}	// A request for deletion made

// Check which page.
$Page_Accessed_Properly = get_recent_click();
if (!isset($main_page)) {
	header("location: manage.php");
	exit();
}

if (get_recent_click()) {
	$_SESSION["prev_page"] = get_recent_click();
	$temp_prev_page_num = $_SESSION["prev_page"];
}

if ($logged_in == true) {
	if (get_recent_click() == 1) { // If page clicked list all attempts
	  echo"<h2>Show all attempts page</h2>";
	  load_filter_inputs($logged_in);
	  list_all_attempts($query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 2) { // If page clicked list half attempts
	  echo"<h2>Show half attempts page</h2>";;
	  load_filter_inputs($logged_in);
	  list_half_attempts($query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 3) { // If page clicked, delete page
	  echo"<h2>Delete page</h2>";
	  load_filter_inputs($logged_in);
	  manual_change_display("delete", 3);
	  delete_attempts($query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 4) { // If page clicked, modify inputs
	  echo"<h2>Modify page</h2>";
	  load_filter_inputs($logged_in);
	  manual_change_display("modify", 4);
	  manage_score($query_produced);
	  $notsearched = false;
	}
} else {
		echo"<section>";
		echo"</br>";
		echo"<h2>Log in, to view results</h2>";
		echo"<p>If you believe this is an error, please contact your server administrator</p>";
		echo"</section>";
}

if ($notsearched == true and $logged_in == true) {
  echo"<h2>Please select an option.</h2>";
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
