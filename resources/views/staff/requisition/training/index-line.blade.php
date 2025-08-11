<div class="flex gap-4 mt-4 mb-2">
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0"><em>Training Participant</em></h3>
    {{-- @if($requisition->Status == 'Pending')
        <x-abutton href="/staff/requisition/imprest/create/line/{{$requisition->No}}" class="pr-4"><x-heroicon-o-plus/> New Line</x-abutton>
    @endif --}}
</div>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Staff No.</x-table.th>
        <x-table.th>Name</x-table.th>
        <x-table.th>Email</x-table.th>
        <x-table.th>Designation</x-table.th>
        <x-table.th>Gender</x-table.th>
    </x-slot>
    <x-slot name="tbody">
        @if($lines != null && count($lines) > 0)
            @foreach($lines as $line)
                <x-table.tr isEven="{{$loop->even}}">
                    <x-table.td>{{$line->Staff_No}}</x-table.td>
                    <x-table.td>{{$line->Staff_Name}}</x-table.td>
                    <x-table.td>{{$line->Company_E_Mail}}</x-table.td>
                    <x-table.td>{{$line->Designation}}</x-table.td>
                    <x-table.td>{{$line->Gender}}</x-table.td>
                </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No Imprest Lines Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
