<?php
class Post {
	private $user_obj;
	private $conn;
	public function __construct($conn, $user){
		$this->con = $conn;
		$this->user_obj = new User($conn, $user);
	}
	// Create new post and insert into the  table
	public function submitPost($body, $user_to, $accessType,$uploadImage) {
		$body = strip_tags($body); //removes html tags
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces
		if($check_empty != "") {
			$body_array = preg_split("/\s+/", $body);
			foreach($body_array as $key => $value) {
				// Creating a new iframe if the video link from youtube is added
				if(strpos($value, "www.youtube.com/watch?v=") !== false) {
					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					//  creating a new iframe to show the video
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;
				}
			}
			$body = implode(" ", $body_array);
			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();
			$added_by_userID = $this->user_obj->getUserId();
			//if not on own profile, set to 0
			if($user_to == $added_by)
				$user_to = 0;
			//insert post

			$query = mysqli_query($this->con, "INSERT INTO post VALUES('', '$added_by_userID',null,'$date_added','$body', 'EVENT', '$accessType',$user_to)");
			// getting auto increment id
			$returned_id = mysqli_insert_id($this->con);
			if($uploadImage != ""){
				$query_media = mysqli_query($this->con, "INSERT INTO media VALUES('', '$returned_id','FILE','$uploadImage')");
			}
			//add notification for post if not posting on own profile
			if($user_to != 0) {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "like");
			}
		}
	}
	public function loadPostsFriends($data, $limit) {
		// load post for the timeline page
		$page = $data['page'];
		$loggedInUsedID = $this->user_obj->getUserId();
		$loggedInUsedIDUserName = $this->user_obj->getUserName();
		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;

		$str = ""; //String to return
		// getting no. of post
		$data_query = mysqli_query($this->con, "SELECT * FROM post where (accesstype = 'PUBLIC') OR (accesstype = 'PRIVATE' AND firstUser ='$loggedInUsedID' ) OR (accesstype = 'FRIENDS' AND firstUser IN (SELECT secondUser from relationship where firstUser = '$loggedInUsedID' and relation = 'FRIEND'))ORDER BY postID DESC");
		if(mysqli_num_rows($data_query) > 0) {
			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;
			while($row = mysqli_fetch_array($data_query)) {
				// fetching post details
				$id = $row['postID'];
				$body = $row['Description'];
				$added_by = $row['firstUser'];
				$date_time = $row['Timestmp'];
				$accessType = $row['accessType'];
				$mediaQuery = mysqli_query($this->con, "SELECT mediaContent FROM media where postID = $id;");
				$row_media = mysqli_fetch_array($mediaQuery);
				$media_pic = $row_media['mediaContent'];
				//Prepare user_to string so it can be included even if not posted to a user
				if($row['secondUser']==0){
					$user_to = "posted to Everyone";
				}
				else{
					//  getting second user data
					$user_to_obj = new User($this->con, $row['secondUser']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "posted to <a href='profile.php?profile_username=" . $row['secondUser'] ."'>" . $user_to_name . "</a>";
			}
				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isInActive()) {
					continue;
				}
				$user_logged_obj = new User($this->con, $loggedInUsedID);
				if($user_logged_obj->isFriend($added_by) || $loggedInUsedID==$added_by){
					if($num_iterations++ < $start)
						continue;
					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}
					if($loggedInUsedID == $added_by){

						$delete_button = "<button class='button' id='post$id' style='height:5%width:10%;float:right;'>Delete Post</button>";
						$privacy = "<button class='button' data-toggle='modal' data-target='#myModal3' style='height:4%;width:15%;' >
						Change Privacy
						</button>

						<div class='modal fade3' id='myModal3' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						  <div class='modal-dialog'>
						    <div class='modal-content'>
						      <div class='modal-header'>
						        <button type='button' class='close' data-dismiss='modal'><span aria-hidden=true'>&times;</span><span class='sr-only'>Close</span></button>
						        <h4 class='modal-title' id='myModalLabel'>Change Privacy Settings</h4>
						      </div>
						      <div class='modal-body'>
										<form action='index.php' method='POST'>
											<div class='form-group'>
											    Privacy Name: <input type='text' name='privacy_name' placeholder='PUBLIC,PRIVATE,FRIENDS,FRIENDOFFRIEND' id='group_input' style='width:70%'><br>

										    </div>
												<div class='form-group'>
												    <input type='hidden' name='postID' value='$id' id='group_input'><br>
											    </div>
									        <div class='modal-footer'>
													<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
											   <input type='submit' name='update_privacy' id='save_details' value='UPDATE' class='info settings_submit'><br>
										    </div>
										</form>
						      </div>
						    </div>
						  </div>
						</div>";

				}
					else{
						$delete_button = "";
						$privacy="";
					}
					// fetching user details
					$user_details_query = mysqli_query($this->con, "SELECT * FROM user WHERE userID='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					// parsing data
					$firstName = $user_row['firstName'];
					$lastName = $user_row['lastName'];
					$profile_pic = $user_row['photoID'];
					?>
					<script>
						function toggle<?php echo $id; ?>() {
							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");
								if(element.style.display == "block")
									element.style.display = "none";
								else
									element.style.display = "block";
							}
						}
					</script>

					<script>
						$(document).ready(function() {
							$('#post<?php echo $id; ?>').on('click', function() {
								// ajax query
								bootbox.confirm("Do you want to delete this post?", function(result) {
									$.post("included_files/handlers/delete_Post.php?post_id=<?php echo $id; ?>", {result:result});
									if(result)
										location.reload();
								});
							});
						});
					</script>

					<?php
					// loading comments for the post
					$comments_check = mysqli_query($this->con, "SELECT * FROM Comment WHERE postID='$id'");
					// getting comments count
					$comments_check_num = mysqli_num_rows($comments_check);
					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
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
					if(isset($media_pic)) {
					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>
								<div class='posted_by' style='color:#ACACAC;'>
									<a href='profile.php?profile_username=".$added_by."'> $firstName $lastName </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$accessType
									$delete_button
									$privacy
								</div>
								<div id='post_body'>
									<img src='support/images/profilePics/$media_pic' width='50'>
									$body
									<br>
									<br>
									<br>
								</div>
								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>
							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='post_comments.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";
						}
						else{
							$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
										<div class='post_profile_pic'>
											<img src='$profile_pic' width='50'>
										</div>
										<div class='posted_by' style='color:#ACACAC;'>
											<a href='profile.php?profile_username=".$added_by."'> $firstName $lastName </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
											$accessType
											$delete_button
											$privacy
										</div>
										<div id='post_body'>
											$body
											<br>
											<br>
											<br>
										</div>
										<div class='newsfeedPostOptions'>
											Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
											<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
										</div>
									</div>
									<div class='post_comment' id='toggleComment$id' style='display:none;'>
										<iframe src='post_comments.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
									</div>
									<hr>";

						}
				}
				?>
				<script>
					$(document).ready(function() {
						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {
								$.post("included_files/handlers/delete_Post.php?post_id=<?php echo $id; ?>", {result:result});
								if(result)
									location.reload();
							});
						});
					});
				</script>
				<?php
			} //End while loop
			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}
		echo $str;
	}
	// loading profile data
	public function loadProfilePosts($data, $limit) {
		$page = $data['page'];
		$profileUserName = $data['profileUsername'];
		$data_query1 = mysqli_query($this->con, "SELECT * FROM user WHERE userName = '$profileUserName' ");
		$user_data = mysqli_fetch_array($data_query1);
		$profileUser = $user_data['userID'];
		$loggedInUsedID = $this->user_obj->getUserId();

		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;
		$str = ""; //String to return
		if($loggedInUsedID == $profileUser){
			$data_query = mysqli_query($this->con, "SELECT * FROM post WHERE secondUser='$loggedInUsedID' OR firstUser = '$loggedInUsedID'  ORDER BY postID DESC");
		}
		else{
			$data_query = mysqli_query($this->con, "SELECT * FROM post WHERE ((firstUser='$loggedInUsedID' AND secondUser='$profileUser') OR secondUser='$profileUser' OR firstUser = '$profileUser')  ORDER BY postID DESC");
		}
		if(mysqli_num_rows($data_query) > 0) {
			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;
			while($row = mysqli_fetch_array($data_query)) {
				// loading post details
				$id = $row['postID'];
				$body = $row['Description'];
				$added_by = $row['firstUser'];
				$date_time = $row['Timestmp'];
				$accessType = $row['accessType'];
					if($num_iterations++ < $start)
						continue;
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if(1 == $profileUser){
						$delete_button = "<button class='button' id='post$id' style='height:5%width:10%;float:right;'>Delete Post</button>";
						$privacy = "<button class='button' data-toggle='modal' data-target='#myModal3' style='height:4%;width:15%;' >
						Change Privacy
						</button>

						<div class='modal fade3' id='myModal3' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						  <div class='modal-dialog'>
						    <div class='modal-content'>
						      <div class='modal-header'>
						        <button type='button' class='close' data-dismiss='modal'><span aria-hidden=true'>&times;</span><span class='sr-only'>Close</span></button>
						        <h4 class='modal-title' id='myModalLabel'>Change Privacy Settings</h4>
						      </div>
						      <div class='modal-body'>
										<form action='index.php' method='POST'>
											<div class='form-group'>
											    Privacy Name: <input type='text' name='privacy_name' placeholder='PUBLIC,PRIVATE,FRIENDS,FRIENDOFFRIEND' id='group_input' style='width:70%'><br>


										    </div>
												<div class='form-group'>
												    <input type='hidden' name='postID' value='$id' id='group_input'><br>
											    </div>
									        <div class='modal-footer'>
													<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
											   <input type='submit' name='update_privacy' id='save_details' value='UPDATE' class='info settings_submit'><br>
										    </div>
										</form>
						      </div>
						    </div>
						  </div>
						</div>";
					}
					else
					{
						$delete_button = "";
						$privacy = "";

					}
					//  getting user details
					$user_details_query = mysqli_query($this->con, "SELECT firstName, lastName, photoID FROM user WHERE userID='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$firstName = $user_row['firstName'];
					$lastName = $user_row['lastName'];
					$profile_pic = $user_row['photoID'];
					?>
					<script>
						function toggle<?php echo $id; ?>() {
							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");
								if(element.style.display == "block")
									element.style.display = "none";
								else
									element.style.display = "block";
							}
						}
					</script>
					<?php
					// comment cound
					$comments_check = mysqli_query($this->con, "SELECT * FROM comment WHERE postID='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);
					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
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

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>
								<div class='posted_by' style='color:#ACACAC;'>
									<a href='profile.php?profile_username=".$added_by."'> $firstName $lastName </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$accessType
									$privacy
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>
								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>
							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='post_comments.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";
				?>
				<script>
					$(document).ready(function() {
						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {
								$.post("included_files/handlers/delete_Post.php?post_id=<?php echo $id; ?>", {result:result});
								if(result)
									location.reload();
							});
						});
					});
				</script>
				<?php
			} //End while loop
			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}
		echo $str;
	}

	//  getting details for the single post on click of notification
	public function getPostDetails($post_id) {
		$loggedInUsedIDUserName = $this->user_obj->getUsername();
		$loggedInUsedID = $this->user_obj->getUserId();
		// $opened_query = mysqli_query($this->con, "UPDATE notification SET opened='yes' WHERE user_to='$loggedInUsedID' AND link LIKE '%=$post_id'");
		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM post WHERE postID ='$post_id'");
		if(mysqli_num_rows($data_query) > 0) {
			  $row = mysqli_fetch_array($data_query);
				$id = $row['postID'];
				$body = $row['Description'];
				$added_by = $row['firstUser'];
				$date_time = $row['Timestmp'];
				//Prepare user_to string so it can be included even if not posted to a user
				$user_to_obj = new User($this->con, $row['secondUser']);
				$user_to_name = $user_to_obj->getFirstAndLastName();
				$user_to = "posted to <a href='" . $row['secondUser'] ."'>" .$user_to_name . "</a>";
				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isInActive()) {
					return;
				}
				$user_logged_obj = new User($this->con, $loggedInUsedID);
				if($user_logged_obj->isFriend($added_by)){
					if($loggedInUsedID == $added_by){
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>Delete Post</button>";
						$privacy = "<button class='button' data-toggle='modal' data-target='#myModal3' style='height:4%;width:15%;' >
						Change Privacy
						</button>

						<div class='modal fade3' id='myModal3' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						  <div class='modal-dialog'>
						    <div class='modal-content'>
						      <div class='modal-header'>
						        <button type='button' class='close' data-dismiss='modal'><span aria-hidden=true'>&times;</span><span class='sr-only'>Close</span></button>
						        <h4 class='modal-title' id='myModalLabel'>Change Privacy Settings</h4>
						      </div>
						      <div class='modal-body'>
										<form action='index.php' method='POST'>
											<div class='form-group'>
											    Privacy Name: <input type='text' name='privacy_name' placeholder='PUBLIC,PRIVATE,FRIENDS,FRIENDOFFRIEND' id='group_input' style='width:70%'><br>


										    </div>
												<div class='form-group'>
												    <input type='hidden' name='postID' value='$id' id='group_input'><br>
											    </div>
									        <div class='modal-footer'>
													<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
											   <input type='submit' name='update_privacy' id='save_details' value='UPDATE' class='info settings_submit'><br>
										    </div>
										</form>
						      </div>
						    </div>
						  </div>
						</div>";
					}
					else{
						$delete_button = "";
						$privacy = "";
					}
					$user_details_query = mysqli_query($this->con, "SELECT * FROM user WHERE userId='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$firstName = $user_row['firstName'];
					$lastName = $user_row['lastName'];
					$profile_pic = $user_row['photoID'];
					?>
					<script>
						function toggle<?php echo $id; ?>() {
							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");
								if(element.style.display == "block")
									element.style.display = "none";
								else
									element.style.display = "block";
							}
						}
					</script>
					<?php
					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$post_id'");
					$comments_check_num = mysqli_num_rows($comments_check);
					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$time_interval = $start_date->diff($end_date); //Difference between dates
					if($time_interval->y >= 1) {
						if($time_interval == 1)
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
					$str .= "<div class='status_post' onClick='javascript:toggle$post_id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>
								<div class='posted_by' style='color:#ACACAC;'>
									<a href= 'profile.php?profile_username=$added_by'> $firstName $lastName </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$accessType
									$privacy
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>
								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$post_id' scrolling='no'></iframe>
								</div>
							</div>
							<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
								<iframe src='post_comments.php?post_id=$post_id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";
				?>
				<script>
					$(document).ready(function() {
						$('#post<?php echo $id; ?>').on('click', function() {
							// do you want to delete
							bootbox.confirm("Do you want to delete this post?", function(result) {
								$.post("included_files/handlers/delete_Post.php?post_id=<?php echo $post_id; ?>", {result:result});
								if(result)
									location.reload();
							});
						});
					});
				</script>
				<?php
				}
				else {
					echo "<p>Not Friends with $user_details_query</p>";
					return;
				}
		}
		else {
			echo "<p>No posts found</p>";
					return;
		}
		echo $str;
	}
}
?>
