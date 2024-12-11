<?php


#$host = 'localhost';
#$db_user = 'root';
#$db_pass = '';
#$db_name = 'registry';

$host = 'sql7.freemysqlhosting.net';
$db_user = 'sql7750640';
$db_pass = 'NSbAz1mKKs';
$db_name = 'sql7750640';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
