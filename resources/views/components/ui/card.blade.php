@props(['class' => ''])

<div {{ $attributes->merge(['class' => trim('modern-card '.$class)]) }}>
    {{ $slot }}
</div>

