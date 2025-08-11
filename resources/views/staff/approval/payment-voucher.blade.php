<h3 class="flex justify-center font-semibold p-2"></h3>
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Document No</x-slot>
            <x-slot name="value">{{$data->No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Date</x-slot>
            <x-slot name="value">{{$data->Date}}</x-slot>
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
            <x-slot name="label">On Behalf Of</x-slot>
            <x-slot name="value">{{$data->On_Behalf_Of}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Cashier</x-slot>
            <x-slot name="value">{{$data->Cashier}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Total Payment Amount</x-slot>
            <x-slot name="value">{{$data->Total_Payment_Amount}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Department Code</x-slot>
            <x-slot name="value">{{$data->Department_Code}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Function Name</x-slot>
            <x-slot name="value">{{$data->Function_Name}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Budget Center Name</x-slot>
            <x-slot name="value">{{$data->Budget_Center_Name}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Total Withholding Tax Amount</x-slot>
            <x-slot name="value">{{$data->Total_Withholding_Tax_Amount}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Total Net Amount</x-slot>
            <x-slot name="value">{{$data->Total_Net_Amount}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Total Payment Amount LCY</x-slot>
            <x-slot name="value">{{$data->Total_Payment_Amount_LCY}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Total Payment Amount LCY</x-slot>
            <x-slot name="value">{{$data->Total_Payment_Amount_LCY}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Payment Narration</x-slot>
            <x-slot name="value">{{$data->Payment_Narration}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Responsibility Center</x-slot>
            <x-slot name="value">{{$data->Responsibility_Center}}</x-slot>
        </x-show-group>
    </x-grid-col>
</x-grid>
<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Payment Voucher Lines</em></h3>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Type</x-table.th>
        <x-table.th>Account Type.</x-table.th>
        <x-table.th>Grouping</x-table.th>
        <x-table.th>Account No</x-table.th>
        <x-table.th>Council No</x-table.th>
        <x-table.th>Account Name</x-table.th>
    </x-slot>
    <x-slot name="tbody">
        @if($data['lines'] != null && count($data['lines']) > 0)
            @foreach($data['lines'] as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Type}}</x-table.td>
                    <x-table.td>{{$line->Account_Type}}</x-table.td>
                    <x-table.td>{{$line->Grouping}}</x-table.td>
                    <x-table.td>{{$line->Account_No}}</x-table.td>
                    <x-table.td>{{$line->Council_No}}</x-table.td>
                    <x-table.td>{{$line->Account_Name}}</x-table.td>
            </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No Voucher Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
