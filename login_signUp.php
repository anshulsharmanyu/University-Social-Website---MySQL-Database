<?php
require 'db_config/db_config.php';
require 'included_files/handlers/login_handler.php';
require 'included_files/handlers/signUp_handler.php';
?>
<html>
<head>
	<title>UCoN</title>
	<link rel="stylesheet" type="text/css" href="support/css/register_style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="support/js/register.js"></script>
</head>
<body>
	<div class="wrapper">
		<div class="login_box">
			<div class="login_header">
				<h1>UCoN</h1>
			</div>
			<br>
			<div id="first">
				<form action="login_signUp.php" method="POST">
					<input type="email" name="log_email" placeholder="Email Address" value="
					<?php
					if(isset($_SESSION['log_email'])) {
						echo $_SESSION['log_email'];
					}
					?>" required>
					<br>
					<input type="password" name="log_password" placeholder="Password">
					<br>
					<?php if(in_array("Incorrect credentials or Inactive user<br>", $errors)) echo  "Incorrect credentials or Inactive user<br>"; ?>
					<input type="submit" name="login_button" value="Login">
					<br>
					<a href="#" id="signup" class="signup">New here? Register!</a>
				</form>
			</div>

			<div id="second">
				<form action="login_signUp.php" method="POST">
					<input type="text" name="first_name" placeholder="First Name" style="text-align:left;"value="
					<?php
					if(isset($_SESSION['first_name'])) {
						echo $_SESSION['first_name'];
					}
					?>" required>
					<!-- checking the parameters -->
					<br>
					<?php if(in_array("Must be between 2 to 15 Characters<br>", $errors)) echo "Your first name must be between 2 and 15 characters<br>"; ?>
					<input type="text" name="last_name" placeholder="Last Name" value="
					<?php
					if(isset($_SESSION['last_name'])) {
						echo $_SESSION['last_name'];
					}
					?>" required>
					<br>
					<?php if(in_array("Must be between 2 to 15 Characters<br>", $errors)) echo "Your first name must be between 2 and 15 characters<br>"; ?>

					<input type="text" name="user_name" placeholder="User Name" value="
					<?php
					// if session user name is set
					if(isset($_SESSION['user_name'])) {
						echo $_SESSION['user_name'];
					}
					?>" required>
					<br>
					<input id="date" name="dob" type="date" placeholder="DOB" style="width:70%;height:4%"><br>
					<?php if(in_array("Must be between 2 to 35 Characters<br>", $errors)) echo "Your last name must be between 2 and 30 characters<br>";
					else if(in_array("Username exist<br>", $errors)) echo "Username exist - Try Another<br>";?>

					<input type="email" name="email_id" placeholder="Email" value="
					<?php
					if(isset($_SESSION['email_id'])) {
						echo $_SESSION['email_id'];
					}
					?>" required>
					<br>
					<!-- if the email id already exists -->
					<?php if(in_array("Email already in use<br>", $errors)) echo "Email already in use<br>";
					else if(in_array("Invalid email format<br>", $errors)) echo "Invalid email format<br>";
					?>

					<input type="password" name="password_first" placeholder="Password" required>
					<br>
					<input type="password" name="password_second" placeholder="Confirm Password" required>
					<br>
					<!-- if present in array -->
					<?php if(in_array("Your passwords do not match<br>", $errors)) echo "Your passwords do not match<br>";
					else if(in_array("Your password can only contain english characters or numbers<br>", $errors)) echo "Your password can only contain english characters or numbers<br>";
					else if(in_array("Your password must be betwen 8 and 30 characters<br>", $errors)) echo "Your password must be betwen 8 and 30 characters<br>"; ?>

					<!-- if clicking on register button -->

					<input type="submit" name="register_button" value="Register">
					<br>
					<?php if(in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $errors)) echo "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>"; ?>

					<a href="#" id="signin" class="signin">Already Member? Log-In</a>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
