<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Receta médica</title></head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#27272a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="padding:40px 16px;background:#f4f4f5;"><tr><td align="center">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e4e4e7;">
            <tr><td style="padding:28px 32px;background:#5b21b6;border-bottom:4px solid #a78bfa;"><p style="margin:0 0 6px;color:#ddd6fe;font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;">VETCORESSEN · Clínica Veterinaria</p><h1 style="margin:0;color:#ffffff;font-size:25px;line-height:1.25;">Receta médica</h1></td></tr>
            <tr><td style="padding:32px;">
                <p style="margin:0 0 18px;font-size:17px;font-weight:700;">Hola, {{ $historia->cita?->cliente?->first_name ?? 'cliente' }}.</p>
                <p style="margin:0 0 22px;line-height:1.65;color:#52525b;">Te compartimos las indicaciones médicas de <strong style="color:#5b21b6;">{{ $historia->mascota?->name ?? 'tu mascota' }}</strong> correspondientes a la consulta del {{ $historia->date?->format('d/m/Y') ?? now()->format('d/m/Y') }}.</p>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 22px;background:#faf5ff;border:1px solid #e9d5ff;border-radius:12px;"><tr><td style="padding:20px;"><p style="margin:0 0 9px;color:#6b21a8;font-size:12px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;">Indicaciones de tratamiento</p><p style="margin:0;white-space:pre-line;line-height:1.6;color:#3f3f46;">{{ $historia->tratamiento_indicaciones ?: 'Sigue las indicaciones brindadas durante la consulta.' }}</p></td></tr></table>
                <p style="margin:0;line-height:1.6;color:#71717a;font-size:13px;">Si tienes dudas o notas algún cambio en tu mascota, comunícate con la clínica antes de modificar la medicación.</p>
            </td></tr>
            <tr><td style="padding:18px 32px;background:#18181b;text-align:center;"><p style="margin:0;color:#a1a1aa;font-size:12px;">Este mensaje es informativo y forma parte del seguimiento clínico de tu mascota.</p></td></tr>
        </table>
    </td></tr></table>
</body>
</html>
