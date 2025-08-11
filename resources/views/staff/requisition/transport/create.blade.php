<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Transport Requisition</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ route('storeTransportReqHeader')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to submit this requisition?');">
                    @csrf
                    @if($action == 'edit')
                        <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->Transport_Requisition_No}}"/>
                    @endif
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Destination</x-slot>
                                <x-slot name="value">
                                    <x-input name="destination" id="destination" value="{{$action == 'edit' && old('destination') == null? $requisition->Destination:old('destination')}}"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Date of Trip</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="dateOfTrip" id="dateOfTrip" value="{{$action == 'edit' && old('dateOfTrip') == null? $requisition->Date_of_Trip:old('dateOfTrip')}}"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">No. of Days</x-slot>
                                <x-slot name="value">
                                    <x-input type="number" name="noOfDays" id="noOfDays" value="{{$action == 'edit' && old('noOfDays') == null? $requisition->No_of_Days_Requested:old('noOfDays')}}"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">No. of Passengers</x-slot>
                                <x-slot name="value">
                                    <x-input type="number" name="noOfPassengers" id="noOfPassengers" value="{{$action == 'edit' && old('noOfPassengers') == null? $requisition->No_Of_Passangers:old('noOfPassengers')}}"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Commencement</x-slot>
                                <x-slot name="value">
                                    <x-input name="commencement" id="commencement" value="{{$action == 'edit' && old('commencement') == null? $requisition->Commencement:old('commencement')}}"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Purpose of the Trip</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="purpose" id="purpose">{{$action == 'edit' && old('purpose') == null? $requisition->Purpose_of_Trip:old('purpose')}}</x-textarea>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Responsibility Center</x-slot>
                                <x-slot name="value">
                                    <x-select name="responsibilityCenter">
                                        <option value="">--select--</option>
                                        @if($respCenters != null)
                                            @foreach($respCenters as $respCenter)
                                                <option value="{{$respCenter->Code}}" {{$action == 'edit'? $requisition->Responsibility_Center == $respCenter->Code? 'selected':'' :old('responsibiltyCenter') }}>{{$respCenter['Name']}}</option>
                                            @endforeach
                                        @endif
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
