<?php
session_start();
session_destroy();
header('Location: Landingpage.php');
exit;
?>