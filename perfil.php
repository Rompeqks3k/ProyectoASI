<link rel="stylesheet" href="styles.css">
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

    .container {
        text-align: center;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #6a0dad; /* Color morado */
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    button:hover {
        background-color: #4b0082; /* Morado más oscuro al pasar el cursor */
    }

    a {
        text-decoration: none;
    }
</style>

<div class="container">
    <h1>Perfil de <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h1>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
    <p><strong>Apellido:</strong> <?php echo htmlspecialchars($usuario['apellido']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['direccion']); ?></p>
    <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['rol']); ?></p>

    <!-- Botón para cambiar la contraseña -->
    <a href="recuperarcontraseña.php">
        <button>Cambiar Contraseña</button>
    </a>
    <br><br>
    <a href="index.php">
        <button>Volver</button>
    </a>
</div>