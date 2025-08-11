<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        {{-- <div class="flex justify-center"> --}}
            <x-panel>
                <x-slot name="title">Transport Requisition Details</x-slot>
                <x-slot name="body">
                    <x-grid>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Requisition No</x-slot>
                                <x-slot name="value">{{$requisition->Transport_Requisition_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Destination</x-slot>
                                <x-slot name="value">{{$requisition->Destination}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">No. of Days</x-slot>
                                <x-slot name="value">{{$requisition->No_of_Days_Requested}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">No. of Passengers</x-slot>
                                <x-slot name="value">{{$requisition->No_Of_Passangers}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Commencement</x-slot>
                                <x-slot name="value">{{$requisition->Commencement}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Purpose of the Trip</x-slot>
                                <x-slot name="value">{{$requisition->Purpose_of_Trip}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Date of the Trip</x-slot>
                                <x-slot name="value">{{$requisition->Date_of_Trip}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Responsibility Center</x-slot>
                                <x-slot name="value">{{$requisition->Responsibility_Center}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Date Requested</x-slot>
                                <x-slot name="value">{{$requisition->Date_of_Request}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Requested By</x-slot>
                                <x-slot name="value">{{$requisition->Requested_By}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @if ($requisition->Status == 'Open' || $requisition->Status == 'Pending Approval')
                                        <x-badge class="bg-blue-600">{{$requisition->Status}}</x-badge>
                                    @elseif ($requisition->Status == 'Approved')
                                        <x-badge class="bg-green-600">{{$requisition->Status}}</x-badge>
                                    @else
                                        <x-badge class="bg-red-600">{{$requisition->Status}}</x-badge>
                                    @endif
                                </x-slot>
                            </x-show-group>
                        </x-grid-col>
                    </x-grid>
                    @if($requisition->Status == 'Pending Approval' || $requisition->Status == 'Approved')
                        @include('staff.approval.approvers')
                    @endif
                    <x-slot name="footer">
                        @include('staff.requisition.transport.actions')
                    </x-slot>
                </x-slot>
            </x-panel>
        {{-- </div> --}}
    </div>
</x-app-layout>
