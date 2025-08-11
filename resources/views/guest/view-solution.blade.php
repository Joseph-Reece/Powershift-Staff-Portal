<x-app-layout>
    <div class="flex justify-center justify-items-center">
        <div class="max-w-2xl items-center">
            <x-slot name="title"></x-slot>
            <h1 class="text-2xl font-semibold text-center">{{$solution->title}}</h1>
            <div class="flex justify-center"><img class="max-h-80 max-w-full p-4" src="/storage/solutions/cover_images/{{$solution->cover_image}}" alt="{{$solution->title}}"></div>
            <div class="">{!!$solution->content!!}</div>
        </div>
    </div>
</x-app-layout>
