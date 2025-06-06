<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación</title>
</head>

<body>
    <h1>Hola, {{ $user->name }}</h1>

    <p>Tú registro con el Código de Cliente: <strong>{{ $code_customer }}</strong>, ha sido registrado.</p>
    <p>Código de Seguimiento: <strong>{{ $codigo_seguimiento }}</strong></p>
    <br>
    <label>Información Enviada:</label><br>
   <pre>{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

</body>

</html>
