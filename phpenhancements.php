<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $title = 'PHP Enhancements'; include './header.inc' ?>
  <body>
    <?php $select = 5; include './menu.inc' ?>
  <body class="php-enh-body">
    <div class="php-enh-main">
      <div class="php-enh-headding">
        <h1>Checkout our web page feature extinction enhancements.</h1>
      </div>
      <div class="php-enh-content-flex">
        <div class="php-enh-content">
          <h2>Normalise database data structure</h2>
          <p>The data submitted for the quiz forum is grouped into two tables to improve data integrity and maintainability. The first group is the user details, the second is the score and attempts. Both groups use a primary-foreign key called unique_id which links the tables together.</p>
        </div>
        <!-- <hr class="php-enh-hr">
        <hr class="php-enh-hr"> -->
        <div class="php-enh-content">
          <h2>Secure database access</h2>
          <p>To prevent unauthorised parties from accessing our stores database of users and scores we protect it with an intuitive and secure login system. Only authorised users are able to access the supervisor web page.</p>
        </div>
      </div>
	  </br>
	<div class="php-enh-content-flex">
        <div class="php-enh-content">
          <h2>Going beyond the data structure requirements</h2>
          <p>All the examples throughout this course have been based on utilising a single table, having additional tables requires a further knowledge of what SQL returns and how to connect those tables together. </p>
        </div>
        <!-- <hr class="php-enh-hr">
        <hr class="php-enh-hr"> -->
        <div class="php-enh-content">
          <h2>Beyond security requirements</h2>
          <p>Whilst briefly covered in how a login system could take input, the course does not require a login system and does not explicitly show into blocking unauthorized requests or having a seperate login table along with other tables regarding data.</p>
        </div>
      </div>
	  </br>
	 <div class="php-enh-content-flex">
        <div class="php-enh-content">
          <h2>Data structure implementation</h2>
		  <h3>Quiz Insertion</h3>
          <p>A new data structure was implemented through having two seperate tables representing the information of the user, connected by numeric_id, which is looked for in searching. The base for this implementation was in quiz page, which a sufficient input was then placed into two tables being id,attempts. This required sending two queries <p>First Query:</p><code>$sql = "INSERT INTO `id`(`unique_id`, `first_name`, `last_name`, `student_number`) VALUES ('$unique_id', '$id[1]', '$id[2]', '$id[0]')";</code> Second Query:
			<code>
			$sql = "INSERT INTO `attempts`(`unique_id`, `attempt`, `score`) VALUES ('$unique_id', '1', '$score')";</code>
			For changes to existing data, a unique identifier had to be used that both tables held that referenced each other. <code>$sql = "SELECT `unique_id` FROM `id` WHERE first_name = '$id[1]' AND last_name = '$id[2]' AND student_number = '$id[0]'";</code>
			The unique identifier returned is then acted upon utilising a WHERE statement.
			</p>
			<h3>Manage Page</h3>
			<p>The manage page utilises multi-table queries to make searching tables far easier than searching each table manually and comparing the results. Having a multi table query allows a WHERE condition to apply to both tables, as if WHERE=student_number was applied to a single table without this column, it would return nothing. Combining both tables utilised the multi-table query. <code>"SELECT id.`first_name`, id.`last_name`, id.`student_number`, attempts.`created`, attempts.`attempt`, attempts.`score` FROM id, attempts WHERE id.unique_id = attempts.unique_id";</code> In this multi table query the column that belongs to a certain table can be collected by (table_name).(column_name) to which getting data from tables using <strong>FROM</strong> collects from both tables using "FROM table1,table2" <p>WHERE checking if both unique_id fields are the same ensure that data from different unique ids are not presented together in the same row.</p></p>
        </div>
        <!-- <hr class="php-enh-hr">
        <hr class="php-enh-hr"> -->
        <div class="php-enh-content">
          <h2>Security Implementation</h2>
          <p>Before letting the user search, the server prevents any request to search for data if they have not explicitly logged in. Usernames and passwords are regulated through a login table, not providing input or providing an incorrect username/password will prevent the user from logging in. This is done through checking if username and password is set <code>if (isset($username) && isset($password))</code> and then saving details into session, to which the user authenticates every time they connect by an sqli_query that counts exact matches <code>$sql = "SELECT COUNT(*) FROM `login` WHERE `username` = '$username' AND `password` = '$password'";
	return mysqli_fetch_array(mysqli_query($conn, $sql))['COUNT(*)'];</code></p>
        </div>
      </div>
    </div>
    <?php include './footer.inc'; ?>
  </body>
</html>
