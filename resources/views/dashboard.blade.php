<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ── Hero / Bienvenida ─────────────────────────────────────── --}}
            <div
                class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-blue-800 dark:from-blue-700 dark:to-blue-950 rounded-2xl shadow-lg px-8 py-10">
                {{-- Decoración de fondo --}}
                <div class="absolute -top-8 -right-8 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none">
                </div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full blur-xl pointer-events-none">
                </div>

                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <p class="text-blue-200 text-sm font-medium mb-1 tracking-wide uppercase">Sistema de Relevamiento
                        </p>
                        <h3 class="text-2xl md:text-3xl font-bold text-white leading-tight">
                            Bienvenido al Panel de Control
                        </h3>
                        <p class="text-blue-200/80 text-sm mt-2 max-w-md">
                            Gestioná relevamientos de equipamiento informático, consultá el inventario y realizá nuevas
                            auditorías por repartición.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 shrink-0">
                        <a href="{{ route('relevamientos.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold rounded-xl transition backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Relevamientos
                        </a>
                        <a href="{{ route('activos.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold rounded-xl transition backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Inventario
                        </a>
                        <a href="{{ route('relevamiento.nuevo') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-blue-700 text-sm font-bold rounded-xl hover:bg-blue-50 transition shadow-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Iniciar Auditoría
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── Accesos rápidos ───────────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <a href="{{ route('relevamiento.nuevo') }}"
                    class="group flex items-start gap-4 p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-500 transition-all">
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Nueva Auditoría</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Iniciar un relevamiento para una
                            oficina o repartición.</p>
                    </div>
                </a>

                <a href="{{ route('relevamientos.index') }}"
                    class="group flex items-start gap-4 p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm hover:shadow-md hover:border-violet-300 dark:hover:border-violet-500 transition-all">
                    <div
                        class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/50 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Gestionar Relevamientos</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ver, editar y exportar los
                            relevamientos cargados.</p>
                    </div>
                </a>

                <a href="{{ route('activos.index') }}"
                    class="group flex items-start gap-4 p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-500 transition-all">
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Inventario de Equipos</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Consultá el listado global de
                            activos por oficina.</p>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
