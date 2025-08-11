@php
    $isShow = isset($show)? '':' hidden';
@endphp
<img id="loader" src="/storage/general/{{$slot != ''? $slot:'loader-circle-sm'}}.gif" {{ $attributes->merge(['class' => "w-5 $isShow"])}}>
