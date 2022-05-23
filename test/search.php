<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Car Search</title>
    <link rel="stylesheet" href="master.css">
  </head>
  <body>
    <h1>Search Form</h1>
    <form action="search.php" method="post">
      <fieldset class="addcar-fieldset">
        <legend>Search</legend>
        <div>
          <label for="search">Search:</label>
          <input type="text" id="search" name="search"><br />
          <input type="submit" value="Search">
        </div>
      </fieldset>
    </form>
    <?php
      include '../db_connect.php';
      $conn = db_connect();
      $sql_table = "id";

      function sanitise($value) {
        $value = trim($value);
        return htmlspecialchars($value);
      }

      if ($conn) {
        if (!empty($_POST)) {
          $a = $_POST['search'];
          $sql = "SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id AND id.`first_name` = '$a'";
          $table = mysqli_query($conn, $sql);

          if(mysqli_num_rows($table) == 0) {
            echo "<p class=\"wrong\">No cars found with: ", $sql, "</p>";
          } else {
            echo "<p>Results: </p>";
            echo "<table class='search-car'>";
            echo "<tr>\n "
            ."<th scope=\"col\">first_name</th>\n "
            ."<th scope=\"col\">last_name</th>\n "
            ."<th scope=\"col\">student_number</th>\n "
            ."<th scope=\"col\">created</th>\n "
            ."<th scope=\"col\">attempt</th>\n "
            ."<th scope=\"col\">score</th>\n "
            ."</tr>\n";
            while ($row = mysqli_fetch_assoc($table)){
              echo "<tr>";
              echo "<td>",$row["first_name"],"</td>" ;
              echo "<td>",$row["last_name"],"</td>";
              echo "<td>",$row["student_number"],"</td>";
              echo "<td>",$row["created"],"</td>";
              echo "<td>",$row["attempt"],"</td>";
              echo "<td>",$row["score"],"</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
          mysqli_close($conn);
        }
      }
    ?>
  </body>
</html>
