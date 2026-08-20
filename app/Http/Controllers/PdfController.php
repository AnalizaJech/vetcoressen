<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    private function getTranslations()
    {
        $lang = request()->query('lang') 
            ?? request()->cookie('vc_locale') 
            ?? session('locale') 
            ?? 'es';
            
        if (!in_array($lang, ['es', 'en'])) {
            $lang = 'es';
        }
        
        $jsonPath = public_path("locales/{$lang}.json");
        $translations = [];
        if (file_exists($jsonPath)) {
            $translations = json_decode(file_get_contents($jsonPath), true);
        }

        return function ($key, $default = null) use ($translations) {
            $keys = explode('.', $key);
            $value = $translations;
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    return $default !== null ? $default : $key;
                }
            }
            return is_string($value) ? $value : ($default !== null ? $default : $key);
        };
    }

    public function cita($id)
    {
        $cita = Appointment::with([
            'cliente',
            'mascota.especie',
            'mascota.raza',
            'veterinario',
        ])->findOrFail($id);

        $t = $this->getTranslations();

        $pdf = Pdf::loadView('pdf.cita', compact('cita', 't'))
            ->setPaper('a4', 'portrait');

        $filename = 'cita-' . str_pad($cita->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        if (request()->query('download')) {
            return $pdf->download($filename);
        }
        return $pdf->stream($filename);
    }

    public function historia($id)
    {
        $historia = \App\Models\MedicalRecord::with(['pet.cliente', 'veterinario', 'prescripciones.producto'])->findOrFail($id);

        $t = $this->getTranslations();

        $pdf = Pdf::loadView('pdf.historia-clinica', compact('historia', 't'))
            ->setPaper('a4', 'portrait');

        $filename = 'historia-clinica-' . str_pad($historia->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        if (request()->query('download')) {
            return $pdf->download($filename);
        }
        return $pdf->stream($filename);
    }

    public function historialMascota($id)
    {
        $mascota = \App\Models\Pet::with(['cliente', 'especie', 'raza', 'historiasClinicas' => function ($q) {
            $q->orderBy('date', 'desc')->with(['veterinario', 'prescripciones.producto']);
        }])->findOrFail($id);

        $t = $this->getTranslations();

        $pdf = Pdf::loadView('pdf.historial-mascota', compact('mascota', 't'))
            ->setPaper('a4', 'portrait');

        $filename = 'historial-mascota-' . str_pad($mascota->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        if (request()->query('download')) {
            return $pdf->download($filename);
        }
        return $pdf->stream($filename);
    }
}
