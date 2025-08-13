<div class="p-2 flex justify-center w-full">
    <div class="flex justify-center gap-4 w-full">
        @if($requisition->Status == 'Open')
            <x-abutton href="/staff/leave/edit/{{$requisition->No}}" class="bg-blue-600 pr-4"><x-heroicon-o-pencil/> Edit</x-abutton>
            <form method="POST" action="{{route('reqLeaveApproval')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to send this leave application for approval?');">
                    @csrf
                <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                <x-jet-button type="submit" class="bg-green-600 pr-4 rounded-full"><x-heroicon-o-check/> Request Approval</x-button>
            </form>
        @endif
        @if($requisition->Status == 'Pending Approval')
            <form method="POST" action="{{route('cancelLeave')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to cancel this leave application?');">
                    @csrf
                <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                <x-jet-button type="submit" class="bg-red-600 pr-4 rounded-full"><x-heroicon-o-x/> Cancel</x-button>
            </form>
        @endif
    </div>
</div>
