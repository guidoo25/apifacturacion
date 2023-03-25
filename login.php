<?php
include ('dbconection.php');

$userName = $_POST['username'];
$password = $_POST['password'];

$sqlQuery= "SELECT * FROM `user` WHERE `username` = '$userName' AND `password` = '$password'";

$exe=mysqli_query($con,$sqlQuery);


