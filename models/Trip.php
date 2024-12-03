<?php
class Trip {
    private $db;
    private $table_name = "viajes"; // Cambia esto según tu tabla

    public $id;
    public $origen;
    public $destino;
    public $fecha_salida;
    public $fecha_llegada;
    public $bus_id;
    public $conductor_id;
    public $precio_vip;
    public $precio_estandar;
    public $precio_economico;
    public $created_at;
    public $usuario_id; 
    public $asiento;     
    public $categoria;
    
    // Nuevos atributos para el pago
    public $user_id;
    public $seats;
    public $payment_method;
    public $card_number;
    public $expiry_date;
    public $cvv;
    public $yape_number;
    public $plin_number;
    public $paypal_email;
    public $total;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
            $query = "INSERT INTO " . $this->table_name . " 
                SET
                    origen = :origen,
                    destino = :destino,
                    fecha_salida = :fecha_salida,
                    fecha_llegada = :fecha_llegada,
                    bus_id = :bus_id,
                    conductor_id = :conductor_id,
                    precio = :precio,  
                    user_id = :user_id,
                    estado = 'En Espera',
                    created_at = CURRENT_TIMESTAMP";
    
        $stmt = $this->db->prepare($query);
    
        // Sanitize y vincular valores
        $this->origen = htmlspecialchars(strip_tags($this->origen));
        $this->destino = htmlspecialchars(strip_tags($this->destino));
        $this->fecha_salida = htmlspecialchars(strip_tags($this->fecha_salida));
        $this->fecha_llegada = htmlspecialchars(strip_tags($this->fecha_llegada));
        $this->bus_id = htmlspecialchars(strip_tags($this->bus_id));
        $this->conductor_id = htmlspecialchars(strip_tags($this->conductor_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->total = htmlspecialchars(strip_tags($this->total)); // Asegúrate de que este valor esté definido
    
        // Vincular valores
        $stmt->bindParam(":origen", $this->origen);
        $stmt->bindParam(":destino", $this->destino);
        $stmt->bindParam(":fecha_salida", $this->fecha_salida);
        $stmt->bindParam(":fecha_llegada", $this->fecha_llegada);
        $stmt->bindParam(":bus_id", $this->bus_id);
        $stmt->bindParam(":conductor_id", $this->conductor_id);
        $stmt->bindParam(":precio", $this->total); // Vincula el precio total
        $stmt->bindParam(":user_id", $this->user_id);
    
        // Ejecutar la consulta para insertar el viaje
        if ($stmt->execute()) {
            // Obtener el ID del viaje recién creado
            $viaje_id = $this->db->lastInsertId();
    
            // Ahora inserta los datos en la tabla 'pagos' sin la columna 'seats'
            $query_pago = "INSERT INTO pagos 
                    SET 
                        trip_id = :trip_id,
                        user_id = :user_id,
                        payment_method = :payment_method,
                        card_number = :card_number,
                        expiry_date = :expiry_date,
                        cvv = :cvv,
                        yape_number = :yape_number,
                        plin_number = :plin_number,
                        paypal_email = :paypal_email,
                        total = :total,
                        created_at = CURRENT_TIMESTAMP";
    
            $stmt_pago = $this->db->prepare($query_pago);
    
            // Asignar valores para el pago
            $this->payment_method = htmlspecialchars(strip_tags($this->payment_method)); // Asegúrate de que payment_method esté definido
            $this->card_number = htmlspecialchars(strip_tags($this->card_number)); // Asegúrate de que card_number esté definido
            $this->expiry_date = htmlspecialchars(strip_tags($this->expiry_date)); // Asegúrate de que expiry_date esté definido
            $this->cvv = htmlspecialchars(strip_tags($this->cvv)); // Asegúrate de que cvv esté definido
            $this->yape_number = htmlspecialchars(strip_tags($this->yape_number)); // Asegúrate de que yape_number esté definido
            $this->plin_number = htmlspecialchars(strip_tags($this->plin_number)); // Asegúrate de que plin_number esté definido
            $this->paypal_email = htmlspecialchars(strip_tags($this->paypal_email)); // Asegúrate de que paypal_email esté definido
    
            // Vincular valores para el pago
            $stmt_pago->bindParam(":trip_id", $viaje_id);
            $stmt_pago->bindParam(":user_id", $this->user_id);
            $stmt_pago->bindParam(":payment_method", $this->payment_method);
            $stmt_pago->bindParam(":card_number", $this->card_number);
            $stmt_pago->bindParam(":expiry_date", $this->expiry_date);
            $stmt_pago->bindParam(":cvv", $this->cvv);
            $stmt_pago->bindParam(":yape_number", $this->yape_number);
            $stmt_pago->bindParam(":plin_number", $this->plin_number);
            $stmt_pago->bindParam(":paypal_email", $this->paypal_email);
            $stmt_pago->bindParam(":total", $this->total);
    
            // Ejecutar la consulta para insertar el pago
            if ($stmt_pago->execute()) {
                // Inserción exitosa en pagos, ahora procesa asientos
                $query_detalle = "INSERT INTO detalle_viaje 
                                SET 
                                    viaje_id = :viaje_id,
                                    usuario_id = :usuario_id,
                                    asiento = :asiento,
                                    categoria = :categoria,
                                    created_at = CURRENT_TIMESTAMP";
        
                $stmt_detalle = $this->db->prepare($query_detalle);
                
                if (is_string($this->seats)) {
                    $this->seats = json_decode($this->seats, true); // Convertir JSON a array
                }
                
                if (!is_array($this->seats)) {
                    throw new Exception("La propiedad seats no es un array válido. Valor recibido: " . json_encode($this->seats));
                }
        
                // Procesar los asientos
                foreach ($this->seats as $seat) {
                    // Asegúrate de que 'numero' y 'categoria' existan
                    $asiento = htmlspecialchars(strip_tags($seat['numero'])); // Correcto
                    $categoria = htmlspecialchars(strip_tags($seat['categoria'])); // Correcto
            
                    // Verifica que los valores no estén vacíos
                    if (empty($asiento) || empty($categoria)) {
                        throw new Exception("Asiento o categoría vacíos: " . json_encode($seat));
                    }
            
                    // Vincular valores específicos para cada asiento
                    $stmt_detalle->bindParam(":viaje_id", $viaje_id);
                    $stmt_detalle->bindParam(":usuario_id", $this->user_id);
                    $stmt_detalle->bindParam(":asiento", $asiento);
                    $stmt_detalle->bindParam(":categoria", $categoria);
            
                    // Ejecutar la inserción
                    if (!$stmt_detalle->execute()) {
                        throw new Exception("Error al insertar el asiento: " . json_encode($seat));
                    }
                }
        
                return true;
                }else {
                throw new Exception("Error al insertar en pagos.");
            }
        } else {
            throw new Exception("Error al insertar en viajes.");
        }
    }


    public function updateSeatStatus() {
        $query = "UPDATE asientos 
                  SET estado = 'ocupado' 
                  WHERE bus_id = :bus_id AND numero_asiento = :numero_asiento";
    
        $stmt = $this->db->prepare($query);
    
        // Vincular valores
        $stmt->bindParam(":bus_id", $this->bus_id); // Asegúrate de que este valor esté definido
        $stmt->bindParam(":numero_asiento", $this->asiento); // Asegúrate de que este valor esté definido
    
        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; 
        } else {
            throw new Exception("Error al actualizar el estado del asiento.");
        }
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt; // Asegúrate de que esto devuelva el resultado esperado
    }

    public function readTrips() {
        $query = "
            SELECT 
                v.id,
                v.origen,
                v.destino,
                v.fecha_salida,
                v.fecha_llegada,
                b.modelo AS bus_modelo,
                c.nombre AS conductor_nombre,
                v.precio,
                v.estado
            FROM viajes v
            JOIN buses b ON v.bus_id = b.id
            JOIN conductores c ON v.conductor_id = c.id
        ";
        
        $stmt = $this->db->prepare($query);
    
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
            return [];
        }
    }
    
    
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    origen = :origen,
                    destino = :destino,
                    fecha_salida = :fecha_salida,
                    fecha_llegada = :fecha_llegada,
                    bus_id = :bus_id,
                    conductor_id = :conductor_id,
                    precio = :precio  -- Elimina 'precio_vip', 'precio_estandar', y 'precio_economico' si no son necesarios
                WHERE id = :id";
    
        $stmt = $this->db->prepare($query);
    
        // Sanitizar
        $this->origen = htmlspecialchars(strip_tags($this->origen));
        $this->destino = htmlspecialchars(strip_tags($this->destino));
        $this->fecha_salida = htmlspecialchars(strip_tags($this->fecha_salida));
        $this->fecha_llegada = htmlspecialchars(strip_tags($this->fecha_llegada));
        $this->bus_id = htmlspecialchars(strip_tags($this->bus_id));
        $this->conductor_id = htmlspecialchars(strip_tags($this->conductor_id));
        $this->precio = htmlspecialchars(strip_tags($this->precio)); // Asegúrate de que este valor esté definido
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // Bind values
        $stmt->bindParam(":origen", $this->origen);
        $stmt->bindParam(":destino", $this->destino);
        $stmt->bindParam(":fecha_salida", $this->fecha_salida);
        $stmt->bindParam(":fecha_llegada", $this->fecha_llegada);
        $stmt->bindParam(":bus_id", $this->bus_id);
        $stmt->bindParam(":conductor_id", $this->conductor_id);
        $stmt->bindParam(":precio", $this->precio); // Asegúrate de que este valor esté definido
        $stmt->bindParam(":id", $this->id);
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->id = $id;  // Asignamos el ID a la propiedad de la clase
        
        // Paso 1: Obtener los asientos ocupados para el viaje
        $queryAsientos = "
            SELECT asiento 
            FROM detalle_viaje 
            WHERE viaje_id = :viaje_id
        ";
        
        $stmtAsientos = $this->db->prepare($queryAsientos);
        $stmtAsientos->bindParam(':viaje_id', $this->id, PDO::PARAM_INT);
        $stmtAsientos->execute();
        $asientosOcupados = $stmtAsientos->fetchAll(PDO::FETCH_COLUMN); // Obtener solo los números de asientos
    
        // Paso 2: Actualizar los asientos de ocupado a disponible
        if (!empty($asientosOcupados)) {
            // Crear una lista de placeholders para la consulta
            $placeholders = implode(',', array_fill(0, count($asientosOcupados), '?'));
            $queryActualizarAsientos = "
                UPDATE asientos 
                SET estado = 'disponible' 
                WHERE bus_id = (
                    SELECT bus_id FROM viajes WHERE id = ?
                ) AND numero_asiento IN ($placeholders)
            ";
            
            $stmtActualizar = $this->db->prepare($queryActualizarAsientos);
            // Ejecutar la consulta con el ID del viaje y los asientos ocupados
            $stmtActualizar->execute(array_merge([$this->id], array_values($asientosOcupados))); // Pasar el ID del viaje y los asientos ocupados
        }
    
        // Paso 3: Eliminar los detalles del viaje
        $queryDetalleViaje = "DELETE FROM detalle_viaje WHERE viaje_id = :viaje_id";
        $stmtDetalleViaje = $this->db->prepare($queryDetalleViaje);
        $stmtDetalleViaje->bindParam(':viaje_id', $this->id, PDO::PARAM_INT);
        $stmtDetalleViaje->execute();
        
        // Paso 4: Eliminar los pagos relacionados
        $queryPagos = "DELETE FROM pagos WHERE trip_id = :viaje_id";
        $stmtPagos = $this->db->prepare($queryPagos);
        $stmtPagos->bindParam(':viaje_id', $this->id, PDO::PARAM_INT);
        $stmtPagos->execute();
        
        // Paso 5: Eliminar el viaje
        $queryViajes = "DELETE FROM viajes WHERE id = :viaje_id";
        $stmtViajes = $this->db->prepare($queryViajes);
        $stmtViajes->bindParam(':viaje_id', $this->id, PDO::PARAM_INT);
        $stmtViajes->execute();
        
        return true; // Retornar true si todo fue exitoso
    }

    public function getTripById($trip_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :trip_id"; // Consulta SQL
        $stmt = $this->db->prepare($query); // Preparar la consulta
        $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT); // Vincular el parámetro
        $stmt->execute(); // Ejecutar la consulta
        return $stmt; // Retornar el objeto de declaración
    }
}
?>