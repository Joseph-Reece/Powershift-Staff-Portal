<x-app-layout>
    <x-slot name="title">Leave Requests</x-slot>
    <div>
        <div class="p-2">
            <x-abutton href="/staff/leave/create" class="bg-blue-900"><x-heroicon-o-plus/> New Request</x-abutton>
        </div>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>Leave No.</x-table.th>
                <x-table.th>Leave Type</x-table.th>
                <x-table.th>Date Applied</x-table.th>
                <x-table.th>Duration</x-table.th>
                <x-table.th>Start Date</x-table.th>
                <x-table.th>End Date</x-table.th>
                <x-table.th>Return Date</x-table.th>
                <x-table.th>Reliever</x-table.th>
                <x-table.th>Status</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($requsitions != null && count($requsitions) > 0)
                {{-- Echo requisitions for debugging --}}                 
                {{-- @dd($requsitions) --}}
                    @foreach($requsitions as $requisition)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/leave/show/{{$requisition->ApplicationCode}}'">
                            <x-table.td>{{$requisition->ApplicationCode}}</x-table.td>
                            <x-table.td>{{$requisition->LeaveType}}</x-table.td>
                            <x-table.td>{{$requisition->ApplicationDate}}</x-table.td>
                            <x-table.td >{{$requisition->DaysApplied." Days"}}</x-table.td>
                            <x-table.td>{{$requisition->StartDate}}</x-table.td>
                            <x-table.td>{{$requisition->EndDate}}</x-table.td>
                            <x-table.td>{{$requisition->ReturnDate}}</x-table.td>
                            <x-table.td>{{$requisition->RelieverName}}</x-table.td>
                            <x-table.td>
                                @if ($requisition->Status == 'Open' || $requisition->Status == 'Pending Approval')
                                    <x-badge :class="'bg-blue-600'">{{$requisition->Status}}</x-badge>
                                @elseif ($requisition->Status == 'Posted')
                                    <x-badge class="bg-green-600">Approved</x-badge>
                                @else
                                    <x-badge class="bg-red-600">{{$requisition->Status}}</x-badge>
                                @endif
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No leave history ***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
</x-app-layout>
