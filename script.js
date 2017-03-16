$("document").ready(function(){
	$("div.main").each(function(){$(this).hide();});
	$("#home").show();
	$(".manu li").on({
		mouseenter: function(){
			$(this).css({"background-color":"black","color":"white"});
		},
		mouseleave: function(){
			$(this).css({"background-color":"white","color":"black"});
		}

	});

	$("header li").click(function(){
		var li_class = $(this).attr('class');
		$("div.main").each(function(){
			if($(this).attr('id') == li_class){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	});

	$("#home button.submit").click(function(){
		$.ajax({
			url:"loadData.php",
			success: function(response){
				$("#home .message").append(response);
			},
			error: function(){
				alert("Error: books not loaded.");
			}
		});
	});

	$("#booksearch button.submit").click(function(){
		var content = $("#booksearch input").val();
		if(content != ""){
			$.ajax({
				url:"searchBook.php",
				data: {data:content},
				dataType: "json",
				method: "post",
				success: function(response){
					$("#booksearch .message").html(response.error);
					$("#booksearch .result").html(response.result);
				},
				error: function(){
					alert("Error: books not loaded.");
				}
			});			
		}
	});

	$("#borrower button.submit").click(function(){
		var firstname = $("#borrower input[name='firstname']").val().trim();
		var lastname = $("#borrower input[name='lastname']").val().trim();
		var ssn = $("#borrower input[name='ssn']").val().trim();
		var street = $("#borrower input[name='street']").val().trim();
		var city = $("#borrower input[name='city']").val().trim();
		var state = $("#borrower input[name='state']").val().trim();
		var phone = $("#borrower input[name='phone']").val().trim();
		var error = "Error: ";
		if(firstname == "" || lastname == ""){
			error += "Enter your full name. ";
		}
		if(ssn == ""){
			error += "Enter your ssn. ";
		}else if(!ssn.match(/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/)){
			error += "Enter your ssn as XXX-XX-XXXX. "
		}
		if(street == "" || city == "" || state == ""){
			error += "Enter your full address. "
		}
		if(phone != "" && !phone.match(/^\([0-9]{3}\)\s[0-9]{3}-[0-9]{4}$/)){
			error += "Enter your phone as (XXX) XXX-XXXX. ";
		}else if(phone == ""){
			phone = "NULL";
		}
		if(error != "Error: "){
			$("#borrower .message").html(error);
		}else{
			var bname = firstname + ", " + lastname;
			var address = street + ", " + city + ", " + state;
			$.ajax({
				url:"createBorrower.php",
				data: {"ssn":ssn,"bname":bname,"address":address,"phone":phone},
				method: "post",
				success: function(response){
					$("#borrower .message").html(response);
				},
				error: function(){
					alert("Error: cannot link createBorrower.php.");
				}
			});	
		}

	});


	$("div.checkin").hide();
	var checkinMess = "", checkoutMess = "";
	$("li.checkout").click(function(){
		$("div.checkout").show();
		$("div.checkin").hide();
		$("#bookloans .message").html(checkoutMess);
	});
	$("li.checkin").click(function(){
		$("div.checkin").show();
		$("div.checkout").hide();
		$("#bookloans .message").html(checkinMess);
	});

	$("#bookloans .checkout button.submit").click(function(){
		var isbn = $("#bookloans input[name='isbn']").val().trim();
		var card_id = $("#bookloans input[name='card_id']").val().trim();
		if(isbn == "" || card_id == ""){
			$("#bookloans .message").html("Error: Please input isbn and card_id");
		}else{
			$.ajax({
				url:"checkOut.php",
				data: {"isbn":isbn,"card_id":card_id},
				method: "post",
				success: function(response){
					$("#bookloans .message").html(response);
					checkoutMess = response;
				},
				error: function(){
					alert("Error: cannot link checkOut.php.");
				}
			});				
		}
	});


	$("#bookloans .checkin button[value='search']").click(function(){
		var content = $("#bookloans input[name='searchbar']").val().trim();
		if(content != ""){
			$.ajax({
				url:"checkIn.php",
				data: {'data':content,'select':""},
				method: "post",
				dataType: "json",
				success: function(response){
					$("#bookloans .message").html(response.error);
					$("#bookloans .result").html(response.result);
					checkinMess = response.error;
				},
				error: function(){
					alert("Error: cannot link checkIn.php.");
				}
			});
		}
	});

	$("#bookloans button[value='dayelapse']").click(function(){
		var dayelapse = $("#bookloans input[name='dayelapse']").val();
		dayelapse ++;
		$("#bookloans input[name='dayelapse']").val(dayelapse);
	});

	$("#bookloans .checkin button[value='checkin']").click(function(){
		var select = 0;
		$("#bookloans .result input[type='radio']").each(function(){
			if($(this).prop("checked")){
				select = $(this).val();
				$(this).parent().parent().remove();
			}
		});
		if(select != 0){
			$.ajax({
				url:"checkIn.php",
				data: {'data':"",'select':select,'dayelapse':$("#bookloans input[name='dayelapse']").val()},
				method: "post",
				dataType: "json",
				success: function(response){
					$("#bookloans .message").html(response.error);
					checkinMess = response.error;
					$("#bookloans input[name='dayelapse']").val(0);
				},
				error: function(){
					alert("Error: cannot link checkIn.php.");
				}				
			});
		}

	});

	$("#fines button[value='dayelapse']").click(function(){
		var dayelapse = $("#fines input[name='dayelapse']").val();
		dayelapse ++;
		$("#fines input[name='dayelapse']").val(dayelapse);
	});

	$("#fines button[value='reset']").click(function(){
		$("#fines input[name='dayelapse']").val("");
		$("#fines .result").html("");
		$.ajax({
			url:"fines.php",
			data: {'menu':'reset'},
			method: "post",
			dataType: 'json',
			success: function(response){
				$("#fines .message").html(response.error);
			},
			error: function(){
				alert("Error: cannot link fines.php.");
			}				
		});


	});

	$("#fines button[value='refresh']").click(function(){
		var dayelapse = $("#fines input[name='dayelapse']").val();
		if(dayelapse == ""){dayelapse = 0;}
		var card_id = $("#fines input[name='card_id']").val().trim();
		if(card_id != ""){
			$.ajax({
				url:"fines.php",
				data: {'dayelapse':dayelapse,'menu':'refresh','card_id':card_id},
				method: "post",
				dataType: 'json',
				success: function(response){
					$("#fines .message").html(response.error);
					$("#fines .result").html(response.result);
				},
				error: function(){
					alert("Error: cannot link fines.php.");
				}				
			});			
		}

	});

	$("#fines button[value='pay']").click(function(){
		var select = 0;
		var paid = 0;
		$("#fines .result input[type='radio']").each(function(){
			if($(this).prop("checked")){
				select = $(this).val();
				paid = $(this).parent().prev().text();
				$(this).parent().parent().hide();
			}
		});
		var total = $("#fines .result td[name = 'total']").text();
		$("#fines .result td[name = 'total']").text((total-paid).toFixed(2));
		
		if(select != 0){
			$.ajax({
				url:"fines.php",
				data: {'menu':'pay','loan_id':select},
				method: "post",
				dataType: "json",
				success: function(response){
					$("#fines .message").html(response.error);
				},
				error: function(){
					alert("Error: cannot link checkIn.php.");
				}				
			});
		}		
	});

	$("button.clear").click(function(){
		$("div.message").text("");
		$("div.result").text("");
		$("div.main input").val("");
	});
});




