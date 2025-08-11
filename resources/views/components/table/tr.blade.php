@php
    $cursor = isset($onClick)? 'cursor-pointer':'';
    $bgColor = isset($isEven) && $isEven? 'bg-gray-200':'';
@endphp
<tr {{ $attributes->merge(['class' => "hover:bg-green-500 focus:bg-green-600 $cursor $bgColor"])}}>
    {{$slot}}
</tr>

