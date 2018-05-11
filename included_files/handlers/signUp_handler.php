<?php
//Initial Declaration
$first_Name=$last_Name=$user_Name=$email=$password=$matchPassword = "";
$errors = array(); //to store any error
if(isset($_POST['register_button'])){
	# Replacing HTML tags, extra spaces, and making lower case before putting in session
	$first_Name = ucfirst(strtolower(str_replace(' ', '', strip_tags($_POST['first_name']))));
	$_SESSION['first_name'] = $first_Name;
	$last_Name = ucfirst(strtolower(str_replace(' ', '', strip_tags($_POST['last_name']))));
	$_SESSION['last_name'] = $last_Name;
	$user_Name = ucfirst(strtolower(str_replace(' ', '', strip_tags($_POST['user_name']))));
	$_SESSION['user_name'] = $user_Name;
	$email = ucfirst(strtolower(str_replace(' ', '', strip_tags($_POST['email_id']))));
	$_SESSION['email_id'] = $email;
	$password = strip_tags($_POST['password_first']);
	$matchPassword = strip_tags($_POST['password_second']);
	$dob = $_POST['dob'];
	//$date = date("Y-m-d"); //Current date
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		//Check existing email id
		$email_check = mysqli_query($conn, "SELECT userEmail FROM User WHERE userEmail='$email'");
		if(mysqli_num_rows($email_check) > 0) {
			array_push($errors, "Email already in use<br>");
		}
	}
	else {
		array_push($errors, "Invalid email format<br>");
	}
	$check_username_query = mysqli_query($conn, "SELECT userName FROM User WHERE userName='$user_Name'");
	if(mysqli_num_rows($check_username_query) != 0){
		array_push($errors, "Username exist<br>");
	}
	if(! in_array(strlen($first_Name), range(2,15))){
		array_push($errors, "Must be between 2 to 15 Characters<br>");
	}
	if(! in_array(strlen($last_Name), range(2,15))){
		array_push($errors,  "Must be between 2 to 15 Characters<br>");
	}
	if($password != $matchPassword) {
		array_push($errors,  "Passwords do not match<br>");
	}
	else {
		if(preg_match('/[^A-Za-z0-9]/', $password)) {
			array_push($errors, "Password can have Alphanumeric values only<br>");
		}
	}
	if(! in_array(strlen($password), range(2,15))){
		array_push($errors, "Your password must be betwen 8 and 30 characters<br>");
	}
	if(empty($errors)) {
		$password_copy = $password;
		$profile_pic = "support/images/profilePics/blue.png";
		$query = mysqli_query($conn, "INSERT INTO User VALUES ('','$user_Name','$email','$password_copy','$profile_pic','$dob','','','$first_Name','$last_Name','ACTIVE')");
		array_push($errors, "<span style='color: #FFFFFF;'>Registered</span><br>");
		$_SESSION['first_name']=$_SESSION['last_name']=$_SESSION['email_id']=$_SESSION['user_name'] = '';
	}
}
?>
