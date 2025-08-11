<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Store Requisition Line</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('storeStoreReqLine')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to submit this requisition line?');">
                    @csrf
                    @if($action == 'edit')
                        <input id="lineNo" type="hidden" name="lineNo" value="{{$line->Line_No}}"/>
                    @endif
                    <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                   <input id="department" type="hidden" name="department" value="{{$requisition->Shortcut_Dimension_2_Code}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Type</x-slot>
                                <x-slot name="value">
                                    <x-select name="type" onchange="getItems()" id="type">
                                        <option value="">--select--</option>
                                        <option value="1" {{$action == 'edit'? $Line->Type == 1? 'selected':'' :old('type') }}>Item</option>
                                        <option value="2" {{$action == 'edit'? $Line->Type == 2? 'selected':'' :old('type') }}>Asset</option>
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Issuing Store</x-slot>
                                <x-slot name="value">
                                    <x-select name="issuingStore" id="issuingStore"  onchange="getBalance()">
                                        <option value="">--select--</option>
                                        @if($locations != null)
                                            @foreach($locations as $location)
                                                <option value="{{$location->Code}}" {{$action == 'edit'? $Line->Issuing_Store == $location->Code? 'selected':'' :old('item') }}>{{$location['Name']}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Store Item</x-slot>
                                <x-slot name="value">
                                    <x-loading />
                                    <div id="divItem" class="hidden w-full">
                                        <x-select id="item" name="item" onchange="getBalance()">
                                            <option value="">--select--</option>
                                        </x-select>
                                    </div>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col id="grdBalance"  class="hidden">
                            <x-form-group>
                                <x-slot name="label">Available Quantity</x-slot>
                                <x-slot name="value">
                                    <x-loading class="loader2"/>
                                    <x-badge id="quantityBalance" class="hidden bg-green-600 font-semibold"></x-badge>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col id="grdQuantity" class="hidden">
                            <x-form-group>
                                <x-slot name="label">Quantity</x-slot>
                                <x-slot name="value">
                                    <x-input type="number" name="quantity" id="quantity">{{$action == 'edit' && old('quantity') == null? $Line->Quantity:old('quantity')}}</x-input>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        
                        {{-- <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Description/Purpose</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="purpose" id="purpose">{{$action == 'edit' && old('purpose') == null? $Line->Description:old('purpose')}}</x-textarea>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col> --}}
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button class="rounded-full bg-blue-800" data-turbo="false"><x-heroicon-o-check/> Submit</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
@push('scripts')
    <script>
        function fnOnLoad(){
            getItems();
        }
        function getItems(){
            var loader = document.getElementById('loader');
            var elType = document.getElementById('type');
            var elItem = document.getElementById('item');
            var elStore = document.getElementById('issuingStore');
            var elDivItem = document.getElementById('divItem');
            var elGrdBalance = document.getElementById('grdBalance');
            var elGrdQuantity = document.getElementById('grdQuantity');
            elItem.classList.add('hidden');
            elDivItem.classList.add('hidden');
            clearSelect('item');
            if(elType.value != ''){
                loader.classList.remove('hidden');
                if(elType.value == 1){
                    axios.get('/staff/get-items').then(response =>{
                        var items = [];
                        items = response.data;
                        if(items != null){
                            for(var i=0; i<items.length; i++){
                                var elOption = document.createElement("option");
                                elOption.textContent = items[i]['Description'];
                                elOption.value = items[i]['No'];
                                elItem.appendChild(elOption);
                            }
                        }
                        loader.classList.add('hidden');
                        elItem.classList.remove('hidden');
                        elDivItem.classList.remove('hidden');
                        elGrdBalance.classList.remove('hidden');
                        elGrdQuantity.classList.remove('hidden');

                    }).catch((error) => {
                        loader.classList.add('hidden');
                    });
                }
                else if(elType.value == 2){
                    axios.get('/staff/get-assets').then(response =>{
                        var items = [];
                        items = response.data;
                        if(items != null){
                            for(var i=0; i<items.length; i++){
                                var elOption = document.createElement("option");
                                elOption.textContent = items[i]['Description'];
                                elOption.value = items[i]['No'];
                                elItem.appendChild(elOption);
                            }
                        }
                        loader.classList.add('hidden');
                        elItem.classList.remove('hidden');
                        elDivItem.classList.remove('hidden');
                        elGrdBalance.classList.add('hidden');
                        elGrdQuantity.classList.add('hidden');

                    }).catch((error) => {
                        loader.classList.add('hidden');
                    });
                }
            }
        }
        function getBalance(){
           var loader = document.getElementsByClassName("loader2");
            var elItem = document.getElementById('item');
            var elBalance = document.getElementById('quantityBalance');
            var elStore = document.getElementById('issuingStore');
            elBalance.classList.add('hidden');
            if(elItem.value != '' && elStore.value != ''){
                for (let element of loader){element.classList.remove('hidden');}
                axios.get('/staff/get-item-balance/'+elItem.value+'/'+elStore.value).then(response =>{
                    if(response.data <1 ){
                        alert('Oops! the item is out of stock');
                        elItem.value = '';
                        elBalance.classList.add('hidden');
                    }else{
                        elBalance.innerHTML = response.data;
                    }
                    for (let element of loader){element.classList.add('hidden');}
                    elBalance.classList.remove('hidden');
                }).catch((error) => {
                    for (let element of loader){element.classList.remove('hidden');};
                });
            }
        }
    </script>
@endpush
@include('inc.general.common-scripts')
</x-app-layout>
