<?php
session_start();
define('MYDIR','../');
require_once(MYDIR."vendor/autoload.php");
require_once "../config.php";

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set("Europe/Bratislava");

$redirect_uri = 'https://wt132.fei.stuba.sk/zadanie_03/oauth2/';

$client = new Google_Client();
$client->setAuthConfig('../configs/credentials.json');
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");
      
$service = new Google_Service_Oauth2($client);
			
if(isset($_GET['code'])){
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token);
  $_SESSION['upload_token'] = $token;

  // redirect back to the example
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// set the access token as part of the client
if (!empty($_SESSION['upload_token'])) {
  $client->setAccessToken($_SESSION['upload_token']);
  if ($client->isAccessTokenExpired()) {
    unset($_SESSION['upload_token']);
  }
} else {
  $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    //Get user profile data from google
    $UserProfile = $service->userinfo->get();
    var_dump($client->getAccessToken());
    if(!empty($UserProfile)){

        $stmt = $conn->query("SELECT * FROM user WHERE user.email = '".$UserProfile['email']."';");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user == null){
            $query = "INSERT INTO user ( `name`, `email`) VALUES ('".$UserProfile['given_name']." ".$UserProfile['family_name']."', '".$UserProfile['email']."');";
            $stmt = $conn->query($query);

            $last_id = $conn->lastInsertId();

            $query = "INSERT INTO `account`(`user_id`, `google_id`) VALUES (".$last_id.",'".$UserProfile['id']."');";
            $stmt = $conn->query($query);

            $_SESSION["logged_user"] = $last_id;

        }else{
            $stmt = $conn->query("SELECT `google_id` FROM `account` WHERE account.user_id=".$user["id"].";");
            $googleId = $stmt->fetch(PDO::FETCH_ASSOC);

            if($googleId["google_id"] == null){
                $stmt = $conn->query("UPDATE `account` SET `google_id`='".$UserProfile['id']."' WHERE account.user_id=".$user["id"].";");
            }
            $_SESSION["logged_user"] = $user["id"];
        }
        $query = $conn->query("SELECT id from account WHERE account.user_id='".$_SESSION["logged_user"]."';");
        $account = $query->fetch(PDO::FETCH_ASSOC);

        $query = $conn->query("INSERT INTO `access`(`account_id`, `type`) VALUES (".$account["id"].", 'google');");

        header('Location:../detail.php');
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }   
  } else {
      $authUrl = $client->createAuthUrl();
      header('Location:'.filter_var($authUrl, FILTER_SANITIZE_URL));
//      $output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="images/glogin.png" alt=""/></a>';
  }
?>

<!--<div>--><?php //echo $output; ?><!--</div>-->
