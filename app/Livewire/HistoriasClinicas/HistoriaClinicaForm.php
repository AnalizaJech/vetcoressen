<?php

namespace App\Livewire\HistoriasClinicas;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Formulario de historia clínica con triaje y prescripciones dinámicas
#[Layout('components.layouts.app')]
#[Title('Historia Clínica')]
class HistoriaClinicaForm extends Component
{
    // Identificador para edición
    public ?int $historiaId = null;

    // Relaciones
    public ?string $customer_id = null;
    public ?string $pet_id = null;
    public ?string $veterinarian_id = null;
    public ?string $appointment_id = null;

    // Datos principales
    public ?string $fecha_consulta = null;
    public ?string $reason = null;

    // Triaje obligatorio
    public ?string $weight = null;
    public ?string $temperatura = null;
    public ?string $frecuencia_cardiaca = null;
    public ?string $frecuencia_respiratoria = null;

    // Diagnóstico y tratamiento
    public ?string $anamnesis = null;
    public ?string $diagnostico_presuntivo = null;
    public ?string $tratamiento_indicaciones = null;
    public ?string $proxima_cita_recomendada = null;
    public ?string $notas_aclaratorias = null;

    // Examen físico general
    public ?string $examen_mucosas = null;
    public ?string $examen_linfonodos = null;
    public ?string $condicion_corporal = null;
    public ?string $nivel_dolor = null;
    public ?string $nivel_hidratacion = null;

    // Examen por sistemas
    public ?string $examen_piel_pelaje = null;
    public ?string $examen_ojos_oidos = null;
    public ?string $examen_cardiovascular = null;
    public ?string $examen_respiratorio = null;
    public ?string $examen_digestivo = null;
    public ?string $examen_musculoesqueletico = null;
    public ?string $examen_neurologico = null;
    public ?string $examen_urinario = null;

    public ?string $alerta_peso = null;
    public ?string $alerta_temp = null;

    // Prescripciones dinámicas (array de arrays)
    public array $prescripciones = [];

    // Reglas de validación
    protected function rules(): array
    {
        $rules = [
            'pet_id'              => 'required|exists:pets,id',
            'veterinarian_id'          => 'required|exists:users,id',
            'appointment_id'                 => 'nullable|exists:appointments,id',
            'fecha_consulta'                   => 'required|date',
            'reason'         => 'required|string|max:500',
            'weight'                    => 'required|numeric|min:0.01|max:999',
            'temperatura'             => 'required|numeric|min:30|max:45',
            'frecuencia_cardiaca'     => 'nullable|integer|min:0|max:300',
            'frecuencia_respiratoria' => 'nullable|integer|min:0|max:200',
            'anamnesis'               => 'nullable|string|max:2000',
            'diagnostico_presuntivo'  => 'nullable|string|max:1000',
            'tratamiento_indicaciones' => 'nullable|string|max:2000',
            'proxima_cita_recomendada' => 'nullable|date|after:today',
            // Examen físico
            'examen_mucosas'          => 'nullable|string|max:50',
            'examen_linfonodos'       => 'nullable|string|max:50',
            'condicion_corporal'      => 'nullable|integer|min:1|max:9',
            'nivel_dolor'             => 'nullable|integer|min:0|max:10',
            'nivel_hidratacion'       => 'nullable|string|max:30',
            'examen_piel_pelaje'      => 'nullable|string|max:1000',
            'examen_ojos_oidos'       => 'nullable|string|max:1000',
            'examen_cardiovascular'   => 'nullable|string|max:1000',
            'examen_respiratorio'     => 'nullable|string|max:1000',
            'examen_digestivo'        => 'nullable|string|max:1000',
            'examen_musculoesqueletico' => 'nullable|string|max:1000',
            'examen_neurologico'      => 'nullable|string|max:1000',
            'examen_urinario'         => 'nullable|string|max:1000',
            // Validación de prescripciones
            'prescripciones.*.product_id'    => 'nullable|integer',
            'prescripciones.*.medicamento'   => 'required|string|max:200',
            'prescripciones.*.dosage'         => 'required|string|max:100',
            'prescripciones.*.frequency'    => 'required|string|max:100',
            'prescripciones.*.duracion_dias' => 'required|integer|min:1|max:365',
            'prescripciones.*.via_administracion' => 'nullable|string|max:50',
            'prescripciones.*.indicaciones'  => 'nullable|string|max:500',
        ];

        if ($this->pet_id) {
            $pet = Pet::with('especie')->find($this->pet_id);
            if ($pet && $pet->especie && in_array($pet->especie->name, ['Exótica', 'Genérica', 'Exótico'])) {
                $rules['weight'] = 'nullable|numeric|min:0.01|max:999';
                $rules['temperatura'] = 'nullable|numeric|min:30|max:45';
            }
        }

        return $rules;
    }

    // Cargar datos si es edición
    public function mount(?int $id = null): void
    {
        $this->fecha_consulta = now()->format('Y-m-d');

        // Pre-seleccionar veterinario actual si es veterinario
        if (auth()->user()->hasRole('veterinario')) {
            $this->veterinarian_id = (string) auth()->id();
        }

        if ($id) {
            $historia = MedicalRecord::with('prescripciones')->findOrFail($id);
            $this->historiaId = $historia->id;
            $this->pet_id = (string) $historia->pet_id;
            $this->veterinarian_id = (string) $historia->veterinarian_id;
            $this->appointment_id = (string) ($historia->appointment_id ?? '');
            $this->fecha_consulta = $historia->date->format('Y-m-d');
            $this->reason = $historia->reason ?? '';
            $this->weight = (string) $historia->weight;
            $this->temperatura = (string) $historia->temperature;
            $this->frecuencia_cardiaca = (string) ($historia->heart_rate ?? '');
            $this->frecuencia_respiratoria = (string) ($historia->respiratory_rate ?? '');
            $this->anamnesis = $historia->anamnesis ?? '';
            $this->diagnostico_presuntivo = $historia->diagnostico_presuntivo ?? '';
            $this->tratamiento_indicaciones = $historia->tratamiento_indicaciones ?? '';
            $this->proxima_cita_recomendada = $historia->proxima_cita_recomendada
                ? $historia->proxima_cita_recomendada->format('Y-m-d') : '';
            $this->notas_aclaratorias = $historia->notas_aclaratorias ?? '';

            // Cargar examen físico
            $this->examen_mucosas = $historia->examen_mucosas ?? '';
            $this->examen_linfonodos = $historia->examen_linfonodos ?? '';
            $this->condicion_corporal = (string) ($historia->condicion_corporal ?? '');
            $this->nivel_dolor = (string) ($historia->nivel_dolor ?? '');
            $this->nivel_hidratacion = $historia->nivel_hidratacion ?? '';
            $this->examen_piel_pelaje = $historia->examen_piel_pelaje ?? '';
            $this->examen_ojos_oidos = $historia->examen_ojos_oidos ?? '';
            $this->examen_cardiovascular = $historia->examen_cardiovascular ?? '';
            $this->examen_respiratorio = $historia->examen_respiratorio ?? '';
            $this->examen_digestivo = $historia->examen_digestivo ?? '';
            $this->examen_musculoesqueletico = $historia->examen_musculoesqueletico ?? '';
            $this->examen_neurologico = $historia->examen_neurologico ?? '';
            $this->examen_urinario = $historia->examen_urinario ?? '';

            // Cargar prescripciones existentes
            foreach ($historia->prescripciones as $rx) {
                $this->prescripciones[] = [
                    'id'                 => $rx->id,
                    'product_id'        => (string) ($rx->product_id ?? ''),
                    'medicamento'        => $rx->medicamento,
                    'dosage'              => $rx->dosage,
                    'frequency'         => $rx->frequency,
                    'via_administracion' => $rx->via_administracion ?? '',
                    'duracion_dias'      => $rx->duracion_dias ?? 1,
                    'indicaciones'       => $rx->indicaciones ?? '',
                ];
            }

            // Cargar cliente a partir de mascota para cascada
            $mascota = Pet::find($this->pet_id);
            if ($mascota) {
                $this->customer_id = (string) $mascota->customer_id;
            }
        }

        // Precargar datos desde cita de origen (flujo "Iniciar Atención")
        $citaId = request()->query('cita');
        if ($citaId && !$id) {
            $cita = Appointment::with(['cliente', 'mascota'])->find($citaId);
            if ($cita) {
                $this->appointment_id = (string) $cita->id;
                $this->customer_id = (string) $cita->customer_id;
                $this->pet_id = (string) $cita->pet_id;
                $this->veterinarian_id = (string) ($cita->veterinarian_id ?? auth()->id());
                $this->reason = $cita->reason ?? '';
                $this->fecha_consulta = $cita->fecha_hora ? $cita->fecha_hora->format('Y-m-d') : now()->format('Y-m-d');
            }
        }
    }

    // Cascada: al cambiar cliente, resetear mascota y cita
    public function updatedCustomerId(): void
    {
        $this->pet_id = null;
        $this->appointment_id = null;
    }

    // Cascada: al cambiar mascota, resetear cita
    public function updatedPetId(): void
    {
        $this->appointment_id = null;
        $this->updatedWeight();
    }

    public function seleccionarCita(int $citaId): void
    {
        $cita = Appointment::find($citaId);
        if ($cita) {
            $this->appointment_id = (string) $cita->id;
            $this->veterinarian_id = (string) ($cita->veterinarian_id ?? auth()->id());
            $this->reason = $cita->reason ?? '';
            // If the user wants date to be the appointment date, we can set it:
            $this->fecha_consulta = $cita->fecha_hora ? $cita->fecha_hora->format('Y-m-d') : now()->format('Y-m-d');
        }
    }

    public function updatedWeight(): void
    {
        $this->alerta_peso = '';
        if ($this->pet_id && $this->weight) {
            $ultimaHistoria = MedicalRecord::where('pet_id', $this->pet_id)
                                ->when($this->historiaId, fn($q) => $q->where('id', '!=', $this->historiaId))
                                ->orderBy('date', 'desc')
                                ->first();
            if ($ultimaHistoria && $ultimaHistoria->weight > 0) {
                $cambioPeso = abs($this->weight - $ultimaHistoria->weight) / $ultimaHistoria->weight;
                if ($cambioPeso > 0.30) {
                    $this->alerta_peso = 'Advertencia: El peso ha cambiado más del 30% respecto a la última consulta (' . $ultimaHistoria->weight . ' kg).';
                }
            }
        }
    }

    public function updatedTemperatura(): void
    {
        $this->alerta_temp = '';
        if ($this->temperatura) {
            if ($this->temperatura < 37.5 || $this->temperatura > 39.5) {
                $this->alerta_temp = 'Alerta: Temperatura fuera de rango normal (37.5 - 39.5°C).';
            }
        }
    }

    // Agregar fila de prescripción vacía
    public function agregarPrescripcion(): void
    {
        $this->prescripciones[] = [
            'id'                 => null,
            'product_id'        => '',
            'medicamento'        => '',
            'dosage'              => '',
            'frequency'         => '',
            'via_administracion' => 'Oral',
            'duracion_dias'      => 1,
            'indicaciones'       => '',
        ];
    }

    // Eliminar fila de prescripción
    public function eliminarPrescripcion(int $index): void
    {
        unset($this->prescripciones[$index]);
        $this->prescripciones = array_values($this->prescripciones);
    }

    public function updatedPrescripciones($name, $value)
    {
        // $name will be something like "0.product_id"
        if (str_ends_with($name, '.product_id') && $value) {
            $index = explode('.', $name)[0];
            $producto = Product::find((int) $value);
            if ($producto) {
                $this->prescripciones[$index]['medicamento'] = $producto->name;
            }
        }
    }

    // Guardar historia clínica + prescripciones
    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'clinic_id'                => 1,
            'pet_id'                => $this->pet_id,
            'veterinarian_id'            => $this->veterinarian_id,
            'appointment_id'                   => $this->appointment_id ?: null,
            'date'                     => $this->fecha_consulta,
            'reason'           => $this->reason,
            'weight'                      => $this->weight,
            'temperature'               => $this->temperatura,
            'heart_rate'       => $this->frecuencia_cardiaca ?: null,
            'respiratory_rate'   => $this->frecuencia_respiratoria ?: null,
            // Examen físico
            'examen_mucosas'             => $this->examen_mucosas ?: null,
            'examen_linfonodos'          => $this->examen_linfonodos ?: null,
            'condicion_corporal'         => $this->condicion_corporal ?: null,
            'nivel_dolor'                => $this->nivel_dolor !== '' ? (int) $this->nivel_dolor : null,
            'nivel_hidratacion'          => $this->nivel_hidratacion ?: null,
            'examen_piel_pelaje'         => $this->examen_piel_pelaje ?: null,
            'examen_ojos_oidos'          => $this->examen_ojos_oidos ?: null,
            'examen_cardiovascular'      => $this->examen_cardiovascular ?: null,
            'examen_respiratorio'        => $this->examen_respiratorio ?: null,
            'examen_digestivo'           => $this->examen_digestivo ?: null,
            'examen_musculoesqueletico'  => $this->examen_musculoesqueletico ?: null,
            'examen_neurologico'         => $this->examen_neurologico ?: null,
            'examen_urinario'            => $this->examen_urinario ?: null,
            // Diagnóstico
            'anamnesis'                 => $this->anamnesis ?: null,
            'diagnostico_presuntivo'    => $this->diagnostico_presuntivo ?: null,
            'tratamiento_indicaciones'  => $this->tratamiento_indicaciones ?: null,
            'proxima_cita_recomendada'  => $this->proxima_cita_recomendada ?: null,
            'notas_aclaratorias'        => $this->notas_aclaratorias ?: null,
        ];

        if ($this->historiaId) {
            $historia = MedicalRecord::findOrFail($this->historiaId);
            
            // Inmutabilidad a las 24h + Notas Aclaratorias Anexas
            $paso24h = clone $historia->created_at;
            $paso24h = $paso24h->diffInHours(now()) >= 24;
            
            if ($paso24h && !auth()->user()->hasRole('super_admin')) {
                $historia->update(['notas_aclaratorias' => $this->notas_aclaratorias]);
                session()->flash('mensaje', 'Historia clínica bloqueada (24h). Solo se guardaron las Notas Aclaratorias Anexas.');
                $this->redirect(route('historias.index'), navigate: true);
                return;
            }

            $datos['notas_aclaratorias'] = $this->notas_aclaratorias;
            $historia->update($datos);
            // Eliminar prescripciones anteriores y recrear
            $historia->prescripciones()->delete();
        } else {
            $historia = MedicalRecord::create($datos);
        }

        // Crear prescripciones
        foreach ($this->prescripciones as $rx) {
            $historia->prescripciones()->create([
                'clinic_id'        => 1,
                'product_id'       => $rx['product_id'] ?: null,
                'medicamento'       => $rx['medicamento'],
                'dosage'             => $rx['dosage'],
                'frequency'        => $rx['frequency'],
                'via_administracion' => $rx['via_administracion'] ?: null,
                'duracion_dias'     => $rx['duracion_dias'],
                'indicaciones'      => $rx['indicaciones'] ?: null,
                'dispensado'        => false,
            ]);
        }

        $accion = $this->historiaId ? 'actualizada' : 'registrada';
        
        // Enviar notificación de receta si hay prescripciones (nueva historia)
        if (!empty($this->prescripciones) && !$this->historiaId) {
            $cliente = Customer::find($this->customer_id);
            if ($cliente && $cliente->email) {
                app(\App\Services\EmailNotificationService::class)->sendRecetaNotification(
                    $cliente->id,
                    $cliente->email,
                    new \App\Mail\RecetaMail($historia)
                );
            }
        }

        // Si la HC tiene cita vinculada y es nueva, marcar la cita como COMPLETADA
        if ($this->appointment_id && !$this->historiaId) {
            $citaVinculada = Appointment::find($this->appointment_id);
            if ($citaVinculada) {
                $citaVinculada->update(['status' => 'COMPLETADA']);
            }
        }

        session()->flash('mensaje', "Historia clínica {$accion} correctamente.");
        $this->redirect(route('historias.index', ['clienteSeleccionadoId' => $this->customer_id]), navigate: true);
    }

    public function render()
    {
        // Clientes activos para selección
        $clientes = Customer::orderBy('first_name')->get();

        // Mascotas del cliente seleccionado
        $mascotas = $this->customer_id
            ? Pet::where('customer_id', $this->customer_id)->orderBy('name')->get()
            : collect();

        // Citas pendientes de la mascota seleccionada
        $citas = $this->pet_id
            ? Appointment::where('pet_id', $this->pet_id)
                ->whereIn('status', ['programada', 'confirmada'])
                ->orderByDesc('fecha_hora')
                ->get()
            : collect();

        // Veterinarios (usuarios con rol veterinario)
        $veterinarios = User::role('veterinario')->orderBy('name')->get();

        // Productos para prescripciones (solo medicamentos)
        $productos = Product::where('is_active', true)
            ->where('categoria', 'Medicamentos')
            ->orderBy('name')
            ->get();

        return view('livewire.historias-clinicas.historia-clinica-form', [
            'clientes'     => $clientes,
            'mascotas'     => $mascotas,
            'citas'        => $citas,
            'veterinarios' => $veterinarios,
            'productos'    => $productos,
        ]);
    }
}
