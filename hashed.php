<?php
$password = 'adminlogin123'; // the password you want to store
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>

