<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verificar si el correo existe en la base de datos
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generar un token único
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour')); // El token expira en 1 hora

        // Guardar el token en la base de datos
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expira) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expira]);

        // Enviar el enlace de recuperación al correo
        $resetLink = "http://localhost/Proyecto%20Andr%C3%A9s%20Rinc%C3%B3n/nuevacontraseña.php?token=$token";
        $subject = "Recuperación de contraseña";
        $message = "Haz clic en el siguiente enlace para cambiar tu contraseña: $resetLink";
        $headers = "From: no-reply@tu-dominio.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "Se ha enviado un enlace de recuperación a tu correo electrónico.";
        } else {
            echo "Error al enviar el correo. Inténtalo de nuevo.";
        }
    } else {
        echo "El correo electrónico no está registrado.";
    }
}
?>