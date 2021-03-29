<?php
require_once "../config.php";
require_once 'PHPGangsta/GoogleAuthenticator.php';

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set("Europe/Bratislava");

$name = $_POST["name"];
$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$code = $_POST["code"];
$secret = $_POST["secret"];

$ga = new PHPGangsta_GoogleAuthenticator();
$result = $ga->verifyCode($secret, $code);

if ($result == 1) {
    $query = "INSERT INTO `user`(`name`, `email`) VALUES ('".$name. "','".$email."')";
    $stmt = $conn->query($query);

    $query = "INSERT INTO `account`(`user_id`, `type`, `password`, `secret`) VALUES (".$conn->lastInsertId().",'2fa','".$password."','".$secret."')";
    $stmt = $conn->query($query);
    header('Location:../index.php');
} else {
    echo 'Login failed';
}
