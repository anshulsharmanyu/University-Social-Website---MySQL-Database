<?php
class Notification {
	private $user_obj;
	private $conn;
	public function __construct($conn, $user){
		$this->con = $conn;
		$this->user_obj = new User($conn, $user);
	}
	// get unread notifications to show the marker
	public function getUnreadNumber() {
		$loggedInUsedID = $this->user_obj->getUserId();
		$query = mysqli_query($this->con, "SELECT * FROM notification WHERE nView='UNREAD' AND seconduser='$loggedInUsedID'");
		return mysqli_num_rows($query);
	}
	//  get all the notification for a particular user
	public function getNotifications($data, $limit) {
		$page = $data['page'];
		$loggedInUsedID = $this->user_obj->getUserId();
		$final_return_result = "";
		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;
		$update_nview = mysqli_query($this->con, "UPDATE notification SET nView='READ' WHERE seconduser='$loggedInUsedID'");
		$query = mysqli_query($this->con, "SELECT * FROM notification WHERE seconduser='$loggedInUsedID' ORDER BY notificationID DESC");
		if(mysqli_num_rows($query) == 0) {
			echo "No new notification!";
			return;
		}
		$num_iterations = 0;
		$count = 1;
		while($row = mysqli_fetch_array($query)) {
			if($num_iterations++ < $start)
				continue;
			if($count > $limit)
				break;
			else
				$count++;
			$firstuser = $row['firstuser'];
			$get_user_data = mysqli_query($this->con, "SELECT * FROM user WHERE userID='$firstuser'");
			$user_data = mysqli_fetch_array($get_user_data);
			//Timeframe
			$current_time = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['Timestamp']); //Time of post
			$end_date = new DateTime($current_time); //Current time
			$time_interval = $start_date->diff($end_date); //Difference between dates
			if($time_interval->y >= 1) {
				if($time_interval->y == 1)
					$updated_time_value = $time_interval->y . " year ago"; //1 year ago
				else
					$updated_time_value = $time_interval->y . " years ago"; //1+ year ago
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
					$updated_time_value = $time_interval->m . " month". $days;
				}
				else {
					$updated_time_value = $time_interval->m . " months". $days;
				}
			}
			else if($time_interval->d >= 1) {
				if($time_interval->d == 1) {
					$updated_time_value = "Yesterday";
				}
				else {
					$updated_time_value = $time_interval->d . " days ago";
				}
			}
			else if($time_interval->h >= 1) {
				if($time_interval->h == 1) {
					$updated_time_value = $time_interval->h . " hour ago";
				}
				else {
					$updated_time_value = $time_interval->h . " hours ago";
				}
			}
			else if($time_interval->i >= 1) {
				if($time_interval->i == 1) {
					$updated_time_value = $time_interval->i . " minute ago";
				}
				else {
					$updated_time_value = $time_interval->i . " minutes ago";
				}
			}
			else {
				if($time_interval->s < 30) {
					$updated_time_value = "Just now";
				}
				else {
					$updated_time_value = $time_interval->s . " seconds ago";
				}
			}
			// Getting value if read or not
			$nView = $row['nView'];
			$style = ($nView == 'UNREAD') ? "background-color: #DDEDFF;" : "";
			$final_return_result .= "<a href='" . $row['link'] . "'>
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<div class='notificationsProfilePic'>
											<img src='" . $user_data['photoID'] . "'>
										</div>
										<p class='timestamp_smaller' id='grey'>" . $updated_time_value . "</p>" . $row['content'] . "
									</div>
								</a>";
		}
		if($count > $limit)
			$final_return_result .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else
			$final_return_result .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";
		return $final_return_result;
	}
	// insert new notifictions on user post, comments and likes
	public function insertNotification($post_id, $seconduser, $type) {
		$loggedInUsedID = $this->user_obj->getUserId();
    $loggedInUsedIDUserName = $this->user_obj->getUsername();
		$loggedInUsedIDName = $this->user_obj->getFirstAndLastName();
		$timestamp = date("Y-m-d H:i:s");
		switch($type) {
			//  if it is a comment
			case 'comment':
				$conntent = $loggedInUsedIDUserName . " commented on post";
				break;
			// if it is a like for the post
			case 'like':
				$conntent = $loggedInUsedIDUserName . " liked post";
				break;
			// if someone posted on your profile
			case 'profile_post':
				$conntent = $loggedInUsedIDUserName . " posted on profile";
				break;
			// if someone commented on your profile
			case 'profile_comment':
				$conntent = $loggedInUsedIDUserName . " commented on profile post";
				break;
		}
		$link = "Post.php?id=" . $post_id;
		$insert_query = mysqli_query($this->con, "INSERT INTO notification VALUES('', '$loggedInUsedID', '$seconduser', '$conntent', '$link', '$timestamp', 'UNREAD')");
	}
}
?>
