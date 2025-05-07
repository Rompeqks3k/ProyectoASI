<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Recuperar Contraseña</h2>
        <form method="POST" action="procesar_recuperacion.php" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Ingresa tu correo electrónico" 
                    required 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 text-gray-700"
                >
            </div>
            <button 
                type="submit" 
                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                Enviar
            </button>
        </form>
    </div>
</body>
</html>