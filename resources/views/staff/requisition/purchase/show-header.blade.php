<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        {{-- <div class="flex justify-center"> --}}
            <x-panel>
                <x-slot name="title">Purchase Requisition Details</x-slot>
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
                                <x-slot name="label">Requested Receipt Date</x-slot>
                                <x-slot name="value">{{$requisition->Requested_Receipt_Date}}</x-slot>
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
                                <x-slot name="label">Department</x-slot>
                                <x-slot name="value">{{$requisition->Shortcut_Dimension_1_Code}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Posting Description</x-slot>
                                <x-slot name="value">{{$requisition->Posting_Description}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Order Date</x-slot>
                                <x-slot name="value">{{$requisition->Order_Date}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Document Date</x-slot>
                                <x-slot name="value">{{$requisition->Document_Date}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Prices Including VAT?</x-slot>
                                <x-slot name="value">{{$requisition->Prices_Including_VAT == 1? 'YES':'NO'}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @if ($requisition->Status == 'Pending' || $requisition->Status == 'Pending Approval')
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
                    @include('staff.requisition.purchase.index-line')
                    @if($requisition->Status == 'Pending Approval' || $requisition->Status == 'Approved')
                        @include('staff.approval.approvers')
                    @endif
                    <x-slot name="footer">
                        @include('staff.requisition.purchase.actions-header')
                    </x-slot>
                </x-slot>
            </x-panel>
        {{-- </div> --}}
    </div>
</x-app-layout>
