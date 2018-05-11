<?php
if(isset($_POST['update_details'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	$interests = $_POST['Interests'];
	$address = $_POST['address'];

	$email_check = mysqli_query($conn, "SELECT * FROM User WHERE userEmail='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['userID'];

	if($matched_user == "" || $matched_user == $loggedInUsedID) {
		$message = "Updated";

		$query = mysqli_query($conn, "UPDATE User SET firstName='$first_name', lastName='$last_name', userEmail='$email',Interests = '$interests',Address = '$address' WHERE userID='$loggedInUsedID'");
	}
	else
		$message = "That email in use!<br><br>";
}
else
	$message = "";


//**************************************************

if(isset($_POST['update_password'])) {

	$old_password = strip_tags($_POST['old_password']);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$password_query = mysqli_query($conn, "SELECT userPassword FROM User WHERE userID='$loggedInUsedID'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['userPassword'];

	if(($old_password) == $db_password) {

		if($new_password_1 == $new_password_2) {


			if(strlen($new_password_1) <= 4) {
				$password_message = "Sorry, your password must be greater than 4 characters<br><br>";
			}
			else {
				$new_password_md5 = ($new_password_1);
				$password_query = mysqli_query($conn, "UPDATE User SET userPassword='$new_password_md5' WHERE userID='$loggedInUsedID'");
				$password_message = "Password has been changed!<br><br>";
			}


		}
		else {
			$password_message = "Your two new passwords need to match!<br><br>";
		}

	}
	else {
			$password_message = "The old password is incorrect! <br><br>";
	}

}
else {
	$password_message = "";
}


/*if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
	}*/


?>
