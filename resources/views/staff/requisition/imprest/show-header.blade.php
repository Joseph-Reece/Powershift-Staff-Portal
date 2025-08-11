<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        {{-- <div class="flex justify-center"> --}}
            <x-panel>
                <x-slot name="title">Imprest Requisition Details</x-slot>
                <x-slot name="body">
                    <x-grid>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Requisition No</x-slot>
                                <x-slot name="value">{{$requisition->No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Employee No</x-slot>
                                <x-slot name="value">{{$requisition->Employee_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Date Required</x-slot>
                                <x-slot name="value">{{$requisition->Date}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Imprest Purpose</x-slot>
                                <x-slot name="value">{{$requisition->Purpose}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Department</x-slot>
                                <x-slot name="value">{{$requisition->Global_Dimension_1_Code}}</x-slot>
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
                                <x-slot name="label">Total Net Amount</x-slot>
                                <x-slot name="value">{{$requisition->Total_Net_Amount}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @if ($requisition->Status == 'Open' || $requisition->Status == 'Pending' || $requisition->Status == 'Pending')
                                        <x-badge class="bg-blue-600">Open</x-badge>
                                    @elseif($requisition->Status == 'Pending Approval')
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
                    @include('staff.requisition.imprest.index-line')
                    @if($requisition->Status == 'Pending Approval' || $requisition->Status == 'Approved')
                        @include('staff.approval.approvers')
                    @endif
                    <x-slot name="footer">
                        @include('staff.requisition.imprest.actions-header')
                    </x-slot>
                </x-slot>
            </x-panel>
        {{-- </div> --}}
    </div>
</x-app-layout>
