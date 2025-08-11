<div>
    <h4 class="font-semibold pt-4">Attachments/Supporting Documents</h4>
    <div class="p-2">

        <x-abutton href="/staff/leave/attachment/create/{{$requisition->ApplicationCode}}" class="bg-blue-900"><x-heroicon-o-plus/> New Attachment</x-abutton>
    </div>
    <x-table.table>
        <x-slot name="thead">
            <x-table.th>Description</x-table.th>
            <x-table.th>Action</x-table.th>
        </x-slot>
        <x-slot name="tbody">
            @if($attachments != null && count($attachments) > 0)
                @foreach($attachments as $attachment)
                    <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/leave/show/{{$attachment->ApplicationCode}}'">
                        <x-table.td>{{$attachment->DocumentDescription}}</x-table.td>
                        <x-table.td>
                        <div class="flex gap-2">
                            <form method="POST" id="viewAttachment" action="{{route('showLeaveAttachment')}}" class="text-blackx" data-turbo-frame="_top">
                                @csrf
                                <input type="hidden" name="leaveNo" value="{{$requisition['ApplicationCode']}}">
                                <input type="hidden" name="attachmentID" value="{{$attachment['ID']}}">
                                <input type="hidden" name="tableID" value="{{$attachment['TableID']}}">
                                <input type="hidden" name="fileName" value="{{$attachment['FileName'].'.'.$attachment['FileExtension']}}">
                                <div class="flex items-center justify-left mt-4" data-turbo="false">
                                        <x-jet-button class="rounded-sm bg-blue-600 !p-1" form="viewAttachment">
                                            View/Download
                                        </x-jet-button>
                                </div>
                            </form>
                            <form method="POST" id="deleteAttachment" action="{{ route('deleteLeaveAttachment') }}" class="text-blackx" data-turbo-frame="_top">
                                @csrf
                                <input type="hidden" name="docId" value="{{$attachment['ID']}}">
                                <input type="hidden" name="leaveNo" value="{{$requisition['ApplicationCode']}}">
                                <div class="flex items-center justify-left mt-4" data-turbo="false">
                                        <x-jet-button class="rounded-sm bg-red-600" form="deleteAttachment">
                                            Delete
                                        </x-jet-button>
                                </div>
                            </form>
                        </div>
                        </x-table.td>
                    </x-table.tr>
                @endforeach
            @else
                <tr class="w-full">
                    <td colspan="9" class="text-black text-center pt-4"><em>*** No attachments ***</em></td>
                </tr>
            @endif
        </x-slot>
    </x-table.table>
</div>
