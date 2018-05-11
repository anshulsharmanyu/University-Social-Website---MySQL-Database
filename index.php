<?php
include("included_files/header.php");
if(isset($_POST['post'])){
	$post = new Post($conn, $loggedInUsedID);
	$post->submitPost($_POST['post_text'], 0, 'PUBLIC',$_POST['fileupload']);
}
 ?>
	<div class="user_details column">
		<?php
			$test = $user['photoID'];
		?>
		<!-- getting user data -->
		<a href="<?php echo "profile.php?profile_username=".$loggedInUsedID; ?>">  <img src="<?php echo $test ?>"> </a>

		<div class="user_details_left_right">
			<a href="<?php echo "profile.php?profile_username=".$loggedInUsedID; ?>">
			<?php
			echo $user['firstName'] . " " . $user['lastName'];
			 ?>
			</a>

			<?php
				$friends_query = mysqli_query($conn, "SELECT * FROM relationship WHERE firstUser= '$loggedInUsedID' and relation = 'FRIEND'");
				$row_firends = mysqli_num_rows($friends_query);
				$group_query = mysqli_query($conn, "SELECT DISTINCT groupID FROM groupmember WHERE memberID= '$loggedInUsedID'");
				$row_groups = mysqli_num_rows($group_query);
				$event_query = mysqli_query($conn, "SELECT DISTINCT eventID FROM eventparticipants WHERE participantID= '$loggedInUsedID'");
				$row_events = mysqli_num_rows($event_query);
				// getting total count
				echo "Friends: " . $row_firends;
				echo "<br>";
				echo "Groups: " . $row_groups;
				echo "<br>";
				echo "Events:  " . $row_events;
			?>
		</div>
	</div>

	<div class="main_column column">
		<form class="post_form" action="index.php" method="POST">
			<textarea name="post_text" id="post_text" placeholder="Post Text or Attach Images or Add Youtube Links"></textarea>
			<input type="file" name="fileupload" value="fileupload" id="fileupload" style="border: none; border-color: transparent;">
			<input type="submit" name="post" id="post_button" value="Post">
		</form>
		<?php
		if(isset($_POST['update_privacy'])) {
			$privacy_name = $_POST['privacy_name'];
			echo $privacy_name;
			$post_id = $_POST['postID'];
			$add_post_query = mysqli_query($conn, "UPDATE post set accessType = '$privacy_name' WHERE postID='$post_id'");
		}


	  if(isset($_POST['create_event_details'])) {
	    $event_name = $_POST['event_name'];
	    $event_description = $_POST['event_description'];
	    $event_date = $_POST['event_date'];
	    $event_time = $_POST['event_time'];
			$event_location = $_POST['event_location'];
			$get_Location_Query = mysqli_query($conn, "SELECT * FROM Location WHERE locationName LIKE '%$event_location%' ");
			$row_get_licationID = mysqli_fetch_array($get_Location_Query);
	    $locationID= $row_get_licationID['locationID'];
	    $userID = $user['userID'];
	    // Posting to event table
	    $add_event_query = mysqli_query($conn, "INSERT INTO Event VALUES ('','$event_name','$event_date','$event_time','$userID',$locationID)");
	    // Getting the event ID from event table
	    $event_data_query = mysqli_query($conn, "SELECT * FROM Event WHERE eventName = '$event_name' and eventDate = '$event_date' and eventTime = '$event_time'");
	  	$row = mysqli_fetch_array($event_data_query);
	    $eventID= $row['eventID'];
	    // Posting the data to post
	    $add_post_query = mysqli_query($conn, "INSERT INTO Post VALUES ('','$userID','$eventID','2018-05-05 21:00:43','$event_description','EVENT','PUBLIC',0)");
	  }
	  ?>

		<button class="button" data-toggle="modal" data-target="#myModal" style="height:4%;width:10%;" >
  Create Event
</button>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Enter Event Details</h4>
      </div>
      <div class="modal-body">
				<form action="index.php" method="POST">
					<div class="form-group">
					    Event Name: <input type="text" name="event_name" placeholder="Event Name" id="event_input"><br>
				    </div>
					<div class="form-group">
					    Event Description: <input type="text" name="event_description" placeholder="Event Description" id="event_input"><br>
				    </div>

			      <div class="form-group">
			     Event Date: <input id="date" name="event_date" type="date">
			    </div>


			     <div class="form-group">
			    Event Time: <input id="settime" name="event_time" type="time" step="1" />
			    </div>

					<div class="form-group">
				 Event Location: <input type="text" name="event_location" placeholder="Event Location" id="event_input"><br>
				 </div>

					<div class="form-group">
				 <!-- <button onclick="getLocation()" >Location</button> -->
				 <!-- <button onclick="window.location.href='loca.html'" >Location</button> -->
				 <button onclick="getLocation()">Current Location</button>
				 </div>

			        <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					   <input type="submit" name="create_event_details" id="save_details" value="CREATE" class="info settings_submit"><br>
				    </div>

				</form>
      </div>
    </div>
  </div>
</div>


<?php
if(isset($_POST['create_group_details'])) {
	$group_name = $_POST['group_name'];
	$group_description = $_POST['group_description'];
	$userID = $user['userID'];
	$add_group_query = mysqli_query($conn, "INSERT INTO Groups VALUES ('','$userID','$group_name','PUBLIC')");

}
?>
<button class="button" data-toggle="modal" data-target="#myModal1" style="height:4%;width:11%;" >
Create Group
</button>

<div class="modal fade1" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Enter Group Details</h4>
      </div>
      <div class="modal-body">
				<form action="index.php" method="POST">
					<div class="form-group">
					    Group Name: <input type="text" name="group_name" placeholder="Group Name" id="group_input"><br>
				    </div>
					<div class="form-group">
					    Group Description: <input type="text" name="group_description" placeholder="Group Description" id="group_input"><br>
				    </div>

			        <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					   <input type="submit" name="create_group_details" id="save_details" value="CREATE" class="info settings_submit"><br>
				    </div>

				</form>
      </div>
    </div>
  </div>
</div>

		<button onclick="getLocation()">Current Location</button>
		<a href="loca.html" target="_blank" ><button>Map Current Location</button></a>
		<p id="demo"></p>

        <script src="http://maps.google.com/maps/api/js?key=AIzaSyAoeNMQxTEkTFm4H-zNu5opnc_pUQBRb2c"></script>
	    <script>
			var x=document.getElementById("demo");
			function getLocation(){
			    if (navigator.geolocation){
			        navigator.geolocation.getCurrentPosition(showPosition,showError);
			    }
			    else{
			        x.innerHTML="Geolocation is not supported by this browser.";
			    }
			}
			function showPosition(position){
			    lat=position.coords.latitude;
			    lon=position.coords.longitude;
			    displayLocation(lat,lon);
			}
			function showError(error){
			    switch(error.code){
			        case error.PERMISSION_DENIED:
			            x.innerHTML="User denied the request for Geolocation."
			        break;
			        case error.POSITION_UNAVAILABLE:
			            x.innerHTML="Location information is unavailable."
			        break;
			        case error.TIMEOUT:
			            x.innerHTML="The request to get user location timed out."
			        break;
			        case error.UNKNOWN_ERROR:
			            x.innerHTML="An unknown error occurred."
			        break;
			    }
			}
			function displayLocation(latitude,longitude){
			    var geocoder;
			    geocoder = new google.maps.Geocoder();
			    var latlng = new google.maps.LatLng(latitude, longitude);
			    geocoder.geocode(
			        {'latLng': latlng},
			        function(results, status) {
			            if (status == google.maps.GeocoderStatus.OK) {
			                if (results[0]) {
			                    var add= results[0].formatted_address ;
			                    var  value=add.split(",");
			                    count=value.length;
			                    country=value[count-1];
			                    state=value[count-2];
			                    city=value[count-3];
			                    x.innerHTML = "Location is: " + city+","+state+","+country;
							    $.ajax({
							                       type: "POST",
							                       url: 'index.php',
							                       data: "indi=" + city,
							                       success: function(data)
							                       {
							                           alert("success!");
							                       }
							                   });
			                }
			                else  {
			                    x.innerHTML = "address not found";
			                }
			            }
			            else {
			                x.innerHTML = "Geocoder failed due to: " + status;
			            }
			        }
			    );
			}
			</script>
		<?php
			$iid = isset($_POST['indi']);
		?>
		<div class="posts_area"></div>
		<img id="loading" src="support/images/icons/loading.gif">
	</div>

	<div class="user_details3 column">

		<h4>CURRENT GROUPS</h4>

		<?php
	  if(isset($_POST['join_group'])) {
			$group_name = $_POST['group_name'];
			$userID = $user['userID'];
			$get_group_query = mysqli_query($conn, "SELECT * FROM Groups WHERE groupName = '$group_name'");
			$row = mysqli_fetch_array($get_group_query);
	    $groupID= $row['groupID'];
	    $add_event_query = mysqli_query($conn, "INSERT INTO GroupMember VALUES ('$groupID','$userID')");
	  }
	  ?>

		<div class="trends">
			<?php
			$userID = $user['userID'];
			$query = mysqli_query($conn, "SELECT * FROM GROUPS where groupID NOT IN( SELECT groupID from groupMember where memberID = '$userID') ORDER BY groupID DESC");
			foreach ($query as $row) {
				$word = $row['groupName'];
				$word_dot = strlen($word) >= 14 ? "..." : "";
				$trimmed_word = str_split($word, 14);
				$trimmed_word = $trimmed_word[0];
				echo "<div style'padding: 1px'>";
				echo $trimmed_word . $word_dot;
				echo "<form action=\"index.php\" method=\"POST\"><input type = \"hidden\" name=\"group_name\" value=\"$word\" id=\"group_name\"> <input type=\"submit\" name=\"join_group\" value=\"JOIN\" style=\"float: right;\"> </form>";
				echo "<br></div><br>";
			}
			?>
		</div>
	</div>
	<div class="user_details2 column">

		<h4>CURRENT EVENTS</h4>

		<?php
	  if(isset($_POST['go_event'])) {
			// get event details
			$event_name = $_POST['event_name'];
			$userID = $user['userID'];
			$get_event_query = mysqli_query($conn, "SELECT * FROM event WHERE eventname = '$event_name'");
			$row = mysqli_fetch_array($get_event_query);
			//  get event id
	    $eventID= $row['eventID'];
	    $add_event_query = mysqli_query($conn, "INSERT INTO eventParticipants VALUES ('$eventID','$userID')");
	  }
	  ?>

		<div class="trends">
			<?php
			$userID = $user['userID'];
			$query = mysqli_query($conn, "SELECT * FROM Event where eventID NOT IN( SELECT eventID from eventParticipants where participantID = '$userID') ORDER BY eventID DESC");
			foreach ($query as $row) {
				// get event name
				$word = $row['eventName'];
				$word_dot = strlen($word) >= 15 ? ".." : "";
				$trimmed_word = str_split($word, 14);
				$trimmed_word = $trimmed_word[0];
				echo "<div style'padding: 1px'>";
				//
				echo $trimmed_word . $word_dot;
				echo "<form action=\"index.php\" method=\"POST\"><input type = \"hidden\" name=\"event_name\" value=\"$word\" id=\"event_name\"> <input type=\"submit\" name=\"go_event\" value=\"GOING\" style=\"float: right;\"> </form>";
				echo "<br></div><br>";
			}
			?>
		</div>
	</div>

	<script>
	var userLoggedIn = '<?php echo $loggedInUsedID; ?>';
	$(document).ready(function() {
		$('#loading').show();
		//Original ajax request for loading first posts
		$.ajax({
			url: "included_files/handlers/ajax_load_posts.php",
			type: "POST",
			// get user data
			data: "page=1&userLoggedIn=" + userLoggedIn,
			cache:false,
			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});
		$(window).scroll(function() {
			var height = $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();
			// loading if more srolling pages
			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
				$('#loading').show();
				var ajaxReq = $.ajax({
					// get loading post
					url: "included_files/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,
					success: function(response) {
						// upload
						$('.posts_area').find('.nextPage').remove();
						// no more post present
						$('.posts_area').find('.noMorePosts').remove();
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
