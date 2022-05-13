<?php

include 'db_connect.php';
$notsearched = true;
$attemptstable = "attempts";

function list_all_attempts($attemptstable) {
    $sql_connection = db_connect();
    $sql_query = "SELECT * from $attemptstable";
    $returned_data = mysqli_query($sql_connection, $sql_query);
    $rows_available = mysqli_num_rows($returned_data);
    $all_fields = mysqli_fetch_fields($returned_data);
    #$test_var = count($all_fields);
    #echo"<p>$test_var</p>";
    if ($returned_data) {
      echo"<table>"; // Create Headers
        echo"<tr>";
        for ($t = 0; $t < count($all_fields); $t++) {
          $local_name = $all_fields[$t]->name;
          echo"<th>$local_name</th>";
        }
        echo"</tr>";
      // End Header
      for ($i=0;$i<$rows_available;$i++) {
        echo"<tr>";
        $associative_return = mysqli_fetch_assoc($returned_data);
        for ($t = 0; $t < count($all_fields); $t++) {
          $local_name = $all_fields[$t]->name;
          $return_data = $associative_return[$local_name];
          echo"<td>$return_data</td>";
        }
        //$return_data = $associative_return["first_name"];
        echo"</tr>";
      }
      echo"</table>";
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
}

if ($notsearched == true) {
  echo"<h2>No search has occured</h2>";
}

?>
