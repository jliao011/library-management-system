1. install MAMP for MAC and start service:
	set Apache Port: 8888;
	set MySQL Port: 8889;
	select php version: 7.0.15;
	select Apache as Web Server;
	Set Document Root as library-management-system/;

2. create library database:
	open WebStart page in MAMP;
	select Tools -> phpMyAdmin;
	select import and upload create_library.sql file;
	MySQL connection parameters:
		host: localhost;
		port: 8889;
		user: root;
		password: root;

3. insert initial book and borrower tuples:
	click My Website in the navigation bar on WebStart page;
	open homepage.html to start; (this is the application interface)
	click home in the navigation bar;
	click load data button in the window;(do this only once)

4. search books:
	click book search in the navigation bar;
	input ISBN, title, and/ or Author(s) separated by '*';
	click search button;

5. book loans:
	click book loans in the navigation bar:
	check out:
		click check out a book in the menu;
		input book isbn and card id;(both not null)
		click submit;
	check in:
		click check in a book in the menu;
		input ISBN, card ID, and/ or borrower name separated by '*' then click search button;
		check one box in select column to select a record to check in;
		click add days button to add days to current date as date_in value;
		click check in button to update a tuple in BOOK_LOANS;

6. pay fines:
	lick fines in the navigation bar;
	input card id;
	click add days button to add days to current date;
	click refresh button to show results based on changing dates;
	check one box in pay column to select a record to pay;
	click pay fines button to update a tuple in FINES;
	whenever there is a date roll back because of day elapse function when pay fines or check in books, 
		click reset button then click refresh button to reload tuples in FINES to avoid mistake tuples;

7. clear input, message and result:
	click clear page button between the header and main window;

8. rebuild the library database:
	repeat step 2 and 3;

