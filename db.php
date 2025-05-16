<?php
// Cargar variables de entorno desde el archivo .env para mayor seguridad y flexibilidad
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    // Lee el archivo línea por línea
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignora líneas sin '=' o que comiencen con '#'
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            // Separa la clave y el valor
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Establece la variable de entorno y la guarda en $_ENV
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Obtiene las variables de entorno necesarias para la conexión
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

try {
    // Crea la conexión PDO a la base de datos PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    // Configura el modo de error para que lance excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si ocurre un error, muestra el mensaje y detiene la ejecución
    die("Error en la conexión: " . $e->getMessage());
}
?>