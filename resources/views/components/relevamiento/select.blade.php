@props(['label', 'wire', 'options' => []])

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">
        {{ $label }}
    </label>

    <select wire:model="{{ $wire }}"
        class="w-full rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
        <option value="">— Seleccionar —</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>

    @error($wire)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
