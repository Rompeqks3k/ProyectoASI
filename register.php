<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $tipo_doc = $_POST['tipo_documento'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $rol = $_POST['rol'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nombre, apellido, email, usuario, tipo_documento, documento, telefono, direccion, rol, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nombre, $apellido, $email, $usuario, $tipo_doc, $documento, $telefono, $direccion, $rol, $password])) {
        echo "Registro exitoso. <a href='login.php'>Inicia sesión</a>";
    } else {
        echo "Error en el registro.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Registro de Usuario</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                <input type="text" id="apellido" name="apellido" placeholder="Apellido" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="usuario" class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                <select id="tipo_documento" name="tipo_documento"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
                    <option value="CC">Cédula</option>
                    <option value="TI">Tarjeta de Identidad</option>
                </select>
            </div>
            <div>
                <label for="documento" class="block text-sm font-medium text-gray-700">Documento</label>
                <input type="text" id="documento" name="documento" placeholder="Documento" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="text" id="telefono" name="telefono" placeholder="Teléfono" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                <input type="text" id="direccion" name="direccion" placeholder="Dirección" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div>
                <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                <select id="rol" name="rol"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
                    <option value="super_usuario">Super Usuario</option>
                    <option value="digitalizador">Digitalizador</option>
                </select>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700">
            </div>
            <div class="col-span-1 md:col-span-2">
                <button type="submit"
                    class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</body>
</html>
