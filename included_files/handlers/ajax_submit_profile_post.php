<?php
require '../../db_config/db_config.php';
include("../classes/user_class.php");
include("../classes/post_class.php");
include("../classes/notification_class.php");
if(isset($_POST['post_body'])) {
	$post = new Post($conn, $_POST['user_from']);
	$post->submitPost($_POST['post_body'], $_POST['user_to'], 'PRIVATE');
}

?>
