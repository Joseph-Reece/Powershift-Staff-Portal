<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-slot name="maxWidth">sm</x-slot>
    <div>
        <div class="flex justify-center">
            <x-panel>
                <x-slot name="title">{{$documentName}}</x-slot>
                <x-slot name="body">
                    <x-grid class="mt-4">
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Document Number</x-slot>
                                <x-slot name="value">{{$document->Document_No}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Document Type</x-slot>
                                <x-slot name="value">{{$document->Document_Type}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Sender ID</x-slot>
                                <x-slot name="value">{{$document->Sender_ID}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Sender Name</x-slot>
                                <x-slot name="value">{{$document->Sender_ID}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Date Sent for Approval</x-slot>
                                <x-slot name="value">{{$document->Date_Time_Sent_for_Approval}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Due Date</x-slot>
                                <x-slot name="value">{{$document->Due_Date}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Last Modified Date</x-slot>
                                <x-slot name="value">{{$document->Last_Date_Time_Modified}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Last User To Modify</x-slot>
                                <x-slot name="value">{{$document->Last_Modified_By_User_ID}}</x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Status</x-slot>
                                <x-slot name="value">
                                    @if($document->Status == 'Open')
                                        <x-badge class="bg-blue-600">Pending Approval</x-badge>
                                    @elseif($document->Status == 'Approved')
                                        <x-badge class="bg-green-600">Approved</x-badge>
                                    @elseif($document->Status == 'Rejected')
                                        <x-badge class="bg-red-600">Rejected</x-badge>
                                    @endif
                                </x-slot>
                            </x-show-group>
                        </x-grid-col>
                        @if($document->Status == 'Approved' || $document->Status == 'Rejected')
                            @if($document['approvalComment'] != null)
                                <x-grid-col>
                                    <x-show-group>
                                        <x-slot name="label">Approver Comments</x-slot>
                                        <x-slot name="value">{{$document['approvalComment']['Comment']}}</x-slot>
                                    </x-show-group>
                                </x-grid-col>
                            @endif
                        @endif
                    </x-grid>
                    @if($data != null)
                        @if($documentName == "Leave Request Approval")
                            @include('staff.approval.requisition.leave')
                        @elseif($documentName == "TransportRequest")
                            @include('staff.approval.requisition.transport')
                        @elseif($documentName == "Imprest Request Approval")
                            @include('staff.approval.requisition.imprest')
                        @elseif($documentName == "Imprest Surrender Approval")
                            @include('staff.approval.requisition.imprest-surrender')
                        @elseif($documentName == "Purchase Requisition Approval")
                            @include('staff.approval.requisition.purchase')
                        @elseif($documentName == "Store Requisition Approval")
                            @include('staff.approval.requisition.store')
                        @elseif($documentName == "Staff Claim Approval")
                            @include('staff.approval.requisition.claim')
                        @elseif($documentName == "Payment Voucher")
                            @include('staff.approval.payment-voucher')
                        @elseif($documentName == "Petty Cash")
                            @include('staff.approval.petty-cash')
                        @elseif($documentName == "Order")
                            @include('staff.approval.purchase-order')
                        @endif
                    @endif
                    @if($document->Status == 'Open')
                        <!--actions-->
                        <div class="flex justify-center gap-2 pt-4" id="divActions">
                            <x-jet-button class="rounded-full bg-green-700 hover:bg-green-900" onclick="action(1)"><x-heroicon-o-check/> Approve Document</x-jet-button>
                            <x-jet-button class="rounded-full bg-red-700 hover:bg-red-900" onclick="action(0)"><x-heroicon-o-x/> Reject Document</x-jet-button>
                        </div>
                        <!--approval-->
                        <div class="hidden flex justify-center sm:min-w-md mt-4" id="approvalForm">
                            <div class="max-w-lg">
                                <form method="POST" id="frmG_Captcha" action="{{ route('documentApproval') }}" class="text-black w-full" data-turbo-frame="_top">
                                    @csrf
                                    <div>
                                        <x-jet-label for="comments" class="font-semibold sm:text-lg" id="lblComments" value="" />
                                        <textarea id="comments" class="block mt-1 w-full rounded-md" type="text" name="comments" required>{{old('comments')}}</textarea>
                                    </div>
                                    <input id="inpApproval" type="hidden" name="isApprove"/>
                                    <input id="docNo" type="hidden" name="docNo" value="{{$document->Document_No}}"/>
                                    <input id="entryNo" type="hidden" name="entryNo" value="{{$document->Entry_No}}"/>
                                    <div class="flex justify-center m-2">
                                        <x-jet-button type="submit" class="rounded-full bg-blue-700" data-turbo="false">Submit</x-jet-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </x-slot>
            </x-panel>
        </div>
    </div>
@push('scripts')
    <script>
        function action(isApprove){
            var approvalForm = document.getElementById('approvalForm');
            var inpApproval = document.getElementById('inpApproval');
            var lblComments = document.getElementById('lblComments');
            var divActions = document.getElementById('divActions');

            approvalForm.classList.add('hidden');
            var msg = "Comments - ";
            if(isApprove == 1){
                msg = msg+' Why are you Approving this document?';
            }else{
                msg = msg+' Why are you Rejecting this document?';
            }
            inpApproval.value = isApprove;
            lblComments.innerHTML  = msg;
            approvalForm.classList.remove('hidden');
            //divActions.style.display = 'none';
        }
    </script>
@endpush
</x-app-layout>
