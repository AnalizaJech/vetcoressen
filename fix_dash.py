import re

path = 'c:/xampp/htdocs/vetcoressen/resources/views/livewire/dashboard.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

replacements = [
    (r'<span>¡Atención de Inventario!</span>', r'<span x-text="$store.i18n.t(\'modal.inventoryAttention\') || \'¡Atención de Inventario!\'">¡Atención de Inventario!</span>'),
    (r'<span>Se han detectado</span>', r'<span x-text="$store.i18n.t(\'modal.inventoryDetected\') || \'Se han detectado\'">Se han detectado</span>'),
    (r'<span>en stock crítico y</span>', r'<span x-text="$store.i18n.t(\'modal.productsCritical\') || \'productos en stock crítico y\'">productos en stock crítico y</span>'),
    (r'<span>próximos a vencer.</span>', r'<span x-text="$store.i18n.t(\'modal.batchesExpiring\') || \'lotes próximos a vencer.\'">lotes próximos a vencer.</span>'),
    (r'Productos Críticos', r'<span x-text="$store.i18n.t(\'modal.criticalProducts\') || \'Productos Críticos\'">Productos Críticos</span>'),
    (r'<span>Stock:</span>', r'<span x-text="$store.i18n.t(\'label.stock\') || \'Stock:\'">Stock:</span>'),
    (r'<span>Min:</span>', r'<span x-text="$store.i18n.t(\'label.min\') || \'Min:\'">Min:</span>'),
    (r'Y \{\{ \$alertasInventario - 3 \}\} productos más...', r'<span x-text="($store.i18n.t(\'modal.moreProducts\') || \'Y {n} productos más...\').replace(\'{n}\', \'{{ $alertasInventario - 3 }}\')">Y {{ $alertasInventario - 3 }} productos más...</span>'),
    (r'Lotes Próximos a Vencer', r'<span x-text="$store.i18n.t(\'modal.expiringBatches\') || \'Lotes Próximos a Vencer\'">Lotes Próximos a Vencer</span>'),
    (r'<span>Lote:</span>', r'<span x-text="$store.i18n.t(\'label.batch\') || \'Lote:\'">Lote:</span>'),
    (r'Y \{\{ \$lotesProximosVencer->count\(\) - 5 \}\} lotes más...', r'<span x-text="($store.i18n.t(\'modal.moreBatches\') || \'Y {n} lotes más...\').replace(\'{n}\', \'{{ $lotesProximosVencer->count() - 5 }}\')">Y {{ $lotesProximosVencer->count() - 5 }} lotes más...</span>'),
    (r'Citas Próximas', r'<span x-text="$store.i18n.t(\'modal.upcomingAppointments\') || \'Citas Próximas\'">Citas Próximas</span>'),
    (r'Tienes \{\{ \$citasProximas->count\(\) \}\} cita\(s\) programada\(s\) para las próximas 2 horas.', r'<span x-text="($store.i18n.t(\'modal.appointmentsScheduled\') || \'Tienes {n} cita(s) programada(s) para las próximas 2 horas.\').replace(\'{n}\', \'{{ $citasProximas->count() }}\')">Tienes {{ $citasProximas->count() }} cita(s) programada(s) para las próximas 2 horas.</span>')
]

for old, new in replacements:
    content = re.sub(old, new, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Dashboard replacements applied')
