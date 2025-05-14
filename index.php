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
        padding: 32px 32px 28px 32px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(106, 13, 173, 0.10);
        width: 360px;
        min-width: 320px;
    }

    .container h1 {
        margin-bottom: 32px;
        font-size: 1.5rem;
        color: #6a0dad;
    }

    .container a {
        text-decoration: none;
        display: block;
        margin-bottom: 18px;
    }

    button {
        width: 90%;
        max-width: 260px;
        padding: 14px 0;
        background-color: #6a0dad;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.08rem;
        font-weight: 600;
        margin: 0 auto;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 2px 8px rgba(106, 13, 173, 0.08);
        display: block;
    }

    button:hover {
        background-color: #4b0082;
        transform: translateY(-2px) scale(1.03);
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
    <a href="registro_inventario.php">
        <button>Registrar Inventario</button>
    </a>
</div>
