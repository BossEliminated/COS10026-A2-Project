<?php

# Get post values
function create_post_values_array(){
	$values = ['ID','given_name','family_name','quiz-question-1','quiz-question-2','quiz-question-3','quiz-question-4','quiz-question-5'];
	$input_array = [];
	$count = count($values);
	$i = 0;

	while($i < $count) {
		array_push($input_array, $_POST[$values[$i]]);
	  $i++;
	}
	return $input_array;
}

function print_post_values_array($input_array){
	foreach($input_array as $input){
		if (!is_array($input)){
			echo $input."<br>";
		} else {
			$end = count($input)-1;
			$i = 0;
			foreach($input as $sub_input){
				echo $sub_input;
				if($i < $end){
					echo ", ";
					$i++;
				};
			};
		}
		echo "<br>";
	}
}

$post_values_array = create_post_values_array();
print_post_values_array($post_values_array);

?>
