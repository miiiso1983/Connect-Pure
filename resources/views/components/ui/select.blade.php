@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'error' => null,
    'help' => null,
])

<div {{ $attributes->class(['w-full']) }}>
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <select id="{{ $id ?? $name }}" name="{{ $name }}" class="form-input">
        {{ $slot }}
    </select>
    @if($help)
        <p class="form-help">{{ $help }}</p>
    @endif
    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

