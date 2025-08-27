@props(['maxWidth' => 'max-w-full'])

<div class="flex justify-center">
    <div {{ $attributes->merge(['class' => "panel border border-green-500 rounded-md w-screen sm:w-full $maxWidth"])}}>
        <div class="section-bg  flex justify-center py-1 px-2 text-white font-semibold w-full">
            {{$title}}
        </div>
        <div class="panel-body p-2 w-full">
            {{$body}}
        </div>
        @if(isset($footer))
            <div class="panel-footer w-full bg-gray-300">
                {{$footer}}
            </div>
        @endif
    </div>
</div>
