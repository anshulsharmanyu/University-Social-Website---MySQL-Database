<?php
require '../../db_config/db_config.php';

	if(isset($_GET['post_id']))
		$post_id = $_GET['post_id'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true')
			echo "here";
			$query = mysqli_query($conn, "DELETE FROM post WHERE postID='$post_id'");
			$query = mysqli_query($conn, "DELETE FROM Comment WHERE postID='$post_id'");
			$query = mysqli_query($conn, "DELETE FROM Media WHERE postID='$post_id'");
	}

?>
