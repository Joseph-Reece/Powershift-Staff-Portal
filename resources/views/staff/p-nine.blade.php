<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel maxWidth="max-w-lg">
            <x-slot name="title">P9 Form</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('generatePNine')}}" class="text-black w-full" data-turbo-frame="_top" target="_blank">
                 @csrf
                    <x-grid cols="">
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Year</x-slot>
                                <x-slot name="value">
                                    <x-input type="number" name="year" id="year" value="{{old('year')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        {{-- <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Start Date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="startDate" id="startDate" value="{{old('startDate')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">End Date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="endDate" id="endDate" value="{{old('endDate')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col> --}}
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button type="submit" class="rounded-full bg-blue-800" data-turbo="false">Generate</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
</x-app-layout>
