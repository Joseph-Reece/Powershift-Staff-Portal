<x-app-layout>
    <x-slot name="title">Imprest Surrender List</x-slot>
    <div>
        <div class="p-2">
            <x-abutton href="/staff/requisition/imprest-surrender/create/header" class="bg-blue-900"><x-heroicon-o-plus/> New Surrender</x-abutton>
        </div>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>No</x-table.th>
                <x-table.th>Surrender Date</x-table.th>
                <x-table.th>Imprest Issue No.</x-table.th>
                <x-table.th>Amount</x-table.th>
                <x-table.th>Account Name</x-table.th>
                <x-table.th>Status</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($requsitions != null && count($requsitions) > 0)
                    @foreach($requsitions as $requisition)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/requisition/imprest-surrender/show/header/{{$requisition->No}}'">
                            <x-table.td>{{$requisition->No}}</x-table.td>
                            <x-table.td>{{$requisition->Surrender_Date}}</x-table.td>
                            <x-table.td>{{$requisition->Imprest_Issue_Doc_No}}</x-table.td>
                            <x-table.td>{{$requisition->Amount}}</x-table.td>
                            <x-table.td>{{$requisition->Account_Name}}</x-table.td>
                            <x-table.td>
                                @if ($requisition->Status == 'Open' || $requisition->Status == 'Pending')
                                    <x-badge :class="'bg-blue-600'">Open</x-badge>
                                @elseif($requisition->Status == 'Pending Approval')
                                        <x-badge class="bg-blue-600">{{$requisition->Status}}</x-badge>
                                @elseif ($requisition->Status == 'Approved' || $requisition->Status == 'Posted')
                                    <x-badge class="bg-green-600">{{$requisition->Status}}</x-badge>
                                @else
                                    <x-badge class="bg-red-600">{{$requisition->Status}}</x-badge>
                                @endif
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No Imprest surrenders Found***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
</x-app-layout>
