@props(['value','textColor'])
@php
    $textColor = (isset($textColor) && $textColor != '')? $textColor:'text-gray-700';
@endphp
<label {{ $attributes->merge(['class' =>  ' text-sm mb-2 '.$textColor]) }}>
    {{ $value ?? $slot }}
</label>
