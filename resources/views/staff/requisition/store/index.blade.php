<x-app-layout>
    <x-slot name="title">Store Requisitions List</x-slot>
    <div>
        <div class="p-2">
            <x-abutton href="/staff/requisition/store/create/header" class="bg-blue-900"><x-heroicon-o-plus/> New Request</x-abutton>
        </div>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>No.</x-table.th>
                <x-table.th>Requisition Date</x-table.th>
                <x-table.th>Location</x-table.th>
                <x-table.th>Department</x-table.th>
                <x-table.th>Status</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($requsitions != null && count($requsitions) > 0)
                    @foreach($requsitions as $requisition)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/requisition/store/show/header/{{$requisition->No}}'">
                            <x-table.td>{{$requisition->No}}</x-table.td>
                            <x-table.td>{{$requisition->Requisition_Date}}</x-table.td>
                            <x-table.td>{{$requisition->Location_Code}}</x-table.td>
                            <x-table.td>{{$requisition->Global_Dimension_1_Code}}</x-table.td>
                            <x-table.td>
                                @if ($requisition->Status == 'Open' || $requisition->Status == 'Pending Approval')
                                    <x-badge :class="'bg-blue-600'">{{$requisition->Status}}</x-badge>
                                @elseif ($requisition->Status == 'Approved')
                                    <x-badge class="bg-green-600">{{$requisition->Status}}</x-badge>
                                @else
                                    <x-badge class="bg-red-600">{{$requisition->Status}}</x-badge>
                                @endif
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No store requisitions Found***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
</x-app-layout>
