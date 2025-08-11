
<div class="p-2 flex justify-center mt-4 sm:mt-10">
<hr>
<div class="flex gap-4">
    {{-- @if(isset($action) && $action != 'create') --}}
        @if($requisition->Status == 'Open')
            <x-abutton href="/staff/requisition/purchase/edit/header/{{$requisition->No}}" class="bg-blue-600 pr-4" ><x-heroicon-o-pencil/> Edit</x-abutton>
            <form method="POST" action="{{route('requestPurchaseReqApproval')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to send this requisition for approval?');">
                 @csrf
                <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                <x-jet-button type="submit" class="bg-green-600 pr-4 rounded-full w-60"><x-heroicon-o-check/> Request Approval</x-button>
            </form>
        @endif
        @if($requisition->Status == 'Pending Approval')
            <form method="POST" action="{{route('cancelPurchaseReq')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to cancel this Requisition?');">
                 @csrf
                <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                <x-jet-button type="submit" class="bg-red-600 pr-4 rounded-full"><x-heroicon-o-x/> Cancel</x-button>
            </form>
        @endif
    {{-- @endif --}}
</div>
</div>
