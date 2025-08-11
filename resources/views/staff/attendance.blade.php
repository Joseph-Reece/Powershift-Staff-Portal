<x-app-layout>
    <x-slot name="title">{{isset($title) && $title != ''? $title:'List'}}</x-slot>

    <div>
        <div class="flex flex-row gap-1 pb-2">
            <form method="POST" action="{{route('checkinCheckout')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to sign in?');">
                @csrf
                <input id="type" type="hidden" name="type" value="checkin"/>
                <input id="checkinLocation" type="hidden" name="checkinLocation"/>
                <x-jet-button type="submit" class="bg-green-600 rounded-full">Sign-in Today</x-button>
            </form>
            <form method="POST" action="{{route('checkinCheckout')}}" class="text-black w-full" data-turbo-frame="_top" data-turbo="false" onsubmit="return confirm('Are you sure you want to sign out?');">
                    @csrf
                <input id="type" type="hidden" name="type" value="checkout"/>
                <input id="checkoutLocation" type="hidden" name="checkoutLocation"/>
                <x-jet-button type="submit" class="bg-blue-600 rounded-full">Sign-out Today</x-button>
            </form>
        </div>
        <x-table.table class="text-xs">
            <x-slot name="thead">
                <x-table.th>Date</x-table.th>
                <x-table.th>Staff Name</x-table.th>
                <x-table.th>Time In</x-table.th>
                <x-table.th>Time Out</x-table.th>
                <x-table.th>Hours Worked</x-table.th>
                <x-table.th>Location</x-table.th>
                <x-table.th>Comments</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($records != null && count($records) > 0)
                    @foreach($records as $record)
                        <x-table.tr isEven="{{$loop->even}}">
                            <x-table.td>{{$record->Transaction_Date}}</x-table.td>
                            <x-table.td>{{$record->Full_Name}}</x-table.td>
                            <x-table.td>{{\Carbon\Carbon::parse(strtotime($record->Time_In))->format('H:i')}}</x-table.td>
                            <x-table.td>{{\Carbon\Carbon::parse(strtotime($record->Time_out))->format('H:i')}}</x-table.td>
                            <x-table.td>{{round($record->Hours_Worked,1)}}</x-table.td>
                            <x-table.td>{{$record->Location_Coordinates}}</x-table.td>
                            <x-table.td>
                                -{{$record->Sign_in_Comments}}</br>
                                -{{$record->Sign_out_Comments}}
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="9" class="text-black text-center pt-4"><em>*** No records found ***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
    </div>
    @push('scripts')
    <script>
        const checkoutLocation = document.getElementById("checkoutLocation");
        const checkinLocation = document.getElementById("checkinLocation");
        function fnOnLoad() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(savePosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
        function savePosition(position) {
            checkinLocation.value = "Latitude: " + position.coords.latitude +
            " Longitude: " + position.coords.longitude;
            checkoutLocation.value = "Latitude: " + position.coords.latitude +
            " Longitude: " + position.coords.longitude;
        }
    </script>
    @endpush
</x-app-layout>
