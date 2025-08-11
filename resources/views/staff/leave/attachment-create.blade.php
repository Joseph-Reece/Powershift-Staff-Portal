<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">New Leave Attachment</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('storeLeaveAttachment')}}" class="text-black w-full" data-turbo-frame="_top" enctype="multipart/form-data"  onsubmit="return confirm('Are you sure you want to submit this attachment?');">
                 @csrf
                 <input type="hidden" name="leaveNo" value="{{$leaveNo}}"/>
                    <x-grid>
                        <x-grid-col id="descriptionDiv">
                            <x-form-group>
                                <x-slot name="label">Attachment Description/Name</x-slot>
                                <x-slot name="value">
                                    <x-input type="text" name="description" value="{{old('description')}}"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Attachment</x-slot>
                                <x-slot name="value">
                                    <x-input type="file" name="file" id="file" value="{{old('file')}}" accept=".doc,.docx,.pdf,image/*"/>
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
