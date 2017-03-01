DROP DATABASE IF EXISTS Library;
CREATE DATABASE Library;

USE Library;

DROP TABLE IF EXISTS BOOK;
CREATE TABLE BOOK (
    isbn        varchar(13) not null,
    title       varchar(40) not null,
    CONSTRAINT pk_book PRIMARY KEY (isbn),
    CONSTRAINT uk_isbn UNIQUE (isbn)
);

DROP TABLE IF EXISTS AUTHORS;
CREATE TABLE AUTHORS(
    author_id   varchar(10) not null,
    name        varchar(20) not null,
    CONSTRAINT pk_authors PRIMARY KEY (author_id)
);

DROP TABLE IF EXISTS BOOK_AUTHORS;
CREATE TABLE BOOK_AUTHORS(
    author_id   varchar(10) not null,
    isbn        varchar(13) not null,
    CONSTRAINT pk_book_authors PRIMARY KEY (author_id,isbn),
    CONSTRAINT fk_book_authors_authors FOREIGN KEY (author_id) REFERENCES AUTHORS(author_id),
    CONSTRAINT fk_book_authors_book FOREIGN KEY (isbn) REFERENCES BOOK(isbn)
);

DROP TABLE IF EXISTS BORROWER;
CREATE TABLE BORROWER(
    card_id     varchar(6) not null,
    ssn         varchar(11) not null,
    bname       varchar(20) not null,
    address     varchar(40),
    phone       varchar(15),
    CONSTRAINT pk_borrower PRIMARY KEY (card_id)
);

DROP TABLE IF EXISTS BOOK_LOANS;
CREATE TABLE BOOK_LOANS(
    loan_id     varchar(10) not null,
    isbn        varchar(13) not null,
    card_id     varchar(6),
    date_out    date not null,
    due_date    date not null,
    date_in     date,
    CONSTRAINT pk_book_loans PRIMARY KEY (loan_id),
    CONSTRAINT fk_book_loans_book FOREIGN KEY (isbn) REFERENCES BOOK(isbn),
    CONSTRAINT fk_book_loans_borrower FOREIGN KEY (card_id) REFERENCES BORROWER(card_id)
);

DROP TABLE IF EXISTS FINES;
CREATE TABLE FINES(
    loan_id     varchar(10) not null,
    fine_amt    float(10,2),
    paid        int,
    CONSTRAINT pk_fines PRIMARY KEY (loan_id),
    CONSTRAINT fk_fines_book_loans FOREIGN KEY (loan_id) REFERENCES BOOK_LOANS(loan_id)
);





