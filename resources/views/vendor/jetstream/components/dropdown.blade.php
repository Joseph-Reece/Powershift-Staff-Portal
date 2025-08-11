@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'border border-2 border-yellow-500', 'active' => $active ?? true? true:false])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
}
@endphp

<div class="relative  {{ $active ?? true? 'border-b-4 border-yellow-500':''}}" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false" @mouseover="open = ! open" @mouseout="open = ! open" @click="open = ! open">
    <div @click="open = ! open"    class="trigger flex items-center justify-between cursor-pointer pt-1 text-white">
        {{ $trigger }}
        <span :class="{'hidden': open, 'block': !open}" class="icon"><x-heroicon-o-chevron-down/></span>
        <span :class="{'hidden': !open, 'block': open}" class="icon hidden"><x-heroicon-o-chevron-up/></span>
    </div>
    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-0 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="shadow-xs {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
