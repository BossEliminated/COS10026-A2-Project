<?php
function db_connect() {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "a_patchy_team";

	try {
		$conn = new mysqli($servername, $username, $password, $dbname);
	} catch(Exception $e) {
		if ($e->getCode() == 1049) {
			// Check Database Exists
			print '<p>MySQLi Created Database</p>';
			$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
			$conn = mysqli_connect($servername, $username, $password);
			mysqli_query($conn, $sql);
			mysqli_close($conn);
			$conn = mysqli_connect($servername, $username, $password, $dbname);
		} else {
			echo '<p>MySQLi Connection Error: ' .$e->getMessage().'</p>';
			return false;
		}
	}

	// Check Attempts Table Exists
	$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = 'attempts'";
	if (!mysqli_fetch_array(mysqli_query($conn, $sql))[0]) {
		echo '<p>MySQLi Created Attempts Table</p>';
		$sql = "CREATE TABLE IF NOT EXISTS attempts (
			id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			unique_id varchar(256) NOT NULL,
			created datetime NOT NULL DEFAULT current_timestamp(),
			attempt int(11) NOT NULL,
			score int(11) NOT NULL
		)";
		mysqli_query($conn, $sql);
	}

	// Check ID Table Exists
	$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = 'id'";
	if (!mysqli_fetch_array(mysqli_query($conn, $sql))[0]) {
		echo '<p>MySQLi Created ID Table</p>';
		$sql = "CREATE TABLE IF NOT EXISTS id (
			id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			unique_id varchar(256) NOT NULL,
			first_name varchar(256) NOT NULL,
			last_name varchar(256) NOT NULL,
			student_number int(11) NOT NULL
		)";
		mysqli_query($conn, $sql);
	}

	return $conn;
}
?>
