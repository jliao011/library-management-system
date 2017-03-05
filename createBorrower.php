<?php
	$error = "";
	$result = "";
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		$error .= "Failed to connect to MySQL: ".mysqli_connect_error()."<br/>";
	}

	$ssn = mysqli_real_escape_string($mysql,$_POST['ssn']);
	$bname = mysqli_real_escape_string($mysql,$_POST['bname']);
	$address = mysqli_real_escape_string($mysql,$_POST['address']);
	if($_POST['phone'] == "NULL"){
		$phone = mysqli_real_escape_string($mysql,$_POST['phone']);
	}else{
		$phone = "'".mysqli_real_escape_string($mysql,$_POST['phone'])."'";
	}
	$query = "SELECT * FROM BORROWER WHERE ssn = '$ssn';";
	$result = mysqli_query($mysql,$query);
	if(mysqli_num_rows($result) != 0){
		$tuple = mysqli_fetch_array($result);
		echo "Error: Borrower exists! Borrowers are allowed to possess exactly one library card.";
		echo "<table>";
		echo "<tr><th>Card ID</th><th>SSN</th><th>Name</th><th>Address</th><th>Phone</th></tr>";
		echo "<tr><td>".$tuple['card_id']."</td><td>".$tuple['ssn']."</td><td>".$tuple['bname'];
		echo "</td><td>".$tuple['address']."</td><td>".$tuple['phone']."</td></tr>";
		echo "</table>";
	}else{
		$query = "SELECT COUNT(*) AS num FROM BORROWER;";
		$result = mysqli_query($mysql,$query);
		$card_id = strval(mysqli_fetch_array($result)['num']+1);
		$len = strlen($card_id);
		for($i=0;$i<6-$len;$i++){
			$card_id = '0'.$card_id;
		}
		$query = "INSERT INTO BORROWER VALUES ('$card_id','$ssn','$bname','$address',$phone)";
		if (!mysqli_query($mysql,$query)) {
			echo "Error: ".$query.mysqli_error($mysql)."<br/>";
		}else{
			echo "Borrower created! Information as follow:";
			echo "<table>";
			echo "<tr><th>Card ID</th><th>SSN</th><th>Name</th><th>Address</th><th>Phone</th></tr>";
			echo "<tr><td>".$card_id."</td><td>".$ssn."</td><td>".$bname."</td><td>".$address."</td><td>".$_POST['phone']."</td></tr>";
			echo "</table>";
		}

	}
	// echo "num is ".$card_id."<br>";
	// echo $ssn.",".$bname.",".$address.",".$phone;
	// echo "<br>".$query;

?>