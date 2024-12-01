### **Proyecto de Reservación de Asientos de Buses Interprovinciales**

#### **Descripción del Proyecto**
Este sistema es una aplicación web diseñada para facilitar la gestión de viajes y la reservación de asientos en buses interprovinciales. Permite a los administradores crear viajes, seleccionar rutas, asignar buses y gestionar la venta de asientos según categorías (VIP, estándar y económico). La plataforma está equipada con una interfaz amigable para seleccionar asientos, calcular costos y gestionar datos relacionados con buses, destinos y precios.

#### **Características Implementadas**
- Gestión de viajes:
  - Creación de viajes con origen, destino, fechas y buses asignados.
  - Cálculo automático del costo del destino y el total del viaje.
- Selección de asientos:
  - Interfaz visual para seleccionar asientos según su categoría.
  - Actualización dinámica del total basado en los asientos seleccionados.
- Gestión de datos:
  - Listado dinámico de ciudades de origen y destino.
  - Asignación de buses disponibles para cada viaje.
- Diseño adaptable:
  - Uso de Tailwind CSS para una interfaz responsiva y moderna.
- Seguridad:
  - Validación de sesión para proteger el acceso al sistema.

#### **Requisitos Técnicos**
- **Backend**: PHP 7.4+ con conexión a base de datos MySQL.
- **Frontend**: HTML5, CSS3 (Tailwind CSS) y JavaScript (uso de fetch para llamadas dinámicas).
- **Base de datos**: Esquema con tablas para `buses`, `viajes`, `destinos` y `reservaciones`.

#### **Cómo Ejecutar el Proyecto**
1. Configura un servidor local con Apache y PHP (por ejemplo, XAMPP o WAMP).
2. Crea una base de datos MySQL usando el esquema proporcionado en el archivo `database.sql`.
3. Configura las credenciales de conexión a la base de datos en `config/database.php`.
4. Inicia sesión como administrador para gestionar viajes.
5. Accede a la página principal para crear viajes y realizar reservaciones.

#### **Cosas que Faltan Implementar**
1. **Funcionalidades de usuario final:**
   - Interfaz para que los pasajeros puedan reservar asientos directamente.
   - Registro y autenticación de pasajeros.
2. **Notificaciones:**
   - Envío de confirmación por correo electrónico después de una reservación.
   - Alertas de recordatorio para los viajes próximos.
3. **Gestión avanzada de asientos:**
   - Mostrar un esquema del bus con los asientos ocupados y disponibles.
   - Permitir la selección por bloques (reservaciones grupales).
4. **Reportes y estadísticas:**
   - Generar reportes de ocupación de buses.
   - Estadísticas de las rutas más utilizadas.
5. **Optimización y mejoras:**
   - Manejo avanzado de errores para consultas fallidas.
   - Cacheo de datos para mejorar el rendimiento.
   - Mejorar la seguridad contra ataques de inyección SQL y XSS.
6. **Mobile-friendly:**
   - Optimizar la interfaz para dispositivos móviles.
7. **Multi-idioma:**
   - Agregar soporte para varios idiomas.

#### **Notas Adicionales**
- Para cualquier problema, consulta los logs generados por PHP o los errores de la consola del navegador para depurar.
- Los archivos del proyecto están organizados en las siguientes carpetas:
  - `/config`: Configuración del sistema y base de datos.
  - `/controllers`: Controladores para la lógica de negocios.
  - `/views`: Vistas y plantillas HTML.
  - `/assets`: Archivos CSS, JavaScript y otros recursos estáticos.

#### **Contribuciones**
Se aceptan sugerencias y contribuciones para mejorar el sistema. Por favor, envía un pull request o abre un issue en el repositorio para discutir mejoras.
