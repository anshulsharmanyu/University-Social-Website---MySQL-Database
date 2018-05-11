<?php
class Message {
	private $user_obj;
	private $conn;
	// Get user details
	public function __construct($conn, $user){
		$this->con = $conn;
		$this->user_obj = new User($conn, $user);
	}
	//  to display the most recent messages
	public function getMostRecentMessageUser() {
		$loggedInUsedID = $this->user_obj->getUserId();
		$query = mysqli_query($this->con, "SELECT targetID, sourceID FROM Message WHERE targetID='$loggedInUsedID' OR sourceID='$loggedInUsedID' ORDER BY messageID DESC LIMIT 1");
		// Getting source and target id of user messages
		if(mysqli_num_rows($query) == 0)
			return false;
		$row = mysqli_fetch_array($query);
		$targetID = $row['targetID'];
		$sourceID = $row['sourceID'];
		if($targetID != $loggedInUsedID)
			return $targetID;
		else
			return $sourceID;
	}
	// send message to different user
	public function sendMessage($targetID, $messageContent, $timestamp) {
		if($messageContent != "") {
			$loggedInUsedID = $this->user_obj->getUserId();
			// Inserting to messages table once the message is sent
			$query = mysqli_query($this->con, "INSERT INTO Message VALUES('', '$loggedInUsedID', '$targetID', '$timestamp', '$messageContent', 'UNREAD')");
		}
	}
	// load messages from different user
	public function getMessage($otherUser) {
		$loggedInUsedID = $this->user_obj->getUserId();
		$data = "";
		$query = mysqli_query($this->con, "UPDATE Message SET mView='READ' WHERE targetID='$loggedInUsedID' AND sourceID='$otherUser'");
		$get_Message_query = mysqli_query($this->con, "SELECT * FROM Message WHERE (targetID='$loggedInUsedID' AND sourceID='$otherUser') OR (sourceID='$loggedInUsedID' AND targetID='$otherUser')");
		// Get multiple messages as there will be many no. of messages.
		while($row = mysqli_fetch_array($get_Message_query)) {
			$targetID = $row['targetID'];
			$sourceID = $row['sourceID'];
			$messageContent = $row['messageContent'];
			$div_top = ($targetID == $loggedInUsedID) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
			$data = $data . $div_top . $messageContent . "</div><br><br>";
		}
		return $data;
	}
	// load latest messaage from user.
	public function getLatestMessage($loggedInUsedID, $user2) {
		// getting the top most messages according to the time stamp
		$details_array = array();
		$they_query = mysqli_query($this->con, "SELECT userName FROM User WHERE userID = '$user2'");
		$they_row = mysqli_fetch_array($they_query);
		// Gettig username
		$they = $they_row['userName'];
		$you_query = mysqli_query($this->con, "SELECT userName FROM User WHERE userID = '$loggedInUsedID'");
		$you_row = mysqli_fetch_array($you_query);
		// getting my username
		$you = $you_row['userName'];
		$query = mysqli_query($this->con, "SELECT messageContent, targetID, timestamp FROM Message WHERE (targetID='$loggedInUsedID' AND sourceID='$user2') OR (targetID='$user2' AND sourceID='$loggedInUsedID') ORDER BY messageID DESC LIMIT 1");
		$row = mysqli_fetch_array($query);
		// creatin conversation logic
		$sent_by = ($row['targetID'] == $loggedInUsedID) ? "$they said: " : "$you said: ";
		//  Calculating time difference
		$current_time = date("Y-m-d H:i:s");
		$start_date = new DateTime($row['timestamp']);
		$end_date = new DateTime($current_time);
		$time_interval = $start_date->diff($end_date); //calculating difference between two dates
		if($time_interval->y >= 1) {
			if($time_interval->y == 1)
				$time_message = $time_interval->y . " year ago";
			else
				$time_message = $time_interval->y . " years ago";
		}
		// days diff
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
			// month diff
			if($time_interval->m == 1) {
				$time_message = $time_interval->m . " month". $days;
			}
			else {
				$time_message = $time_interval->m . " months". $days;
			}
		}
		// days diff
		else if($time_interval->d >= 1) {
			if($time_interval->d == 1) {
				$time_message = "Yesterday";
			}
			else {
				$time_message = $time_interval->d . " days ago";
			}
		}
		// hour dif
		else if($time_interval->h >= 1) {
			if($time_interval->h == 1) {
				$time_message = $time_interval->h . " hour ago";
			}
			else {
				$time_message = $time_interval->h . " hours ago";
			}
		}
		// mins diff
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
			// seconds diff
			else {
				$time_message = $time_interval->s . " seconds ago";
			}
		}
		array_push($details_array, $sent_by);
		array_push($details_array, $row['messageContent']);
		array_push($details_array, $time_message);
		return $details_array;
	}
	//  get the conversations between two users
	public function getConvos() {
		$loggedInUsedID = $this->user_obj->getUserId();
		$final_return_result = "";
		$connvos = array();
		// get convo between two user
		$query = mysqli_query($this->con, "SELECT targetID, sourceID FROM Message WHERE targetID='$loggedInUsedID' OR sourceID='$loggedInUsedID' ORDER BY messageID DESC");
		while($row = mysqli_fetch_array($query)) {
			$targetID_push = ($row['targetID'] != $loggedInUsedID) ? $row['targetID'] : $row['sourceID'];
			if(!in_array($targetID_push, $connvos)) {
				array_push($connvos, $targetID_push);
			}
		}
		// For each message from user
		foreach($connvos as $userID) {
			$user_found_obj = new User($this->con, $userID);
			// for every user get latest messages
			$latest_message_details = $this->getLatestMessage($loggedInUsedID, $userID);
			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots;
			$final_return_result .= "<a href='messages.php?u=$userID'> <div class='user_found_Message'>
								<img src='" . $user_found_obj->getProfilePic() . "' width='50' style='border-radius: 5px; margin-right: 5px;'>
								" . $user_found_obj->getFirstAndLastName() . "
								<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
								<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
								</div>
								</a>";
		}
		return $final_return_result;
	}
	//  show the conversation in the dropdown
	public function getConversationsDropdown($data, $limit) {
		$page = $data['page'];
		// get current user id
		$loggedInUsedID = $this->user_obj->getUserId();
		$final_return_result = "";
		$connvos = array();
		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;
		// update table if read
		$set_viewed_query = mysqli_query($this->con, "UPDATE Message SET mView='READ' WHERE targetID='$loggedInUsedID'");
		$query = mysqli_query($this->con, "SELECT targetID, sourceID FROM Message WHERE targetID='$loggedInUsedID' OR sourceID='$loggedInUsedID' ORDER BY messageID DESC");
		while($row = mysqli_fetch_array($query)) {
			$targetID_push = ($row['targetID'] != $loggedInUsedID) ? $row['targetID'] : $row['sourceID'];
			if(!in_array($targetID_push, $connvos)) {
				array_push($connvos, $targetID_push);
			}
		}
		$num_iterations = 0;
		$count = 1;
		 // for each user i
		foreach($connvos as $userID) {
			if($num_iterations++ < $start)
				continue;
			if($count > $limit)
				break;
			else

				$count++;
			//  getting status if read or not
			$is_unread_query = mysqli_query($this->con, "SELECT mView FROM Message WHERE targetID='$loggedInUsedID' AND sourceID='$userID' ORDER BY messageID DESC");
			$row = mysqli_fetch_array($is_unread_query);
			$style = ($row['mView'] == 'UNREAD') ? "background-color: #DDEDFF;" : "";
			$user_found_obj = new User($this->con, $userID);
			$latest_message_details = $this->getLatestMessage($loggedInUsedID, $userID);
			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots;
			$final_return_result .= "<a href='messages.php?u=$userID'>
								<div class='user_found_Message' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
								" . $user_found_obj->getFirstAndLastName() . "
								<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
								<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
								</div>
								</a>";
		}
		// load new page if reached the end of the page.
		if($count > $limit)
			$final_return_result .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else
			$final_return_result .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more Message to load!</p>";
		return $final_return_result;
	}
	// get no. of unread messages for notification count
	public function getUnreadNumber() {
		$loggedInUsedID = $this->user_obj->getUserId();
		$query = mysqli_query($this->con, "SELECT * FROM Message WHERE mView='UNREAD' AND targetID='$loggedInUsedID'");
		return mysqli_num_rows($query);
	}
}
?>
