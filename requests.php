<?php
include("included_files/header.php"); //Header
?>
<div class="main_column column" id="main_column_request">
	<h4>Friend Requests</h4>
	<?php
	// select users
	$query = mysqli_query($conn, "SELECT * from user where userid in (SELECT firstUser FROM relationship WHERE secondUser='$loggedInUsedID' and relation = 'REQUESTED') ");
	$test = mysqli_num_rows($query);
	if(mysqli_num_rows($query) == 0){
		echo "You have no friend requests at this time!";
	}
	else {
		while($row = mysqli_fetch_array($query)) {
			$user_from = $row['userID'];
			$user_pic = $row['photoID'];
			echo $row['userName']."(".$row['firstName']." ".$row['lastName'].")"." sent you a friend request!";
			if(isset($_POST['accept_request' . $user_from ])) {
				// add friend if accepted
				$friend = 'FRIEND';
				$add_friend_query = mysqli_query($conn, "INSERT INTO relationship VALUES('$loggedInUsedID','$user_from','$friend')");
				$add_friend_query = mysqli_query($conn, "INSERT INTO relationship VALUES('$user_from','$loggedInUsedID','$friend')");
				$delete_query = mysqli_query($conn, "DELETE FROM relationship WHERE secondUser='$loggedInUsedID' AND firstUser='$user_from' and relation = 'REQUESTED'");
				echo "You are now friends!";
				header("Location: requests.php");
			}

			// if want to decline the request
			if(isset($_POST['decline_request' . $user_from ])) {
				$delete_query = mysqli_query($conn, "DELETE FROM relationship WHERE secondUser='$loggedInUsedID' AND firstUser='$user_from' and relation = 'REQUESTED'");
				echo "Request declined!";
				header("Location: requests.php");
			}
			?>
			<!-- accept/decline option -->
			<form action="requests.php" method="POST">
				<img src="<?php echo $user_pic?>" width = '70'>
				<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept" style="width:5%;height:5%;">
				<input type="submit" name="decline_request<?php echo $user_from; ?>" id="decline_button" value="Decline"style="width:5%;height:5%;"><br>
			</form>
			<?php
		}

	}

	?>


</div>
