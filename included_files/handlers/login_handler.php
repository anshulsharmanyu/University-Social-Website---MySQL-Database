<?php
$errors = array();
if(isset($_POST['login_button'])) {

	$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);
	$_SESSION['log_email'] = $email;
	$password = ($_POST['log_password']);
	$check_database_query = mysqli_query($conn, "SELECT * FROM User WHERE userEmail='$email' AND userPassword='$password' AND stat = 'ACTIVE'");
	if(mysqli_num_rows($check_database_query) == 1) {
		$row = mysqli_fetch_array($check_database_query);
		$_SESSION['userID'] = $row['userID'];
		$_SESSION['username'] = $row['userName'];
		header("Location: index.php");
		exit();
	}
	else{
		array_push($errors,"Incorrect credentials or Inactive user<br>");
		// $abc="Incorrect credentials or Inactive user<br>";
		// array_push="Incorrect credentials or Inactive user<br>";
	}


}
?>
