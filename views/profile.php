<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$title = 'Perfil de Usuario';
include 'layouts/header.php'; 
?>
<body class="bg-gray-100">
    <!-- Main Content -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="max-w-2xl mx-auto px-6 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-amber-900">Mi Perfil</h1>
                <button class="rounded-full bg-amber-600 p-3 text-white hover:bg-amber-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <p class="text-gray-900">Usuario Demo</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">usuario@ejemplo.com</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'layouts/footer.php'; ?>
</body>
</html>