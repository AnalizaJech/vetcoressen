<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Pago</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Inter', Helvetica, Arial, sans-serif; color: #27272a;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #3b82f6; padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">VETCORESSEN</h1>
                            <p style="color: #dbeafe; margin: 5px 0 0 0; font-size: 14px;">Comprobante de Compra</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #18181b; font-size: 20px;">¡Hola, {{ $venta->cliente->first_name ?? 'Cliente' }}!</h2>
                            <p style="margin: 0 0 20px 0; line-height: 1.6; color: #52525b;">Gracias por tu compra en VETCORESSEN. Aquí tienes los detalles de tu comprobante:</p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5; border-radius: 8px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0;"><strong>Comprobante:</strong> <span style="color: #3f3f46;">{{ $venta->tipo_comprobante }} #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                                        <p style="margin: 0 0 10px 0;"><strong>Fecha:</strong> <span style="color: #3f3f46;">{{ $venta->created_at->format('d/m/Y h:i A') }}</span></p>
                                        <p style="margin: 0 0 10px 0;"><strong>Método de Pago:</strong> <span style="color: #3f3f46;">{{ $venta->metodo_pago }}</span></p>
                                        <p style="margin: 0;"><strong>Total Pagado:</strong> <strong style="color: #3b82f6; font-size: 18px;">S/ {{ number_format($venta->total, 2) }}</strong></p>
                                    </td>
                                </tr>
                            </table>
                            
                            @if($venta->detalles && $venta->detalles->count() > 0)
                            <h3 style="margin: 0 0 10px 0; color: #3f3f46; font-size: 16px;">Detalle de Productos</h3>
                            <table width="100%" cellpadding="10" cellspacing="0" border="0" style="margin-bottom: 30px; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e4e4e7;">
                                        <th align="left" style="color: #71717a; font-size: 12px; text-transform: uppercase;">Producto</th>
                                        <th align="center" style="color: #71717a; font-size: 12px; text-transform: uppercase;">Cant.</th>
                                        <th align="right" style="color: #71717a; font-size: 12px; text-transform: uppercase;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($venta->detalles as $detalle)
                                    <tr style="border-bottom: 1px solid #f4f4f5;">
                                        <td align="left" style="color: #3f3f46; font-size: 14px;">{{ $detalle->producto->name ?? 'Producto' }}</td>
                                        <td align="center" style="color: #52525b; font-size: 14px;">{{ $detalle->cantidad }}</td>
                                        <td align="right" style="color: #3f3f46; font-size: 14px; font-weight: bold;">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                            
                            <p style="margin: 0 0 30px 0; line-height: 1.6; color: #52525b;">Puedes visualizar los detalles de tu compra o descargar el comprobante oficial desde nuestra plataforma.</p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}" style="display: inline-block; padding: 12px 24px; background-color: #3b82f6; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 6px;">Ir al Sistema</a>
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
