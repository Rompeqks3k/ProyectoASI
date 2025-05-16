<?php
// Inicia la sesión y conecta con la base de datos
session_start();
require 'db.php';

// Variable para mostrar mensaje de error si las credenciales son incorrectas
$mensaje_error = '';

// Procesa el formulario de inicio de sesión cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['email']; // Puede ser usuario o email
    $password = $_POST['password'];

    // Consulta para buscar el usuario por nombre de usuario o email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica si el usuario existe y la contraseña es correcta
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['usuario'] = $user['usuario']; // Guarda el nombre de usuario en la sesión
        $_SESSION['rol'] = $user['rol']; // Guarda el rol del usuario
        header("Location: index.php");
        exit();
    } else {
        // Muestra mensaje de error si las credenciales no coinciden
        $mensaje_error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl px-10 py-12 flex flex-col items-center" style="margin-top:40px; margin-bottom:40px;">
            <!-- Título de la página -->
            <h2 class="text-3xl font-bold text-purple-700 mb-8 text-center tracking-tight">Iniciar Sesión</h2>
            <!-- Notificación de error si las credenciales son incorrectas -->
            <?php if (!empty($mensaje_error)): ?>
                <div id="noti-error" class="mb-6 w-full bg-red-100 border border-red-300 text-red-700 px-6 py-3 rounded-lg shadow font-semibold text-center transition">
                    <?php echo htmlspecialchars($mensaje_error); ?>
                </div>
                <script>
                    setTimeout(function() {
                        var noti = document.getElementById('noti-error');
                        if (noti) noti.style.display = 'none';
                    }, 4000);
                </script>
            <?php endif; ?>
            <!-- Formulario de inicio de sesión -->
            <form method="POST" autocomplete="off" class="w-full flex flex-col gap-6">
                <div class="flex flex-col">
                    <label for="email" class="text-purple-700 font-medium mb-1">Usuario o Email</label>
                    <input type="text" name="email" id="email" placeholder="Usuario o Email" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition text-base">
                </div>
                <div class="flex flex-col">
                    <label for="password" class="text-purple-700 font-medium mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="Contraseña" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition text-base">
                </div>
                <button type="submit" class="mt-4 bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg tracking-wide">
                    Entrar
                </button>
            </form>
            <!-- Enlace para crear cuenta, mostrado como texto pequeño -->
            <div class="w-full flex justify-center mt-8">
                <a href="register.php" class="text-purple-700 font-semibold hover:underline text-base transition">
                    ¿No tienes cuenta? <span class="underline">Crear cuenta</span>
                </a>
            </div>
            <!-- agregar más contenido dinámico como tablas, gráficas, etc. -->
        </div>
    </div>
</body>
</html>
