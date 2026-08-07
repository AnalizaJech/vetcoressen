<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Cita</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Inter', Helvetica, Arial, sans-serif; color: #27272a;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #10b981; padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">VETCORESSEN</h1>
                            <p style="color: #d1fae5; margin: 5px 0 0 0; font-size: 14px;">Clínica Veterinaria</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #18181b; font-size: 20px;">¡Hola, {{ $cita->cliente->first_name }}!</h2>
                            <p style="margin: 0 0 20px 0; line-height: 1.6; color: #52525b;">Hemos confirmado una cita para tu mascota <strong style="color: #10b981;">{{ $cita->mascota->name }}</strong>. Aquí tienes los detalles:</p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0;"><strong>Fecha y Hora:</strong> <span style="color: #3f3f46;">{{ $cita->fecha_hora->format('d/m/Y h:i A') }}</span></p>
                                        <p style="margin: 0 0 10px 0;"><strong>Motivo:</strong> <span style="color: #3f3f46;">{{ $cita->reason ?? 'Consulta General' }}</span></p>
                                        <p style="margin: 0;"><strong>Veterinario:</strong> <span style="color: #3f3f46;">{{ $cita->veterinario->name ?? 'Por asignar' }}</span></p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 0 0 30px 0; line-height: 1.6; color: #52525b;">Por favor, procura llegar con 10 minutos de anticipación. Si necesitas reprogramar, contáctanos lo antes posible.</p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}" style="display: inline-block; padding: 12px 24px; background-color: #10b981; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 6px;">Visitar Sitio Web</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #18181b; padding: 20px; text-align: center;">
                            <p style="margin: 0; color: #a1a1aa; font-size: 12px;">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
