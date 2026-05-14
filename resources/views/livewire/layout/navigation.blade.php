<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }"
    class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700/60 sticky top-0 z-40 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- ── Logo + Links principales ──────────────────────────── --}}
            <div class="flex items-center gap-8">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span
                            class="font-bold text-slate-800 dark:text-slate-100 text-sm tracking-tight hidden sm:block">
                            Relevamiento
                        </span>
                    </a>
                </div>

                {{-- Links desktop --}}
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" wire:navigate @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition',
                        'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30' => request()->routeIs(
                            'dashboard'),
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                            'dashboard'),
                    ])>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Panel
                    </a>
                    <a href="{{ route('relevamientos.index') }}" wire:navigate @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition',
                        'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30' => request()->routeIs(
                            'relevamientos.*'),
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                            'relevamientos.*'),
                    ])>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Relevamientos
                    </a>
                    <a href="{{ route('activos.index') }}" wire:navigate @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition',
                        'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30' => request()->routeIs(
                            'activos.*'),
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                            'activos.*'),
                    ])>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Inventario
                    </a>
                    <a href="{{ route('impresoras.index') }}" wire:navigate @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition',
                        'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30' => request()->routeIs(
                            'impresoras.*'),
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                            'impresoras.*'),
                    ])>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2.56l1.54 3h1.38l-1.54-3H21M17 17v-6a1 1 0 011-1h2a1 1 0 011 1v6M17 17h4M6 21h12M7 13h10a1 1 0 011 1v5H6v-5a1 1 0 011-1z" />
                        </svg>
                        Impresoras
                    </a>
                    <a href="{{ route('modems.index') }}" wire:navigate @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition',
                        'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30' => request()->routeIs(
                            'modems.*'),
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                            'modems.*'),
                    ])>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                        Módems
                    </a>
                </div>
            </div>

            {{-- ── Derecha: CTA + User menu ──────────────────────────── --}}
            <div class="hidden sm:flex items-center gap-3">


                {{-- Divider --}}
                <div class="w-px h-5 bg-slate-200 dark:bg-slate-700"></div>

                {{-- User dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <div
                                class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name" class="max-w-[120px] truncate">
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- ── Hamburger (mobile) ────────────────────────────────── --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Menú responsive (mobile) ──────────────────────────────────── --}}
    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden border-t border-slate-200 dark:border-slate-700">
        <div class="px-4 pt-3 pb-2 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                Panel
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('relevamientos.index')" :active="request()->routeIs('relevamientos.*')" wire:navigate>
                Relevamientos
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('activos.index')" :active="request()->routeIs('activos.*')" wire:navigate>
                Inventario de Equipos
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('impresoras.index')" :active="request()->routeIs('impresoras.*')" wire:navigate>
                Inventario de Impresoras
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('modems.index')" :active="request()->routeIs('modems.*')" wire:navigate>
                Inventario de Módems
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('relevamiento.nuevo')" :active="request()->routeIs('relevamiento.nuevo')" wire:navigate>
                + Nueva Auditoría
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-3 border-t border-slate-200 dark:border-slate-700">
            <div class="px-4 flex items-center gap-3 mb-3">
                <div
                    class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center text-sm font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate"
                        x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name">
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="space-y-1 px-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    Mi Perfil
                </x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
