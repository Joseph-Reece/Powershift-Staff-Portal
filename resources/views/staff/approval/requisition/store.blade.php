<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Store Requisition Header</em></h3>
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Requisition No</x-slot>
            <x-slot name="value">{{$data->No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Request Date</x-slot>
            <x-slot name="value">{{$data->Request_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Required Date</x-slot>
            <x-slot name="value">{{$data->Required_Date}}</x-slot>
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
            <x-slot name="value">{{$data->Shortcut_Dimension_2_Code}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Request Description</x-slot>
            <x-slot name="value">{{$data->Request_Description}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Issue Date</x-slot>
            <x-slot name="value">{{$data->Issue_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">SRN No.</x-slot>
            <x-slot name="value">{{$data->SRN_No}}</x-slot>
        </x-show-group>
    </x-grid-col>
</x-grid>
<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Store Request Lines</em></h3>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Type</x-table.th>
        <x-table.th>Issuing Store</x-table.th>
        <x-table.th>No</x-table.th>
        <x-table.th>Description</x-table.th>
        <x-table.th>Quantity Requested</x-table.th>
    </x-slot>
    <x-slot name="tbody">
        @if($data['lines'] != null && count($data['lines']) > 0)
            @foreach($data['lines'] as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Type}}</x-table.td>
                    <x-table.td>{{$line->Issuing_Store}}</x-table.td>
                    <x-table.td>{{$line->No}}</x-table.td>
                    <x-table.td>{{$line->Description}}</x-table.td>
                    <x-table.td>{{$line->Quantity}}</x-table.td>
                </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No store Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
