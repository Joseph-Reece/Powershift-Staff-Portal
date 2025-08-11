<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{$action == 'create'? 'New':'Edit'}} Claim Request</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ $action == 'create'? route('storeClaimHeader'): route('updateClaimHeader')}}" class="text-black w-full" data-turbo-frame="_top" onsubmit="return confirm('Are you sure you want to submit this claim?');">
                    @csrf
                    @if($action == 'edit')
                        @method('PUT')
                        <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                    @endif
                   <input id="action" type="hidden" name="action" value="{{$action}}"/>
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Claim Date</x-slot>
                                <x-slot name="value">
                                    <x-input type="date" name="claimDate" id="claimDate" value="{{$action == 'edit' && old('claimDate') == null? $requisition->Date:old('claimDate')}}" />
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Claim Purpose</x-slot>
                                <x-slot name="value">
                                    <x-textarea name="purpose" id="purpose">{{$action == 'edit' && old('purpose') == null? $Line->Purpose:old('purpose')}}</x-textarea>
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
