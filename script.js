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

	$("button[value=loadData]").click(function(){
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

	$("#booksearch button[value=search]").click(function(){
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






	$("button[value=test]").click(function(){
		$("#testdiv").text("success");
	});
});




