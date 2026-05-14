<?php

use App\Models\Activo;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Activo::query()
            ->with(['relevamiento.oficina', 'relevamiento.area'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('ip', 'like', "%{$this->search}%")
                    ->orWhere('mac', 'like', "%{$this->search}%")
                    ->orWhere('usuario_nombre', 'like', "%{$this->search}%")
                    ->orWhere('usuario_apellido', 'like', "%{$this->search}%")
                    ->orWhere('codigo_inventario', 'like', "%{$this->search}%")
                    ->orWhereHas('relevamiento.oficina', function ($sq) {
                        $sq->where('nombre', 'like', "%{$this->search}%");
                    });
            });
        }

        return [
            'activos' => $query->paginate(15),
        ];
    }
};

?>

<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ── Header ──────────────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Inventario de Equipos</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Listado general de activos (PCs, notebooks)
                    registrados por oficina.</p>
            </div>

            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por IP, MAC, usuario u oficina..."
                    class="block w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
            </div>
        </div>

        {{-- ── Tabla ────────────────────────────────────────────────────── --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80">
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Equipo</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Ubicación</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Red</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Usuario</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Estado</th>
                            <th
                                class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($activos as $activo)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                {{-- Equipo --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div @class([
                                            'w-10 h-10 rounded-lg flex items-center justify-center shrink-0',
                                            'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' =>
                                                $activo->tipo === 'notebook',
                                            'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' =>
                                                $activo->tipo !== 'notebook',
                                        ])>
                                            @if ($activo->tipo === 'notebook')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                                {{ $activo->marca ?: 'Sin Marca' }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Inv: {{ $activo->codigo_inventario ?: 'S/N' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Ubicación --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-100">
                                        {{ $activo->relevamiento?->oficina?->nombre ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $activo->relevamiento?->area?->nombre ?? 'N/A' }}
                                    </p>
                                </td>

                                {{-- Red --}}
                                <td class="px-6 py-4">
                                    <p
                                        class="text-xs font-mono text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-900/50 px-2.5 py-1.5 rounded-lg whitespace-nowrap inline-block">
                                        {{ $activo->ip ?: '—' }}
                                    </p>
                                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                                        {{ $activo->mac ?: '—' }}
                                    </p>
                                </td>

                                {{-- Usuario --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-800 dark:text-slate-100">
                                        {{ $activo->usuario_nombre }} {{ $activo->usuario_apellido }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 capitalize mt-0.5">
                                        {{ $activo->usuario_caracter ?: 'Sin carácter' }}
                                    </p>
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4">
                                    <span @class([
                                        'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide',
                                        'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' =>
                                            $activo->estado === 'activo',
                                        'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' =>
                                            $activo->estado === 'reparacion',
                                        'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' =>
                                            $activo->estado === 'inactivo',
                                    ])>
                                        @if ($activo->estado === 'activo')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @elseif($activo->estado === 'reparacion')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        {{ $activo->estado }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('relevamiento.editar', ['id' => $activo->relevamiento_id, 'activo' => $activo->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Ver/Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No se
                                            encontraron equipos</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">Intenta con otro término
                                            de búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activos->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $activos->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
