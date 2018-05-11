<?php
include("../../db_config/db_config.php");
include("../classes/user_class.php");
include("../classes/notification_class.php");

$limit = 7; //Number of messages to load

$notification = new Notification($conn, $_REQUEST['userLoggedIn']);
echo $notification->getNotifications($_REQUEST, $limit);

?>