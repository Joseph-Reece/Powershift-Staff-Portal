<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Staff Claim Header</em></h3>
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Claim No</x-slot>
            <x-slot name="value">{{$data->No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Claim Date</x-slot>
            <x-slot name="value">{{$data->Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Employee No</x-slot>
            <x-slot name="value">{{$data->Employee_No}}</x-slot>
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
            <x-slot name="label">Account No.</x-slot>
            <x-slot name="value">{{$data->Account_No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Purpose</x-slot>
            <x-slot name="value">{{$data->Purpose}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Total Claim Amount</x-slot>
            <x-slot name="value">{{$data->Total_Net_Amount}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Currency</x-slot>
            <x-slot name="value">{{$data->Currency_Code == ""? 'KES':$data->Currency_Code}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Department</x-slot>
            <x-slot name="value">{{$data->Shortcut_Dimension_2_Code}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Responsibility Center</x-slot>
            <x-slot name="value">{{$data->Responsibility_Center}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Status</x-slot>
            <x-slot name="value">
                @include('staff.requisition.claim.status')
            </x-slot>
        </x-show-group>
    </x-grid-col>
</x-grid>
<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Claim Lines</em></h3>
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
    </x-slot>
    <x-slot name="tbody">
        @if($data['lines'] != null && count($data['lines']) > 0)
            @foreach($data['lines'] as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Advance_Type}}</x-table.td>
                    <x-table.td>{{$line->Account_No}}</x-table.td>
                    <x-table.td>{{$line->Account_Name}}</x-table.td>
                    <x-table.td>{{$line->Amount}}</x-table.td>
                    <x-table.td>{{$line->Medical_Amount}}</x-table.td>
                    <x-table.td>{{$line->Claim_ReceiptNo}}</x-table.td>
                    <x-table.td>{{$line->Expenditure_Date}}</x-table.td>
                    <x-table.td>{{$line->Purpose}}</x-table.td>
            </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No Claim Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
