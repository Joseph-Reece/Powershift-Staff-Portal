@if(isset($approvers))
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Approvers</em></h3>
    <x-table.table class="max-w-sm">
        <x-slot name="thead">
            <x-table.th>Name</x-table.th>
            <x-table.th>Status</x-table.th>
            <x-table.th>Sequence</x-table.th>
        </x-slot>
        <x-slot name="tbody">
            @foreach($approvers as $approver)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$approver->employeeDesc != null? $approver->employeeDesc['First_Name'].' '.$approver->employeeDesc['Middle_Name']:$approver->User_ID}}</x-table.td>
                    <x-table.td>{{$approver->Status}}</x-table.td>
                    <x-table.td>{{$approver->Sequence_No}}</x-table.td>
                </x-table.tr>
            @endforeach
        </x-slot>
    </x-table.table>
@endif