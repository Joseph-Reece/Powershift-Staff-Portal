<div class="flex gap-4 mt-4 mb-2">
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0"><em>Claim Lines</em></h3>
    @if($requisition->Status == 'Pending')
        <x-abutton href="/staff/requisition/claim/create/line/{{$requisition->No}}"><x-heroicon-o-plus/> New Line</x-abutton>
    @endif
</div>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Claim Type</x-table.th>
        <x-table.th>Account No</x-table.th>
        <x-table.th>Account Name</x-table.th>
        <x-table.th>Amount</x-table.th>
        <x-table.th>Medical Amount</x-table.th>
        <x-table.th>Claim Receipt No.</x-table.th>
        <x-table.th>Expenditure Date</x-table.th>
        <x-table.th>Expenditure Description</x-table.th>
        <x-table.th>Action</x-table.th>
    </x-slot>
    <x-slot name="tbody">
        @if($lines != null && count($lines) > 0)
            @foreach($lines as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Advance_Type}}</x-table.td>
                    <x-table.td>{{$line->Account_No}}</x-table.td>
                    <x-table.td>{{$line->Account_Name}}</x-table.td>
                    <x-table.td>{{$line->Amount}}</x-table.td>
                    <x-table.td>{{$line->Medical_Amount}}</x-table.td>
                    <x-table.td>{{$line->Claim_ReceiptNo}}</x-table.td>
                    <x-table.td>{{$line->Expenditure_Date}}</x-table.td>
                    <x-table.td>{{$line->Purpose}}</x-table.td>
                    @if($requisition->Status == 'Pending')
                        <x-table.td>
                            <form method="POST" action="{{route('deleteClaimLine')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to delete this claim Line?');">
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
                <td colspan="9" class="text-black text-center pt-4"><em>*** No Claim Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
