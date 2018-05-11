<?php
ob_start();
session_start();
// Credential to database
$timezone = date_default_timezone_set("America/New_York");
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'projectStudentSocial';
$conn=mysqli_connect($servername, $username, $password, $dbname);
if(mysqli_connect_errno())
{echo "Connection Fail" . mysqli_connect_errno();}
?>
