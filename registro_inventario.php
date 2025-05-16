<?php
// Inicia la sesión y verifica si el usuario está autenticado
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluye la conexión a la base de datos
include 'db.php';

// Obtiene el rol del usuario actual para determinar permisos
$stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE usuario = ? OR email = ?");
$stmt->execute([$_SESSION['usuario'], $_SESSION['usuario']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$es_digitalizador = (isset($user['rol']) && strtolower($user['rol']) === 'digitalizador');

// Obtiene la lista de personas responsables para mostrar en formularios si es necesario
$stmt = $pdo->query("SELECT id_persona, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM personas");
$personas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables para mensajes de éxito o error
$mensaje_csv = '';
$mensaje_error = '';

// Maneja la carga de archivos CSV solo si el usuario es digitalizador
if ($es_digitalizador && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $caracteres_peligrosos = "/[;\'\"--]/"; // Expresión regular para detectar caracteres peligrosos
    $linea = 1;
    $csv_invalido = false;
    // Abre el archivo CSV para validación previa
    if (($handle = fopen($file, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $linea++;
            // Verifica que cada fila tenga exactamente 7 columnas
            if (count($data) !== 7) {
                $mensaje_error = "Formato CSV inválido en la línea $linea.";
                $csv_invalido = true;
                break;
            }
            // Busca caracteres peligrosos en cada campo para evitar inyección
            foreach ($data as $campo) {
                if (preg_match($caracteres_peligrosos, $campo)) {
                    $mensaje_error = "Intento de inyección detectado en la línea $linea. El archivo fue rechazado.";
                    $csv_invalido = true;
                    break 2;
                }
            }
            // Verifica que los campos numéricos sean realmente números
            if (!is_numeric($data[0]) || !is_numeric($data[6])) {
                $mensaje_error = "Campos numéricos inválidos en la línea $linea.";
                $csv_invalido = true;
                break;
            }
        }
        fclose($handle);
    }
    // Si el CSV es seguro, procesa e inserta los datos en la base de datos
    if (!$csv_invalido) {
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $id_persona_csv = (int)$data[6];
            // Verifica si la persona existe, si no la crea con nombre y apellido por defecto
            $stmt = $pdo->prepare("SELECT 1 FROM personas WHERE id_persona = ?");
            $stmt->execute([$id_persona_csv]);
            if (!$stmt->fetch()) {
                $stmt_insert = $pdo->prepare("INSERT INTO personas (id_persona, nombre, apellido) VALUES (?, 'SinNombre', 'SinApellido')");
                $stmt_insert->execute([$id_persona_csv]);
            }
            try {
                // Inserta el registro en la tabla inventario
                $stmt = $pdo->prepare("INSERT INTO inventario (id_inventario, marca, modelo, serial, categoria, estado, id_persona) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute($data);
            } catch (PDOException $e) {
                // Captura errores de inserción (por ejemplo, duplicados)
                $mensaje_error = "Error al cargar CSV: " . strtok($e->getMessage(), "\n");
                break;
            }
        }
        fclose($handle);
        if (!$mensaje_error) {
            $mensaje_csv = "Archivo CSV cargado exitosamente.";
        }
    }
}

// Maneja el registro manual de inventario solo si el usuario es digitalizador
if ($es_digitalizador && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marca'])) {
    // Valida que el ID de persona sea numérico
    if (!is_numeric($_POST['id_persona'])) {
        $mensaje_error = "El ID de la persona debe ser numérico.";
    } else {
        $id_persona = (int)$_POST['id_persona'];
        // Verifica si la persona existe, si no la crea con nombre y apellido por defecto
        $stmt = $pdo->prepare("SELECT 1 FROM personas WHERE id_persona = ?");
        $stmt->execute([$id_persona]);
        if (!$stmt->fetch()) {
            $stmt_insert = $pdo->prepare("INSERT INTO personas (id_persona, nombre, apellido) VALUES (?, 'SinNombre', 'SinApellido')");
            $stmt_insert->execute([$id_persona]);
        }
        try {
            // Inserta el registro en la tabla inventario, con o sin ID de inventario según corresponda
            if (!empty($_POST['id_inventario']) && is_numeric($_POST['id_inventario'])) {
                $stmt = $pdo->prepare("INSERT INTO inventario (id_inventario, marca, modelo, serial, categoria, estado, id_persona) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    (int)$_POST['id_inventario'],
                    $_POST['marca'],
                    $_POST['modelo'],
                    $_POST['serial'],
                    $_POST['categoria'],
                    $_POST['estado'],
                    $id_persona
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO inventario (marca, modelo, serial, categoria, estado, id_persona) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['marca'],
                    $_POST['modelo'],
                    $_POST['serial'],
                    $_POST['categoria'],
                    $_POST['estado'],
                    $id_persona
                ]);
            }
            $mensaje_csv = "Elemento registrado exitosamente.";
        } catch (PDOException $e) {
            $mensaje_error = "Error al registrar, información duplicada";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <!-- Botón "Atrás" siempre visible en la esquina superior izquierda -->
    <a href="index.php" class="fixed top-8 left-8 z-50 bg-purple-700 hover:bg-purple-900 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition text-base">
        Atrás
    </a>
    <div class="w-full min-h-screen flex flex-col items-center justify-center py-10">
        <!-- Contenedor principal centrado con sombra y bordes redondeados -->
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl px-10 py-12 flex flex-col items-center gap-10">
            <h1 class="text-3xl font-bold text-purple-700 mb-2 text-center tracking-tight">Registro de Inventario</h1>

            <!-- Notificación de error flotante si ocurre algún problema -->
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

            <!-- Notificación de éxito al cargar CSV o registrar manualmente -->
            <?php if (!empty($mensaje_csv)): ?>
                <div id="alerta-csv" class="mb-6 w-full bg-purple-100 border border-purple-300 text-purple-800 px-6 py-3 rounded-lg shadow font-semibold text-center transition">
                    <?php echo $mensaje_csv; ?>
                </div>
                <script>
                    setTimeout(function() {
                        var alerta = document.getElementById('alerta-csv');
                        if (alerta) alerta.style.display = 'none';
                    }, 4000);
                </script>
            <?php endif; ?>

            <?php if ($es_digitalizador): ?>
            <!-- Formulario para cargar archivo CSV -->
            <form method="POST" enctype="multipart/form-data" class="w-full max-w-lg bg-gray-50 rounded-xl shadow p-6 flex flex-col gap-4">
                <h2 class="text-xl font-semibold text-purple-700 mb-2">Carga Automática (CSV)</h2>
                <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition">
                <button type="submit" class="mt-2 bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-2 rounded-xl shadow transition text-base">
                    Cargar CSV
                </button>
            </form>

            <!-- Formulario para registro manual de inventario -->
            <form method="POST" class="w-full max-w-lg bg-gray-50 rounded-xl shadow p-6 flex flex-col gap-4 mt-8">
                <h2 class="text-xl font-semibold text-purple-700 mb-2">Registro Manual</h2>
                <!-- Campo ID Inventario (opcional) -->
                <div class="flex flex-col gap-2">
                    <label for="id_inventario" class="text-purple-700 font-medium">ID Inventario (opcional):</label>
                    <input type="number" name="id_inventario" id="id_inventario" min="1" class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <!-- Campo Marca -->
                <div class="flex flex-col gap-2">
                    <label for="marca" class="text-purple-700 font-medium">Marca:</label>
                    <input type="text" name="marca" id="marca" required class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <!-- Campo Modelo -->
                <div class="flex flex-col gap-2">
                    <label for="modelo" class="text-purple-700 font-medium">Modelo:</label>
                    <input type="text" name="modelo" id="modelo" required class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <!-- Campo Serial -->
                <div class="flex flex-col gap-2">
                    <label for="serial" class="text-purple-700 font-medium">Serial:</label>
                    <input type="text" name="serial" id="serial" required class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <!-- Campo Categoría -->
                <div class="flex flex-col gap-2">
                    <label for="categoria" class="text-purple-700 font-medium">Categoría:</label>
                    <select name="categoria" id="categoria" required class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                        <option value="Portátil">Portátil</option>
                        <option value="Impresora">Impresora</option>
                        <option value="Monitor">Monitor</option>
                        <!-- Agrega más categorías según sea necesario -->
                    </select>
                </div>
                <!-- Campo Estado -->
                <div class="flex flex-col gap-2">
                    <label for="estado" class="text-purple-700 font-medium">Estado:</label>
                    <select name="estado" id="estado" required class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                        <option value="Operativo">Operativo</option>
                        <option value="Dañado">Dañado</option>
                        <!-- Agrega más estados según sea necesario -->
                    </select>
                </div>
                <!-- Campo Persona Responsable -->
                <div class="flex flex-col gap-2">
                    <label for="id_persona" class="text-purple-700 font-medium">Persona Responsable (ID):</label>
                    <input type="number" name="id_persona" id="id_persona" required min="1" class="rounded-lg border border-purple-200 px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                </div>
                <button type="submit" class="mt-4 bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 text-white font-bold py-2 rounded-xl shadow transition text-base">
                    Registrar
                </button>
            </form>
            <?php endif; ?>

            <!-- Tabla para mostrar el inventario registrado -->
            <div class="w-full flex flex-col items-center mt-10">
                <h2 class="text-2xl font-bold text-purple-700 mb-6 text-center">Inventario Registrado</h2>
                <div class="overflow-x-auto w-full">
                    <table class="min-w-full bg-white rounded-xl shadow text-gray-700 text-base">
                        <thead>
                            <tr class="bg-purple-700 text-white">
                                <th class="py-3 px-4 border-b border-purple-200">ID Inventario</th>
                                <th class="py-3 px-4 border-b border-purple-200">Marca</th>
                                <th class="py-3 px-4 border-b border-purple-200">Modelo</th>
                                <th class="py-3 px-4 border-b border-purple-200">Serial</th>
                                <th class="py-3 px-4 border-b border-purple-200">Categoría</th>
                                <th class="py-3 px-4 border-b border-purple-200">Estado</th>
                                <th class="py-3 px-4 border-b border-purple-200">ID Persona</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Consulta y muestra todos los registros de inventario
                        $stmt = $pdo->query("SELECT * FROM inventario ORDER BY id_inventario DESC");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr class='bg-purple-50 hover:bg-purple-100 transition'>";
                            echo "<td class='py-2 px-4 border-b border-purple-100 text-center'>{$row['id_inventario']}</td>";
                            echo "<td class='py-2 px-4 border-b border-purple-100'>{$row['marca']}</td>";
                            echo "<td class='py-2 px-4 border-b border-purple-100'>{$row['modelo']}</td>";
                            echo "<td class='py-2 px-4 border-b border-purple-100'>{$row['serial']}</td>";
                            echo "<td class='py-2 px-4 border-b border-purple-100'>{$row['categoria']}</td>";
                            echo "<td class='py-2 px-4 border-b border-purple-100'>{$row['estado']}</td>";
                            echo "<td class='py-2 px-4 border-b border-purple-100 text-center'>{$row['id_persona']}</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Espacio extra para estética en la parte inferior -->
        <div class="h-16"></div>
    </div>
</body>
</html>