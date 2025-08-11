<div class="flex h-screen">
    <div class="m-auto">
        <div class="flex items-center justify-center">
            {{$logo}}
        </div>
        <div class="flex items-center justify-center wx-full mt-2">
            <div class="min-h-0 flex flex-col sm:justify-center items-center ptx-6 pt-1 pb-3 bg-theme1 pr-2 pl-2 rounded-lg">
                <div class="flex justify-center items-center text-white font-semibold sm:pt-1 italicx border-b border-white">
                    {{ isset($title)? $title:'' }}
                </div>
                <div class="w-screen roundedx-md max-w-xs sm:max-w-sm md:max-sm-md mt-2 sm:mt-2 px-6 py-4 bg-theme1 shadow-md overflow-hidden">
                    {{ $slot }}
                </div>
                <div class="italic pt-1 text-xs">
                    Powered by <a href="https://www.potestastechnologies.net/" class="underline" target="_blank">Potestas Technologies Ltd.</a>
                </div>
            </div>
        </div>
    </div>
</div>
