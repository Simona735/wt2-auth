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
    $stmt = $conn->query("SELECT * FROM user WHERE user.email = '".$email."';");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user == null){
        $query = "INSERT INTO `user`(`name`, `email`) VALUES ('".$name. "','".$email."')";
        $stmt = $conn->query($query);

        $query = "INSERT INTO `account`(`user_id`, `password`, `secret`) VALUES (".$conn->lastInsertId().",'".$password."','".$secret."')";
        $stmt = $conn->query($query);

    }else{
        $stmt = $conn->query("UPDATE `account` SET `password`='".$password."', `secret`='".$secret."' WHERE account.user_id=".$user["id"].";");
    }
    header('Location:../index.php');
} else {
    header('Location:../index.php?alert=message');
    echo 'Login failed';
}
