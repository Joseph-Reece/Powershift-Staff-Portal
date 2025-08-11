<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">lg</x-slot>
    <div>
        {{-- <div class="flex justify-center"> --}}
            <x-panel>
                <x-slot name="title">Imprest Surrender Details</x-slot>
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
                                <x-slot name="label">Account No</x-slot>
                                <x-slot name="value">{{$requisition->Account_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Payee</x-slot>
                                <x-slot name="value">{{$requisition->Payee}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Received From</x-slot>
                                <x-slot name="value">{{$requisition->Received_From}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Department</x-slot>
                                <x-slot name="value">{{$requisition->Global_Dimension_2_Code}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Imprest Issue Date</x-slot>
                                <x-slot name="value">{{$requisition->Imprest_Issue_Date}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Region</x-slot>
                                <x-slot name="value">{{$requisition->Global_Dimension_1_Code}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Amount Surrendered</x-slot>
                                <x-slot name="value">{{$requisition->Amount_Surrendered_LCY}}</x-slot>
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
                    @include('staff.requisition.imprest-surrender.index-line')
                    @if($requisition->Status == 'Pending Approval' || $requisition->Status == 'Approved')
                        @include('staff.approval.approvers')
                    @endif
                    <x-slot name="footer">
                        @include('staff.requisition.imprest-surrender.actions-header')
                    </x-slot>
                </x-slot>
            </x-panel>
        {{-- </div> --}}
    </div>
</x-app-layout>
