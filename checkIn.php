<?php
	$error = "Error: ";
	$result = "";
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		$error .= "Failed to connect to MySQL: ".mysqli_connect_error()."<br/>";
	}

	if($_POST['data'] != ""){
		$keys = mysqli_real_escape_string($mysql,$_POST['data']);
		$keys = explode("*",$keys);

		$query = "SELECT BOOK_LOANS.loan_id, BOOK_LOANS.isbn, BOOK_LOANS.card_id, BORROWER.bname, BOOK_LOANS.date_out, BOOK_LOANS.due_date,BOOK_LOANS.date_in FROM BOOK_LOANS JOIN BORROWER ON BORROWER.card_id = BOOK_LOANS.card_id WHERE BOOK_LOANS.date_in IS NULL AND ( ";
		for($i=0;$i<sizeof($keys);$i++){
			$key = trim($keys[$i]);
			$query .= "BOOK_LOANS.isbn LIKE '%$key%' OR ";
			$query .= "BOOK_LOANS.card_id LIKE '%$key%' OR ";
			$query .= "BORROWER.bname LIKE '%$key%'";
			if($i != sizeof($keys)-1){
				$query .= " OR ";
			}
		}	
		$query .= ");";
	// 0425176428* 000001 * jason
		$result = "<table><tr><th>loan ID</th><th>isbn</th><th>card ID</th><th>borrower</th><th>date out</th><th>due date</th><th>select</th></tr>";
		$loan = mysqli_query($mysql,$query);
		$num = mysqli_num_rows($loan);
		while($tuple = mysqli_fetch_array($loan)){
			$result .= "<tr>";
			$result .= "<td>".$tuple['loan_id']."</td>";
			$result .= "<td>".$tuple['isbn']."</td>";
			$result .= "<td>".$tuple['card_id']."</td>";
			$result .= "<td>".$tuple['bname']."</td>";
			$result .= "<td>".$tuple['date_out']."</td>";
			$result .= "<td>".$tuple['due_date']."</td>";
			$result .= "<td><input type='radio' name='checkin' value='".$tuple['loan_id']."'></td>";
			$result .= "</tr>";
		}
		$result .= "</table><br/>";
		// $result .= "<button type='button' class='submit' value='checkin'>Check In</button>";
		if($error == "Error: "){
			$error = $num." loan result(s) found.";
		}

	}else{
		$loan_id = $_POST['select'];
		$query = "UPDATE BOOK_LOANS SET date_in = curdate() WHERE loan_id = $loan_id;";
		if (!mysqli_query($mysql,$query)) {
			$error .= $query.mysqli_error($mysql)."<br/>";
		}
		if($error == "Error: "){
			$query = "SELECT * FROM BOOK_LOANS JOIN BORROWER ON BORROWER.card_id = BOOK_LOANS.card_id WHERE BOOK_LOANS.loan_id = $loan_id;";
			$tuple = mysqli_query($mysql,$query);
			$tuple = mysqli_fetch_array($tuple);
			$error = "Success Check In, detail information as follow: <br/>";
			$error .= "<table>";
			$error .= "<table><tr><th>loan ID</th><th>isbn</th><th>card ID</th><th>borrower</th><th>date out</th><th>due date</th><th>date in</th></tr>";
			$error .= "<tr>";
			$error .= "<td>".$tuple['loan_id']."</td>";
			$error .= "<td>".$tuple['isbn']."</td>";
			$error .= "<td>".$tuple['card_id']."</td>";
			$error .= "<td>".$tuple['bname']."</td>";
			$error .= "<td>".$tuple['date_out']."</td>";
			$error .= "<td>".$tuple['due_date']."</td>";
			$error .= "<td>".$tuple['date_in']."</td>";
			$error .= "</td></table><br/>";
		}


	}
		$message = array('error'=>$error,'result'=>$result);
		echo json_encode($message);
?>