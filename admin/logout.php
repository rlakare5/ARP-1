<?php
require_once '../include/config.php';
session_destroy();
redirect('login.php');
?>
