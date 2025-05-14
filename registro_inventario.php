<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de conexión
include 'db.php';

// Obtener la lista de personas responsables
$stmt = $pdo->query("SELECT id_persona, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM personas");
$personas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejar la carga de archivos CSV
$mensaje_csv = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Validar que la fila tenga 7 columnas
            if (count($data) !== 7) {
                continue; // O muestra un mensaje de error si prefieres
            }
            // Verificar si la persona existe
            $id_persona_csv = (int)$data[6];
            $stmt = $pdo->prepare("SELECT 1 FROM personas WHERE id_persona = ?");
            $stmt->execute([$id_persona_csv]);
            if (!$stmt->fetch()) {
                $stmt_insert = $pdo->prepare("INSERT INTO personas (id_persona, nombre, apellido) VALUES (?, 'SinNombre', 'SinApellido')");
                $stmt_insert->execute([$id_persona_csv]);
            }
            // Insertar en inventario
            $stmt = $pdo->prepare("INSERT INTO inventario (id_inventario, marca, modelo, serial, categoria, estado, id_persona) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($data);
        }
        fclose($handle);
        $mensaje_csv = "Archivo CSV cargado exitosamente.";
    }
}

// Manejar el registro manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marca'])) {
    // Validar que id_persona sea un número
    if (!is_numeric($_POST['id_persona'])) {
        die("Error: El ID de la persona responsable debe ser un número.");
    }

    $id_persona = (int)$_POST['id_persona'];

    // Verificar si la persona existe
    $stmt = $pdo->prepare("SELECT 1 FROM personas WHERE id_persona = ?");
    $stmt->execute([$id_persona]);
    if (!$stmt->fetch()) {
        // Si no existe, crear una persona básica (puedes pedir más datos si lo deseas)
        $stmt_insert = $pdo->prepare("INSERT INTO personas (id_persona, nombre, apellido) VALUES (?, 'SinNombre', 'SinApellido')");
        $stmt_insert->execute([$id_persona]);
    }

    // Si se ingresó un id_inventario, usarlo en el INSERT
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
        // Si no, dejar que la BD lo genere automáticamente
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
    echo "Elemento registrado exitosamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Inventario</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Botón flotante a la izquierda, siempre visible */
        .btn-atras {
            position: fixed;
            top: 30px;
            left: 30px;
            z-index: 1000;
            background: #6C3483;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(108,52,131,0.15);
            transition: background 0.2s;
        }
        .btn-atras:hover {
            background: #512E5F;
        }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var alerta = document.getElementById('alerta-csv');
            if (alerta) {
                setTimeout(function() {
                    alerta.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</head>
<body>
    <!-- Botón "Atrás" siempre visible -->
    <a href="index.php" class="btn-atras">Atrás</a>
    <div class="container">
        <h1>Registro de Inventario</h1>

        <!-- Notificación de carga CSV -->
        <?php if (!empty($mensaje_csv)): ?>
            <div id="alerta-csv" style="background:#D1C4E9;color:#4A235A;padding:14px 0;margin-bottom:18px;border-radius:6px;font-weight:bold;text-align:center;">
                <?php echo $mensaje_csv; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para cargar archivo CSV -->
        <form method="POST" enctype="multipart/form-data">
            <h2>Carga Automática (CSV)</h2>
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit">Cargar CSV</button>
        </form>

        <hr>

        <!-- Formulario para registro manual -->
        <form method="POST">
            <h2>Registro Manual</h2>
            <label for="id_inventario">ID Inventario (opcional):</label>
            <input type="number" name="id_inventario" id="id_inventario" min="1">

            <label for="marca">Marca:</label>
            <input type="text" name="marca" id="marca" required>

            <label for="modelo">Modelo:</label>
            <input type="text" name="modelo" id="modelo" required>

            <label for="serial">Serial:</label>
            <input type="text" name="serial" id="serial" required>

            <label for="categoria">Categoría:</label>
            <select name="categoria" id="categoria" required>
                <option value="Portátil">Portátil</option>
                <option value="Impresora">Impresora</option>
                <option value="Monitor">Monitor</option>
                <!-- Agrega más categorías según sea necesario -->
            </select>

            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="Operativo">Operativo</option>
                <option value="Dañado">Dañado</option>
                <!-- Agrega más estados según sea necesario -->
            </select>

            <label for="id_persona">Persona Responsable (ID):</label>
            <input type="number" name="id_persona" id="id_persona" required min="1">

            <button type="submit">Registrar</button>
        </form>

        <hr>

        <!-- Mostrar inventario registrado -->
        <div style="display: flex; flex-direction: column; align-items: center;">
            <h2 style="text-align: center; color: #6C3483;">Inventario Registrado</h2>
            <table style="
                border-collapse: collapse;
                min-width: 800px;
                background: #fff;
                box-shadow: 0 2px 8px rgba(108,52,131,0.10);
                margin-bottom: 40px;
                ">
                <thead>
                    <tr style="background: #6C3483; color: #fff;">
                        <th style="padding: 10px; border: 1px solid #B39DDB;">ID Inventario</th>
                        <th style="padding: 10px; border: 1px solid #B39DDB;">Marca</th>
                        <th style="padding: 10px; border: 1px solid #B39DDB;">Modelo</th>
                        <th style="padding: 10px; border: 1px solid #B39DDB;">Serial</th>
                        <th style="padding: 10px; border: 1px solid #B39DDB;">Categoría</th>
                        <th style="padding: 10px; border: 1px solid #B39DDB;">Estado</th>
                        <th style="padding: 10px; border: 1px solid #B39DDB;">ID Persona</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM inventario ORDER BY id_inventario DESC");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr style='background: #F3E5F5; color: #4A235A;'>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB; text-align:center;'>{$row['id_inventario']}</td>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB;'>{$row['marca']}</td>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB;'>{$row['modelo']}</td>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB;'>{$row['serial']}</td>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB;'>{$row['categoria']}</td>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB;'>{$row['estado']}</td>";
                    echo "<td style='padding: 8px; border: 1px solid #B39DDB; text-align:center;'>{$row['id_persona']}</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
            <!-- Espacio extra para estética -->
            <div style="height: 60px;"></div>
        </div>
    </div>
</body>
</html>