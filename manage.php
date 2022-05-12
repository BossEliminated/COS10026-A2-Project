<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $notsearched = true; $title = 'Supervisor Management Quiz View'; include_once './header.inc' ?>
  <body>
    <?php $select = 6; include_once './menu.inc' ?>
    <div class="manage-flex-content">
      <div class="manage-menu">
        <form method="post" action="manage.php">
          <button type="submit" name="action" value="1">List All attempts</button>
          <button type="submit" name="action" value="2">List half attempts</button>
          <button type="submit" name="action" value="3">Delete attempts</button>
          <button type="submit" name="action" value="4">Manage Score</button>
          <hr class="manage-menu-hr">
          <div class="manage-id-field">
            <!-- Do not print these if logged in -->
            <label for="student_id"><input name="username" type="text" placeholder="Username"></label>
            <label for="student_name"><input name="password" type="text" placeholder="Password"></label>
            <!-- <p class="">If loged in Put User Name here</p> -->
          </div>
          <button type="submit" name="action" value="5">Login/Sign Up</button>
          <!-- Show logout button if loged in -->
          <!-- <button type="submit" name="action" value="6">Logout</button> -->
        </form>
        <div style="height: 500px;"></div>
      </div>
      <div class="manage-content">
        <h1>Management Page</h1>
        <section>
          <form method="post" action="https://mercury.swin.edu.au/it000000/formtest.php">
            <label for="student_id">Student ID </label><input name="student_id" id="student_id" type="text" placeholder="Student ID">
            <label for="student_name">Student Name </label><input name="student_name" id="student_name" type="text" placeholder="Name">
            <br>
            <input type="radio" name="mark_filter" id="no_filter"><label for="no_filter">No Filtering</label>
            <input type="radio" name="mark_filter" id="mark_filtering_hundred"><label for="mark_filtering_hundred">100% First Attempt</label>
            <input type="radio" name="mark_filter" id="mark_filtering_less_than"><label for="mark_filtering_less_than"><50% Second Attempt</label><br>
            <input type="radio" class="selection_appearance" name="mark_filter" id="custom_filter"><label for="custom_filter">Custom Range</label><input type="text" name="custom_filter" id="custom_filter" placeholder="eg. 20-50">
            <input type="submit" value="Submit">
          </form>
          <hr>
          <h2>Attempts table</h2>
        </section>
        <section>
          <table>
            <?php  
			$attemptstable = "attempts";
			
			
			function connect_to_sql() {
				$notsearched = false;
				$servername = "localhost";
				$username = "root";
				$password = "";
				$dbname = "a_patchy_team";
				$sql_connection = mysqli_connect($servername, $username, $password, $dbname);
				if ($sql_connection) {
					$database_connection_error = false;
					return $sql_connection;
				}
				else {
					$database_connection_error = true;
				}
				
			}
			
			function list_all_attempts($attemptstable) {
					
					$sql_connection = connect_to_sql();
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
          </table>
        </section>
      </div>
    </div>
    <?php include './footer.inc'; ?>
  </body>
</html>
