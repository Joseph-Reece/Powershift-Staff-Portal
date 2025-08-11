
<div class="p-2 flex justify-center mt-4 sm:mt-10">
<hr>
<div class="flex gap-4">
    {{-- @if(isset($action) && $action != 'create') --}}
        @if($record->Status == 'New' && $record->Employee_No == session('authUser')['employeeNo'])
            <form method="POST" action="{{route('submitAppraisal')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to send this appraisal to supervisor?');">
                 @csrf
                <input id="docNo" type="hidden" name="docNo" value="{{$record->Appraisal_No}}"/>
                <x-jet-button type="submit" class="bg-green-600 pr-4 rounded-full w-60"><x-heroicon-o-check/> Send to Supervisor</x-button>
            </form>
        @endif
        @if($record->Status == 'Pending Approval' && $record->Supervisor == session('authUser')['employeeNo'])
            <form method="POST" action="{{route('submitAppraisal')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to submit this appraisal?');">
                 @csrf
                <input id="docNo" type="hidden" name="docNo" value="{{$record->Appraisal_No}}"/>
                <x-jet-button type="submit" class="bg-green-600 pr-4 rounded-full"><x-heroicon-o-x/> Submit Appraisal</x-button>
            </form>
        @endif
    {{-- @endif --}}
</div>
</div>
