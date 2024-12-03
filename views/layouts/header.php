<?php define('BASE_URL', '/project/'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'BusBooker'; ?></title>
    <link rel="stylesheet" href="../css/styles.css">    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- Estilos adicionales para la barra lateral -->
    <style>
        /* Estilo para la barra lateral */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        /* Ocultar la barra lateral en pantallas pequeñas */
        #sidebar.hidden {
            transform: translateX(-100%);
        }

        /* Mostrar la barra lateral en pantallas grandes */
        @media (min-width: 640px) {
            #sidebar {
                transform: translateX(0);
            }
        }

        /* Estilos para el botón de menú */
        #menuButton {
            display: inline-block;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            background-color: transparent;
        }

        #menuButton i {
            font-size: 1.5rem;
            color: #4A4A4A;
        }
        .hidden {
            display: none; /* Ocultar el elemento */
        }

        #currency-menu {
            position: absolute; /* Asegúrate de que el menú se posicione correctamente */
            z-index: 10; /* Asegúrate de que esté por encima de otros elementos */
        }
    </style>
</head>

<body class="bg-gray-50 font-inter">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <a href="<?php echo BASE_URL; ?>views/home.php" class="text-white flex items-center space-x-2 px-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span class="text-2xl font-extrabold">BusBooker</span>
            </a>

            <!-- Navegación para Usuario -->
            <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 0): ?>
            <nav>
                <a href="../views/profile.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white flex flex-col items-center justify-center">
                    <i class="bi bi-person-circle text-4xl mb-2"></i>
                    <span class="text-sm">Mi Perfil</span>
                </a>
            </nav>

            <nav>
                <a href="../views/home.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-house-door w-5 h-5 inline-block mr-2"></i>
                    Inicio
                </a>
                <a href="../views/my-trips.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-card-list w-5 h-5 inline-block mr-2"></i>
                    Mis Viajes
                </a>
                <a href="../views/destinations.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-geo-alt w-5 h-5 inline-block mr-2"></i>
                    Destinos
                </a>
            </nav>
            <?php endif; ?>

            <!-- Navegación para Administrador -->
            <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1): ?>
            <nav>
                <a href="<?php echo BASE_URL; ?>views/profile.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white flex flex-col items-center justify-center">
                    <i class="bi bi-person-circle text-4xl mb-2"></i>
                    <span class="text-sm">Mi Perfil</span>
                </a>
            </nav>

            <nav>
                <a href="<?php echo BASE_URL; ?>views/home.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-house-door w-5 h-5 inline-block mr-2"></i>
                    Inicio
                </a>
                <a href="<?php echo BASE_URL; ?>views/my-trips.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-card-list w-5 h-5 inline-block mr-2"></i>
                    Mis Viajes
                </a>
                <a href="<?php echo BASE_URL; ?>views/destinations.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-geo-alt w-5 h-5 inline-block mr-2"></i>
                    Destinos
                </a>
                <div class="px-4 py-2 text-gray-300 font-semibold">Acciones de Administrador</div>
                <a href="<?php echo BASE_URL; ?>views/admin/admin-dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-house-door w-5 h-5 inline-block mr-2"></i>
                    Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>views/admin/users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-person w-5 h-5 inline-block mr-2"></i>
                    Usuarios
                </a>
                <a href="<?php echo BASE_URL; ?>views/admin/buses.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-bus-front w-5 h-5 inline-block mr-2"></i>
                    Buses
                </a>
                <a href="<?php echo BASE_URL; ?>views/admin/conductores.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-person-badge w-5 h-5 inline-block mr-2"></i>
                    Conductores
                </a>
                <a href="<?php echo BASE_URL; ?>views/admin/viajes.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-map w-5 h-5 inline-block mr-2"></i>
                    Viajes
                </a>
            </nav>
            <?php endif; ?>
        </div>
        

        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <!-- Botón de menú (solo visible en pantallas pequeñas) -->
                    <button id="menuButton" class="text-gray-700 sm:hidden" onclick="toggleSidebar()">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <h1 class="text-2xl font-semibold text-amber-900"><?php echo isset($title) ? $title : 'BusBooker'; ?></h1>

                    <div class="flex items-center space-x-4">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-gray-700"><?php echo $_SESSION['nombre'] ?? 'Invitado'; ?></span>
                                <span class="text-sm text-gray-500">(<?php echo (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 0) ? 'Usuario' : 'Administrador' ?>)</span>
                                
                                <!-- Dropdown para selección de moneda -->
                                <div class="relative inline-block text-left">
                                    <div>
                                        <button type="button" id="currency-button" onclick="toggleCurrencyMenu()" class="inline-flex justify-center items-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <span id="currency-symbol">S/</span> <!-- Símbolo de moneda -->
                                        </button>
                                    </div>

                                    <div id="currency-menu" class="absolute right-0 z-10 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="currency-button" tabindex="-1">
                                        <div class="py-1" role="none">
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" onclick="setCurrency('S/'); return false;">Soles (S/)</a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" onclick="setCurrency('$'); return false;">Dólares ($)</a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" onclick="setCurrency('€'); return false;">Euros (€)</a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" onclick="setCurrency('¥'); return false;">Yenes (¥)</a>
                                        </div>
                                    </div>
                                </div>

                                <form action="<?php echo BASE_URL; ?>api/logout.php" method="POST" class="inline">
                                    <button type="submit" class="flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <a href="/views/login.php" class="flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                                Iniciar Sesión
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            

    
<script>
    // Función para alternar la visibilidad de la barra lateral en pantallas pequeñas
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
    }
    function setCurrency(symbol) {
        // Guardar la moneda seleccionada en localStorage
        localStorage.setItem('currency', symbol);
        document.getElementById('currency-symbol').textContent = symbol; // Cambiar el símbolo en el botón
        closeCurrencyMenu(); // Cerrar el menú después de seleccionar
    }

    function toggleCurrencyMenu() {
        const menu = document.getElementById('currency-menu');
        menu.classList.toggle('hidden'); // Alternar la visibilidad del menú
    }

    function closeCurrencyMenu() {
        const menu = document.getElementById('currency-menu');
        menu.classList.add('hidden'); // Asegurarse de que el menú esté oculto
    }

    // Cerrar el menú al hacer clic fuera de él
    window.onclick = function(event) {
        const menu = document.getElementById('currency-menu');
        const button = document.getElementById('currency-button');
        if (!button.contains(event.target) && !menu.contains(event.target)) {
            closeCurrencyMenu(); // Cerrar el menú si se hace clic fuera de él
        }
    }

    // Cargar la moneda seleccionada al cargar la página
    window.onload = function() {
        const currency = localStorage.getItem('currency') || 'S/'; // Obtener la moneda seleccionada o usar 'S/' por defecto
        document.getElementById('currency-symbol').textContent = currency; // Actualizar el símbolo en el botón
    }
</script>
