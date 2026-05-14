<?php

use App\Models\Relevamiento;
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
        $relevamientos = Relevamiento::with(['oficina', 'area'])
            ->where('tiene_impresora', true)
            ->get();

        $allPrinters = collect();

        foreach ($relevamientos as $r) {
            foreach ($r->impresoras ?? [] as $imp) {
                $allPrinters->push(
                    (object) [
                        'relevamiento_id' => $r->id,
                        'oficina' => $r->oficina?->nombre ?? 'N/A',
                        'area' => $r->area?->nombre ?? 'N/A',
                        'marca' => $imp['marca'] ?? '—',
                        'modelo' => $imp['modelo'] ?? '—',
                        'ip' => $imp['ip'] ?? '—',
                        'conexion' => $imp['conexion'] ?? 'red',
                        'tipo' => $imp['tipo'] ?? '—',
                        'escaner' => $imp['escaner'] ?? false,
                    ],
                );
            }
        }

        if ($this->search) {
            $s = strtolower($this->search);
            $allPrinters = $allPrinters->filter(function ($p) use ($s) {
                return str_contains(strtolower($p->oficina), $s) || str_contains(strtolower($p->marca), $s) || str_contains(strtolower($p->modelo), $s) || str_contains(strtolower($p->ip), $s);
            });
        }

        $page = request()->get('page', 1);
        $perPage = 15;
        $paginatedPrinters = new \Illuminate\Pagination\LengthAwarePaginator($allPrinters->forPage($page, $perPage), $allPrinters->count(), $perPage, $page, ['path' => request()->url(), 'query' => request()->query()]);

        return [
            'impresoras' => $paginatedPrinters,
        ];
    }
};

?>

<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ── Header ──────────────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Inventario de Impresoras</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Listado de todas las impresoras relevadas
                    por oficina.</p>
            </div>

            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar marca, modelo u oficina..."
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
                                Oficina / Área</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Impresora</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                IP</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Tipo</th>
                            <th
                                class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Conexión</th>
                            <th
                                class="px-6 py-3.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Escáner</th>
                            <th
                                class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($impresoras as $imp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                {{-- Oficina / Área --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $imp->oficina }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $imp->area }}</p>
                                </td>

                                {{-- Marca / Modelo --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                {{ $imp->marca }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                {{ $imp->modelo }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- IP --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono text-xs text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-900/50 px-2.5 py-1.5 rounded-lg">
                                        {{ $imp->ip }}
                                    </span>
                                </td>

                                {{-- Tipo --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                        @if (str_contains($imp->tipo, 'tinta')) bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300
                                        @elseif(str_contains($imp->tipo, 'toner'))
                                            bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300
                                        @else
                                            bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 @endif
                                    ">
                                        {{ str_replace('_', ' ', $imp->tipo) }}
                                    </span>
                                </td>

                                {{-- Conexión --}}
                                <td class="px-6 py-4">
                                    <span class="text-xs text-slate-600 dark:text-slate-400 uppercase font-medium">
                                        {{ $imp->conexion }}
                                    </span>
                                </td>

                                {{-- Escáner --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($imp->escaner)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Sí
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            No
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('relevamiento.editar', $imp->relevamiento_id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        Ver Relevamiento
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No se
                                            encontraron impresoras</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">Intenta con otro término
                                            de búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($impresoras->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $impresoras->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
