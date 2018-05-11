<?php
include("../../db_config/db_config.php");
include("../../included_files/classes/user_class.php");
$query = $_POST['query'];
$loggedInUsedID = $_POST['userLoggedIn'];
$names = explode(" ", $query);
//If query contains an underscore, assume user is searching for usernames
if(strpos($query, '_') !== false)
	$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM user WHERE userName LIKE '$query%' AND stat = 'ACTIVE' LIMIT 8");
//If there are two words, assume they are first and last names respectively
else if(count($names) == 2)
	$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM user WHERE (firstName LIKE '$names[0]%' AND lastName LIKE '$names[1]%') AND stat= 'ACTIVE' LIMIT 8");
//If query has one word only, search first names or last names
else
	$usersReturnedQuery = mysqli_query($conn, "SELECT * FROM user WHERE (firstName LIKE '$names[0]%' OR lastName LIKE '$names[0]%') AND stat = 'ACTIVE' LIMIT 8");
if($query != ""){
	while($row = mysqli_fetch_array($usersReturnedQuery)) {
		$user = new User($conn, $loggedInUsedID);
		if($row['userID'] != $loggedInUsedID)
			$mutual_friends = $user->getMutualFriends($row['userID']) . " friends in common";
		else
			$mutual_friends = "";
		echo "<div class='resultDisplay'>
				<a href='profile.php?profile_username=" . $row['userID'] . "' style='color: #1485BD'>
					<div class='liveSearchProfilePic'>
						<img src='" . $row['photoID'] ."'>
					</div>
					<div class='liveSearchText'>
						" . $row['firstName'] . " " . $row['lastName'] . "
						<p>" . $row['userName'] ."</p>
						<p id='grey'>" . $mutual_friends ."</p>
					</div>
				</a>
				</div>";
	}
}
?>
