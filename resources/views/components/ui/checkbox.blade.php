@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'checked' => false,
])

<label class="inline-flex items-center space-x-3">
    <input id="{{ $id ?? $name }}" name="{{ $name }}" type="checkbox" @checked(old($name, $checked)) class="h-5 w-5 rounded-md border-gray-300 text-blue-600 focus:ring-blue-500" />
    @if($label)
        <span class="text-sm text-gray-700">{{ $label }}</span>
    @endif
</label>

