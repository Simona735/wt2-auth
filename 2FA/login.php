<?php
require_once '../config.php';
require_once 'PHPGangsta/GoogleAuthenticator.php';

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set("Europe/Bratislava");

session_start();
if(isset($_SESSION["user_id"])){
    if(isset($_POST["code"])){
        $query = $conn->query("SELECT id, password, secret from account WHERE account.user_id='".$_SESSION["user_id"]."';");
        $account = $query->fetch(PDO::FETCH_ASSOC);
        $secret = $account["secret"];
        $code = $_POST["code"];

        $ga = new PHPGangsta_GoogleAuthenticator();
        $result = $ga->verifyCode($secret, $code);

        if ($result == 1) {
            echo 'Logged in';
            $_SESSION["logged_user"] = $_SESSION["user_id"];
            unset($_SESSION["user_id"]);
            $query = $conn->query("INSERT INTO `access`(`account_id`, `type`) VALUES (".$account["id"].", '2fa');");
            header('Location:../detail.php');
        } else {
            ?>
            <script> alert("Nesprávny kód");</script>
            <?php
        }
    }
}else if(isset($_SESSION["logged_user"])){  //TODO nechce ma presmerovat ak som prihlasena
    header('Location:../detail.php');

}else{
    echo "why are you here? -_-";
    header('Location:../index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Login</title>
</head>
<body>
    <div id="logreg-forms">
        <form action="login.php" method="post" class="form-signin">
            <h1 class="h3 mb-3 font-weight-normal text-center">Enter code</h1>

            <input type="text" class="form-control" placeholder="code" name="code" id="googlecode" required="" autofocus="" />

            <div class="d-grid gap-2">
                <button class="btn btn-success btn-block" id="submit-googlecode" type="submit"><i class="fas fa-sign-in-alt"></i> Sign in</button>
            </div>

            <hr>
            <a href="../index.php" ><i class="fas fa-angle-left"></i> Back</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>

</body>
</html>