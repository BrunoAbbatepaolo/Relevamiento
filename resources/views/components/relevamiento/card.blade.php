@props(['title', 'icon' => ''])

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50">
        <h3 class="font-semibold text-slate-800">{{ $title }}</h3>
    </div>
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
