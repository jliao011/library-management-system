<?php
	$mysql = mysqli_connect("localhost:8889","root","root","Library");
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: ".mysqli_connect_error();
	}



	$author_id = 0;
	$books = fopen("books.csv","r");
	$data = fgetcsv($books, 1000, "	");
	while (($data = fgetcsv($books, 1000, "	")) !== FALSE) {
		$isbn = mysqli_real_escape_string($mysql,trim($data[0]));
		$title = mysqli_real_escape_string($mysql,trim($data[2]));
		$authors = explode(",",trim($data[3]));

		$insert_book = "INSERT INTO BOOK VALUES ('$isbn','$title');";
		if (!mysqli_query($mysql,$insert_book)) {
		    echo "Error: ".$insert_book.mysqli_error($mysql)."\n";
		}

		// echo $isbn."<br>".$title."<br>";
		foreach ($authors as $author) {
			$author = mysqli_real_escape_string($mysql,trim($author));
			if($author == "" || strcasecmp($author, "(none)") == 0){continue;}
			$check_author = "SELECT * FROM AUTHORS WHERE name='$author';";
			$result = mysqli_query($mysql,$check_author);
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_array($result);
				$insert_book_authors = "INSERT IGNORE INTO BOOK_AUTHORS VALUES ($row[0],'$isbn');";
				if (!mysqli_query($mysql,$insert_book_authors)) {
					echo "Error: ".$insert_book_authors.mysqli_error($mysql)."\n";
				}	
			}else{
				$insert_author = "INSERT INTO AUTHORS VALUES ('$author_id','$author');";
				if (!mysqli_query($mysql,$insert_author)) {
					echo "Error: ".$insert_author.mysqli_error($mysql)."\n";
				}
				$insert_book_authors = "INSERT INTO BOOK_AUTHORS VALUES ('$author_id','$isbn');";
				if (!mysqli_query($mysql,$insert_book_authors)) {
					echo "Error: ".$insert_book_authors.mysqli_error($mysql)."\n";
				}	
				$author_id++;
			}

			// echo mysqli_real_escape_string($mysql,$author)."<br>";
		}

	}
	echo "Load books and borrowers successfully!"


?>