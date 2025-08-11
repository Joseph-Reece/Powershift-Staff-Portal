<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Appraisal Score</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ $action == 'create'? route('storeAppraisalScore'): route('storeAppraisalScore')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to save this record?');">
                    @csrf
                    <input type="hidden" name="docNo" value="{{$line->Appraisal_No}}"/>
                    <input type="hidden" name="area" value="{{$line->Key_Result_Area}}"/>
                    <input type="hidden" name="measure" value="{{$line->Performance_Measure}}"/>
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Key Result Area</x-slot>
                                <x-slot name="value">
                                    {{$line->Key_Result_Area}}
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Performance Measure</x-slot>
                                <x-slot name="value">
                                    {{$line->Performance_Measure}}
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Weight</x-slot>
                                <x-slot name="value">
                                    {{$line->Weight}}
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Target</x-slot>
                                <x-slot name="value">
                                    {{$line->Target}}
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Employee Rating</x-slot>
                                <x-slot name="value">
                                    @if($record->Employee_No == session('authUser')['employeeNo'] && $record->Status == "New")
                                    <x-input type="number" name="employeeRating" id="employeeRating" value="{{$action == 'edit' && old('employeeRating') == null? $line->Employee_Rating:old('employeeRating')}}" />
                                    @else
                                        {{$line->Employee_Rating}}
                                    @endif
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Employee Comments</x-slot>
                                <x-slot name="value">
                                    @if($record->Employee_No == session('authUser')['employeeNo'] && $record->Status == "New")
                                        <x-textarea name="employeeComments" id="employeeComments">{{$action == 'edit' && old('employeeComments') == null? $line->Employee_Comments:old('employeeComments')}}</x-textarea>
                                    @else
                                        {{$line->Employee_Comments}}
                                    @endif
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Supervisor Rating</x-slot>
                                <x-slot name="value">
                                    @if($record->Supervisor == session('authUser')['employeeNo'] && $record->Status == "Pending Approval")
                                        <x-input type="number" name="supervisorRating" id="supervisorRating" value="{{$action == 'edit' && old('supervisorRating') == null? $line->Supervisor_Rating:old('supervisorRating')}}" />
                                    @else
                                        {{$line->Supervisor_Rating}}
                                    @endif
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Supervisor Comments</x-slot>
                                <x-slot name="value">
                                    @if($record->Supervisor == session('authUser')['employeeNo'] && $record->Status == "Pending Approval")
                                        <x-textarea name="supervisorComments" id="supervisorComments">{{$action == 'edit' && old('supervisorComments') == null? $line->Supervisor_Comments:old('supervisorComments')}}</x-textarea>
                                    @else
                                        {{$line->Supervisor_Comments}}
                                    @endif
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        @if(($record->Employee_No == session('authUser')['employeeNo'] && $record->Status == "New") || ($record->Supervisor == session('authUser')['employeeNo'] && $record->Status == "Pending Approval"))
                            <x-jet-button class="rounded-full bg-blue-800" data-turbo="false">Submit</x-jet-button>
                        @endif
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
</x-app-layout>
