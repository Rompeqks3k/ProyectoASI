<?php
$to = "markoandres.mamr@gmail.com"; // Reemplaza con tu correo electrónico
$subject = "Prueba de correo desde PHP";
$message = "Este es un correo de prueba enviado desde PHP usando la función mail().";
$headers = "From: markoandres.mamr@gmail.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Correo enviado correctamente.";
} else {
    echo "Error al enviar el correo.";
}
?>