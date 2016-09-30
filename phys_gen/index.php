<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="mystyle.css">
	<title>Physics Open Response Generator</title>
</head>
<body>
	<!--DESCRIPTION-->
	<h1>Physics Open Response Generator</h1>
	Type your question into the "Question" box, using "x", "y", and "z" for your variables, then enter the value for these variables into their respective boxes. Type the formula used to find the answer into the "Formula" box, referencing the variables as "x", "y", and "z".
	<br><br><a href="mc.php">Click Here to Generate Multiple Choice Questions</a>
	
	<?php
	$question_file = fopen("question_file.txt", "a");
	$number_file = fopen("number_file.txt","a+");
	$answer_file = fopen("answer_file.txt", "a");

	
	// ERROR TEXTS
	$q_error = "Please enter a question with a variable. Ex: If Daisy ran x miles in y hours on her way to school and in z hours on her way back, what was her average speed?";
	$var_error = "<br> Please enter a non-zero numeric value for your variable. Ex: -2.5";
	$var2_error = "Please enter a non-zero numerical value for y.";
	$var3_error = "Please enter a non-zero numerical value for z.";
	$exp_error = "Please enter a formula with a variable. Ex: (2+x)/3";

	//FUNCTIONS
	//Takes user inputted question and replaces variables with their values
	function scanInput($q, $var, $var2, $var3, $q_error, $var2_error, $var3_error){
		//If there is a variable "x" in the question
		if (strpos($q, " x ")!==false){
			//If there is a variable "y" in the question but no value in the "y" box, return error
			if ((strpos($q, " y ") !==false) and (empty($_POST['var2']))){
				return $var2_error;
			} else {
				if ((strpos($q, " z ") !==false) and (empty($_POST['var3']))){
					return $var3_error;
				} else { 
					//Otherwise for each character in the question, if it is an "x" or "y", replace it with their respective values
					$output = $q;
					for($i=0; $i<strlen($output); $i++){
						if (substr($output, $i, 3) == " x "){
							$output = substr_replace($output, $var, $i+1,1);
						}
						if (substr($output, $i, 3) == " y "){
							$output = substr_replace($output, $var2, $i+1,1);
						}
						if (substr($output, $i, 3) == " z "){
							$output = substr_replace($output, $var3, $i+1,1);
						}
					}
				}
			}
			return $output;
		} else {
			//If there is no "x" in the question return error
			return $q_error;
		}
	}
	
	//Replaces the variables "x" and "y" in the formula with their respective values
	function expression($exp, $var, $var2, $var3, $exp_error){
		//If there is an "x" in the formula
		if (strpos($exp, "x")!== false){
			$output = $exp;
			//For each character in the formula replace "x" and "y" with their values
			for($i=0; $i<strlen($output); $i++){
				if ($output[$i] == "x"){
					$output = substr_replace($output, $var, $i, 1);
				}
				if ($output[$i] == "y"){
					$output = substr_replace($output, $var2, $i, 1);
				}
				if ($output[$i] == "z"){
					$output = substr_replace($output, $var3, $i, 1);
				}

			}
			return $output;
		} else {
			//If there is no "x" in formula return error
			return $exp_error;
		}
	}
	
	//Evaluates the expression with user inputted values
	function answer($exp, $var2_error, $var3_error){
		//If a formula is entered and it includes an "x"
		if (isset($_POST['formula']) and (strpos($_POST['formula'], "x")!==false)) {
			//If there is a "y" in the formula but no inputted value return error
			if ((strpos($_POST['formula'], "y")!==false) and (empty($_POST['var2']))){
				return $var2_error;
			} else {
				if ((strpos($_POST['formula'], "z")!==false) and (empty($_POST['var3']))){
					return $var3_error;
				} else {
					//Otherwise calculate the answer
					include('evalmath.class.php'); 
					$m = new EvalMath($allowconstants = true, $allowimplicitmultiplication = true); 
		    			$m->suppress_errors = true; 
					$output = $m->evaluate($exp);
					return "Answer: $output";
				}
			}
		} else {
			//If the formula is not entered or does not include an "x" return the original expression
			return $exp;
		}
	}
	?>
	
	<form action="index.php" method="post">
	<h2>Question: </h2>
	<textarea name="question" rows="5" cols="60" style="font-size=30pt"><?php echo $_POST["question"]; ?></textarea>
	<br><h2>Formula: </h2><input type="text" name="formula" id="formula" value="<?php echo $_POST["formula"]; ?>">
	<br><h2>x: </h2><input type="number" step="any" name="var" value="<?php echo $_POST["var"]; ?>"><br>
	<h2>y: </h2><input type="number" step="any" name="var2" value="<?php echo $_POST["var2"]; ?>"><br>
	<h2>z: </h2><input type="number" step="any" name="var3" value="<?php echo $_POST["var3"]; ?>"><br>
	<br><input type="submit" name="submit" id="submit" value="Submit"><br> 
	</form>
    	
    	<?php
    	if (empty($_POST["question"])) {
		    $question = "";
		    echo "<br>";
		    echo $q_error;
	  } else {
	  	if (empty($_POST['formula'])) {
	  		$formula = "";
	    		echo "<br>";
	    		echo $exp_error;
	  	} else {
	  		if (empty($_POST["var"])) {
	    			$var = "";
	    			echo $var_error;
	  		} else {
	  			$question = $_POST["question"];
				$x = $_POST["var"];
				$y = $_POST["var2"];
				$z = $_POST["var3"];
			  	$formula = expression($_POST["formula"], $x, $y, $z, $exp_error);
			  	echo "<br>";
			  	$string = scanInput($question, $x, $y, $z, $q_error, $var2_error, $var3_error);
			  	echo $string;
			  	$n = fgets($number_file);
			  	fwrite($question_file, "$n) $string \n");
			  	fclose($question_file);
			  	echo "<br><br>";
			  	$key = answer($formula, $var2_error, $var3_error);
			  	echo $key;
			  	fwrite($answer_file, "$n) $key \n");
			  	fclose($answer_file);
				$n = $n + 1;
				file_put_contents("number_file.txt", strval($n));
				//to reset number counter back to 1
				//file_put_contents("number_file.txt", "1");
				
	  			}
	  		}
	  	}
	
	?>
	
	<br><br><a href="download.php">Download Questions</a>
	<br><a href="download_answers.php">Download Answer Key</a>
  
	
	<?php
	// 	echo '<br><br><a href="unlink.php?file='.$question_file.'">Clear Files</a>';
	?>
    		
</body>
</html>