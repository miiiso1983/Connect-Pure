@props([
    'id' => null,
    'name' => null,
    'checked' => false,
    'label' => null,
])

<label class="flex items-center space-x-3 cursor-pointer select-none">
    <span class="text-sm text-gray-700">{{ $label }}</span>
    <input id="{{ $id ?? $name }}" name="{{ $name }}" type="checkbox" class="sr-only peer" @checked(old($name, $checked)) />
    <div class="w-11 h-6 bg-gray-300 rounded-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 transition-all duration-200 peer-checked:bg-blue-600 relative">
        <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-all duration-200 peer-checked:translate-x-5 shadow"></span>
    </div>
</label>

