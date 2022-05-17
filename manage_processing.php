<?php
session_start();
include 'db_connect.php';
include 'sanitise_framework.php';
$notsearched = true;
$attemptstable = "attempts";
$filter_fields = ["student_id", "student_name", "mark_filter_no","mark_filter","mark_filter","mark_filter", "custom_filter"];
$id_refer = ["student_number", "first_nameORlast_name", "id=id", "score=100 AND attempt=1", "score<=50 AND attempt=2", "on", "score"];

function filter_considerations($filter_fields) {
	$filter_provided_array = [];
	for ($counter=0;$counter<count($filter_fields);$counter++) {
		if (isset($_POST[$filter_fields[$counter]])) {
			$sanitised_filter_fields = sanitise_input($_POST[$filter_fields[$counter]]);
			if (is_array($sanitised_filter_fields)) {

			}// If radio button.
			else {
				if ($_POST[$filter_fields[$counter]] != "") {
					array_push($filter_provided_array, $_POST[$filter_fields[$counter]]);
				}
				else {
					array_push($filter_provided_array, "NO_FILT");
				}
			}
		}
		else {
			array_push($filter_provided_array, "NO_FILT");
		}
	}
	return $filter_provided_array;
}

function modify_query_based_on_filter($id_refer, $filters_set, $is_first_filter) {
	if ($is_first_filter == true) {
		$base_query = "WHERE ";
	}
	else {
		$base_query = " AND ";
	}
	$test = count($id_refer);
	$query_addition = 0;
	for ($counter=0;$counter<count($id_refer);$counter++) {
		if ($filters_set[$counter] != "NO_FILT") { // Add to base query
			$query_addition = $query_addition + 1;
			echo"<p>$query_addition</p>";
			if ($query_addition > 1) {
				$base_query = ($base_query . " AND ");
			}
			//echo"<p>find $id_refer[$counter]</p>";
			if (strpos($id_refer[$counter], "OR")) { // Deduction of filter
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
	echo"<hr>";
	echo"<h2>Specific Change Request</h2>";
	echo"<form method='POST' action='manage.php'>";
	echo"<label>Student ID: </label><input type='text' name='manual_change_id' placeholder='Student Id'/>";
	echo"<label> Attempt: </label><input type='number' name='manual_change_attempt' size='10' min='0' max='2' placeholder='Attempt'/>";
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
        echo"<tr>";
        for ($t = 0; $t < count($all_fields); $t++) {
          $local_name = $all_fields[$t]->name;
          echo"<th class='manage_table_info'>$local_name</th>";
        }
        echo"</tr>";
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


if (isset($_POST["filter_all"])) { // Does user want to filter data?
	$filters_set = filter_considerations($filter_fields);
	$query_produced = modify_query_based_on_filter($id_refer, $filters_set, true);
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
if (get_recent_click() == 1) {
  echo"<h2>Show all attempts page</h2>";
  list_all_attempts($attemptstable, $query_produced);
  $notsearched = false;
}

if (get_recent_click() == 2) {
  echo"<h2>Show half attempts page</h2>";
  list_half_attempts($attemptstable, $query_produced);
  $notsearched = false;
}

if (get_recent_click() == 3) {
  echo"<h2>Delete page</h2>";
  manual_change_display("delete", 3);
  delete_attempts($attemptstable, $query_produced);
  $notsearched = false;
}

if (get_recent_click() == 4) {
  echo"<h2>Modify page</h2>";
  manual_change_display("modify", 4);
  manage_score($attemptstable, $query_produced);
  $notsearched = false;
}







if ($notsearched == true) {
  echo"<h2>No search has occured</h2>";
}


?>
