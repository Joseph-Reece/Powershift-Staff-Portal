<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel maxWidth="max-w-lg">
            <x-slot name="title">Payslip</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('generatePayslip')}}" class="text-black w-full" data-turbo-frame="_top" target="_blank">
                 @csrf
                    <x-grid cols="sm:grid-cols-1">
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Payroll Period Year</x-slot>
                                <x-slot name="value">
                                    <x-select id="year" name="year" onchange="getYearMonths()">
                                        <option value="">--select--</option>
                                        @if($periods != null && count($periods) > 0)
                                            @foreach($periods as $period)
                                                <option value="{{$period->Period_Year}}" {{old('year') == $period->Period_Year? 'selected':''}}>{{$period->Period_Year}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Period Month</x-slot>
                                <x-slot name="value">
                                    <x-loading />
                                    <div id="divMonths" class="hidden w-full">
                                        <x-select id="month" name="month">
                                            <option value="">--select--</option>
                                        </x-select>
                                    </div>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button type="submit" class="rounded-full bg-blue-800" data-turbo="false">Generate</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
@push('scripts')
    <script>
        function getYearMonths(){
            var loader = document.getElementById('loader');
            var elYear = document.getElementById('year');
            var elMonth = document.getElementById('month');
            var eldivMonths = document.getElementById('divMonths');
            elMonth.classList.add('hidden');
            eldivMonths.classList.add('hidden');
            clearSelect('month');
            if(elYear.value != ''){
                loader.classList.remove('hidden');
                axios.get('/staff/payroll-period/year-month/'+elYear.value).then(response =>{
                    var months = response.data['months'];
                    // console.log(months['months']);
                    if(months != null){
                        for(var i=0; i<months.length; i++){
                            var elOption = document.createElement("option");
                            elOption.textContent = months[i]['MonthName'];
                            elOption.value = months[i]['PeriodMonth'];
                            elMonth.appendChild(elOption);
                        }
                    }
                    loader.classList.add('hidden');
                    elMonth.classList.remove('hidden');
                    eldivMonths.classList.remove('hidden');
                }).catch((error) => {
                    loader.classList.add('hidden');
                });
            }
        }
    </script>
@endpush
@include('inc.general.common-scripts')
</x-app-layout>
