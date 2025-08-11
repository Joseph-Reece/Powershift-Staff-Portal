<x-app-layout>
    <div class="flex justify-center justify-items-center">
        <div class="max-w-2xl items-center">
            <x-slot name="title"></x-slot>
            <h1 class="text-2xl font-semibold text-center">{{$training->title}}</h1>
            <div class="flex justify-center"><img class="max-h-80 max-w-full p-4" src="/storage/training/cover_images/{{$training->cover_image}}" alt="{{$training->title}}"></div>
            <div class="">{!!$training->content!!}</div>
        </div>
    </div>
</x-app-layout>
