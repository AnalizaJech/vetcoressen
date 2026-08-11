import re

path = r'c:\xampp\htdocs\vetcoressen\resources\views\livewire\reportes\reporte-index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

replacements = [
    (r'0 PRODUCTOS CON STOCK BAJO', r'<span x-text="$store.i18n.t(\'report.lowStockProducts\') || \'0 PRODUCTOS CON STOCK BAJO\'">0 PRODUCTOS CON STOCK BAJO</span>'),
    (r'INGRESOS DEL MES', r'<span x-text="$store.i18n.t(\'report.monthlyRevenue\') || \'INGRESOS DEL MES\'">INGRESOS DEL MES</span>'),
    (r'TICKET PROMEDIO DEL MES', r'<span x-text="$store.i18n.t(\'report.monthlyTicket\') || \'TICKET PROMEDIO DEL MES\'">TICKET PROMEDIO DEL MES</span>'),
    (r'CITAS COMPLETADAS DEL MES', r'<span x-text="$store.i18n.t(\'report.monthlyApptsCompleted\') || \'CITAS COMPLETADAS DEL MES\'">CITAS COMPLETADAS DEL MES</span>'),
    (r'CITAS CANCELADAS DEL MES', r'<span x-text="$store.i18n.t(\'report.monthlyApptsCancelled\') || \'CITAS CANCELADAS DEL MES\'">CITAS CANCELADAS DEL MES</span>'),
    (r'NUEVAS RESERVAS DEL MES', r'<span x-text="$store.i18n.t(\'report.monthlyNewBookings\') || \'NUEVAS RESERVAS DEL MES\'">NUEVAS RESERVAS DEL MES</span>'),
    (r'Descargar PDF', r'<span x-text="$store.i18n.t(\'report.downloadPDF\') || \'Descargar PDF\'">Descargar PDF</span>'),
    (r'Exportar Datos \(CSV\)', r'<span x-text="$store.i18n.t(\'report.exportCSV\') || \'Exportar Datos (CSV)\'">Exportar Datos (CSV)</span>'),
]

for old, new in replacements:
    content = re.sub(old, new, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print('reporte-index.blade.php updated.')
