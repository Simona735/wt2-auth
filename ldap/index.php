<?php
require_once '../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set("Europe/Bratislava");


session_start();
if(isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];


    $ldapconfig['host'] = 'ldap.stuba.sk';//CHANGE THIS TO THE CORRECT LDAP SERVER
    $ldapconfig['port'] = '389';
    $ldapconfig['basedn'] = 'ou=People, DC=stuba, DC=sk';//CHANGE THIS TO THE CORRECT BASE DN
    $ldapconfig['usersdn'] = 'cn=users';//CHANGE THIS TO THE CORRECT USER OU/CN
    $ds = ldap_connect($ldapconfig['host'], $ldapconfig['port']);

    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 10);

    $dn = "uid=" . $username . "," . $ldapconfig['basedn'];
    if (isset($_POST['username'])) {
        if ($bind = ldap_bind($ds, $dn, $password)) {
            echo("Login correct");//REPLACE THIS WITH THE CORRECT FUNCTION LIKE A REDIRECT;
            $sr = ldap_search($ds, 'ou=People, DC=stuba, DC=sk', 'uid=' . $username, ['givenname', 'surname', 'mail'] );
//            var_dump(ldap_get_entries($ds, $sr));
            $name = ldap_get_entries($ds, $sr)[0]["givenname"][0] . " " . ldap_get_entries($ds, $sr)[0]["sn"][0];
            $mail = ldap_get_entries($ds, $sr)[0]["mail"][0];
//            var_dump($name);
//            var_dump($mail);

            $stmt = $conn->query("SELECT * FROM user WHERE user.email = '".$mail."';");
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user == null){
                $query = "INSERT INTO user ( `name`, `email`) VALUES ('".$name."', '".$mail."');";
                $stmt = $conn->query($query);

                $last_id = $conn->lastInsertId();

                $query = "INSERT INTO `account`(`user_id`, `type`) VALUES (".$last_id.",'ldap')";
                $stmt = $conn->query($query);

                $_SESSION["logged_user"] = $last_id;

            }else{
                $_SESSION["logged_user"] = $user["id"];
            }
            $query = $conn->query("SELECT id from account WHERE account.user_id='".$_SESSION["logged_user"]."';");
            $account = $query->fetch(PDO::FETCH_ASSOC);

            $query = $conn->query("INSERT INTO `access`(`account_id`) VALUES (".$account["id"].");");
            header('Location:../detail.php');

        } else {

            echo "Login Failed: Please check your username or password";
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Autentification</title>
</head>
<body>
<div id="logreg-forms">



    <form method="post" action="index.php" class="form-signin">
        <h1 class="h3 mb-3 font-weight-normal text-center" > Sign in</h1>
        <input type="text" id="username" class="form-control" placeholder="Username" name="username" required="" autofocus="">
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">


        <div class="d-grid gap-2">
            <button class="btn btn-success btn-block" type="submit"><i class="fas fa-sign-in-alt"></i> Sign in</button>
        </div>

        <hr>
        <a href="../index.php" ><i class="fas fa-angle-left"></i> Back</a>
    </form>


</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>
