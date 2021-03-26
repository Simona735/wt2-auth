<?php
session_start();
$_SESSION['meno']="Andrej";
echo "meno je: ".$_SESSION['meno']."<br />";
session_unset();
echo "meno je: ".$_SESSION['meno']."<br />";
?>
