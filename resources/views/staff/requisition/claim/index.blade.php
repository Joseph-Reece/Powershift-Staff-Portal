<x-app-layout>
    <x-slot name="title">Claims List</x-slot>
    <div>
        <div class="p-2">
            <x-abutton href="/staff/requisition/claim/create/header" class="bg-blue-900"><x-heroicon-o-plus/> New Claim</x-abutton>
        </div>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>No.</x-table.th>
                <x-table.th>Claim Date</x-table.th>
                <x-table.th>Total Amount</x-table.th>
                <x-table.th>Purpose</x-table.th>
                <x-table.th>Status</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($requsitions != null && count($requsitions) > 0)
                    @foreach($requsitions as $requisition)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/requisition/claim/show/header/{{$requisition->No}}'">
                            <x-table.td>{{$requisition->No}}</x-table.td>
                            <x-table.td>{{$requisition->Date}}</x-table.td>
                            <x-table.td>{{$requisition->Total_Net_Amount}}</x-table.td>
                            <x-table.td>{{$requisition->Purpose}}</x-table.td>
                            <x-table.td>
                                @include('staff.requisition.claim.status')
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No Claims Found***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
</x-app-layout>
