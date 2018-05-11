$(document).ready(function() {
	//To show the registration form
	$("#signup").click(function() {
		$("#first").fadeOut("slow", function(){
			$("#second").fadeIn("slow");
		});
	});
	//To show the login form
	$("#signin").click(function() {
		$("#second").fadeOut("slow", function(){
			$("#first").fadeIn("slow");
		});
	});
});
