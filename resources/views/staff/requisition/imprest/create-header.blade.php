<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Imprest Request</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ $action == 'create'? route('storeImprestHeader'): route('updateImprestHeader')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to submit this imprest?');">
                    @csrf
                    @if($action == 'edit')
                        @method('PUT')
                        <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                    @endif
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Date Required</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="dateRequired" id="dateRequired" value="{{$action == 'edit' && old('dateRequired') == null? $requisition->Date:old('dateRequired')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Imprest Purpose</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="purpose" id="purpose">{{$action == 'edit' && old('purpose') == null? $requisition->Purpose:old('purpose')}}</x-textarea>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col class="sm:grid-cols-1">
                            <x-form-group>
                                <x-slot name="label">Standing Imprest?</x-slot>
                                <x-slot name="value">
                                    <x-select name="isStandingImprest">
                                        <option value="0" {{$action == 'edit'? $requisition->Standing_Imprest == false? 'selected':'' :old('isStandingImprest')}}>No</option>
                                        <option value="1" {{$action == 'edit'? $requisition->Standing_Imprest == true? 'selected':'' :old('isStandingImprest') }}>Yes</option>
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Travel Destination/Purpose</x-slot>
                                <x-slot name="value">
                                    <x-select name="travelDestination">
                                        <option value="">--select--</option>
                                        @if($locations != null)
                                            @foreach($locations as $location)
                                                <option value="{{$location->Code}}" {{$action == 'edit'? $requisition->Travel_Destination == $location->Code? 'selected':'' :old('travelDestination') }}>{{$location['Name']}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Start date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="travelDate" id="travelDate" value="{{$action == 'edit' && old('travelDate') == null? $requisition->Date:old('travelDate')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Return date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="returnDate" id="returnDate" value="{{$action == 'edit' && old('returnDate') == null? $requisition->Date:old('returnDate')}}" />
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
