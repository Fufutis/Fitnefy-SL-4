<?php
session_start();
session_unset();
session_destroy();
header("Location: http://localhost/login/final-project/WebsiteProject/index.php");
exit;
