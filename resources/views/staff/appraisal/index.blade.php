<x-app-layout>
    <x-slot name="title">Appraisals List</x-slot>
    <div>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>Appraisal No.</x-table.th>
                <x-table.th>Employee Name</x-table.th>
                <x-table.th>Appraisal Period.</x-table.th>
                <x-table.th>Supervisor</x-table.th>
                <x-table.th>Status</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($records != null && count($records) > 0)
                    @foreach($records as $record)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/appraisal/show/header/{{$record->Appraisal_No}}'">
                            <x-table.td>{{$record->Appraisal_No}}</x-table.td>
                            <x-table.td>{{$record->Employee_Name}}</x-table.td>
                            <x-table.td>{{$record->Appraisal_Period}}</x-table.td>
                            <x-table.td>{{$record->Supervisor}}</x-table.td>
                            <x-table.td>
                                @if ($record->Status == 'Open' || $record->Status == 'Pending')
                                    <x-badge :class="'bg-blue-600'">Open</x-badge>
                                @elseif($record->Status == 'Pending Approval')
                                        <x-badge class="bg-blue-600">Pending Supervisor Approval</x-badge>
                                @elseif ($record->Status == 'Approved')
                                    <x-badge class="bg-green-600">{{$record->Status}}</x-badge>
                                @else
                                    <x-badge class="bg-red-600">{{$record->Status}}</x-badge>
                                @endif
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No records Found***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
</x-app-layout>
