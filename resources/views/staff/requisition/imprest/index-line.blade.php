<div class="flex gap-4 mt-4 mb-2">
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0"><em>Imprest Lines</em></h3>
    @if($requisition->Status == 'Pending')
        <x-abutton href="/staff/requisition/imprest/create/line/{{$requisition->No}}" class="pr-4"><x-heroicon-o-plus/> New Line</x-abutton>
    @endif
</div>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Advance Type</x-table.th>
        <x-table.th>Account No.</x-table.th>
        <x-table.th>Account Name</x-table.th>
        <x-table.th>Amount</x-table.th>
        @if($requisition->Status == 'Pending')
            <x-table.th>Action</x-table.th>
        @endif
    </x-slot>
    <x-slot name="tbody">
        @if($lines != null && count($lines) > 0)
            @foreach($lines as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Advance_Type}}</x-table.td>
                    <x-table.td>{{$line->Account_No}}</x-table.td>
                    <x-table.td>{{$line->Account_Name}}</x-table.td>
                    <x-table.td>{{$line->Amount}}</x-table.td>
                    @if($requisition->Status == 'Pending')
                        <x-table.td>
                            <form method="POST" action="{{route('deleteImprestLine')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to delete this imprest Line?');">
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
                <td colspan="9" class="text-black text-center pt-4"><em>*** No Imprest Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
