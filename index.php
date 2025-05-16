<?php
// Inicia la sesión y verifica si el usuario está autenticado
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluye la conexión a la base de datos
include 'db.php';

// Consulta los datos del usuario actual (nombre, apellido y rol)
$stmt = $pdo->prepare("SELECT nombre, apellido, rol FROM usuarios WHERE usuario = ? OR email = ?");
$stmt->execute([$_SESSION['usuario'], $_SESSION['usuario']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra el usuario, muestra un error y detiene la ejecución
if (!$user) {
    die("Error: No se encontraron datos del usuario.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full min-h-screen flex items-center justify-center">
        <!-- Contenedor principal centrado con sombra y bordes redondeados -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl px-10 py-12 flex flex-col items-center" style="margin-top:40px; margin-bottom:40px;">
            <!-- Mensaje de bienvenida con el nombre y apellido del usuario -->
            <h1 class="text-3xl font-bold text-purple-700 mb-10 text-center tracking-tight">
                Bienvenido, <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?>
            </h1>
            <!-- Menú de acciones principales -->
            <div class="w-full flex flex-col gap-5">
                <!-- Botón para ir al perfil -->
                <a href="perfil.php" class="w-full">
                    <button class="w-full bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg mb-2">
                        Perfil
                    </button>
                </a>
                <!-- Botón para registrar inventario (solo si no es super_usuario) -->
                <?php if (strtolower($user['rol']) !== 'super_usuario'): ?>
                <a href="registro_inventario.php" class="w-full">
                    <button class="w-full bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg mb-2">
                        Registrar Inventario
                    </button>
                </a>
                <?php endif; ?>
                <!-- Botón para ver inventario (solo si no es digitalizador) -->
                <?php if (strtolower($user['rol']) !== 'digitalizador'): ?>
                <a href="registro_inventario.php" class="w-full">
                    <button class="w-full bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg mb-2">
                        Inventario
                    </button>
                </a>
                <?php endif; ?>
                <!-- Botón para cerrar sesión -->
                <a href="logout.php" class="w-full">
                    <button class="w-full bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg mt-2">
                        Cerrar sesión
                    </button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
