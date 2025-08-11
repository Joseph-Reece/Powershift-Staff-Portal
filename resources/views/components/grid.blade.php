@props(['cols' => 'sm:grid-cols-2 md:grid-cols-3'])

<div {{ $attributes->merge(['class' => "grid grid-cols-1 $cols border-t border-b border-l"])}}>
    {{$slot}}
</div>
