<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel maxWidth="max-w-lg">
            <x-slot name="title">Leave Statement</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('generateLeaveStatement')}}" class="text-black w-full" data-turbo-frame="_top" target="_blank">
                 @csrf
                    <x-grid cols="">
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Leave Type</x-slot>
                                <x-slot name="value">
                                     <x-select id="leaveType" name="leaveType">
                                        <option value="">--select--</option>
                                        @if($leaveTypes != null && count($leaveTypes) > 0)
                                            @foreach($leaveTypes as $type)
                                                <option value="{{$type->Code}}" {{old('leaveType') == $type->Code? 'selected':''}}>{{$type->Code}}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
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
</x-app-layout>
