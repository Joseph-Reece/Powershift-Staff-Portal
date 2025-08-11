<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Purchase Requisition (Header)</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ route('storePurchaseReqHeader')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to submit this requisition?');">
                    @csrf
                    @if($action == 'edit')
                        <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                    @endif
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Needed By Date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="dateNeeded" id="dateNeeded" value="{{$action == 'edit' && old('dateNeeded') == null? $requisition->Needed_By_Date:old('dateNeeded')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Purchase Description & Reason</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="description" id="description">{{$action == 'edit' && old('description') == null? $requisition->Posting_Description:old('description')}}</x-textarea>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col class="sm:grid-cols-1">
                            <x-form-group>
                                <x-slot name="label">Prices Including VAT?</x-slot>
                                <x-slot name="value">
                                    <x-select name="includingVAT">
                                        <option value="0" {{$action == 'edit'? $requisition->Prices_Including_VAT == false? 'selected':'' :old('includingVAT')}}>No</option>
                                        <option value="1" {{$action == 'edit'? $requisition->Prices_Including_VAT == true? 'selected':'' :old('includingVAT') }}>Yes</option>
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button class="rounded-full bg-blue-800" data-turbo="false">Submit</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
</x-app-layout>
