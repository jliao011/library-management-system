$("document").ready(function(){
	$("button").click(function(){
		loadData();
	});

});

function loadData(){
	$.ajax({
		url:"loadData.php",
		success: function(response){
			$("#message").append(response);
		},
		error: function(){
			alert("Error: books not loaded.");
		}
	});
}


