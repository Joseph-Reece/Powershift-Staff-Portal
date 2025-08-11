<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Purchase Requisition Header</em></h3>
<x-grid>
        <x-grid-col>
            <x-show-group>
                <x-slot name="label">Requisition No</x-slot>
                <x-slot name="value">{{$data->No}}</x-slot>
            </x-show-group>
        </x-grid-col>
        <x-grid-col>
            <x-show-group>
                <x-slot name="label">Requested Receipt Date</x-slot>
                <x-slot name="value">{{$data->Requested_Receipt_Date}}</x-slot>
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
                <x-slot name="label">Department</x-slot>
                <x-slot name="value">{{$data->Shortcut_Dimension_1_Code}}</x-slot>
            </x-show-group>
        </x-grid-col>
        <x-grid-col>
            <x-show-group>
                <x-slot name="label">Posting Description</x-slot>
                <x-slot name="value">{{$data->Posting_Description}}</x-slot>
            </x-show-group>
        </x-grid-col>
        <x-grid-col>
            <x-show-group>
                <x-slot name="label">Order Date</x-slot>
                <x-slot name="value">{{$data->Order_Date}}</x-slot>
            </x-show-group>
        </x-grid-col>
        <x-grid-col>
            <x-show-group>
                <x-slot name="label">Document Date</x-slot>
                <x-slot name="value">{{$data->Document_Date}}</x-slot>
            </x-show-group>
        </x-grid-col>
        <x-grid-col>
            <x-show-group>
                <x-slot name="label">Prices Including VAT?</x-slot>
                <x-slot name="value">{{$data->Prices_Including_VAT == 1? 'YES':'NO'}}</x-slot>
            </x-show-group>
        </x-grid-col>
</x-grid>
<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Purchase Lines</em></h3>
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
    </x-slot>
    <x-slot name="tbody">
        @if($data['lines'] != null && count($data['lines']) > 0)
            @foreach($data['lines'] as $line)
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
                </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No purchase Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
