<link rel="stylesheet" href="styles.css">
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de conexión
include 'db.php';

// Obtener los datos del usuario desde la base de datos
$stmt = $pdo->prepare("SELECT nombre, apellido FROM usuarios WHERE usuario = ? OR email = ?");
$stmt->execute([$_SESSION['usuario'], $_SESSION['usuario']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Error: No se encontraron datos del usuario.");
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
    <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></h1>
    <a href="logout.php">
        <button>Cerrar sesión</button>
    </a>
    <a href="perfil.php">
        <button>Perfil</button>
    </a>
</div>
