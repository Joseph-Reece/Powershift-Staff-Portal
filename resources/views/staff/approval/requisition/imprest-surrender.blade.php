<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Imprest Surrender Header</em></h3>
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Requisition No</x-slot>
            <x-slot name="value">{{$data->No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Account No</x-slot>
            <x-slot name="value">{{$data->Account_No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Payee</x-slot>
            <x-slot name="value">{{$data->Payee}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Received From</x-slot>
            <x-slot name="value">{{$data->Received_From}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Department</x-slot>
            <x-slot name="value">{{$data->Global_Dimension_2_Code}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Imprest Issue Date</x-slot>
            <x-slot name="value">{{$data->Imprest_Issue_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Region</x-slot>
            <x-slot name="value">{{$data->Global_Dimension_1_Code}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Amount Surrendered</x-slot>
            <x-slot name="value">{{$data->Amount_Surrendered_LCY}}</x-slot>
        </x-show-group>
    </x-grid-col>
</x-grid>
<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Imprest Surrender Lines</em></h3>
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
        @if($data['lines'] != null && count($data['lines']) > 0)
            @foreach($data['lines'] as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Account_No}}</x-table.td>
                        <x-table.td>{{$line->Surrender_Doc_No}}</x-table.td>
                        <x-table.td>{{$line->Account_Name}}</x-table.td>
                        <x-table.td>{{$line->Amount}}</x-table.td>
                    <x-table.td>
                        {{$line->Actual_Spent}}
                    </x-table.td>
                    <x-table.td>
                        {{$line->Account_No}}
                    </x-table.td>
                    <x-table.td>
                        {{$line->Cash_Receipt_Amount}}
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
