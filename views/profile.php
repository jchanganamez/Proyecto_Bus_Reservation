<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Perfil Profesional';
include 'layouts/header.php'; 

require_once '../controllers/UserController.php';
$userController = new UserController();
$user = json_decode($userController->getUserById($_SESSION['user_id']), true);

$message = '';

// Manejo del formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    if (empty($nombre) || empty($email) || empty($telefono)) {
        $message = '<p class="text-red-500">Por favor, complete todos los campos.</p>';
    } else {
        $response = $userController->updateProfile($_SESSION['user_id'], $nombre, $email, $telefono);        $responseArray = json_decode($response, true);

        if (isset($responseArray['success']) && $responseArray['success']) {
            $message = '<p class="text-green-500">Perfil actualizado exitosamente.</p>';
            $_SESSION['nombre'] = $nombre;
        } else {
            $message = '<p class="text-red-500">Error al actualizar el perfil: ' . htmlspecialchars($responseArray['error']) . '</p>';
        }
    }
}
?>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="max-w-3xl mx-auto px-6 py-8">
            <h1 class="text-4xl font-bold text-amber-900 mb-8">Perfil</h1>
            <?= $message ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Información del usuario -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Información Personal</h2>
                    <p class="text-gray-600 mt-4"><strong>Nombre:</strong> <?= htmlspecialchars($user['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-gray-600 mt-2"><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-gray-600 mt-2"><strong>Número:</strong> <?= htmlspecialchars($user['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                
                <!-- Formulario de edición -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Editar Perfil</h2>
                    <form method="POST" class="space-y-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline" 
                                   value="<?= htmlspecialchars($user['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline" 
                                   value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                            <input type="text" name="telefono" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline" 
                                   value="<?= htmlspecialchars($user['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include 'layouts/footer.php'; ?>
</body>

<script>
    document.querySelector('#updateProfileForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/api/user/updateProfile', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profile updated successfully');
            } else {
                alert(data.error);
            }
        });
});

</script>
</html>
