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
}
