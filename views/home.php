<?php
require_once '../config/database.php'; // Asegúrate de que la ruta sea correcta

// Verificar si el usuario está logueado
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Crear conexión a la base de datos
$database = new Database();
$conn = $database->getConnection(); // Asegúrate de que la conexión se establezca

// Obtener lista de ciudades desde la tabla destinos
$ciudades = $conn->query("SELECT DISTINCT nombre_ciudad FROM destinos ORDER BY nombre_ciudad")->fetchAll(PDO::FETCH_ASSOC);
$title = "Inicio - BusBooker";
include 'layouts/header.php';
?>
<style>
    .btn-viaje {
    width: 100%; /* Ocupa todo el ancho */
    height: 3rem; /* Altura de 12 unidades (3 * 4px por unidad) */
    font-weight: bold; /* Negrita */
    border-radius: 0.375rem; /* Bordes redondeados */
    background-color: #f97316; /* Color naranja inicial (#orange-500 en Tailwind) */
    color: white; /* Texto en blanco */
    border: none; /* Sin borde */
    cursor: pointer; /* Indicador de clic */
    transition: background-color 0.2s ease-in-out; /* Transición suave */
}

.btn-viaje:hover {
    background-color: #c2410c; /* Color naranja más oscuro al pasar el cursor (#orange-700 en Tailwind) */
}

</style>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
    <div class="container mx-auto px-6 py-8">
        <!-- Hero Section -->
        <div style="background-color: #F59E0B;" class="relative bg-orange-600 rounded-3xl p-12 mb-8 shadow-2xl overflow-hidden flex items-center justify-between">
            <div class="relative z-10">
                <h1 class="text-5xl font-bold text-white mb-4">Viaja con Comodidad y Seguridad</h1>
                <p class="text-xl text-amber-100">Reserva tus pasajes en bus con solo unos clics</p>
            </div>
            <i class="fas fa-bus text-white text-3xl sm:text-4xl md:text-6xl opacity-20"></i>
        </div>

        <!-- Search Form -->
        <div class="grid md:grid-cols-3 gap-8 mb-12 px-6 md:px-16">
    <!-- Formulario de selección de viaje -->
    <form action="bus-selection.php" method="GET" class="md:col-span-2 bg-white rounded-2xl shadow-xl p-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div class="relative">
                <label class="block text-lg font-medium text-amber-700 mb-2">Ciudad de partida:</label>
                <select id="origen" name="origen" required onchange="updateDestinoOptions()" class="block w-full h-20 rounded-lg border-2 border-amber-200 bg-orange-100 text-amber-700 text-lg focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition-all">
                    <option value="">Selecciona una ciudad</option>
                    <?php foreach ($ciudades as $ciudad): ?>
                        <option value="<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>">
                            <?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="relative">
                <label class="block text-lg font-medium text-amber-700 mb-2">Ciudad de destino:</label>
                <select id="destino" name="destino" required class="block w-full h-20 rounded-lg border-2 border-amber-200 bg-orange-100 text-amber-700 text-lg focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition-all">
                    <option value="">Selecciona una ciudad</option>
                    <?php foreach ($ciudades as $ciudad): ?>
                        <option value="<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>">
                            <?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="relative">
                <label class="block text-lg font-medium text-amber-700 mb-2">Fecha de salida:</label>
                <input type="date" id="fecha_ida" name="fecha_ida" required class="block w-full h-20 rounded-lg border-2 border-amber-200 bg-orange-100 text-amber-700 text-lg focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition-all">
            </div>
            <div class="relative">
                <label class="block text-lg font-medium text-amber-700 mb-2">Fecha de retorno (Opcional):</label>
                <input type="date" id="fecha_vuelta" name="fecha_vuelta" class="block w-full h-20 rounded-lg border-2 border-amber-200 bg-orange-100 text-amber-700 text-lg focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition-all">
            </div>
        </div>
        <button type="submit" class="btn-viaje">Buscar Buses Disponibles</button>
        </form>

    <!-- Información del sitio y últimas novedades -->
    <div class="md:col-span-1 space-y-6">
        <!-- Por qué elegirnos -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-xl font-bold text-amber-900 mb-6">¿Por qué elegirnos?</h2>
            <div class="space-y-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-amber-900">Seguridad Primero</h3>
                        <p class="text-amber-700 text-sm">Todos nuestros buses pasan rigurosas pruebas de seguridad</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-amber-900">Soporte 24/7</h3>
                        <p class="text-amber-700 text-sm">Nuestro equipo de atención siempre está para ayudarte</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Últimas novedades -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 border border-amber-100">
            <h3 class="text-lg font-semibold text-amber-900 mb-4">Últimas Novedades</h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🎉</span>
                    <p class="text-amber-700">¡Nuevas rutas añadidas para temporada de verano!</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🌟</span>
                    <p class="text-amber-700">Obtén 10% de descuento en viajes de ida y vuelta</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">💺</span>
                    <p class="text-amber-700">Asientos premium disponibles en todas las rutas</p>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Stats Section -->
        <div class="grid grid-cols-3 gap-8 my-12">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-amber-900">10,000+</p>
                <p class="text-amber-600 font-medium">Usuarios Activos</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-amber-900">500+</p>
                <p class="text-amber-600 font-medium">Buses en Flota</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h- ```php
                    8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-amber-900">1,000+</p>
                <p class="text-amber-600 font-medium">Viajes Diarios</p>
            </div>
        </div>

        <!-- Popular Destinations -->
        <div class="my-12">
            <h2 class="text-3xl font-bold text-amber-900 mb-8">Destinos Más Populares</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Cusco -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform hover:scale-105 transition-all">
                    <div class="h-48 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1" 
                             alt="Cusco" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-2 right-2 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            98% popularidad
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-amber-900 mb-2">Cusco</h3>
                        <p class="text-amber-600 text-sm mb-4">Maravilla mundial, hogar de Machu Picchu</p>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-amber-500">Desde S/120</span>
                            <button class="px-4 py-2 bg-amber-100 text-amber-600 rounded-full text-sm font-medium hover:bg-amber-200 transition-colors">
                                Reservar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Arequipa -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform hover:scale-105 transition-all">
                    <div class="h-48 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1596402184320-417e7178b2cd"
                            alt="Arequipa" 
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-2 right-2 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            92% popularidad
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-amber-900 mb-2">Arequipa</h3>
                        <p class="text-amber-600 text-sm mb-4">La Ciudad Blanca y el Cañón del Colca</p>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-amber-500">Desde S/90</span>
                            <button class="px-4 py-2 bg-amber-100 text-amber-600 rounded-full text-sm font-medium hover:bg-amber-200 transition-colors">
                                Reservar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lima -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform hover:scale-105 transition-all">
                    <div class="h-48 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1531968455001-5c5272a41129" 
                             alt="Lima" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-2 right-2 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            95% popularidad
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-amber-900 mb-2">Lima</h3>
                        <p class=" text-amber-600 text-sm mb-4">Capital gastronómica de América</p>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-amber-500">Desde S/60</span>
                            <button class="px-4 py-2 bg-amber-100 text-amber-600 rounded-full text-sm font-medium hover:bg-amber-200 transition-colors">
                                Reservar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Trujillo -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform hover:scale-105 transition-all">
                    <div class="h-48 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1596402184320-417e7178b2cd" 
                             alt="Trujillo" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-2 right-2 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            88% popularidad
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-amber-900 mb-2">Trujillo</h3>
                        <p class="text-amber-600 text-sm mb-4">Ciudad de la Eterna Primavera</p>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-amber-500">Desde S/80</span>
                            <button class="px-4 py-2 bg-amber-100 text-amber-600 rounded-full text-sm font-medium hover:bg-amber-200 transition-colors">
                                Reservar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'layouts/footer.php'; ?>

<script>
    // Funcionalidad para el menú móvil
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.bg-gray-800');
        const menuButton = document.createElement('button');
        menuButton.className = 'md:hidden fixed top-4 left-4 z-50 bg-amber-500 p-2 rounded-lg text-white';
        menuButton.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        `;
        document.body.appendChild(menuButton);

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    });

    // Animaciones suaves al scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeIn');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.transform').forEach(el => observer.observe(el));

    function updateDestinoOptions() {
    const origenSelect = document.getElementById("origen");
    const destinoSelect = document.getElementById("destino");
    const ciudadOrigen = origenSelect.value;

    // Limpiar opciones de destino
    destinoSelect.innerHTML = "<option value=''>Selecciona una ciudad</option>";

    // Agregar las ciudades de destino que no sean la ciudad de origen
    <?php foreach ($ciudades as $ciudad): ?>
        if ("<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>" !== ciudadOrigen) {
            const option = document.createElement("option");
            option.value = "<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>";
            option.textContent = "<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>";
            destinoSelect.appendChild(option);
        }
    <?php endforeach; ?>
}

    function updateOrigenOptions() {
        const origenSelect = document.getElementById('origen');
        const destinoSelect = document.getElementById('destino');
        const selectedValue = destinoSelect.value;

        // Recorrer las opciones de origen y habilitar/deshabilitar según la selección de destino
        for (let i = 0; i < origenSelect.options.length; i++) {
            const option = origenSelect.options[i];
            if (option.value === selectedValue) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        }

        // Si el origen seleccionado es el mismo que el nuevo destino, limpiar la selección de origen
        if (origenSelect.value === selectedValue) {
            origenSelect.value = '';
        }
    }

    window.onload = function() {
        updateDestinoOptions();
        updateOrigenOptions();
    };
</script>