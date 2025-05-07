<link rel="stylesheet" href="styles.css">
<?php
session_start();
session_destroy();
header("Location: login.php");
exit();

?>
