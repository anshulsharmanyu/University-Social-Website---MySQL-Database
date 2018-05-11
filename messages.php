<?php
include("included_files/header.php");
$message_obj = new Message($conn, $loggedInUsedID);
if(isset($_GET['u']))
	$targetID = $_GET['u'];
else {
	$targetID = $message_obj->getMostRecentMessageUser();
	if($targetID == false)
		$targetID = 'new';
}
if($targetID != "new")
	$targetID_obj = new User($conn, $targetID);
if(isset($_POST['post_message'])) {
	if(isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($conn, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($targetID, $body, $date);
	}
}
 ?>
 <div class="user_details_message column">
		<a href="<?php echo "profile.php?profile_username=".$loggedInUsedID; ?>">  <img src="<?php echo $user['photoID']; ?>"> </a>

		<div class="user_details_left_right">
			<a href="<?php echo "profile.php?profile_username=".$loggedInUsedID; ?>" >
			<?php
			echo $user['firstName'] . " " . $user['lastName'];
			 ?>
			</a>
			<br>
			<!-- <?php echo "Posts: " . $user['num_posts']. "<br>";
			echo "Likes: " . $user['num_likes'];
			?> -->
		</div>
	</div>

	<div class="main_column_message column" id="main_column">
		<?php
		if($targetID != "new"){
			echo "<h4>You and <a href = 'profile.php?profile_username=$targetID'>" . $targetID_obj->getFirstAndLastName() . "</a></h4><hr><br>";
			echo "<div class='loaded_messages' id='scroll_messages'>";
				echo $message_obj->getMessage($targetID);
			echo "</div>";
		}
		else {
			echo "<h4>New Message</h4>";
		}
		?>



		<div class="message_post">
			<form action="" method="POST">
				<?php
				if($targetID == "new") {
					echo "Select the friend you would like to message <br><br>";
					?>
					To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $loggedInUsedID; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>

					<?php
					echo "<div class='results'></div>";
				}
				else {
					echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
					echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
				}
				?>
			</form>

		</div>

		<script>
			var div = document.getElementById("scroll_messages");
			div.scrollTop = div.scrollHeight;
		</script>

	</div>

	<div class="user_details5 column" id="conversations">
			<h4>Conversations</h4>

			<div class="loaded_conversations">
				<?php echo $message_obj->getConvos(); ?>
			</div>
			<br>
			<a href="messages.php?u=new">New Message</a>

		</div>
