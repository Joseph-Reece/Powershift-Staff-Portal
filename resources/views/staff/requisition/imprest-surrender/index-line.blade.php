<div class="flex gap-4 mt-4 mb-2">
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0"><em>Imprest Surrender Lines</em></h3>
</div>
<form method="POST" action="{{route('updateImprestSurrenderLines')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to update imprest surrender lines?');">
    @if($requisition->Status == 'Pending')
        @csrf
        @method('PUT')
    @endif
    <x-table.table>
        <x-slot name="thead">
            <x-table.th>Account No.</x-table.th>
            <x-table.th>Surrender Doc No</x-table.th>
            <x-table.th>Account Name</x-table.th>
            <x-table.th>Amount</x-table.th>
            <x-table.th>Actual Spent</x-table.th>
            <x-table.th>Cash Receipt No.</x-table.th>
            <x-table.th>Cash Receipt Amount.</x-table.th>
        </x-slot>
        <x-slot name="tbody">
            @if($lines != null && count($lines) > 0)
            @foreach($lines as $line)
                <input id="lineNo" type="hidden" name="lineNo__{{$line->Line_No}}" value="{{$line->Line_No}}"/>
                <input type="hidden" name="accountNo__{{$line->Line_No}}" value="{{$line->Account_No}}"/>
                <input type="hidden" name="surrenderNo__{{$line->Line_No}}" value="{{$line->Surrender_Doc_No}}"/>
                    <x-table.tr isEven="{{$loop->even}}">
                        <x-table.td>{{$line->Account_No}}</x-table.td>
                        <x-table.td>{{$line->Surrender_Doc_No}}</x-table.td>
                        <x-table.td>{{$line->Account_Name}}</x-table.td>
                        <x-table.td>{{$line->Amount}}</x-table.td>
                        <x-table.td>
                        @if($requisition->Status == 'Pending')
                            <x-input type="number" name="spent__{{$line->Line_No}}" value="{{old('spent__'.$line->Line_No) == null? $line->Actual_Spent:old('spent__'.$line->Line_No)}}" />
                        @else
                            {{$line->Actual_Spent}}
                        @endif
                        </x-table.td>
                        <x-table.td>
                            @if($requisition->Status == 'Pending')
                                <x-select name="receipt__{{$line->Line_No}}" :required="false">
                                    <option value="">--select--</option>
                                    @if($receipts != null)
                                        @foreach($receipts as $receipt)
                                            <option value="{{$receipt->No}}" {{$line->Cash_Receipt_No == $receipt->No? 'selected':old('receipt__'.$line->Line_No) }}>{{$receipt['No'].' - '.$receipt['Received_From']}}</option>
                                        @endforeach
                                    @endif
                                </x-select>
                            @else
                                {{$line->Account_No}}
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if($requisition->Status == 'Pending')
                                <x-input type="number" name="receiptAmount__{{$line->Line_No}}" value="{{old('receiptAmount__'.$line->Line_No) == null? $line->Cash_Receipt_Amount:old('receiptAmount__'.$line->Line_No)}}" />
                            @else
                                {{$line->Cash_Receipt_Amount}}
                            @endif
                            </x-table.td>
                    </x-table.tr>
                @endforeach
            @else
                <tr class="w-full">
                    <td colspan="9" class="text-black text-center pt-4"><em>*** No Imprest Surrender Lines Found***</em></td>
                </tr>
            @endif
        </x-slot>
    </x-table.table>
    @if($requisition->Status == 'Pending')
        <div class="p-2 flex justify-center">
            <x-jet-button class="rounded-full bg-blue-800" data-turbo="false">Save</x-jet-button>
        </div>
    @endif
</form>
