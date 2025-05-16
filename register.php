<?php
require 'db.php';

$registro_exitoso = false;

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
        $registro_exitoso = true;
    } else {
        echo "<div class='fixed top-6 left-1/2 transform -translate-x-1/2 bg-red-200 text-red-800 px-6 py-3 rounded-lg shadow-lg font-semibold z-50'>Error en el registro.</div>";
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
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <?php if ($registro_exitoso): ?>
        <div id="registro-exitoso" class="fixed top-6 left-1/2 -translate-x-1/2 bg-purple-600 text-white px-8 py-4 rounded-lg shadow-lg font-semibold z-50 text-lg text-center">
            Registro exitoso.
        </div>
    <?php endif; ?>

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl px-10 py-12 flex flex-col items-center" style="margin-top:40px; margin-bottom:40px;">
        <h2 class="text-3xl font-bold text-purple-700 mb-8 text-center">Registro de Usuario</h2>
        <form method="POST" id="form-registro" autocomplete="off" class="w-full flex flex-col gap-5">
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col">
                    <label for="nombre" class="text-purple-700 font-medium mb-1">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <div class="flex-1 flex flex-col">
                    <label for="apellido" class="text-purple-700 font-medium mb-1">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Apellido" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col">
                    <label for="email" class="text-purple-700 font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <div class="flex-1 flex flex-col">
                    <label for="usuario" class="text-purple-700 font-medium mb-1">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col">
                    <label for="tipo_documento" class="text-purple-700 font-medium mb-1">Tipo de Documento</label>
                    <select id="tipo_documento" name="tipo_documento" class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                        <option value="CC">Cédula</option>
                        <option value="TI">Tarjeta de Identidad</option>
                    </select>
                </div>
                <div class="flex-1 flex flex-col">
                    <label for="documento" class="text-purple-700 font-medium mb-1">Documento</label>
                    <input type="text" id="documento" name="documento" placeholder="Documento" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col">
                    <label for="telefono" class="text-purple-700 font-medium mb-1">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" placeholder="Teléfono" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <div class="flex-1 flex flex-col">
                    <label for="direccion" class="text-purple-700 font-medium mb-1">Dirección</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Dirección" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col">
                    <label for="rol" class="text-purple-700 font-medium mb-1">Rol</label>
                    <select id="rol" name="rol" class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                        <option value="super_usuario">Super Usuario</option>
                        <option value="digitalizador">Digitalizador</option>
                    </select>
                </div>
                <div class="flex-1"></div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col">
                    <label for="password" class="text-purple-700 font-medium mb-1">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Contraseña" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <div class="flex-1 flex flex-col">
                    <label for="password2" class="text-purple-700 font-medium mb-1">Verificar Contraseña</label>
                    <input type="password" id="password2" name="password2" placeholder="Repite la contraseña" required class="rounded-xl border border-purple-200 px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
            </div>
            <button type="submit" class="mt-6 bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-lg">
                Registrarse
            </button>
        </form>
        <div class="w-full flex justify-center mt-8">
            <a href="login.php" class="bg-white border-2 border-purple-300 text-purple-700 font-bold py-3 px-10 rounded-xl shadow hover:bg-purple-50 transition text-base">
                Iniciar sesión
            </a>
        </div>
    </div>

    <script>
        // Ocultar la notificación de registro exitoso después de 3 segundos
        window.addEventListener('DOMContentLoaded', function() {
            var alerta = document.getElementById('registro-exitoso');
            if (alerta) {
                setTimeout(function() {
                    alerta.classList.add('opacity-0');
                    setTimeout(function() {
                        alerta.style.display = 'none';
                    }, 700);
                }, 3000);
            }

            // Validar contraseñas iguales antes de enviar el formulario
            document.getElementById('form-registro').addEventListener('submit', function(e) {
                var pass1 = document.getElementById('password').value;
                var pass2 = document.getElementById('password2').value;
                if (pass1 !== pass2) {
                    e.preventDefault();
                    mostrarNotificacion("Las contraseñas no coinciden");
                }
            });

            // Función para mostrar notificación flotante
            function mostrarNotificacion(mensaje) {
                var noti = document.createElement('div');
                noti.className = 'fixed top-6 left-1/2 -translate-x-1/2 bg-red-600 text-white px-8 py-4 rounded-lg shadow-lg font-semibold z-50 text-lg text-center';
                noti.textContent = mensaje;
                document.body.appendChild(noti);
                setTimeout(function() {
                    noti.classList.add('opacity-0');
                    setTimeout(function() {
                        noti.remove();
                    }, 700);
                }, 3000);
            }
        });
    </script>
</body>
</html>
