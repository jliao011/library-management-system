$("document").ready(function(){
$("button").click(function(){
	loadData();
});









});
function loadData(){
	alert("link script");
	$.ajax({
		url:"loadData.php",

		success: function(response){
			$("#booklist").append(response);
		},
		error: function(){
			alert("error");
		}
	});

}


