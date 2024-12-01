<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Viaje - BusBooker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2">
            <a href="/admin" class="text-white flex items-center space-x-2 px-4">
                <span class="text-2xl font-extrabold">BusBooker</span>
            </a>
            <nav>
                <a href="/admin/users.html" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Usuarios</a>
                <a href="/admin/buses.html" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Buses</a>
                <a href="/admin/drivers.html" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Conductores</a>
                <a href="/admin/trips.html" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700 text-white">Viajes</a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-amber-900">Editar Viaje</h1>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <form class="bg-white rounded-lg shadow-lg p-6">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="destination">Destino</label>
                            <input type="text" id="destination" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Destino del viaje" value="Ciudad A">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="date">Fecha</label>
                            <input type="date" id="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="2024-05-01">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="bus">Bus</label>
                            <select id="bus" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="bus1" selected>Bus Modelo X</option>
                                <option value="bus2">Bus Modelo Y</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="bg-amber-500 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </main>

            <footer class="bg-white shadow-sm mt-auto">
                <div class="container mx-auto px-6 py-4">
                    <p class="text-center text-gray-600">© 2024 BusBooker. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>