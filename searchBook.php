<?php
	$error = "";
	$result = "";
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		$error .= "Failed to connect to MySQL: ".mysqli_connect_error()."<br/>";
	}
// 0393045218*The Mummies Of Urumchi*Elizabeth Wayland Barber*E. J. W. Barber     000001,850-47-3740

// insert into book_loans values(1,'0393045218','000001',curdate(),date_add(curdate(),interval 14 day),null);
// select * from book,book_authors,authors,borrower,book_loans where book.isbn=book_authors.isbn and authors.author_id=book_authors.author_id and book_loans.isbn = book.isbn and book_loans.card_id=borrower.card_id;

	$keys = mysqli_real_escape_string($mysql,$_POST['data']);
	$keys = explode("*",$keys);

	$query = "SELECT DISTINCT book.isbn, book.title FROM (BOOK_AUTHORS JOIN BOOK ON BOOK_AUTHORS.isbn=BOOK.isbn) 
	          JOIN AUTHORS ON BOOK_AUTHORS.author_id=AUTHORS.author_id WHERE ";
	
	for($i=0;$i<sizeof($keys);$i++){
		$key = trim($keys[$i]);
		$query .= "BOOK.isbn LIKE '%$key%' OR ";
		$query .= "BOOK.title LIKE '%$key%' OR ";
		$query .= "AUTHORS.name LIKE '%$key%'";
		if($i != sizeof($keys)-1){
			$query .= " OR ";
		}
	}
	$query .= ";";
	$result = "<table><tr><th>isbn</th><th>title</th><th>authors</th><th>availability</th></tr>";

	$book = mysqli_query($mysql,$query);

	while($tuple = mysqli_fetch_array($book)){
		$result .= "<tr>";
		$result .= "<td>".$tuple['isbn']."</td>";
		$result .= "<td>".$tuple['title']."</td>";
		$result .= "<td>";
		$query = "SELECT DISTINCT AUTHORS.name FROM (BOOK_AUTHORS JOIN BOOK ON BOOK_AUTHORS.isbn=BOOK.isbn) 
	          JOIN AUTHORS ON BOOK_AUTHORS.author_id=AUTHORS.author_id WHERE BOOK.isbn = '".$tuple['isbn']."';";
	    $authors = mysqli_query($mysql,$query);
	    while($author = mysqli_fetch_array($authors)){
	    	$result .= $author['name'].", ";
	    }
	    $result = rtrim($result,', ');
	    $result .= "</td>";

	    $query = "SELECT * FROM BOOK JOIN BOOK_LOANS ON BOOK.isbn=BOOK_LOANS.isbn WHERE BOOK_LOANS.date_in IS NULL 
	              AND BOOK.isbn = '".$tuple['isbn']."';";
	    $num = mysqli_query($mysql,$query);
	    if(mysqli_num_rows($num) == 0){
	    	$result .= "<td>&#10003</td>";
	    }else{
	    	$result .= "<td>&#10005</td>";
	    }
	    $result .= "</tr>";
	}
	$result .= "</table>";

	$error = mysqli_num_rows($book)." book(s) found.";
	$message = array('error'=>$error,'result'=>$result);
	echo json_encode($message);
?>