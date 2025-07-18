<?php
// Verificar el estado real del pago usando la API de MercadoPago
// Aquí puedes vaciar el carrito solo si el pago está realmente aprobado

session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pago Exitoso</title>
</head>
<body>
    <h1>¡Pago Exitoso!</h1>
    <p>Gracias por tu compra. Hemos recibido tu pago correctamente.</p>
    <p>Número de orden: <?php echo $_GET['payment_id'] ?? ''; ?></p>
    <a href="/">Volver al inicio</a>
</body>
</html>
