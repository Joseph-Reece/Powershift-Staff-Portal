<x-app-layout>
    <div class="flex justify-center justify-items-center">
        <div class="max-w-2xl items-center">
            <x-slot name="title"></x-slot>
            <h1 class="text-2xl font-semibold text-center">{{$service->title}}</h1>
            <div class="flex justify-center"><img class="max-h-80 max-w-full p-4" src="/storage/services/cover_images/{{$service->cover_image}}" alt="{{$service->title}}"></div>
            <div class="">{!!$service->content!!}</div>
        </div>
    </div>
</x-app-layout>
