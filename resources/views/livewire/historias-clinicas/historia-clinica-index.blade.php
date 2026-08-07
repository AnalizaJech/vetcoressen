<div x-data>
    <x-slot:title x-text="$store.i18n.t('page.records')">Medical Records</x-slot:title>

<div class="animate-slide-up">
    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--purple">
                <span class="material-symbols-outlined">clinical_notes</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('page.records')"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('page.recordsSub')"></span></flux:subheading>
            </div>
        </div>
        <div class="w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('historias.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newRecord')"></span>
            </a>
        </div>
    </div>

    <x-vc-table-layout 
        :data="$historias"
        icon="clinical_notes"
        emptyTitle="Sin historias"
        emptyText="No hay historias clínicas que coincidan con los filtros."
        :searchable="true"
        searchModel="busqueda"
        x-bind:searchPlaceholder="$store.i18n.t('btn.search') || 'Buscar...'"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            @foreach($historias as $historia)
                <div class="vc-card flex flex-col p-4 rounded-xl shadow-sm border transition-shadow duration-300 hover:shadow-md" style="background: var(--vc-surface-alt); border-color: var(--vc-border);">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl" style="color: var(--vc-accent-light);">pets</span>
                            <div>
                                <h3 class="font-bold text-sm" style="color: var(--vc-text);">{{ $historia->mascota?->name ?? '-' }}</h3>
                                <p class="text-xs" style="color: var(--vc-text-muted);">{{ $historia->mascota?->cliente?->name_completo ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium" style="background: var(--vc-emerald-glow); color: var(--vc-emerald-light);">
                            <span class="material-symbols-outlined text-[14px]">event_note</span>
                            {{ $historia->date?->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    <div class="mb-4 flex-1">
                        <p class="text-xs mb-1 font-medium" style="color: var(--vc-text-muted);"><span x-text="$store.i18n.t('table.diagnosis') || 'Diagnóstico'"></span>:</p>
                        <p class="text-sm line-clamp-2" style="color: var(--vc-text);">{{ $historia->diagnosis ?? 'Sin diagnóstico registrado' }}</p>
                        
                        <div class="mt-3 flex items-center gap-1.5 text-xs" style="color: var(--vc-text-muted);">
                            <span class="material-symbols-outlined text-[14px]">stethoscope</span>
                            {{ $historia->veterinario?->name ?? '-' }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 pt-3" style="border-top: 1px solid var(--vc-border-subtle);">
                        <a href="{{ route('historias.ver', $historia->id) }}" class="vc-btn-action vc-btn-view p-1.5 rounded-lg flex items-center gap-1 transition-colors hover:bg-vc-surface-alt" :data-vc-tooltip="$store.i18n.t('btn.view') ?? 'Ver'">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        <a href="{{ route('historias.editar', $historia) }}" class="vc-btn-action vc-btn-edit p-1.5 rounded-lg flex items-center gap-1 transition-colors hover:bg-vc-surface-alt" :data-vc-tooltip="$store.i18n.t('btn.edit') ?? 'Editar'">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                        <button type="button" class="vc-btn-action vc-btn-delete p-1.5 rounded-lg flex items-center gap-1 transition-colors hover:bg-red-500/10 text-red-400" :data-vc-tooltip="$store.i18n.t('btn.delete') ?? 'Eliminar'"
                            @click="$wire.historiaEliminarId = {{ $historia->id }}; Flux.modal('confirmar-eliminar').show()"
                        >
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $historias->links() }}
        </div>
    </x-vc-table-layout>
</div>

    {{-- Modal --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg"><span x-text="$store.i18n.t('modal.deleteRecord')"></span></flux:heading>
                <flux:text class="mt-2"><span x-text="$store.i18n.t('modal.deleteRecordMsg')"></span></flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel')"></span></flux:button>
                </flux:modal.close>
                <flux:button
                    variant="danger"
                    wire:click="eliminar($wire.historiaEliminarId)"
                    x-on:click="Flux.modal('confirmar-eliminar').close()"
                >
                    <span x-text="$store.i18n.t('btn.delete')"></span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

