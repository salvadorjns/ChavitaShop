<?php
require __DIR__ . '/vendor/autoload.php';

// Configuración para México
$access_token = 'APP_USR-4602505045231776-071809-e71371a44a875a2dd0169c1580c33551-310526399';
MercadoPago\SDK::setAccessToken($access_token);

// Obtener datos del carrito desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

try {
    $preference = new MercadoPago\Preference();
    
    // Configurar URLs de retorno (actualiza con tus URLs reales)
    $preference->back_urls = [
        "success" => "http://tudominio.com/pago-exitoso.php",
        "failure" => "http://tudominio.com/pago-fallido.php",
        "pending" => "http://tudominio.com/pago-pendiente.php"
    ];
    
    $preference->auto_return = "approved";
    
    // Convertir productos del carrito a items de MercadoPago
    $items = [];
    foreach ($data['productos'] as $producto) {
        $item = new MercadoPago\Item();
        $item->title = $producto['nombre'];
        $item->quantity = $producto['cantidad'] ?? 1;
        $item->unit_price = $producto['precio'];
        $item->currency_id = "MXN";
        $items[] = $item;
    }
    
    // Agregar costo de envío como item adicional si aplica
    if ($data['envio'] > 0) {
        $item = new MercadoPago\Item();
        $item->title = "Costo de envío";
        $item->quantity = 1;
        $item->unit_price = $data['envio'];
        $item->currency_id = "MXN";
        $items[] = $item;
    }
    
    $preference->items = $items;
    
    // Agregar información del comprador si está disponible
    if (isset($data['direccion']['email'])) {
        $payer = new MercadoPago\Payer();
        $payer->email = $data['direccion']['email'];
        $payer->name = $data['direccion']['nombreCompleto'];
        $payer->phone = [
            "area_code" => "52",
            "number" => $data['direccion']['telefono']
        ];
        $payer->address = [
            "street_name" => $data['direccion']['calle'],
            "street_number" => $data['direccion']['numero'],
            "zip_code" => $data['direccion']['codigoPostal']
        ];
        $preference->payer = $payer;
    }
    
    $preference->save();
    
    if (!isset($preference->id)) {
        throw new Exception("Error al crear la preferencia de pago");
    }
    
    // Devolver el ID de preferencia al frontend
    echo json_encode(['preferenceId' => $preference->id]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
