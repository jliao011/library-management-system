$("document").ready(function(){
	$("div.main").each(function(){$(this).hide();});
	$("#home").show();
	$("header li").on({
		mouseenter: function(){
			$(this).css({"background-color":"black","color":"white"});
		},
		mouseleave: function(){
			$(this).css({"background-color":"white","color":"black"});
		},
		click: function(){
			var li_class = $(this).attr('class');
			$("div.main").each(function(){
				$("div.message").text("");
				$("div.result").text("");
				$("div.main input").val("");
				if($(this).attr('id') == li_class){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
		}
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




	$("button.test").click(function(){
		$("#testdiv").text("success");
	});
});




