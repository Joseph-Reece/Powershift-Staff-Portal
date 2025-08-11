<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Transport Request Header</em></h3>
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Requested By</x-slot>
            <x-slot name="value">{{$data->Requested_By}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Requester Name</x-slot>
            <x-slot name="value">{{$employeeDesc != null? $employeeDesc['First_Name'].' '.$employeeDesc['Middle_Name'].' '.$employeeDesc['Last_Name']:''}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Destination</x-slot>
            <x-slot name="value">{{$data->Destination}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">No. of Days</x-slot>
            <x-slot name="value">{{$data->No_of_Days_Requested}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">No. of Passengers</x-slot>
            <x-slot name="value">{{$data->No_Of_Passangers}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Commencement</x-slot>
            <x-slot name="value">{{$data->Commencement}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Purpose of the Trip</x-slot>
            <x-slot name="value">{{$data->Purpose_of_Trip}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Date of the Trip</x-slot>
            <x-slot name="value">{{$data->Date_of_Trip}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Responsibility Center</x-slot>
            <x-slot name="value">{{$data->Responsibility_Center}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Date Requested</x-slot>
            <x-slot name="value">{{$data->Date_of_Request}}</x-slot>
        </x-show-group>
    </x-grid-col>

</x-grid>
