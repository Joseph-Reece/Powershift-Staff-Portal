<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        <div class="flex justify-center">
            <x-panel>
                <x-slot name="title">Staff Details</x-slot>
                <x-slot name="body">
                    <div class="flex justify-center max-w-20 p-4">
                        <img src="/storage/general/avatar.jpg" class="rounded-full" style="max-height:100px; max-width:100px;">
                    </div>
                    <x-grid>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Employee No</x-slot>
                                <x-slot name="value">{{$employee->No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">First Name</x-slot>
                                <x-slot name="value">{{$employee->First_Name}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Middle Name</x-slot>
                                <x-slot name="value">{{$employee->Middle_Name}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Last Name</x-slot>
                                <x-slot name="value">{{$employee->Last_Name}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Phone No</x-slot>
                                <x-slot name="value">{{$employee->Home_Phone_Number}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Email</x-slot>
                                <x-slot name="value">{{$employee->E_Mail}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">ID Number</x-slot>
                                <x-slot name="value">{{$employee->ID_Number}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Gender</x-slot>
                                <x-slot name="value">{{$employee->Gender}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Job Mode</x-slot>
                                <x-slot name="value">{{$employee->Type_of_Contract}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                    </x-grid>
                </x-slot>
            </x-panel>
        </div>
    </div>
</x-app-layout>
