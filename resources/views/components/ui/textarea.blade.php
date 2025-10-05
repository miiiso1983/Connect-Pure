@props([
    'id' => null,
    'name' => null,
    'rows' => 4,
    'value' => null,
    'placeholder' => '',
    'error' => null,
    'label' => null,
    'help' => null,
])

<div {{ $attributes->class(['w-full']) }}>
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <textarea id="{{ $id ?? $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}" class="form-input">{{ old($name, $value) }}</textarea>
    @if($help)
        <p class="form-help">{{ $help }}</p>
    @endif
    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

