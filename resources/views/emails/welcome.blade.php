<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>¡Bienvenido a {{ config('app.name') }}!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f5;
            color: #3f3f46;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #10b981; /* Emerald 500 */
            color: white;
            text-align: center;
            padding: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 32px;
            line-height: 1.6;
        }
        .footer {
            background-color: #f8fafc;
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: #71717a;
            border-top: 1px solid #e4e4e7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a {{ config('app.name') }}!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $nombreCompleto }}</strong>,</p>
            
            <p>Nos alegra mucho darte la bienvenida como {{ strtolower($tipoPersona) }} de nuestra clínica veterinaria.</p>
            
            <p>En {{ config('app.name') }} estamos comprometidos con brindar la mejor atención para tus mascotas. Si tienes alguna duda o necesitas programar una cita, no dudes en contactarnos.</p>
            
            <p>¡Esperamos verte pronto!</p>
            
            <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} Clínica Veterinaria. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
