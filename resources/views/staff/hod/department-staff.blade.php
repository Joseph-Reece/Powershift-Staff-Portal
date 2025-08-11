<x-app-layout>
    <x-slot name="title">{{isset($title) && $title != ''? $title:'Department Staff'}}</x-slot>
    <div>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>Staff No</x-table.th>
                <x-table.th>Name</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($staff != null && count($staff) > 0)
                    @foreach($staff as $employee)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/hod/employee/{{$employee->No}}'">
                            <x-table.td>{{$employee->No}}</x-table.td>
                            <x-table.td>{{$employee->First_Name.' '.$employee->Middle_Name.' '.$employee->Last_Name}}</x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No staff found ***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
</x-app-layout>
