<?php
include("included_files/header.php");
if(isset($_GET['q'])) {
	$query = $_GET['q'];
}
else {
	$query = "";
}
if(isset($_GET['type'])) {
	$type = $_GET['type'];
}
else {
	$type = "name";
}
?>
<!-- search -->

<div class="main_column column" id="main_column_search">

	<?php
	if($query == "")
		echo "You must enter something in the search box.";
	else {

		if($type == "username")
			$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM User WHERE userName LIKE '$query%'");
		// Getting firstname and last name
		else {
			$names = explode(" ", $query);
			if(count($names) == 3)
				$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM User WHERE (firstname LIKE '$names[0]%' AND lastname LIKE '$names[2]%')");

			else if(count($names) == 2)
			//
				$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM User WHERE (firstname LIKE '$names[0]%' AND lastname LIKE '$names[1]%')");
			else
				$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM User WHERE (firstname LIKE '$names[0]%' OR lastname LIKE '$names[0]%')");
		}
		// If we got any resuts
		if(mysqli_num_rows($usersReturnedQuery) == 0)
			echo "We can't find anyone with a " . $type . " like: " .$query;
		else
			echo mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";
		echo "<p id='grey'>Try searching for:</p>";
		echo "<a href='search.php?q=" . $query ."&type=name'>Names</a>, <a href='search.php?q=" . $query ."&type=username'>Usernames</a><br><br><hr id='search_hr'>";
		while($row = mysqli_fetch_array($usersReturnedQuery)) {
			$user_obj = new User($conn, $user['userID']);
			$button = "";
			$mutual_friends = "";
			if($user['userName'] != $row['userName']) {
				//Generate button depending on friendship status
				if($user_obj->isFriend($row['userID']))
					$button = "<input type='submit' name='" . $row['userName'] . "' class='danger' value='Remove Friend'>";
				// get se
				else if($user_obj->didReceiveRequest($row['userID']))
					$button = "<input type='submit' name='" . $row['userName'] . "' class='warning' value='Respond to request'>";
				else if($user_obj->didSendRequest($row['userID']))
					$button = "<input type='submit' class='default' value='Request Sent'>";
				else
					$button = "<input type='submit' name='" . $row['userName'] . "' class='success' value='Add Friend'>";
				$mutual_friends = $user_obj->getMutualFriends($row['userID']) . " friends in common";
				//Button forms
				if(isset($_POST[$row['userName']])) {
					if($user_obj->isFriend($row['userID'])) {
						$user_obj->removeFriend($row['userID']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					}
					else if($user_obj->didReceiveRequest($row['userID'])) {
						header("Location: requests.php");
					}
					else if($user_obj->didSendRequest($row['userID'])) {
					}
					else {
						$user_obj->sendRequest($row['userID']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					}
				}
			}
			// Creating
			echo "<div class='search_result'>
					<div class='searchPageFriendButtons'>
						<form action='' method='POST'>
							" . $button . "
							<br>
						</form>
					</div>
					<div class='result_profile_pic'>
						<a href='profile.php?profile_username=" . $row['userID'] ."'><img src='". $row['photoID'] ."' style='height: 100px;'></a>
					</div>
						<a href='profile.php?profile_username=" . $row['userID'] ."'> " . $row['firstName'] . " " . $row['lastName'] . "
						<p id='grey'> " . $row['userName'] ."</p>
						</a>
						<br>
						" . $mutual_friends ."<br>
				</div>
				<hr id='search_hr'>";
		} //End while
	}
	?>
</div>
