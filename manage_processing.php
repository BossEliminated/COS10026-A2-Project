<?php
//session_start();
include 'db_connect.php';
include 'sanitise_framework.php';
$notsearched = true;
$attemptstable = "attempts";
$filter_fields = ["student_id", "student_name", "mark_filter"];
$attempts_filter = ["id=id", "score=5 AND attempt=1", "score<=2 AND attempt=2"];
$id_refer = ["student_number", "first_nameORlast_name"];
$index_of_radio_buttons = 2;
$logged_in = false;
// Correlate radio with filter.

// Functions required for log in
function get_recent_click() {
  if (isset($_POST["action"])) {
	$action_val = sanitise_input($_POST["action"]);
    if (is_numeric($action_val)) {
      return($action_val);
    }
  }
  else {
	 // echo"<p>session check reached</p>";
	  if (isset($_SESSION["prev_page"])) {
		$session_number = sanitise_input($_SESSION["prev_page"]);
		//echo"<p>$session_number</p>";
		return($session_number);
	  }
  }
  return false;
}





if (!isset($_SESSION["username"])) {
	$logged_in = false;
}
else { // Attempt login
	$sanitised_user = sanitise_input($_SESSION["username"]);
	if (get_recent_click() != 6) {
		if (log_in_available() == true) {
			if (attempt_log_in(sanitise_input($_SESSION["username"]), sanitise_input($_SESSION["password"]), true) == true) {
				echo "<h5 class='log_in_notif'>Logged in as $sanitised_user !</h5>";
				echo"<hr/>";
				$logged_in = true;
			}
		}
	}
}

function log_in_available() {
	$sql_connect = db_connect();
	$table = "passwords";
	$creation_query = "CREATE TABLE IF NOT EXISTS login  (
							login_id INT AUTO_INCREMENT PRIMARY KEY,
							username VARCHAR(30),
							password VARCHAR(30)
						);";
	$creation_login = mysqli_query($sql_connect, $creation_query);
	if ($creation_login) {
		mysqli_close($sql_connect);
		return true;
	}
	else {
		echo"<h3>Cannot connect to login servers, cannot login, try again later.</h3>";
		return false;
	}
}

function attempt_log_in($username, $password, $recheck) {
		$sql_connect = db_connect();
		$login_query = "SELECT COUNT('username') FROM login WHERE password='$password' AND username='$username'";
		//echo"<p>$login_query</p>";
		$login_establishment = mysqli_query($sql_connect, $login_query);
		$output_refinement = mysqli_fetch_all($login_establishment)[0][0];
		if ($output_refinement == 1) {
			if ($recheck == false) {
				echo"<h2 class='log_in_notif'>Logged in as, $username</h2>";
			}
			$_SESSION["username"] = $username;
			$_SESSION["password"] = $password;
			return true;
		}
		else {
			session_destroy();
			if ($recheck == false) {
				echo"<h2 class='fail_log'>Failed to log in, username or password may be incorrect</h2>";
			}
			else {
				echo"<h2 class='fail_log'>Password was changed whilst logged in. Retry log in.</h2>";
			}
			echo"</hr>";
			return false;
		}
}


// Initiate log in phase
if (get_recent_click() == 5) {
	if (isset($_POST["username"])) {
		$username = sanitise_input($_POST["username"]);
	}
	if (isset($_POST["password"])) {
		$password = sanitise_input($_POST["password"]);
	}
	if (isset($username) == true and isset($password) == true) {
		if ($username == "" or $password == "") {
			echo"<h2 class='fail_log'>Username/Password input left blank!</h2>";
		}
		else {
			//echo"<p>DEBUG: Attempting log in</p>";
			log_in_available();
			if (attempt_log_in($username, $password, false) == true) {
				$logged_in = true;
			};
		}
	}
}




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
				<label for="mark_filtering_less_than">Scored 50% on second Attempt </label>
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
//

function debug_check() {
	foreach ($_POST as $param_name => $param_val) {
    print "<p>Name: $param_name, Value: $param_val</p>";
}
	// foreach ($variable as $a) {
	// 	if isset($_POST[$a]) {
	// 		print ($_POST[$a]);
	// 	}
	// }
}

//debug_check();





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

function modify_query_based_on_filter($id_refer, $filters_set, $is_first_filter, $index_of_radio_buttons, $radio_mode) { // id_refer is query used., filters set are results of POST 
	$anyfiltering_done = false;
	for ($basic_counter = 0; $basic_counter < count($filters_set); $basic_counter++) {
		//echo"<p>$filters_set[$basic_counter]</p>";
		if ($radio_mode == false) {
			if ($filters_set[$basic_counter] == "NO_FILT" or $filters_set[$basic_counter] == " ") {
			}
			else {
				//$debug = $filters_set[$basic_counter];
				//echo"<p>debug</p>";
				if ($basic_counter != $index_of_radio_buttons) {
					$anyfiltering_done = true;
				}
			}
		}
		elseif ($radio_mode == true) {
			if ($filters_set[$index_of_radio_buttons] != "NO_FILT") {
				$anyfiltering_done = true;
			}
		}
	}
	$base_query = "";
	if ($anyfiltering_done == true) {
		if ($is_first_filter == true) {
			$base_query = "WHERE ";
		}
		else {
			$base_query = " AND ";
		}
	}
	$test = count($id_refer);
	$query_addition = 0;
	for ($counter=0;$counter<count($id_refer);$counter++) {
		if ($filters_set[$counter] != "NO_FILT") { // Add to base query
			$query_addition = $query_addition + 1;
			if ($query_addition > 1 and $radio_mode == false) {
				echo"<p>im responsible.</p>";
				$base_query = ($base_query . " AND ");
			}
			$test = $id_refer[$counter];
			//print_r($id_refer);
			//print_r($filters_set);
			//echo"<p>does it have =? $test</p>";
			if (strpos($id_refer[$counter], "=")) { // Verbatim
				$temp_string = "(";
				if ($counter == $index_of_radio_buttons) {
					echo"<p>added query for radio</p>";
					$temp_string = ($temp_string . $id_refer[$filters_set[$counter]] . ")"); // Looking for number
					$base_query = ($base_query . $temp_string);
					$debug = $id_refer[$filters_set[$counter]];
					echo"<p>added $debug</p>";
					echo"$temp_string";
				}
			}
			elseif (strpos($id_refer[$counter], "OR")) { // Deduction of filter
				$temp_string = "(";
				$temp_string = ($temp_string . $id_refer[$counter]);
				$seperator = '"';
				//echo"<p>$temp_string</p>";
				$temp_string = str_replace("OR", " = $seperator$filters_set[$counter]$seperator OR ", $temp_string);
				//echo"<p>$temp_string</p>";
				$temp_string = ($temp_string . " = $seperator$filters_set[$counter]$seperator" . ")");
				$base_query = ($base_query . $temp_string);
			}
			elseif (strpos($id_refer[$counter], "AND")) { // Deduction of filter
				$temp_string = "(";
				$temp_string = ($temp_string . $id_refer[$counter]);
				//echo"<p>$temp_string</p>";
				//echo"<p>$temp_string</p>";
				$base_query = ($base_query . $temp_string . ")");
			}
			else {
				$base_query = ($base_query . $id_refer[$counter] . "=" . $filters_set[$counter]);
			}
		}
	}
	echo"<p>Sent Query: $base_query</p>";
	return $base_query;
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
	echo"<label> Attempt: </label><input type='number' name='manual_change_attempt' size='10' min='1' max='2' placeholder='Attempt'/>";
	if ($mode == "modify") {
		echo"</br>";
		echo"<label> New Score: </label><input type='number' name='desired_score' min='0' max='5' size='6' placeholder='Score'/>";
	}
	echo"<button type='submit' name='modify_request' value='true'>$mode</button>";
	echo"<input type='hidden' name='action' value='$page_num'/>";
	echo"<hr>";
	echo"</form>";
}

function display_results_in_table($returned_data, $mode, $page_num) {
	$rows_available = mysqli_num_rows($returned_data);
    $all_fields = mysqli_fetch_fields($returned_data);
	$starter = 0;
	if ($mode == "half") {
		$starter = round($rows_available / 2);
	}
	 echo"<table class='manage_table'>"; // Create Headers
		echo"<thead>";
        echo"<tr>";
        for ($t = 0; $t < count($all_fields); $t++) {
          $local_name = $all_fields[$t]->name;
          echo"<th>$local_name</th>";
        }
        echo"</tr>";
		echo"</thead>";
      // End Header
	  if ($rows_available != 0) {
		  for ($i=$starter;$i<$rows_available;$i++) {
			echo"<tr>";
			$associative_return = mysqli_fetch_assoc($returned_data);
			for ($t = 0; $t < count($all_fields); $t++) {
			  $local_name = $all_fields[$t]->name;
			  $return_data = $associative_return[$local_name];
			  if (($mode == "delete" or $mode == "manage") and $t == 0) {
				  echo"<form method='POST' action='manage.php'>";
				  echo"<td><button type='submit' name='which_selected' value='$return_data'>$return_data</button>";
				  if ($mode == "manage") {
					  echo"
									<input type='number' placeholder='Score' name='desired_score' min='1' max='5'></input>
								</td>";
				  }
				  echo"<input type='hidden' name='action' value='$page_num'>";
				  echo"</form>";
			  }
			  else {
				  echo"<td class='manage_table_info'>$return_data</td>";
			  }
			}
			//$return_data = $associative_return["first_name"];
			echo"</tr>";
		  }
	  }
	  else {
		 echo"<h3>No results found! Filter may be too strict!</h3>";
	  }
      echo"</table>";
	  mysqli_free_result($returned_data);
}

function modify_attempt($attemptstable, $id_val, $score_desired, $manual) {
	echo"<p>modification request</p>";
	$database = db_connect();
	if ($manual == false) {
		$sql_query = "UPDATE $attemptstable SET score=$score_desired WHERE id=$id_val";
	}
	else {
		$sql_query = "UPDATE $attemptstable SET score=$score_desired WHERE ";
		$sql_query = ($sql_query . str_replace(":", "=", $manual));
		echo"<p>$sql_query</p>";
	}
	$attemptmodify = mysqli_query($database, $sql_query);
	if ($attemptmodify) {
		echo"<h2>Dataset successfully modified!</h2>";
	}
	else {
		echo"<h2>Dataset could not be modified, may not exist!</h2>";
	}
}


function delete_attempt($attemptstable, $id_val, $manual) {
	$database = db_connect();
	if ($manual == false) {
		$sql_query = "DELETE FROM $attemptstable WHERE id=$id_val";
	}
	else {
		$sql_query = "DELETE FROM $attemptstable WHERE ";
		if (strpos($manual, "and")) {
			$manual = str_replace("and", "AND", $manual);
			$manual = str_replace(":", "=", $manual);
		}
		$manual = str_replace(":", "=", $manual);
		$sql_query = $sql_query . $manual;
		echo"<p>Query Sent: $sql_query</p>"; // INFO QUERY
	}
	$attemptdelete = mysqli_query($database, $sql_query);
	$affected_row_num = mysqli_affected_rows($database);
	if ($affected_row_num > 0) {
		echo"<h3>Data was deleted!</h2>";
	}
	else {
		echo"<h3>No data deleted, found no corresponding results!</h2>";
	}
}

function confirmation($id_val, $search_val) {
	echo"<dialog open='true'>Are you sure you want to delete row with id $id_val?
		<form method='POST' action='manage.php'>
			<input type='hidden' name='action' value='3'>
			<input type='hidden' name='which_selected' value='$id_val'>";
			if ($search_val == true) {
				echo"<input type='hidden' name='what_searched' value='$id_val'>";
				echo"<input type='hidden' name='type_of_change' value='$id_val'>";
			}
			echo"
			<button type='submit' name='confirmation' value='true'>YES</button>
			<button type='submit' name='confirmation' value='false'>NO</button>
		</form>
	</dialog>";
}

function update_query($old_query, $new_query_filters) {
	if ($new_query_filters != false) {
		$old_query = ($old_query . " " . $new_query_filters );
		//echo"<p>New query produced $old_query</p>";
		return $old_query;
	}
	return $old_query;
}

function list_all_attempts($attemptstable, $query_produced) {
    $sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
	//echo"<p>Query specification $query_produced</p>";
	$sql_query = update_query($sql_query, $query_produced);
    $returned_data = mysqli_query($sql_connection, $sql_query);
    #$test_var = count($all_fields);
    #echo"<p>$test_var</p>";
    if ($returned_data) {
     display_results_in_table($returned_data, "all", 1);
    }
    else {
      $query_failure = true;
    }
}


function list_half_attempts($attemptstable, $query_produced) {
	$sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
	$sql_query = update_query($sql_query, $query_produced);
    $returned_data = mysqli_query($sql_connection, $sql_query);
    #$test_var = count($all_fields);
    #echo"<p>$test_var</p>";
    if ($returned_data) {
     display_results_in_table($returned_data, "half", 2);
    }
    else {
      $query_failure = true;
    }
}


function manage_score($attemptstable, $query_produced) {
	$sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
	$sql_query = update_query($sql_query, $query_produced);
    $returned_data = mysqli_query($sql_connection, $sql_query);
    #$test_var = count($all_fields);
    #echo"<p>$test_var</p>";
    if ($returned_data) {
     display_results_in_table($returned_data, "manage", 4);
    }
    else {
      $query_failure = true;
    }
}

function delete_attempts($attemptstable, $query_produced) {
	$sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
	$sql_query = update_query($sql_query, $query_produced);
    $returned_data = mysqli_query($sql_connection, $sql_query);
    #$test_var = count($all_fields);
    #echo"<p>$test_var</p>";
    if ($returned_data) {
     display_results_in_table($returned_data, "delete", 3);
    }
    else {
      $query_failure = true;
    }
}

// Start of Main Sequence

// Check if a deletion was prompted.
if (!isset($_POST["confirmation"]) and get_recent_click() == 3) { // Confirmation given by user? NO? Then prompt confirmation.
	if (isset($_POST["which_selected"]) or isset($_POST["modify_request"])) {
		//echo"<p>hi im a test</p>";
		if (isset($_POST["modify_request"]) and isset($_POST["manual_change_id"])) {
			if ($_POST["manual_change_id"] != "") {
					if (isset($_POST["manual_change_attempt"])) {
							if (sanitise_input($_POST["manual_change_attempt"]) != "") {
							confirmation(sanitise_input("student_number:". $_POST["manual_change_id"]) . " and attempt:" . sanitise_input($_POST["manual_change_attempt"]), true);
							}
							else {
								confirmation(sanitise_input("student_number:". $_POST["manual_change_id"]), true);
							}
					}
					else {
						confirmation(sanitise_input("student_number:".$_POST["manual_change_id"]), true);
					}
			}
			else {
				if (isset($_POST["manual_change_attempt"])) {
					confirmation(sanitise_input("attempt:".$_POST["manual_change_attempt"]), true);
				}
			}
		}
		else {
			confirmation(sanitise_input($_POST["which_selected"]), false);
		}
	}
}
else {
	if (isset($_POST["which_selected"]) or isset($_POST["modify_request"])) {
		if (isset($_POST["which_selected"])) {
			$which_selected = sanitise_input($_POST["which_selected"]);
		}
		if (get_recent_click() == 3) {
			$test_var = $_POST["confirmation"];
			//echo"$test_var";
			if (sanitise_input($_POST["confirmation"]) == "true") {
				//echo"<p>im true</p>";
				if (isset($_POST["what_searched"])) {
					delete_attempt($attemptstable, $which_selected, sanitise_input(($_POST["what_searched"])));
				}
				else {
					delete_attempt($attemptstable, $which_selected, false);
				}
			}
		}
		elseif (get_recent_click() == 4) {
			echo"<p>this is a test marker, remove when done</p>";
			$score_desired = sanitise_input($_POST["desired_score"]);
			if (is_numeric($score_desired)) {
				if ($score_desired >= 0 and $score_desired <= 5) {
					$id_var_name = "student_number";
					$attempt_var_name = "attempt";
					if (isset($_POST["modify_request"])) {
						if ($_POST["manual_change_id"] != "" and $_POST["manual_change_attempt"] != "") {
							modify_attempt($attemptstable, 0, $score_desired, sanitise_input("$id_var_name=" . ($_POST["manual_change_id"]) . " AND $attempt_var_name=" . ($_POST["manual_change_attempt"])));
						}
						elseif ($_POST["manual_change_id"] != "") {
							modify_attempt($attemptstable, 0, $score_desired, sanitise_input("$id_var_name=" .($_POST["manual_change_id"])));
						}
						elseif($_POST["manual_change_attempt"] != "") {
							modify_attempt($attemptstable, 0, $score_desired, sanitise_input("$attempt_var_name=" .($_POST["manual_change_attempt"])));
						}
					}
					else {
						modify_attempt($attemptstable, $which_selected, $score_desired, false);
					}
				}
			}
		}
	}
}

if (get_recent_click() == 6) { // Log out
	 session_destroy();
	 $logged_in = false;
	 echo"<h2 class='log_in_notif'>You've been logged out.</h2>";
}



if (isset($_POST["filter_all"])) { // Does user want to filter data?
	$filters_set = filter_considerations($filter_fields, $attempts_filter, $index_of_radio_buttons);
	$query_produced = modify_query_based_on_filter($id_refer, $filters_set, true, $index_of_radio_buttons, false); // for text input;
	if ($query_produced == "") {
		$query_started = true;
		echo"<p>first filter</p>";
	}
	else {
		$query_started = false;
	}
	$second_query_produced = modify_query_based_on_filter($attempts_filter, $filters_set, $query_started, $index_of_radio_buttons, true); // For Radio Buttons
	$query_produced = ($query_produced. " " . $second_query_produced);
	echo"<p>final query $query_produced</p>";
}
else {
	$query_produced = false;
}




if (get_recent_click()) {
	$_SESSION["prev_page"] = get_recent_click();
	$temp_prev_page_num = $_SESSION["prev_page"];
	//echo"<p>$temp_prev_page_num</p>";
}




// Check which page.
$Page_Accessed_Properly = get_recent_click();
if (!isset($main_page)) {
	header("location: manage.php");
}





if ($logged_in == true) {
	if (get_recent_click() == 1) {
	  echo"<h2>Show all attempts page</h2>";
	  load_filter_inputs($logged_in);
	  list_all_attempts($attemptstable, $query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 2) {
	  echo"<h2>Show half attempts page</h2>";;
	  load_filter_inputs($logged_in);
	  list_half_attempts($attemptstable, $query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 3) {
	  echo"<h2>Delete page</h2>";
	  load_filter_inputs($logged_in);
	  manual_change_display("delete", 3);
	  delete_attempts($attemptstable, $query_produced);
	  $notsearched = false;
	}

	if (get_recent_click() == 4) {
	  echo"<h2>Modify page</h2>";
	  load_filter_inputs($logged_in);
	  manual_change_display("modify", 4);
	  manage_score($attemptstable, $query_produced);
	  $notsearched = false;
	}
}
else {
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


?>
