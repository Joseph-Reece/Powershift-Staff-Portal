<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">Imprest Surrender</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ route('storeImprestSurrenderHeader')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to submit this imprest surrender?');">
                    @csrf
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Imprest to Surrender</x-slot>
                                <x-slot name="value">
                                    <x-select name="imprest" id="imprest" onchange="onChangeImprest()">
                                        <option value="">--select--</option>
                                        @if($requisitions != null)
                                            @foreach($requisitions as $requisition)
                                                <option value="{{$requisition->No}}">{{$requisition['No']}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Imprest Purpose</x-slot>
                                <x-slot name="value"><x-loading class="loaders"/><span id="purpose" class="values"></span></x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Imprest Amount</x-slot>
                                <x-slot name="value"><x-loading class="loaders"/><span id="amount" class="values"></span></x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Department</x-slot>
                                <x-slot name="value"><span id="department" class="values"></span></x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Responsibility Center</x-slot>
                                <x-slot name="value"><span id="responsibility" class="values"></span></x-slot>
                            </x-show-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-show-group>
                                <x-slot name="label">Date Required</x-slot>
                                <x-slot name="value"><span id="dateRequired" class="values"></span></x-slot>
                            </x-show-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button class="rounded-full bg-blue-800" data-turbo="false">Submit</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
    @push('scripts')
        <script>
            window.onload = function(){
                onChangeImprest();
            }
            function onChangeImprest(){
                var loader = document.getElementsByClassName("loaders");
                var values = document.getElementsByClassName("values");
                var elImprest = document.getElementById('imprest');
                var elPurpose= document.getElementById('purpose');
                var elDepartment= document.getElementById('department');
                var elResponsibility= document.getElementById('responsibility');
                var elDateRequired= document.getElementById('dateRequired');
                var elAmount= document.getElementById('amount');
                if(elImprest.value != ''){
                    for (let element of loader){element.classList.remove('hidden');}
                    axios.get('/staff/requisition/imprest-header-details/'+elImprest.value).then(response =>{
                        var items = [];
                        var header = response.data;
                        elPurpose.innerHTML = header['Purpose'];
                        elDepartment.innerHTML = header['Global_Dimension_2_Code'];
                        elResponsibility.innerHTML = header['Responsibility_Center'];
                        elDateRequired.innerHTML = header['Payment_Release_Date'];
                        elAmount.innerHTML = header['Total_Net_Amount'];
                        for (let element of loader){element.classList.add('hidden');}
                    }).catch((error) => {
                        loader.classList.add('hidden');
                    });
                }
            }

        </script>
    @endpush
</x-app-layout>
