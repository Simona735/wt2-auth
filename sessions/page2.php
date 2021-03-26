<?php
// page2.php
// prevzate z http://php.net/manual/en/function.session-start.php
session_id($_GET['PHPSESSID']);
session_start();

 
//echo "<br /><br />\$_REQUEST:  ";
//print_r($_REQUEST);
//echo "<br /><br />";
echo "session_id(): ".session_id()."<br />";
echo "\$_GET:     ".$_GET['PHPSESSID']."<br />";
echo "\$_COOKIE:  ".$_COOKIE['PHPSESSID']."<br />";


echo 'Welcome to page #2<br /><br />';

echo $_SESSION['favcolor']."<br />"; // green
echo $_SESSION['animal']."<br />";   // cat
echo date('Y m d H:i:s', $_SESSION['time'])."<br />";

// You may want to use SID here, like we did in page1.php
echo '<br /><a href="page1.php">page 1</a>';

?>
