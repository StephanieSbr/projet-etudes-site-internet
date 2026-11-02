<?php
require_once 'securite.php';
$_SESSION = [];
session_destroy();
header('Location: ../index.php');
exit();