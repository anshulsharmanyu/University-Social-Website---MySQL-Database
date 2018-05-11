<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="support/css/style.css">
</head>
<body>
	<style type="text/css">
	* {
		font-family: 'LucidaGrande';
	}
	body {
		background-color: #fff;
	}
	form {
		position: absolute;
		top: 0;
	}
	</style>
	<?php
	require 'db_config/db_config.php';
	include("included_files/classes/user_class.php");
	include("included_files/classes/post_class.php");
	include("included_files/classes/notification_class.php");
	if (isset($_SESSION['userID'])) {
		$loggedInUsedID = $_SESSION['userID'];
		$user_details_query = mysqli_query($conn, "SELECT * FROM user WHERE usedID='$loggedInUsedID'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: login_signUp.php");
	}
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}
	// get likes data and calculate total number of likes
	$get_likes = mysqli_query($conn, "SELECT likes FROM liked WHERE postID='$post_id'");
	$row = mysqli_fetch_array($get_likes);
	$total_likes = $row['likes'];
	$user_liked = $row['userID'];
	// getting count likes from users
	$user_details_query = mysqli_query($conn, "SELECT count(userID) FROM likes WHERE postID='$post_id' ORDERBY postID");
	$row = mysqli_fetch_array($user_details_query);
	$total_user_likes = $row['count(userID)'];

	//if set for like and unlike button
	if(isset($_POST['likeButton'])) {
		$total_likes++;
		$query = mysqli_query($conn, "UPDATE likes SET likes='$total_likes' WHERE postID='$post_id'");
		$total_user_likes++;
		$insert_user = mysqli_query($conn, "INSERT INTO likes VALUES('$post_id', '$total_user_likes', '$loggedInUsedID')");


		if($user_liked != $loggedInUsedID) {
			$notification = new Notification($conn, $loggedInUsedID);
			$notification->insertNotification($post_id, $user_liked, "like");
		}
	}
	if(isset($_POST['unlikeButton'])) {
		$total_likes--;
		$query = mysqli_query($conn, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
		$total_user_likes--;
		// $user_likes = mysqli_query($conn, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		$insert_user = mysqli_query($conn, "DELETE FROM likes WHERE userID='$loggedInUsedID' AND postID='$post_id'");
	}
	// checking number of columns received
	$check_query = mysqli_query($conn, "SELECT * FROM likes WHERE userID='$loggedInUsedID' AND postID='$post_id'");
	$num_rows = mysqli_num_rows($check_query);
	// like and unlike button form
	if($num_rows > 0) {
		echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" class="comment_like" name="unlikeButton" value="Unlike">
			</form>
		';
	}
	else {
		echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" class="comment_like" name="likeButton" value="Like">
			</form>
		';
	}
	?>
</body>
</html>
