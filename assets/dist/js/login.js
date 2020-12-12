$(document).ready(function(){
	$("form#loginform").on("submit", function(e){
		e.preventDefault();
		$("div.login-error-container").hide();
		var username = $("input#username").val();
		var password = $("input#password").val();

		var d = {
			"username" : username,
			"password" : password
		};

		$.ajax({
			method: "POST",
			url: baseurl+"index.php/login",
			data: d,
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					window.location = baseurl;
				}else{
					$("div.login-error-container").fadeIn("slow");
				}
			}
		});
	});
});
