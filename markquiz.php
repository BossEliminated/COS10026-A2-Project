<?php

# Get post values
function create_post_values_array($post_value_ids){
	$input_array = [];
	foreach ($post_value_ids as $i => $value) {
		array_push($input_array, $a = $_POST[$post_value_ids[$i]] ?? 'fallback'); // Useing Null Coalescing Operator
		$a != 'fallback' ?: print "<h2 style='display: inline;'>Error Missing Value </h2><p style='display: inline;'>".$post_value_ids[$i]."</p><br><br>"; // Temporary Ternary Operator error warning, I will add data checks later

		// // Non Null Coalescing Operator Version
		// if (isset($_POST[$post_value_ids[$i]])) {
		// 	array_push($input_array, $_POST[$post_value_ids[$i]]);
		// } else {
		// 	array_push($input_array, 0);
		// 	print "<h2 style='display: inline;'>Error Missing Value </h2><p style='display: inline;'>".$post_value_ids[$i]."</p><br><br>";
		// }
	}
	return $input_array;
}

function check_post_values_array($post_values_array, $answers){
	$exclude = 3;
	$i = $exclude;
	$count = count($post_values_array);

	while($i < $count) {
		if (!is_array($post_values_array[$i])){
			if ($post_values_array[$i] == $answers[$i-$exclude]) {
				print "Correct: ".$post_values_array[$i];
			} else {
				print "Wrong: ".$post_values_array[$i];
			}
		} else {
			$result = array_diff($post_values_array[$i], $answers[$i-$exclude]);
			if (count($result) == 0 && count($post_values_array[$i]) == count($answers[$i-$exclude])) {
				print "Correct";
			} else {
				print "Wrong";
			}
		}
		echo "<br>";
	  $i++;
	}
}

$post_value_ids = ['ID','given_name','family_name','quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
$answers = ['slowloris',['process-based_mode','hybrid_mode'],'','2004','1994'];

$post_values_array = create_post_values_array($post_value_ids);
check_post_values_array($post_values_array, $answers);

?>
