<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Claim Line</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('storeClaimLine')}}" class="text-black w-full" data-turbo-frame="_top" onsubmit="return confirm('Are you sure you want to add this line?');">
                    @csrf
                    <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                    @if($action == 'edit')
                        <input id="lineNo" type="hidden" name="lineNo" value="{{$line->Line_No}}"/>
                    @endif
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                   <input id="department" type="hidden" name="department" value="{{$requisition->Shortcut_Dimension_2_Code}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Claim Type</x-slot>
                                <x-slot name="value">
                                    <x-select name="claimType">
                                        <option value="">--select--</option>
                                        @if($ClaimTypes != null)
                                            @foreach($ClaimTypes as $ClaimType)
                                                <option value="{{$ClaimType->Code}}" {{$action == 'edit'? $Line->Claim_Type == $ClaimType->Code? 'selected':'' :old('claimType') }}>{{$ClaimType['Description']}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Account No.</x-slot>
                                <x-slot name="value">
                                    <x-select name="accountNo">
                                        <option value="">--select--</option>
                                        @if($GLs != null)
                                            @foreach($GLs as $GL)
                                                <option value="{{$GL->No}}" {{$action == 'edit'? $Line->Account_No == $GL->No? 'selected':'' :old('accountNo') }}>{{$GL['Name']}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Amount</x-slot>
                                <x-slot name="value">
                                    <x-input type="number" name="amount" id="amount">{{$action == 'edit' && old('amount') == null? $Line->Amount:old('amount')}}</x-input>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Claim Receipt No.</x-slot>
                                <x-slot name="value">
                                    <x-input type="text" name="receiptNo" :required="false" id="receiptNo">{{$action == 'edit' && old('receiptNo') == null? $Line->Claim_Receipt_No:old('receiptNo')}}</x-input>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Expenditure Date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="expenditureDate" id="expenditureDate" value="{{$action == 'edit' && old('expenditureDate') == null? $requisition->Expenditure_Date:old('expenditureDate')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Expenditure Description</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="expenditureDescription" id="expenditureDescription">{{$action == 'edit' && old('expenditureDescription') == null? $Line->Expenditure_Description:old('expenditureDescription')}}</x-textarea>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Attachment</x-slot>
                                <x-slot name="value">
                                    <x-input type="file" name="attachment" id="attachment" multiple/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button class="rounded-full bg-blue-800" data-turbo="false"><x-heroicon-o-check/> Submit</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
</x-app-layout>
