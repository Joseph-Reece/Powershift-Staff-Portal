<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        <div class="flex justify-center">
            <x-panel>
                {{-- @dd($requisition) --}}
                <x-slot name="title">Leave Application Details</x-slot>
                <x-slot name="body">
                    <x-grid>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Leave No</x-slot>
                                <x-slot name="value">{{$requisition->ApplicationCode}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Employee No</x-slot>
                                <x-slot name="value">{{$requisition->EmployeeNo}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Leave Type</x-slot>
                                <x-slot name="value">{{$requisition->LeaveType}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Applied Duration</x-slot>
                                <x-slot name="value">{{$requisition->DaysApplied." Days"}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Starting Date</x-slot>
                                <x-slot name="value">{{$requisition->StartDate}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">End Date</x-slot>
                                <x-slot name="value">{{$requisition->EndDate}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Return Date</x-slot>
                                <x-slot name="value">{{$requisition->ReturnDate}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Application Date</x-slot>
                                <x-slot name="value">{{$requisition->ApplicationDate}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Reliever No</x-slot>
                                <x-slot name="value">{{$requisition->Reliever}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Reliever Name</x-slot>
                                <x-slot name="value">{{$requisition->RelieverName}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Leave Purpose</x-slot>
                                <x-slot name="value">{{$requisition->Reasonforleave}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @if ($requisition->Status == 'Open' || $requisition->Status == 'Pending Approval')
                                        <x-badge class="bg-blue-600">{{$requisition->Status}}</x-badge>
                                    @elseif ($requisition->Status == 'Posted')
                                        <x-badge class="bg-green-600">Approved</x-badge>
                                    @else
                                        <x-badge class="bg-red-600">{{$requisition->Status}}</x-badge>
                                    @endif
                                </x-slot>
                            </x-show-group>
                        </x-grid-col>
                    </x-grid>
                    @if($requisition->Status == 'Pending Approval' || $requisition->Status == 'Posted')
                        @include('staff.approval.approvers')
                    @endif
                    @include('staff.leave.actions')
                </x-slot>
            </x-panel>
        </div>
    </div>
</x-app-layout>
