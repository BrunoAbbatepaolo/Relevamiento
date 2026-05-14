@props(['label', 'wire', 'type' => 'text', 'placeholder' => '', 'step' => null])

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">
        {{ $label }}
    </label>

    <input type="{{ $type }}" wire:model="{{ $wire }}" placeholder="{{ $placeholder }}"
        {{ $step ? 'step=' . $step : '' }}
        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" />

    @error($wire)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
