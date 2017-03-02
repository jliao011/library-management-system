<?php
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: ".mysqli_connect_error()."\n";
	}

	$borrowers = fopen("borrowers.csv","r");
	$data = fgetcsv($borrowers, 1000, ",");
	while (($data = fgetcsv($borrowers, 1000, ",")) !== FALSE) {
		$card_id = mysqli_real_escape_string($mysql,trim($data[0]));
		$ssn = mysqli_real_escape_string($mysql,trim($data[1]));
		$bname = mysqli_real_escape_string($mysql,trim($data[2]." ".$data[3]));
		$address = mysqli_real_escape_string($mysql,trim($data[5].", ".$data[6].", ".$data[7]));
		$phone = mysqli_real_escape_string($mysql,trim($data[8]));
		$insert_borrower = "INSERT INTO BORROWER VALUES ('$card_id','$ssn','$bname','$address','phone');";
		if (!mysqli_query($mysql,$insert_borrower)) {
			echo "Error: ".$insert_borrower.mysqli_error($mysql)."\n";
		}
	}
	echo "Finished loading borrowers.csv!\n"
?>