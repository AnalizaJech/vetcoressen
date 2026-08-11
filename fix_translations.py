import os
import re
import json

locales = {
    'es': r'c:\xampp\htdocs\vetcoressen\public\locales\es.json',
    'en': r'c:\xampp\htdocs\vetcoressen\public\locales\en.json'
}

keys_to_add = {
    'es': {
        'status.pagado': 'Pagado',
        'status.anulado': 'Anulado',
        'status.pendiente': 'Pendiente',
        'status.confirmada': 'Confirmada',
        'status.en_progreso': 'En Progreso',
        'status.completada': 'Completada',
        'status.cancelada': 'Cancelada',
        'status.emergencia': 'Emergencia',
        
        # Reports
        'report.salesEvol': 'Ventas (S/)',
        'report.completed': 'Completadas',
        'report.cancelled': 'Canceladas',
        'report.pendingOther': 'Pendientes/Otras',
        'report.salesDistribution': 'Distribución de Ventas',
        'report.topProducts': 'Top Productos',
        'report.topServices': 'Top Servicios',
        
        'dashboard.filter': 'Filtrar',
        'dashboard.today': 'Hoy',
        'dashboard.lastWeek': 'Última Semana',
        'dashboard.thisMonth': 'Este Mes',
        'dashboard.thisYear': 'Este Año',
    },
    'en': {
        'status.pagado': 'Paid',
        'status.anulado': 'Annulled',
        'status.pendiente': 'Pending',
        'status.confirmada': 'Confirmed',
        'status.en_progreso': 'In Progress',
        'status.completada': 'Completed',
        'status.cancelada': 'Cancelled',
        'status.emergencia': 'Emergency',
        
        # Reports
        'report.salesEvol': 'Sales ($)',
        'report.completed': 'Completed',
        'report.cancelled': 'Cancelled',
        'report.pendingOther': 'Pending/Other',
        'report.salesDistribution': 'Sales Distribution',
        'report.topProducts': 'Top Products',
        'report.topServices': 'Top Services',
        
        'dashboard.filter': 'Filter',
        'dashboard.today': 'Today',
        'dashboard.lastWeek': 'Last Week',
        'dashboard.thisMonth': 'This Month',
        'dashboard.thisYear': 'This Year',
    }
}

for lang, path in locales.items():
    with open(path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    changed = False
    for k, v in keys_to_add[lang].items():
        if k not in data or data[k] != v:
            data[k] = v
            changed = True
            
    if changed:
        with open(path, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=4)
        print(f'Updated {path}')
    else:
        print(f'No changes for {path}')
