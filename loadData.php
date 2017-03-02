<?php
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: ".mysqli_connect_error()."<br/>";
	}
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	$author_id = 0;
	$books = fopen("books.csv","r");
	$data = fgetcsv($books, 1000, "	");
	while (($data = fgetcsv($books, 1000, "	")) !== FALSE) {
		$isbn = mysqli_real_escape_string($mysql,trim($data[0]));
		$title = mysqli_real_escape_string($mysql,trim($data[2]));
		$authors = trim($data[3]);

		$insert_book = "INSERT INTO BOOK VALUES ('$isbn','$title');";
		if (!mysqli_query($mysql,$insert_book)) {
			echo "Error: ".$insert_book.mysqli_error($mysql)."<br/>";
		}

		if (strpos($authors,'&amp;') !== false) {
			$authors = explode("&amp;",$authors);	
		}elseif (strpos($authors,';') !== false) {
			$authors = explode(";",$authors);
		}else{
			$authors = explode(",",$authors);
		}

		foreach ($authors as $author) {
			$author = mysqli_real_escape_string($mysql,trim($author));
			if($author == "" || strcasecmp($author, "(none)") == 0){continue;}
			$check_author = "SELECT * FROM AUTHORS WHERE name='$author';";
			$result = mysqli_query($mysql,$check_author);
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_array($result);
				$insert_book_authors = "INSERT IGNORE INTO BOOK_AUTHORS VALUES ($row[0],'$isbn');";
				if (!mysqli_query($mysql,$insert_book_authors)) {
					echo "Error: ".$insert_book_authors.mysqli_error($mysql)."<br/>";
				}	
			}else{
				$insert_author = "INSERT INTO AUTHORS VALUES ('$author_id','$author');";
				if (!mysqli_query($mysql,$insert_author)) {
					echo "Error: ".$insert_author.mysqli_error($mysql)."<br/>";
				}
				$insert_book_authors = "INSERT INTO BOOK_AUTHORS VALUES ('$author_id','$isbn');";
				if (!mysqli_query($mysql,$insert_book_authors)) {
					echo "Error: ".$insert_book_authors.mysqli_error($mysql)."<br/>";
				}	
				$author_id++;
			}
		}
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	$borrowers = fopen("borrowers.csv","r");
	$data = fgetcsv($borrowers, 1000, ",");
	while (($data = fgetcsv($borrowers, 1000, ",")) !== FALSE) {
		$card_id = mysqli_real_escape_string($mysql,trim($data[0]));
		$ssn = mysqli_real_escape_string($mysql,trim($data[1]));
		$bname = mysqli_real_escape_string($mysql,trim($data[2].", ".$data[3]));
		$address = mysqli_real_escape_string($mysql,trim($data[5].", ".$data[6].", ".$data[7]));
		$phone = mysqli_real_escape_string($mysql,trim($data[8]));
		$insert_borrower = "INSERT INTO BORROWER VALUES ('$card_id','$ssn','$bname','$address','phone');";
		if (!mysqli_query($mysql,$insert_borrower)) {
			echo "Error: ".$insert_borrower.mysqli_error($mysql)."<br/>";
		}
	}

	$count_book = "SELECT COUNT(*) AS count FROM BOOK;";
	$result = mysqli_query($mysql,$count_book);
	$number = mysqli_fetch_array($result);
	echo $number['count']." row(s) inserted to BOOK.<br/>";
	
	$count_borrower = "SELECT COUNT(*) AS count FROM BORROWER;";
	$result = mysqli_query($mysql,$count_borrower);
	$number = mysqli_fetch_array($result);
	echo $number['count']." row(s) inserted to BORROWER.<br/>";

?>