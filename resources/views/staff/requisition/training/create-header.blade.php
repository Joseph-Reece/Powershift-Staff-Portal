<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Training Request</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ $action == 'create'? route('storeTrainingHeader'): route('updateTrainingHeader')}}" class="text-black w-full" data-turbo-frame="_top"  onsubmit="return confirm('Are you sure you want to submit this training?');">
                    @csrf
                    @if($action == 'edit')
                        @method('PUT')
                        <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->Application_No}}"/>
                    @endif
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Training Need</x-slot>
                                <x-slot name="value">
                                    <x-select name="trainingNeed">
                                        <option value="">--select--</option>
                                        @if($needs != null)
                                            @foreach($needs as $need)
                                                <option value="{{$need->Code}}" {{$action == 'edit'? $requisition->Training_Need_Code == $need->Code? 'selected':'' :old('trainingNeed') }}>{{$need['Description']}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Comments</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="comments" id="purpose">{{$action == 'edit' && old('comments') == null? $requisition->Comments:old('comments')}}</x-textarea>
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
