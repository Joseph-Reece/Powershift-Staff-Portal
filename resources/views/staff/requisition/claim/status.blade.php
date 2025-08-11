@php
 if(isset($requisition))
 {
    $docData = $requisition;
 }
 elseif(isset($data))
 {
    $docData = $data;
 }
@endphp
@if($docData != null)
    @if ($docData->Status == 'Open' || $docData->Status == 'Pending')
        <x-badge :class="'bg-blue-600'">Open</x-badge>
    @elseif($docData->Status == 'Pending Approval')
            <x-badge class="bg-blue-600">{{$docData->Status}}</x-badge>
    @elseif ($docData->Status == 'Approved')
        <x-badge class="bg-green-600">{{$docData->Status}}</x-badge>
    @else
        <x-badge class="bg-red-600">{{$docData->Status}}</x-badge>
    @endif
@endif