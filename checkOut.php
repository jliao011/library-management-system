<?php
	$message = "Error: ";
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		$message .= "Failed to connect to MySQL: ".mysqli_connect_error()."<br/>";
	}

	$isbn = mysqli_real_escape_string($mysql,$_POST['isbn']);
	$card_id = mysqli_real_escape_string($mysql,$_POST['card_id']);

	$queryNo = "SELECT * FROM BOOK_LOANS WHERE card_id = '$card_id' AND date_in IS NULL;";
	$queryAvailable = "SELECT * FROM BOOK JOIN BOOK_LOANS ON BOOK.isbn=BOOK_LOANS.isbn WHERE BOOK_LOANS.date_in IS NULL 
	              AND BOOK.isbn = '".$isbn."';";
	$number = mysqli_query($mysql,$queryNo);
	$available = mysqli_query($mysql,$queryAvailable);
	if(mysqli_num_rows($number) >= 3){
		$message .= "Each borrower is permitted a max of 3 books.";
	}elseif(mysqli_num_rows($available) != 0){
		$message .= "This book is not available now.";
	}else{
		$queryID = "SELECT COUNT(*) AS num FROM BOOK_LOANS;";
		$result = mysqli_query($mysql,$queryID);
		$result = mysqli_fetch_array($result);
		$loan_id = $result['num'] + 1;
		$date_out = 'curdate()';
		$due_date = 'date_add(curdate(),interval 14 day)';
		$date_in = 'NULL';
		$query = "INSERT INTO BOOK_LOANS VALUES ($loan_id,'".$isbn."','".$card_id."',$date_out,$due_date,$date_in);";
		if (!mysqli_query($mysql,$query)) {
			$message .= $query.mysqli_error($mysql);
		}else{
			$queryDate = "SELECT * FROM BOOK_LOANS WHERE loan_id = '$loan_id';";
			$date = mysqli_query($mysql,$queryDate);
			$date = mysqli_fetch_array($date);
			$message = "Success check out, detail information as follow: <br/>";
			$message .= "<table>";
			$message .= "<tr><th>loan_id</th><th>isbn</th><th>card_id</th><th>date_out</th><th>due_date</th></tr>";
			$message .= "<tr><td>".$loan_id."</td><td>".$isbn."</td><td>".$card_id."</td><td>".$date['date_out']."</td><td>".$date['due_date']."</td></tr>";
		}
	}


	echo $message;
?>