<?php
include("../../db_config/db_config.php");
include("../classes/user_class.php");
$query = $_POST['query'];
$loggedInUsedID = $_POST['userLoggedIn'];
$names = explode(" ", $query);
if(strpos($query, "_") !== false) {
	$usersReturned = mysqli_query($conn, "SELECT * FROM user WHERE userName LIKE '$query%' AND stat='ACTIVE' LIMIT 8");
}
else if(count($names) == 2) {
	$usersReturned = mysqli_query($conn, "SELECT * FROM user WHERE (firstName LIKE '%$names[0]%' AND lastName LIKE '%$names[1]%') AND stat='ACTIVE' LIMIT 8");
}
else {
	$usersReturned = mysqli_query($conn, "SELECT * FROM user WHERE (firstName LIKE '%$names[0]%' OR lastName LIKE '%$names[0]%') AND stat='ACTIVE' LIMIT 8");
}
if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {
		$user = new User($conn, $loggedInUsedID);
		if($row['userName'] != $loggedInUsedID) {
			$mutual_friends = $user->getMutualFriends($row['userID']) . " friend(s) in common";
		}
		else {
			$mutual_friends = "";
		}
		if($user->isFriend($row['userID'])) {
			echo "<div class='resultDisplay'>
					<a href='messages.php?u=" . $row['userID'] . "' style='color: #000'>
						<div class='liveSearchProfilePic'>
							<img src='". $row['photoID'] . "'>
						</div>
						<div class='liveSearchText'>
							".$row['firstName'] . " " . $row['lastName']. "
							<p style='margin: 0;'>". $row['userName'] . "</p>
							<p id='grey'>".$mutual_friends . "</p>
						</div>
					</a>
				</div>";
		}
	}
}
?>
