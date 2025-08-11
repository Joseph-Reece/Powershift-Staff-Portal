<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        {{-- <div class="flex justify-center"> --}}
            <x-panel>
                <x-slot name="title">Appraisal Requisition Details</x-slot>
                <x-slot name="body">
                    <x-grid>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Appraisal No</x-slot>
                                <x-slot name="value">{{$record->Appraisal_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Employee No</x-slot>
                                <x-slot name="value">{{$record->Employee_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Employee Name</x-slot>
                                <x-slot name="value">{{$record->Employee_Name}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Date of Employment</x-slot>
                                <x-slot name="value">{{$record->Date_of_Employment}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Job Title</x-slot>
                                <x-slot name="value">{{$record->Job_Title}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Supervisor</x-slot>
                                <x-slot name="value">{{$record->Supervisor}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Appraisal Period</x-slot>
                                <x-slot name="value">{{$record->Appraisal_Period}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Department</x-slot>
                                <x-slot name="value">{{$record->Department}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @if ($record->Status == 'Open' || $record->Status == 'Pending' || $record->Status == 'Pending')
                                        <x-badge class="bg-blue-600">Open</x-badge>
                                    @elseif($record->Status == 'Pending Approval')
                                        <x-badge class="bg-blue-600">Pending Supervisor Approval</x-badge>
                                    @elseif ($record->Status == 'Approved')
                                        <x-badge class="bg-green-600">{{$record->Status}}</x-badge>
                                    @else
                                        <x-badge class="bg-red-600">{{$record->Status}}</x-badge>
                                    @endif
                                </x-slot>
                            </x-show-group>
                        </x-grid-col>
                    </x-grid>
                    @include('staff.appraisal.index-line')
                    <x-slot name="footer">
                        @include('staff.appraisal.actions-header')
                    </x-slot>
                </x-slot>
            </x-panel>
        {{-- </div> --}}
    </div>
</x-app-layout>
