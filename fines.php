<?php
	$error = "";
	$result = "";
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		$error .= "Failed to connect to MySQL: ".mysqli_connect_error()."<br/>";
	}
// date_add(curdate(),interval $dayelapse day) > due_date
	if($_POST['menu'] == 'reset'){
		$query = "DELETE FROM FINES;";
		mysqli_query($mysql,$query);
		$error = "TABLE FINES reset.";
	}

	if($_POST['menu'] == 'refresh'){
		$dayelapse = $_POST['dayelapse'];
		$query = "SELECT loan_id, DATEDIFF(date_in,due_date) AS diff FROM BOOK_LOANS WHERE date_in > due_date AND loan_id NOT IN (SELECT loan_id FROM FINES);";
		$returned = mysqli_query($mysql,$query);
		while($tuple = mysqli_fetch_array($returned)){
			$loan_id = $tuple['loan_id'];
			$diff = $tuple['diff'];
			$insertFines = "INSERT INTO FINES VALUES ($loan_id,$diff*0.25,0);";
			if (!mysqli_query($mysql,$insertFines)) {
				$error .= $insertFines.mysqli_error($mysql)."<br/>";
			}

		}
		$query = "SELECT loan_id, DATEDIFF(date_in,due_date) AS diff FROM BOOK_LOANS WHERE date_in > due_date AND loan_id IN (SELECT loan_id FROM FINES WHERE paid = 0);";
		$returned = mysqli_query($mysql,$query);
		while($tuple = mysqli_fetch_array($returned)){
			$loan_id = $tuple['loan_id'];
			$diff = $tuple['diff'];
			$insertFines = "UPDATE FINES SET fine_amt = $diff*0.25 WHERE loan_id = $loan_id;";
			if (!mysqli_query($mysql,$insertFines)) {
				$error .= $insertFines.mysqli_error($mysql)."<br/>";
			}

		}
		$query = "SELECT loan_id, DATEDIFF(date_add(curdate(),interval $dayelapse day),due_date) AS diff FROM BOOK_LOANS WHERE date_in IS NULL AND date_add(curdate(),interval $dayelapse day) > due_date AND loan_id IN (SELECT loan_id FROM FINES WHERE paid = 0);";
		$update = mysqli_query($mysql,$query);
		while($tuple = mysqli_fetch_array($update)){
			$loan_id = $tuple['loan_id'];
			$diff = $tuple['diff'];
			$updateFines = "UPDATE FINES SET fine_amt = $diff*0.25 WHERE loan_id = $loan_id;";
			if (!mysqli_query($mysql,$updateFines)) {
				$error .= $updateFines.mysqli_error($mysql)."<br/>";
			}

		}

		$query = "SELECT loan_id, DATEDIFF(date_add(curdate(),interval $dayelapse day),due_date) AS diff FROM BOOK_LOANS WHERE date_in IS NULL AND date_add(curdate(),interval $dayelapse day) > due_date AND loan_id NOT IN (SELECT loan_id FROM FINES);";
		$update = mysqli_query($mysql,$query);
		while($tuple = mysqli_fetch_array($update)){
			$loan_id = $tuple['loan_id'];
			$diff = $tuple['diff'];
			$insertFines = "INSERT INTO FINES VALUES ($loan_id,$diff*0.25,0);";
			if (!mysqli_query($mysql,$insertFines)) {
				$error .= $insertFines.mysqli_error($mysql)."<br/>";
			}

		}

		$card_id = $_POST['card_id'];
		$query = "SELECT SUM(FINES.fine_amt) AS fine FROM FINES JOIN BOOK_LOANS ON FINES.loan_id=BOOK_LOANS.loan_id WHERE FINES.paid = 0 AND BOOK_LOANS.card_id = '".$card_id."' GROUP BY BOOK_LOANS.card_id;";
		$group = mysqli_query($mysql,$query);
		$result = "<table><tr><th>card ID</th><th>total fine</th></tr>";
		$tuple = mysqli_fetch_array($group);
		$result .= "<tr>";
		$result .= "<td>".$card_id."</td>";
		$result .= "<td name='total'>".$tuple['fine']."</td>";
		$result .= "</tr></table>";

		$query = "SELECT BOOK_LOANS.loan_id,BOOK_LOANS.date_out,BOOK_LOANS.due_date,BOOK_LOANS.date_in,FINES.fine_amt FROM BOOK_LOANS JOIN FINES ON BOOK_LOANS.loan_id=FINES.loan_id WHERE FINES.paid = 0 AND BOOK_LOANS.card_id = $card_id;";
		$result .= "<br/><table><tr><th>loan ID</th><th>date out</th><th>due date</th><th>date in</th><th>fine</th><th>pay</th></tr>";
		$fines = mysqli_query($mysql,$query);
		$num = mysqli_num_rows($fines);
		while($tuple = mysqli_fetch_array($fines)){
			$result .= "<tr>";
			$result .= "<td>".$tuple['loan_id']."</td>";
			$result .= "<td>".$tuple['date_out']."</td>";
			$result .= "<td>".$tuple['due_date']."</td>";
			$result .= "<td>".$tuple['date_in']."</td>";
			$result .= "<td>".$tuple['fine_amt']."</td>";
			if($tuple['date_in'] == ""){
				$result .= "<td><input type='radio' name='pay' value='".$tuple['loan_id']."' disabled = 'true'></td>";
			}else{
				$result .= "<td><input type='radio' name='pay' value='".$tuple['loan_id']."'></td>";
			}
			$result .= "</tr>";
		}
		$result .= "</table>";
		$date = mysqli_query($mysql,"SELECT date_add(curdate(),interval $dayelapse day) AS today;");
		$date = mysqli_fetch_array($date);
		$date = $date['today'];
		if($error == ""){
			$error = "Current date ".$date.": ".$num." record for card ID ".$card_id." found.";
		}


	}

	if($_POST['menu'] == 'pay'){
		$loan_id = $_POST['loan_id'];
		$query = "UPDATE FINES SET paid = 1 WHERE loan_id = $loan_id;";
		if (!mysqli_query($mysql,$query)) {
			$error .= $query.mysqli_error($mysql)."<br/>";
		}	
		$query = "SELECT BOOK_LOANS.loan_id,BOOK_LOANS.date_out,BOOK_LOANS.due_date,BOOK_LOANS.date_in,FINES.fine_amt,FINES.paid FROM BOOK_LOANS JOIN FINES ON BOOK_LOANS.loan_id=FINES.loan_id WHERE BOOK_LOANS.loan_id = $loan_id;";

		$paid = mysqli_query($mysql,$query);
		$tuple = mysqli_fetch_array($paid);
		if($error == ""){
			$error = "Success paid: <br/>";
			$error .= "<table><tr><th>loan ID</th><th>date out</th><th>due date</th><th>date in</th><th>fine</th><th>paid</th></tr>";
			$error .= "<tr><td>".$tuple['loan_id']."</td>";
			$error .= "<td>".$tuple['date_out']."</td>";
			$error .= "<td>".$tuple['due_date']."</td>";
			$error .= "<td>".$tuple['date_in']."</td>";
			$error .= "<td>".$tuple['fine_amt']."</td>";
			$error .= "<td>".$tuple['paid']."</td></tr></table>";
		}	
	}



	$message = array('error'=>$error,'result'=>$result);
	echo json_encode($message);
?>