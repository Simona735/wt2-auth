<?php
    require_once 'PHPGangsta/GoogleAuthenticator.php';
    $secret = 'G6XRHY3QCAF2KPSS';
 
    if (isset($_POST['code'])){
        $code = $_POST['code'];
 
        $ga = new PHPGangsta_GoogleAuthenticator();
        $result = $ga->verifyCode($secret, $code);
 
        if ($result == 1) {
            echo $result;
        } else {
            echo 'Login failed';
        }
    }
?>