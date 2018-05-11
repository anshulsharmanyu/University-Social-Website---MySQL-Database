<?php
include("../../db_config/db_config.php");
include("../classes/user_class.php");
include("../classes/post_class.php");

$limit = 100; //Number of posts to be loaded per call

$posts = new Post($conn, $_REQUEST['userLoggedIn']);
$posts->loadPostsFriends($_REQUEST, $limit);
?>
