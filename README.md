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
1. **Conectar todo al Dashboard:**
   - Integrar todas las funcionalidades en un panel de administración para gestionar los viajes, buses y reservaciones de manera centralizada.
2. **Añadir lo de la moneda:**
   - Implementar una funcionalidad para mostrar los precios en una moneda seleccionada por el usuario. Esto implicaría la conversión de monedas y la actualización de los precios de los destinos y asientos.
4. **Modificar la eliminación de un viaje para eliminar todo desde su raíz:**
   - Al eliminar un viaje, es necesario asegurar que se eliminen todas las relaciones asociadas a ese viaje (reservaciones, asientos ocupados, etc.) desde la raíz.
6. **Hacer el trigger y la tabla para las últimas acciones:**
   - Crear un trigger que registre las últimas acciones realizadas en el sistema (como la creación, actualización o eliminación de viajes, reservaciones, etc.) y almacenarlas en una tabla específica para auditoría.

#### **Notas Adicionales**
- Para cualquier problema, consulta los logs generados por PHP o los errores de la consola del navegador para depurar.
- Los archivos del proyecto están organizados en las siguientes carpetas:
  - `/config`: Configuración del sistema y base de datos.
  - `/controllers`: Controladores para la lógica de negocios.
  - `/views`: Vistas y plantillas HTML.
  - `/assets`: Archivos CSS, JavaScript y otros recursos estáticos.
  - `/views/database.sql`: Archivo SQL, Estructura de la Base de Datos relacional.


#### **Contribuciones**
Se aceptan sugerencias y contribuciones para mejorar el sistema. Por favor, envía un pull request o abre un issue en el repositorio para discutir mejoras.
