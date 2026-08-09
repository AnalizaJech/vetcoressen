<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function cita($id)
    {
        $cita = Appointment::with(['cliente', 'mascota', 'veterinario'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.cita', compact('cita'));

        return $pdf->download('cita-' . $cita->id . '.pdf');
    }

    public function historia($id)
    {
        $historia = \App\Models\MedicalRecord::with(['pet.customer', 'veterinario', 'prescripciones.producto'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.historia-clinica', compact('historia'));

        return $pdf->download('historia-clinica-' . str_pad($historia->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function historialMascota($id)
    {
        $mascota = \App\Models\Pet::with(['customer', 'especie', 'raza', 'historiasClinicas' => function ($q) {
            $q->orderBy('date', 'desc')->with(['veterinario', 'prescripciones.producto']);
        }])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.historial-mascota', compact('mascota'));

        return $pdf->download('historial-mascota-' . str_pad($mascota->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}
