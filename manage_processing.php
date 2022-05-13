<?php

include 'db_connect.php';
$notsearched = true;
$attemptstable = "attempts";

function display_results_in_table($returned_data, $mode) {
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
      for ($i=$starter;$i<$rows_available;$i++) {
        echo"<tr>";
        $associative_return = mysqli_fetch_assoc($returned_data);
        for ($t = 0; $t < count($all_fields); $t++) {
          $local_name = $all_fields[$t]->name;
		  $return_data = $associative_return[$local_name];
		  if ($mode == "delete" and $t == 0) {
			  echo"<form method='POST' action='manage.php'>"
			  echo"<td><button type='submit' name='which_to_delete' value='$return_data'>$return_data</button></td>";
		  }
		  else {
			  echo"<td class='manage_table_info'>$return_data</td>";
		  }
        }
        //$return_data = $associative_return["first_name"];
        echo"</tr>";
      }
      echo"</table>";
}




function list_all_attempts($attemptstable) {
    $sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
    $returned_data = mysqli_query($sql_connection, $sql_query);
    #$test_var = count($all_fields);
    #echo"<p>$test_var</p>";
    if ($returned_data) {
     display_results_in_table($returned_data, "all");
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
     display_results_in_table($returned_data, "half");
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
     display_results_in_table($returned_data, "delete");
    }
    else {
      $query_failure = true;
    }
}

function get_recent_click() {
  if (isset($_POST["action"])) {
    if (is_numeric($_POST["action"])) {
      return($_POST["action"]);
    }
  }
  return false;
}

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

if ($notsearched == true) {
  echo"<h2>No search has occured</h2>";
}

?>
