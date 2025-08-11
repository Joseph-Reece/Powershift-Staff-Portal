@props(['id' => $id, 'maxWidth' => null,'xScope' => $xScope])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" :xScope="$xScope" {{ $attributes }}>
    <div class="text-md bg-gray-100 px-2 py-1 flex justify-between items-center">
        <span class="text-center">{{ $title }}</span>
        @if($id != null && $id != 'null')
            <x-heroicon-o-x class="text-red-500 cursor-pointer h-2 w-2" x-on:click="$dispatch('dlg-modal',{status:false});showx = false;$wire.updateVariable('{{$xScope}}','{{$id}}')"/>
        @else
            <x-heroicon-o-x class="text-red-500 cursor-pointer h-2 w-2" x-on:click="show = false"/>
        @endif
    </div>
    <hr>
    <div class="px-2 py-1 mt-1">
        {{ $content }}
    </div>
    <div class="px-2 py-1 bg-gray-100 text-right flex justify-end">
        {{ $footer }}
    </div>
</x-jet-modal>
