<div class="flex gap-4 mt-4 mb-2">
    <h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0"><em>Appraisal Scores</em></h3>
</div>
<x-table.table>
    <x-slot name="thead">
        <x-table.th>Key Result Area</x-table.th>
        <x-table.th>Performance Measure</x-table.th>
        <x-table.th>Weight</x-table.th>
        <x-table.th>Target</x-table.th>
        <x-table.th>Employee Rating</x-table.th>
        <x-table.th>Employee Weighted Score</x-table.th>
        <x-table.th>Employee Comments</x-table.th>
        <x-table.th>Supervisor Rating</x-table.th>
        <x-table.th>Supervisor Weighted Score</x-table.th>
        <x-table.th>Supervisor Comments</x-table.th>
    </x-slot>
    <x-slot name="tbody">
        @if($lines != null && count($lines) > 0)
            @foreach($lines as $line)
                <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/appraisal/edit/score/{{$line->Appraisal_No}}/{{$line->Key_Result_Area}}/{{$line->Performance_Measure}}'">
                    <x-table.td>{{$line->Key_Result_Area}}</x-table.td>
                    <x-table.td>{{$line->Performance_Measure}}</x-table.td>
                    <x-table.td>{{$line->Weight}}</x-table.td>
                    <x-table.td>{{$line->Target}}</x-table.td>
                    <x-table.td>{{$line->Employee_Rating}}</x-table.td>
                    <x-table.td>{{$line->Employee_Weighted_Score}}</x-table.td>
                    <x-table.td>{{$line->Employee_Comments}}</x-table.td>
                    <x-table.td>{{$line->Supervisor_Rating}}</x-table.td>
                    <x-table.td>{{$line->Supervisor_Weighted_Score}}</x-table.td>
                    <x-table.td>{{$line->Supervisor_Comments}}</x-table.td>
                </x-table.tr>
            @endforeach
        @else
            <tr class="w-full">
                <td colspan="9" class="text-black text-center pt-4"><em>*** No records Found***</em></td>
            </tr>
        @endif
    </x-slot>
</x-table.table>
