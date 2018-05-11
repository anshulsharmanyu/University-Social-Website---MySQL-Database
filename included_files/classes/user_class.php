<?php
class User {
	private $user;
	private $conn;
	public function __construct($conn, $user){
		// user conection
		$this->con = $conn;
		$user_details_query = mysqli_query($conn, "SELECT * FROM User WHERE userID='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}
	// Get basic user details
	public function getUsername() {
		return $this->user['userName'];
	}
	public function getUserId() {
		return $this->user['userID'];
	}
	public function getNumberOfFriendRequests() {
		$userid = $this->user['userID'];
		$query = mysqli_query($this->con, "SELECT * FROM relationship where secondUser = '$userid' and relation = 'REQUESTED'");
		return mysqli_num_rows($query);
	}
	// get first name of the user
	public function getFirstAndLastName() {
		$userid = $this->user['userID'];
		// query yo get details
		$query = mysqli_query($this->con, "SELECT firstName, lastName FROM User WHERE userID='$userid'");
		$row = mysqli_fetch_array($query);
		return $row['firstName'] . " " . $row['lastName'];
	}
	// get profile pic
	public function getProfilePic() {
		$userid = $this->user['userID'];
		$query = mysqli_query($this->con, "SELECT photoID FROM User WHERE userID='$userid'");
		$row = mysqli_fetch_array($query);
		return $row['photoID'];
	}
	// send request
	public function sendRequest($target_user_id) {
		$sender_user_id = $this->user['userID'];
		$query = mysqli_query($this->con, "INSERT INTO relationship VALUES('$sender_user_id','$target_user_id','REQUESTED')");
	}
	// get the user active
	public function isInActive() {
		$userID = $this->user['userID'];
		$query = mysqli_query($this->con, "SELECT stat FROM User WHERE userID='$userID'");
		$row = mysqli_fetch_array($query);
		if($row['stat'] == 'INACTIVE'){
			return true;
		}
		else
			return false;
	}
	//  is friends with user
	public function isFriend($userId_to_check) {
		$currentuserId = $this->user['userID'];
		$query = mysqli_query($this->con, "SELECT * FROM relationship WHERE firstUser='$currentuserId' AND secondUser='$userId_to_check' AND relation = 'FRIEND'");
		if(mysqli_num_rows($query)==1) {
			return true;
		}
		else {
			return false;
		}
	}
	// remove friend
	public function removeFriend($targetUser) {
		echo "here";
		$currentUser = $this->user['userID'];
		// update queries
		$remove_friend1 = mysqli_query($this->con, "DELETE FROM Relationship WHERE relation= 'FRIEND' AND firstUser='$currentUser' AND secondUser = '$targetUser' ");
		$remove_friend2 = mysqli_query($this->con, "DELETE FROM Relationship WHERE relation= 'FRIEND' AND firstUser='$targetUser' AND secondUser = '$currentUser' ");
	}
	// get count of mutual friends
	public function getMutualFriends($targetUser) {
		$currentUser = $this->user['userID'];
		$query = mysqli_query($this->con, "SELECT secondUser from relationship where firstUser = '$currentUser' and relation = 'FRIEND' and secondUser <> '$targetUser' and secondUser IN (SELECT secondUser from relationship where firstUser = '$targetUser' and secondUser <> '$currentUser' and relation = 'FRIEND') ");
		return mysqli_num_rows($query);
	}
	// did current user sent request
	public function didSendRequest($secondUser) {
		$firstUser = $this->user['userID'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM relationship WHERE secondUser ='$secondUser' AND firstUser ='$firstUser' and relation = 'REQUESTED'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	// did receive any request from other user
	public function didReceiveRequest($firstUser) {
		$secondUser = $this->user['userID'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM relationship WHERE firstUser='$firstUser' AND secondUser='$secondUser' and relation = 'REQUESTED'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	// total number of friend count for the user
	public function getFriendCount() {
		$userID = $this->user['userID'];
		// query
		$query = mysqli_query($this->con, "SELECT * FROM relationship WHERE firstUser='$userID' and relation = 'FRIEND'");
		return mysqli_num_rows($query);
	}
}
?>
