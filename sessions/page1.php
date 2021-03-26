<?php
// page1.php
// prevzate z http://php.net/manual/en/function.session-start.php

session_start();

echo 'Welcome to page #1<br />';

$_SESSION['favcolor'] = 'green';
$_SESSION['animal']   = 'cat';
$_SESSION['time']     = time();

// Works if session cookie was accepted
echo '<br><a href="page2.php">page 2</a>';

echo '<br><a href="page2.php?'.session_name().'='.session_id().'">page 2</a>';

?>
