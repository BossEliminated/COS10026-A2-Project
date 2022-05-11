<!DOCTYPE html>
<head>
 <meta charset="utf-8">
 <meta name="Author" content="Nicholas, Yash, Syed, Chris">
 <meta name="KeyWords" content="PHP, SQL, MANAGEMENT">
 <meta name="Description" content="Supervisor View">
 <title>Supervisor Management Quiz View</title>
</head>
<body>
	<?php 
	$notsearched = true;
	include_once("header.inc");
	$select = 6;
	include_once("menu.inc"); # Maybe give option for user to search in a range, rather than options......
	?>
	<h1>Management Page</h1> 
	<section> 
	<form method="post" action="https://mercury.swin.edu.au/it000000/formtest.php"">
		<label for="student_id">Student ID </label><input name="student_id" id="student_id" type="text" placeholder="Student ID">
		<label for="student_name">Student Name </label><input name="student_name" id="student_name" type="text" placeholder="Name">
		<br>
		<input type="radio" name="mark_filter" id="no_filter"><label for="no_filter">No Filtering</label>
		<input type="radio" name="mark_filter" id="mark_filtering_hundred"><label for="mark_filtering_hundred">100% First Attempt</label>
		<input type="radio" name="mark_filter" id="mark_filtering_less_than"><label for="mark_filtering_less_than"><50% Second Attempt</label><br>
		<input type="radio" class="selection_appearance" name="mark_filter" id="custom_filter"><label for="custom_filter">Custom Range</label><input type="text" name="custom_filter" id="custom_filter" placeholder="eg. 20-50">
		<br>
		<button type="submit">Search</button> 
	</form>
		<hr>
		<h2>Attempts table</h2>
	</section>
	<section>
		<table>
		<?php  # Go mad styling this page.
		if ($notsearched == true) {
			echo"<h2>No search has occured</h2>";
		}
		?>
		</table>
	</section>
</body>