<?php

include 'db_connect.php';
include 'sanitise_framework.php';
$notsearched = true;
$attemptstable = "attempts";





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
			if ($mode == "manage") {
				echo"<th class='manage_table_info'>Desired Score</th>";
			}
        echo"</tr>";
      // End Header
      for ($i=$starter;$i<$rows_available;$i++) {
        echo"<tr>";
        $associative_return = mysqli_fetch_assoc($returned_data);
        for ($t = 0; $t < count($all_fields); $t++) {
          $local_name = $all_fields[$t]->name;
		  $return_data = $associative_return[$local_name];
		  if (($mode == "delete" or $mode == "manage") and $t == 0) {
			  echo"<form method='POST' action='manage.php'>";
			  echo"<td><button type='submit' name='which_selected' value='$return_data'>$return_data</button></td>";
			  echo"<input type='hidden' name='action' value='$page_num'>";
			  echo"</form>";
		  }
		  else {
			  echo"<td class='manage_table_info'>$return_data</td>";
				if ($mode == "manage" and $t == count($all_fields) - 1) {
					echo"<td>
							<input type='number' placeholder='1-5' name='desired_score' min='1' max='5'></input>
						</td>";
						echo"</form>";
				}
		  }
        }
        //$return_data = $associative_return["first_name"];
        echo"</tr>";
      }
      echo"</table>";
}

function modify_attempt($attemptstable, $id_val, $score_desired) {
	$database = db_connect();
	$sql_query = "UPDATE $attemptstable SET score=$score_desired WHERE id=$id_val";
	$attemptmodify = mysqli_query($database, $sql_query);
	if ($attemptmodify) {
		echo"<h2>Dataset successfully modified!</h2>";
	}
	else {
		echo"<h2>Dataset could not be modified, may not exist!</h2>";
	}
}


function delete_attempt($attemptstable, $id_val) {
	$database = db_connect();
	$sql_query = "DELETE FROM $attemptstable WHERE id=$id_val";
	$attemptdelete = mysqli_query($database, $sql_query);
	if ($attemptdelete) {
		echo"<h2>Dataset successfully deleted!</h2>";
	}
	else {
		echo"<h2>Dataset could not be deleted, may not exist!</h2>";
	}
}

function confirmation($id_val) {
	echo"<dialog open='true'>Are you sure you want to delete row with id $id_val? 
		<form method='POST' action='manage.php'>
			<input type='hidden' name='action' value='3'>
			<input type='hidden' name='which_selected' value='$id_val'>
			<button type='submit' name='confirmation' value='true'>YES</button>
			<button type='submit' name='confirmation' value='false'>NO</button>
		</form>
	</dialog>";
}


function list_all_attempts($attemptstable) {
    $sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
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


function list_half_attempts($attemptstable) {
	$sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
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


function manage_score($attemptstable) {
	$sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
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

function delete_attempts($attemptstable) {
	$sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
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
  return false;
}
// Start of Main Sequence

// Check if a deletion was prompted.
if (!isset($_POST["confirmation"]) and get_recent_click() == 3) { // Confirmation given by user? NO? Then prompt confirmation.
	if (isset($_POST["which_selected"])) {
		//echo"<p>hi im a test</p>";
		confirmation(sanitise_input($_POST["which_selected"]));
	}
}
else {
	if (isset($_POST["which_selected"])) {
		$which_selected = sanitise_input($_POST["which_selected"]);
		if (get_recent_click() == 3) {
			if (sanitise_input($_POST["confirmation"]) == true) {
				delete_attempt($attemptstable, $which_selected);
			}
		}
		elseif (get_Recent_click() == 4) {
			$score_desired = sanitise_input($_POST["desired_score"]);
			modify_attempt($attemptstable, $which_selected, $score_desired);
		}
	}
}


// Check which page.
if (get_recent_click() == 1) {
  list_all_attempts($attemptstable);
  $notsearched = false;
}

if (get_recent_click() == 2) {
  list_half_attempts($attemptstable);
  $notsearched = false;
}

if (get_recent_click() == 3) {
  delete_attempts($attemptstable);
  $notsearched = false;
}

if (get_recent_click() == 4) {
  manage_score($attemptstable);
  $notsearched = false;
}






if ($notsearched == true) {
  echo"<h2>No search has occured</h2>";
}


?>
