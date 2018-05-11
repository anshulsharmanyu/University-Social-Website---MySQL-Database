<?php
// include("../../db_config/db_config.php");
include("$_SERVER[DOCUMENT_ROOT]/UCoN/db_config/db_config.php");
// include("../header.php");
include("../classes/user_class.php");
include("../classes/post_class.php");
$limit = 10; //Number of posts to be loaded per call
$posts = new Post($conn, $_REQUEST['usernameLoggedIn']);
$posts->loadProfilePosts($_REQUEST, $limit);
?>
