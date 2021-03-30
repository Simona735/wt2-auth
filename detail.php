<?php
require_once 'config.php';

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
date_default_timezone_set("Europe/Bratislava");

session_start();
if (isset($_POST["logout"])){
    session_destroy();
    header("Location: index.php");

}else if(isset($_SESSION["logged_user"])){

    $stmt = $conn->query("SELECT id, name, email FROM user WHERE user.id = ".$_SESSION["logged_user"].";");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT user.email, access.timestamp, access.type FROM user JOIN account ON user.id = account.user_id JOIN access on account.id = access.account_id WHERE user.id = ".$_SESSION["logged_user"].";");

    $accesses = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        array_push($accesses, [$row['email'], $row['timestamp'], $row['type']]);
    }

    $stmt2 = $conn->query("SELECT access.type, COUNT(*) as count FROM account JOIN access on account.id = access.account_id GROUP BY access.type;");
    $statistics = [];
    while($row = $stmt2->fetch(PDO::FETCH_ASSOC))
    {
        array_push($statistics, [$row['type'], $row['count']]);
    }

}else{
    header('Location:index.php');
}

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Richterova">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>Detail</title>
</head>
<body class="bg-light">
<div class="container">
    <main>
        <div class="py-5 text-center">
            <h2>Hello <?php echo $user["name"]?></h2>
            <p>Welcome to this page :)</p>
            <form action="detail.php" method="post" class="form-logout">
                <button class="btn btn-warning btn-block mb-2" name="logout" type="submit"><i class="fas fa-sign-in-alt"></i> Log out</button>
            </form>
            <button class="btn btn-success btn-block m-2" id="infoButton" ><i class="fas fa-sign-in-alt"></i> Info</button>
            <button class="btn btn-success btn-block" id="statisticsButton" ><i class="fas fa-sign-in-alt"></i> All statistics</button>

            <div id="statisticsTable" class="table-responsive">
                <table class="table table-hover mt-5" >
                    <thead>
                    <tr class="table-success">
                        <th scope="col" >Login type</th>
                        <th scope="col" >Number of logins</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($statistics as $info){ ?>
                        <tr>
                            <th scope="row"><?php echo $info[0] ?></th>
                            <td><?php echo $info[1] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="infoTable" class="table-responsive">
                <table class="table table-hover mt-5">
                    <thead>
                    <tr class="table-success">
                        <th scope="col" >E-mail</th>
                        <th scope="col" >Time</th>
                        <th scope="col" >Login type</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($accesses as $info){ ?>
                        <tr>
                            <th scope="row"><?php echo $info[0] ?></th>
                            <td><?php echo $info[1] ?></td>
                            <td><?php echo $info[2] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="my-3 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy;2021 WEBTECH2</p>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script src="js/javascript.js"></script>
</body>
</html>