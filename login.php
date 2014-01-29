<!doctype html>
<html>
<head>
<meta charset="utf-8"> 
<style type="text/css">

body {
	font-family: "Lucida Sans Unicode","Lucida Grande",sans-serif;
	color: #444444;
	font-size: 13px;
}

#login-container {
	width: 350px;
	margin: 50px auto 0 auto
}

.login-element {
	overflow: hidden;
	margin: 0 0 30px 0;
}

.login-element label {
	float: left;
	width: 150px;
	cursor: pointer;
}

.login-element input {
	float: left;
	width: 200px;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	border: 1px solid #cccccc;
	padding: 4px;
	font-size: inherit;
	font-family: inherit;
}

#login-button {
	width: 100px;
	margin: 0 0 0 150px;
	background-color: #139DE8;
	color: white;
	padding: 5px;
	cursor: pointer;
	text-align: center;
}

#error {
	margin: 30px 0 0 0;
	background-color: #FF717E;
	color: white;
	padding: 5px;
	display: none;
}

</style>
<script type="text/javascript" src="js/lib/jquery-2.0.3.min.js"></script>
</head>

<body>

<div id="login-container">
	<div class="login-element">
		<label for="username">Username</label>
		<input type="text" id="username" />
	</div>
	<div class="login-element">
		<label for="password">Password</label>
		<input type="password" id="password" />
	</div>
	<div id="login-button" data-in-progress="0">Login</div>
	<div id="error">Error</div>
</div>

<script type="text/javascript">

$("#proceed").on('click', function() { 
	$("#error").hide();
	$("#login-button").css('opacity', '0.5').text('Logging ..').attr('data-in-progress', '1');

	$.ajax({
			type: 'GET',
			cache: false;
			url: 'controller.php',
			data: { command: 'Login' },
			dataType: 'json',
			success: function (response) {
				if(response.error == 0) {
					$("#login-button").css('opacity', '1').text('Login').attr('data-in-progress', '0');
					document.location = '/'
				}
				else {
					$("#error").html('Login failed').show();
				}
			}
		});
});

</script>

</body>
</html>