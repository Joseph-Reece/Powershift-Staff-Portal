<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Order Header</em></h3>
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Requisition No</x-slot>
            <x-slot name="value">{{$data->No}}</x-slot>
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
            <x-slot name="label">Employee Name</x-slot>
            <x-slot name="value">{{$employeeDesc != null? $employeeDesc['First_Name'].' '.$employeeDesc['Middle_Name'].' '.$employeeDesc['Last_Name']:''}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Purpose</x-slot>
            <x-slot name="value">{{$data->Posting_Description}}</x-slot>
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
</x-grid>
<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Order Lines</em></h3>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Type</x-table.th>
        <x-table.th>Item</x-table.th>
        <x-table.th>Quantity</x-table.th>
        <x-table.th>Unit of Mea.</x-table.th>
        <x-table.th>Amount</x-table.th>
        <x-table.th>Remarks</x-table.th>
    </x-slot>
    <x-slot name="tbody">
        @if($data['lines'] != null && count($data['lines']) > 0)
            @foreach($data['lines'] as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Type}}</x-table.td>
                    <x-table.td>{{$line['itemDesc']['Description']}}</x-table.td>
                    <x-table.td>{{$line->Quantity}}</x-table.td>
                    <x-table.td>{{$line->Unit_of_Measure}}</x-table.td>
                    <x-table.td>{{$line->Amount}}</x-table.td>
                    <x-table.td>{{$line->Description_2}}</x-table.td>
                </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No purchase Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
