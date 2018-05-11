<?php
include("../../db_config/db_config.php");
include("../classes/user_class.php");
include("../classes/message_class.php");

$limit = 7; //Number of messages to load

$message = new Message($conn, $_REQUEST['userLoggedIn']);
echo $message->getConversationsDropdown($_REQUEST, $limit);

?>