{{-- <x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Imprest Line</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('storeImprestLine')}}" class="text-black w-full" data-turbo-frame="_top" onsubmit="return confirm('Are you sure you want to add this line?');">
                    @csrf
                    <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                   <input id="department" type="hidden" name="department" value="{{$requisition->Shortcut_Dimension_2_Code}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Imprest Type</x-slot>
                                <x-slot name="value">
                                    <x-select name="advanceType">
                                        <option value="">--select--</option>
                                        @if($imprestTypes != null)
                                            @foreach($imprestTypes as $imprestType)
                                                <option value="{{$imprestType->Code}}" {{$action == 'edit'? $Line->Imprest_Type == $imprestType->Code? 'selected':'' :old('advanceType') }}>{{$imprestType['Description']}}</option>
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
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button class="rounded-full bg-blue-800" data-turbo="false"><x-heroicon-o-check/> Submit</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
</x-app-layout> --}}
