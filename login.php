<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['email']; // Puede ser usuario o email
    $password = $_POST['password'];

    // Ajustar la consulta para buscar por usuario o email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['usuario'] = $user['usuario']; // Guarda el nombre de usuario en la sesión
        $_SESSION['rol'] = $user['rol']; // Guarda el rol del usuario
        header("Location: index.php");
        exit();
    } else {
        echo "Credenciales incorrectas";
    }
}
?>
<link rel="stylesheet" href="styles.css">

<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
    }

    form {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 300px;
    }

    input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #6a0dad; /* Color morado */
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #4b0082; /* Morado más oscuro al pasar el cursor */
    }
</style>

<form method="POST">
    <h2>Iniciar Sesión</h2>
    <input type="text" name="email" placeholder="Usuario o Email" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Iniciar sesión</button>
</form>
