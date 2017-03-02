$("document").ready(function(){

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
				if($(this).attr('id') == li_class){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
		}
	});

	$("button[value=loadData]").click(function(){
		loadData();
	});

	$("#booksearch button[value=search]").click(function(){
		var content = $("#booksearch input").val();
		$("#booksearch .message").append(content);
		searchBook();
	});






	$("button[value=test]").click(function(){
		$("#testdiv").text("success");
	});
});

function loadData(){
	$.ajax({
		url:"loadData.php",
		success: function(response){
			$("#home .message").append(response);
		},
		error: function(){
			alert("Error: books not loaded.");
		}
	});
}

function searchBook(){
	$.ajax({
		url:"searchBook.php",
		success: function(response){
			$("#booksearch .message").append(response);
		},
		error: function(){
			alert("Error: books not loaded.");
		}
	});
}


