<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="support/css/style.css">
</head>
<body>

	<style type="text/css">
	* {
		font-size: 12px;
		font-family: Arial, Helvetica, Sans-serif;
	}

	</style>

	<?php
	require 'db_config/db_config.php';
	include("included_files/classes/user_class.php");
	include("included_files/classes/post_class.php");
	include("included_files/classes/notification_class.php");

	if (isset($_SESSION['userID'])) {
		$loggedInUsedID = $_SESSION['userID'];
		$user_details_query = mysqli_query($conn, "SELECT * FROM user WHERE userID='$loggedInUsedID'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: login_signUp.php");
	}

	?>
	<script>
		function toggle() {
			var element = document.getElementById("comment_section");
			if(element.style.display == "block")
				element.style.display = "none";
			else
				element.style.display = "block";
		}
	</script>

	<?php
	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$user_query = mysqli_query($conn, "SELECT firstUser, secondUser FROM post WHERE postID='$post_id'");
	$row = mysqli_fetch_array($user_query);
	$posted_to = $row['firstUser'];
	$user_to = $row['secondUser'];

	if(isset($_POST['postComment' . $post_id])) {
		$post_body = $_POST['post_body'];
		$post_body = mysqli_escape_string($conn, $post_body);
		$date_time_now = date("Y-m-d H:i:s");
		// $insert_post = mysqli_query($conn, "INSERT INTO comment VALUES ('', '$post_body', '$loggedInUsedID', '$posted_to', '$date_time_now')");
		$insert_post = mysqli_query($conn, "INSERT INTO comment VALUES ('', '$post_id', '$loggedInUsedID', '$post_body', '$date_time_now')");

		// if the logged in user is not owner of the the current post send notification to owner
		if($posted_to != $loggedInUsedID) {
			$notification = new Notification($conn, $loggedInUsedID);
			$notification->insertNotification($post_id, $posted_to, "comment");
		}

		// if the logged in user is not receiver of the post send notification to them as well
		if($user_to != 0 && $user_to != $loggedInUsedID) {
			$notification = new Notification($conn, $loggedInUsedID);
			$notification->insertNotification($post_id, $user_to, "profile_comment");
		}

		// notify all other commentors too on the post
		$get_commenters = mysqli_query($conn, "SELECT * FROM comment WHERE postID='$post_id'");
		$notified_users = array();
		while($row = mysqli_fetch_array($get_commenters)) {

			if($row['userID'] != $posted_to && $row['userID'] != $user_to
				&& $row['userID'] != $loggedInUsedID && !in_array($row['userID'], $notified_users)) {

				$notification = new Notification($conn, $loggedInUsedID);
				// insert new notification
				$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

				array_push($notified_users, $row['posted_by']);
			}

		}


		echo "<p>Comment Posted! </p>";
	}
	?>
	<form action="post_comments.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
		<textarea name="post_body"></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
	</form>

	<!-- Load comments -->
	<?php
	$get_comments = mysqli_query($conn, "SELECT * FROM comment WHERE postID='$post_id' ORDER BY commentID ASC");
	$count = mysqli_num_rows($get_comments);

	if($count != 0) {

		while($comment = mysqli_fetch_array($get_comments)) {

			$comment_body = $comment['commentBody'];
			// $posted_to = $comment['posted_to'];
			$posted_to = 'none';
			$posted_by = $comment['userID'];
			$date_added = $comment['Timestamp'];
			// $removed = $comment['removed'];

			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$time_interval = $start_date->diff($end_date); //Difference between dates
			if($time_interval->y >= 1) {
				if($time_interval->y == 1)
					$time_message = $time_interval->y . " year ago"; //1 year ago
				else
					$time_message = $time_interval->y . " years ago"; //1+ year ago
			}
			else if ($time_interval->m >= 1) {
				if($time_interval->d == 0) {
					$days = " ago";
				}
				else if($time_interval->d == 1) {
					$days = $time_interval->d . " day ago";
				}
				else {
					$days = $time_interval->d . " days ago";
				}


				if($time_interval->m == 1) {
					$time_message = $time_interval->m . " month". $days;
				}
				else {
					$time_message = $time_interval->m . " months". $days;
				}

			}
			else if($time_interval->d >= 1) {
				if($time_interval->d == 1) {
					$time_message = "Yesterday";
				}
				else {
					$time_message = $time_interval->d . " days ago";
				}
			}
			else if($time_interval->h >= 1) {
				if($time_interval->h == 1) {
					$time_message = $time_interval->h . " hour ago";
				}
				else {
					$time_message = $time_interval->h . " hours ago";
				}
			}
			else if($time_interval->i >= 1) {
				if($time_interval->i == 1) {
					$time_message = $time_interval->i . " minute ago";
				}
				else {
					$time_message = $time_interval->i . " minutes ago";
				}
			}
			else {
				if($time_interval->s < 30) {
					$time_message = "Just now";
				}
				else {
					$time_message = $time_interval->s . " seconds ago";
				}
			}
			$user_obj = new User($conn, $posted_by);
			?>
			<div class="comment_section">
				<!-- navigate to new page on button click-->
				<a href="<?php echo "profile.php?profile_username=".$posted_by?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
				<a href="<?php echo "profile.php?profile_username=".$posted_by?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
				&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?>
				<hr>
			</div>
			<?php
		}
	}
	?>
</body>
</html>
