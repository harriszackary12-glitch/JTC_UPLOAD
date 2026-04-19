<!-- Database connection, may be useful -->
<?php
include "password.php";
$dbhost = "172.17.56.25";
$dbuser = "11153527";
$dbname = "kn11153527";
$dbpass = $password;

$GLOBALS['username_maxlength'] = 30;
$GLOBALS['email_maxlength'] = 100;
$GLOBALS['postcode_maxlength'] = 32;

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}