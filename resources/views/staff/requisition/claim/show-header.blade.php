<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        {{-- <div class="flex justify-center"> --}}
            <x-panel>
                <x-slot name="title">Claim Requisition Details</x-slot>
                <x-slot name="body">
                    <x-grid>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Claim No</x-slot>
                                <x-slot name="value">{{$requisition->No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Claim Date</x-slot>
                                <x-slot name="value">{{$requisition->Date}}</x-slot>
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
                                <x-slot name="label">Payee</x-slot>
                                <x-slot name="value">{{$requisition->Payee}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Account No.</x-slot>
                                <x-slot name="value">{{$requisition->Account_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Purpose</x-slot>
                                <x-slot name="value">{{$requisition->Purpose}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Total Claim Amount</x-slot>
                                <x-slot name="value">{{$requisition->Total_Net_Amount}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Currency</x-slot>
                                <x-slot name="value">{{$requisition->Currency_Code == ""? 'KES':$requisition->Currency_Code}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Department</x-slot>
                                <x-slot name="value">{{$requisition->Shortcut_Dimension_2_Code}}</x-slot>
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
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @include('staff.requisition.claim.status')
                                </x-slot>
                            </x-show-group>
                        </x-grid-col>
                    </x-grid>
                    @include('staff.requisition.claim.index-line')
                    @if($requisition->Status == 'Pending Approval' || $requisition->Status == 'Approved')
                        @include('staff.approval.approvers')
                    @endif
                    <x-slot name="footer">
                        @include('staff.requisition.claim.actions-header')
                    </x-slot>
                </x-slot>
            </x-panel>
        {{-- </div> --}}
    </div>
</x-app-layout>
