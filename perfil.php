<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de conexión
include 'db.php';

// Ajustar la consulta para buscar por el campo correcto
$sql = "SELECT nombre, apellido, email, telefono, direccion, rol FROM usuarios WHERE usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Error: No se encontraron datos del usuario. Verifica que el campo 'usuario' en la base de datos coincida con la sesión.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <!-- Botón Atrás -->
    <a href="index.php" class="fixed top-8 left-8 z-50 bg-purple-700 hover:bg-purple-900 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition text-base">
        Atrás
    </a>
    <div class="w-full min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl px-10 py-12 flex flex-col items-center" style="margin-top:40px; margin-bottom:40px;">
            <h1 class="text-3xl font-bold text-purple-700 mb-6 text-center tracking-tight">Perfil de <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h1>
            <div class="w-full flex flex-col gap-4 text-gray-700 text-base">
                <div class="flex flex-col md:flex-row md:justify-between">
                    <span class="font-semibold">Nombre:</span>
                    <span><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                </div>
                <div class="flex flex-col md:flex-row md:justify-between">
                    <span class="font-semibold">Apellido:</span>
                    <span><?php echo htmlspecialchars($usuario['apellido']); ?></span>
                </div>
                <div class="flex flex-col md:flex-row md:justify-between">
                    <span class="font-semibold">Email:</span>
                    <span><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
                <div class="flex flex-col md:flex-row md:justify-between">
                    <span class="font-semibold">Teléfono:</span>
                    <span><?php echo htmlspecialchars($usuario['telefono']); ?></span>
                </div>
                <div class="flex flex-col md:flex-row md:justify-between">
                    <span class="font-semibold">Dirección:</span>
                    <span><?php echo htmlspecialchars($usuario['direccion']); ?></span>
                </div>
                <div class="flex flex-col md:flex-row md:justify-between">
                    <span class="font-semibold">Rol:</span>
                    <span><?php echo htmlspecialchars($usuario['rol']); ?></span>
                </div>
            </div>
            <a href="recuperarcontraseña.php" class="mt-8 w-full">
                <button class="w-full bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg">
                    Cambiar Contraseña
                </button>
            </a>
        </div>
    </div>
</body>
</html>