<div {{ $attributes->merge(['class' => "flex justify-center items-center bg-gray-200 w-full h-28 md:h-28 text-white border-l-8"])}}>
    <div class="flex flex-col items-center justify-center pl-1 w-full h-full">
        {{-- <div class="flex sm:text-3xl">{{$value}}</div> --}}
        <div class="pl-1 flex items-center text-2xl">{{$icon}}</div>
        <div class="flex justify-center items-center gap-2 pt-2 underline">
            {{-- <div class="pl-1 flex items-center flex-grow">{{$icon}}</div> --}}
            {{$label}}
        </div>
    </div>
</div>
