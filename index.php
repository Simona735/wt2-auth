<?php
require_once 'config.php';
require_once '2FA/PHPGangsta/GoogleAuthenticator.php';

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set("Europe/Bratislava");

session_start();

if(isset($_POST["sign-email"]) && isset($_POST["sign-password"])){
    $email = $_POST["sign-email"];

    $stmt = $conn->query("SELECT * FROM user WHERE user.email = '".$email."';");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user != null){
        $stmt = $conn->query("SELECT password, secret FROM account WHERE account.user_id = '".$user["id"]."';");
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        $secret = $account["secret"];

        if($secret == null){
            ?>
            <script> alert("Prosim zaregistruj sa.");</script>
            <?php
        }
        else {
            if (password_verify($_POST["sign-password"],$account["password"])){
                echo "is OK";

                $_SESSION["user_id"] = $user["id"];
                header("Location: 2FA/login.php");
            }else{
                ?>
                <script> alert("Nesprávne prihlasovacie údaje");</script>
                <?php
            }
        }
    }
}else if(isset($_SESSION["logged_user"])){
    header('Location:detail.php');
}

//***********   QR code    ********************
$websiteTitle = 'Zadanie03';
$ga = new PHPGangsta_GoogleAuthenticator();
$secretNew = $ga->createSecret();
$qrCodeUrl = $ga->getQRCodeGoogleUrl($websiteTitle, $secretNew);
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
    <link rel="stylesheet" href="css/style.css">
    <title>Autentification</title>
</head>
<body>
<div id="logreg-forms">
    <form action="index.php" method="post" class="form-signin">
        <h1 class="h3 mb-3 font-weight-normal text-center"> Sign in</h1>
        <div class="social-login ">
            <div class="d-inline-block">
                <a href="ldap/index.php" class="btn ldap-btn social-btn py-2" type="button"><span>Sign in with <b>AIS</b></span></a>
            </div>
            <div class="d-inline-block">
                <a href="oauth2/index.php" class="btn google-btn social-btn py-2" type="button"><span><i class="fab fa-google-plus-g"></i> Sign in with Google+</span> </a>
            </div>
        </div>
        <p class="text-center" > OR  </p>
        <input type="email" id="inputEmail" name="sign-email" class="form-control" placeholder="Email address" required="" autofocus="">
        <input type="password" id="inputPassword" name="sign-password" class="form-control" placeholder="Password" required="">


        <div class="d-grid gap-2">
            <button class="btn btn-success btn-block" type="submit"><i class="fas fa-sign-in-alt"></i> Sign in</button>
        </div>

        <hr>
        <!-- <p>Don't have an account!</p>  -->
        <div class="d-grid gap-2">
            <button class="btn btn-primary btn-block" type="button" id="btn-signup"><i class="fas fa-user-plus"></i> Sign up New Account</button>
        </div>
    </form>



    <form action="2FA/signup.php" method="post" class="form-signup">
        <div class="social-login">
            <a href="ldap/index.php" class="btn ldap-btn social-btn" ><span>Sign in with <b>AIS</b></span></a>
        </div>
        <div class="social-login">
            <a href="oauth2/index.php" class="btn google-btn social-btn" type="button"><span><i class="fab fa-google-plus-g"></i> Sign up with Google+</span> </a>
        </div>

        <p class="text-center">OR</p>

        <input type="text" id="user-name" name="name" class="form-control" placeholder="Full name" required="" autofocus="">
        <input type="email" id="user-email" name="email" class="form-control" placeholder="Email address" required autofocus="">
        <input type="password" id="user-pass" name="password" class="form-control" placeholder="Password" required autofocus="">

        <h5 class="text-center mt-4">Please scan this QR code in Google Authenticator app</h5>
        <div class="text-center">
            <img src="<?php echo $qrCodeUrl; ?>" />
            <input type="text" id="code" name="code" class="form-control mt-2" placeholder="App code" required="" autofocus="">
            <input type="hidden" id="secret" name="secret" value="<?php echo $secretNew?>">
        </div>
        <hr>
        <div class="text-center">
            <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-user-plus"></i> Sign Up</button>
        </div>
        <a href="#" id="cancel_signup"><i class="fas fa-angle-left"></i> Back</a>
    </form>
    <br>

</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
<script src="js/javascript.js"></script>
</body>
</html>