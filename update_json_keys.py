import json
import os

def update_json(filepath, updates):
    with open(filepath, 'r', encoding='utf-8') as f:
        data = json.load(f)

    for path, value in updates.items():
        keys = path.split('.')
        current = data
        for k in keys[:-1]:
            if k not in current:
                current[k] = {}
            current = current[k]
        current[keys[-1]] = value

    with open(filepath, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=4, ensure_ascii=False)

en_updates = {
    "btn.newAppointment": "New Appointment",
    "btn.newSale": "New Sale",
    "btn.viewAll": "View All",
    "btn.receiveOrder": "Receive Order",
    "btn.newPet": "New Pet",
    "label.income": "Income",
    "label.latestSales": "Latest Sales",
    "label.noSales": "No Sales",
    "label.noRecentSales": "No recent sales",
    "menu.inventory": "Inventory",
    "form.pricesAndStock": "Prices and Stock",
    "form.presentation": "Presentation",
    "form.requiresPrescription": "Requires Prescription",
    "form.overTheCounter": "Over the Counter",
    "form.weightPresentation": "Weight / Presentation",
    "form.withIGV": "With IGV",
    "form.withoutIGV": "Without IGV",
    "report.thisMonth": "This Month",
    "report.income": "Income",
    "report.period_hoy": "Today",
    "report.period_semana_actual": "This Week",
    "report.period_mes_actual": "This Month",
    "report.period_año_actual": "This Year",
    "report.averageTicket": "Average Ticket",
    "report.appointmentsPerf": "Appointments Performance",
    "report.completedAppts": "Completed Appointments",
    "report.cancelledAppts": "Cancelled Appointments",
    "report.newReservations": "New Reservations",
    "table.breed": "Breed",
    "table.sex": "Sex",
    "table.owner": "Owner",
    "placeholder.searchBranch": "Search by name, address...",
    "placeholder.searchSupplier": "Search by company, RUC, contact..."
}

es_updates = {
    "btn.newAppointment": "Nueva Cita",
    "btn.newSale": "Nueva Venta",
    "btn.viewAll": "Ver Todos",
    "btn.receiveOrder": "Recibir Pedido",
    "btn.newPet": "Nueva Mascota",
    "label.income": "Ingresos",
    "label.latestSales": "Últimas Ventas",
    "label.noSales": "Sin Ventas",
    "label.noRecentSales": "No hay ventas recientes",
    "menu.inventory": "Inventario",
    "form.pricesAndStock": "Precios y Stock",
    "form.presentation": "Presentación",
    "form.requiresPrescription": "Requiere Receta",
    "form.overTheCounter": "Venta Libre",
    "form.weightPresentation": "Peso / Presentación",
    "form.withIGV": "Con IGV",
    "form.withoutIGV": "Sin IGV",
    "report.thisMonth": "Este Mes",
    "report.income": "Ingresos",
    "report.period_hoy": "de Hoy",
    "report.period_semana_actual": "de la Semana",
    "report.period_mes_actual": "del Mes",
    "report.period_año_actual": "del Año",
    "report.averageTicket": "Ticket Promedio",
    "report.appointmentsPerf": "Rendimiento de Citas",
    "report.completedAppts": "Citas Completadas",
    "report.cancelledAppts": "Citas Canceladas",
    "report.newReservations": "Nuevas Reservas",
    "table.breed": "Raza",
    "table.sex": "Sexo",
    "table.owner": "Propietario",
    "placeholder.searchBranch": "Buscar por nombre, dirección...",
    "placeholder.searchSupplier": "Buscar por empresa, RUC, contacto..."
}

update_json(r'c:\xampp\htdocs\vetcoressen\public\locales\en.json', en_updates)
update_json(r'c:\xampp\htdocs\vetcoressen\public\locales\es.json', es_updates)

print("JSON files updated.")
