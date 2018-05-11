<?php
include("included_files/header.php");
$message_obj = new Message($conn, $loggedInUsedID);
// get user name for logged in user
$usernameLoggedIn_query = mysqli_query($conn, "SELECT userName FROM User WHERE userID='$loggedInUsedID'");
$usernameLoggedInRow = mysqli_fetch_array($usernameLoggedIn_query);
$usernameLoggedIn = $usernameLoggedInRow['userName'];
// if get profile username
if(isset($_GET['profile_username'])) {
	$userID = $_GET['profile_username'];
  $username_query = mysqli_query($conn, "SELECT userName FROM User WHERE userID='$userID'");
  $row = mysqli_fetch_array($username_query);
  $username = $row['userName'];
	$user_details_query = mysqli_query($conn, "SELECT * FROM User WHERE userID='$userID'");
	$user_array = mysqli_fetch_array($user_details_query);
  $user = new User($conn, $userID);
  $friend_count = $user->getFriendCount();
}
// if set to remove friends
if(isset($_POST['remove_friend'])) {
	$user = new User($conn, $loggedInUsedID);
	$user->removeFriend($userID);
}
// if set to add friends
if(isset($_POST['add_friend'])) {
	$user = new User($conn, $loggedInUsedID);
	$user->sendRequest($userID);
}
// respond to friend request
if(isset($_POST['respond_request'])) {
	header("Location: requests.php");
}
// if post message
if(isset($_POST['post_message'])) {
  if(isset($_POST['message_body'])) {
    $messageContent = mysqli_real_escape_string($conn, $_POST['message_body']);
    $timestamp = date("Y-m-d H:i:s");
    $message_obj->sendMessage($userID, $messageContent, $timestamp);
  }
  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script>
          $(function() {
              $('" . $link ."').tab('show');
          });
        </script>";
}
 ?>

 	<style type="text/css">
	 	.wrapper {
	 		margin-left: 0px;
			padding-left: 0px;
	 	}
 	</style>

 	<div class="profile_left">
 		<img src="<?php echo $user_array['photoID']; ?>">

 		<div class="profile_info">

 			<p><?php echo "Number of Friends: " . $friend_count ?></p>
			<p>
			<?php
				// get distinct group id
				$group_query = mysqli_query($conn, "SELECT DISTINCT groupID FROM groupmember WHERE memberID= '$loggedInUsedID'");
				$row_groups = mysqli_num_rows($group_query);
				// get event id
				$event_query = mysqli_query($conn, "SELECT DISTINCT eventID FROM eventparticipants WHERE participantID= '$loggedInUsedID'");
				$row_events = mysqli_num_rows($event_query);
				// get interests to display
				$interest_query = mysqli_query($conn, "SELECT interests FROM user WHERE userID= '$loggedInUsedID'");
				$row_interests = mysqli_fetch_array($interest_query);
				$dob_query = mysqli_query($conn, "SELECT DOB FROM user WHERE userID= '$loggedInUsedID'");
				$row_dob = mysqli_fetch_array($dob_query);
				echo "Groups Joined: " . $row_groups;
				echo "<br>";
				echo "Events Going:  " . $row_events;
				echo "<br>";
				echo "Interests:  " . $row_interests['interests'];
				echo "<br>";
				echo "DOB:  " . $row_dob['DOB'];
				echo "<br>";
			?>
			</p>
			<p><?php
				$logged_in_user_obj = new User($conn, $loggedInUsedID);
				if($loggedInUsedID != $userID) {
		        echo "Mutual Friends: ".$logged_in_user_obj->getMutualFriends($userID);
	    }
			?> </p>

 		</div>

 		<form action=<?php echo "profile.php?profile_username=".$userID ?> method="POST">
 			<?php
 			$profile_user_obj = new User($conn, $userID);
 			if($profile_user_obj->isInActive()) {
 				header("Location: user_closed.php");
 			}

 			if($loggedInUsedID != $userID) {
 				if($logged_in_user_obj->isFriend($userID)) {
 					echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend" style="height:4%;width:41%;float:left">';
 				}
 				else if ($logged_in_user_obj->didReceiveRequest($userID)) {
 					echo '<input type="submit" name="respond_request" class="warning" value="Respond" style="height:4%;width:41%;float:left">';
 				}
 				else if ($logged_in_user_obj->didSendRequest($userID)) {
 					echo '<input type="submit" name="" class="default" value="Request Sent" style="height:4%;width:41%;float:left">';
 				}
 				else
 					echo '<input type="submit" name="add_friend" class="success" value="Add Friend" style="height:4%;width:41%;float:left">';
 			}
 			?>
 		</form>
 		<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post on profile" style="height:4.2%;width:41%;margin-top:-3%;">

 	</div>

<!-- profile main window for the user -->
	<div class="profile_main_column1 column">

    <ul class="nav nav-tabs" role="tablist" id="profileTabs">
      <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Feed</a></li>
      <li role="presentation"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
    </ul>

    <div class="tab-content">

      <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
        <div class="posts_area"></div>
        <img id="loading" src="support/images/icons/loading.gif">
      </div>


      <div role="tabpanel" class="tab-pane fade" id="messages_div">
        <?php
          echo "<h4>You and <a href='" . $username ."'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
          echo "<div class='loaded_messages' id='scroll_messages'>";
          echo $message_obj->getMessage($userID);
          echo "</div>";
        ?>


        <div class="message_post">
          <form action="" method="POST">
              <textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
              <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
          </form>

        </div>

        <script>
          var div = document.getElementById("scroll_messages");
          div.scrollTop = div.scrollHeight;
        </script>
      </div>


    </div>


	</div>


<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="postModalLabel">Post something!</h4>
      </div>

      <div class="modal-body">

      	<form class="profile_post" action="" method="POST">
      		<div class="form-group">
      			<textarea class="form-control" name="post_body"></textarea>
      			<input type="hidden" name="user_from" value="<?php echo $loggedInUsedID; ?>">
      			<input type="hidden" name="user_to" value="<?php echo $userID; ?>">
      		</div>
      	</form>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
      </div>
    </div>
  </div>
</div>


<script>
  var usernameLoggedIn = '<?php echo $usernameLoggedIn; ?>';
  var profileUsername = '<?php echo $username; ?>';
  $(document).ready(function() {
    $('#loading').show();
    //Original ajax request for loading first posts
    $.ajax({
      url: "included_files/handlers/ajax_load_profile_posts.php",
      type: "POST",
      data: "page=1&usernameLoggedIn=" + usernameLoggedIn + "&profileUsername=" + profileUsername,
      cache:false,
      success: function(data) {
        $('#loading').hide();
        $('.posts_area').html(data);
      }
    });
		//funtion call to ajax
    $(window).scroll(function() {
      var height = $('.posts_area').height(); //Div containing posts
      var scroll_top = $(this).scrollTop();
      var page = $('.posts_area').find('.nextPage').val();
      var noMorePosts = $('.posts_area').find('.noMorePosts').val();
      if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
        $('#loading').show();
        var ajaxReq = $.ajax({
          url: "included_files/handlers/ajax_load_profile_posts.php",
          type: "POST",
          data: "page=" + page + "&usernameLoggedIn=" + usernameLoggedIn + "&profileUsername=" + profileUsername,
          cache:false,
          success: function(response) {
            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
            $('#loading').hide();
            $('.posts_area').append(response);
          }
        });
      }
      return false;
    });
  });
  </script>
	</div>
</body>
</html>
