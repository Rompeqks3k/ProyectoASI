<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar si el token es válido y no ha expirado
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expira > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        die("El enlace de recuperación es inválido o ha expirado.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Verificar el token nuevamente
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expira > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset) {
        // Actualizar la contraseña en la base de datos
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        $stmt->execute([$password, $reset['email']]);

        // Eliminar el token de la base de datos
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        echo "Tu contraseña ha sido actualizada correctamente.";
    } else {
        echo "El enlace de recuperación es inválido o ha expirado.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Nueva Contraseña</h2>
        <form method="POST" action="nuevacontraseña.php" class="space-y-4">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Ingresa tu nueva contraseña" 
                    required 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700"
                >
            </div>
            <button 
                type="submit" 
                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                Cambiar Contraseña
            </button>
        </form>
    </div>
</body>
</html>