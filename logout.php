<link rel="stylesheet" href="styles.css">
<?php
// Inicia la sesión para poder destruirla
session_start();

// Destruye todas las variables de sesión y la sesión misma
session_destroy();

// Redirige al usuario a la página de inicio de sesión
header("Location: login.php");
exit();
?>
