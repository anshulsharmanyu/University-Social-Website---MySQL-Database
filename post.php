<?php
include("included_files/header.php");

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	$id = 0;
}
?>
<div class="user_details_main_post column">
		<?php
			$test = $user['photoID'];
		?>
		<a href="<?php echo "profile.php?profile_username=".$loggedInUsedID; ?>"> <img src="<?php echo $test ?>"> </a>
		<!--  -->
		<div class="user_details_left_right">
			<a href="<?php echo "profile.php?profile_username=".$loggedInUsedID; ?>">
				<!--  -->
			<?php
			echo $user['firstName'] . " " . $user['lastName'];
			 ?>
			</a>
			<br>
<!--  -->
		</div>
	</div>

	<div class="main_column_main_post column" id="main_column">
		<div class="posts_area">
			<?php
				$post = new Post($conn, $loggedInUsedID);
				$post->getPostDetails($id);
			?>

		</div>

	</div>
