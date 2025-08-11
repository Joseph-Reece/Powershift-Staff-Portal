<div class="flex gap-4 mt-4 mb-2">
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0"><em>Purchase Lines</em></h3>
    @if($requisition->Status == 'Open')
        <x-abutton href="/staff/requisition/purchase/create/line/{{$requisition->No}}" class="pr-4"><x-heroicon-o-plus/> New Line</x-abutton>
    @endif
</div>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Type</x-table.th>
        <x-table.th>No.</x-table.th>
        <x-table.th>Description</x-table.th>
        <x-table.th>Procurement Plan</x-table.th>
        <x-table.th>Purpose</x-table.th>
        <x-table.th>Quantity</x-table.th>
        <x-table.th>Unit</x-table.th>
        <x-table.th>Location</x-table.th>
        <x-table.th>Amount Incl. VAT</x-table.th>
        @if($requisition->Status == 'Open')
            <x-table.th>Action</x-table.th>
        @endif
    </x-slot>
    <x-slot name="tbody">
        @if($lines != null && count($lines) > 0)
            @foreach($lines as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Type}}</x-table.td>
                    <x-table.td>{{$line->No}}</x-table.td>
                    <x-table.td>{{$line['Description']}}</x-table.td>
                    <x-table.td>{{$line->procurement_Plan}}</x-table.td>
                    <x-table.td>{{$line->Reason_for_Request}}</x-table.td>
                    <x-table.td>{{$line->Quantity}}</x-table.td>
                    <x-table.td>{{$line->Unit_of_Measure}}</x-table.td>
                    <x-table.td>{{$line->Location_Code}}</x-table.td>
                    <x-table.td>{{$line->Amount_Including_VAT}}</x-table.td>
                    @if($requisition->Status == 'Open')
                        <x-table.td>
                                <form method="POST" action="{{route('deletePurchaseReqLine')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to delete this purchase requisition Line?');">
                                    @csrf
                                    @method('delete')
                                    <input id="requisitionNo" type="hidden" name="requisitionNo" value="{{$requisition->No}}"/>
                                    <input id="lineNo" type="hidden" name="lineNo" value="{{$line->Line_No}}"/>
                                    <x-jet-button type="submit" class="bg-red-600 pr-4 rounded-full p-0"><x-heroicon-o-x/> Delete</x-button>
                                </form>
                        </x-table.td>
                    @endif
                </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No purchase Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
