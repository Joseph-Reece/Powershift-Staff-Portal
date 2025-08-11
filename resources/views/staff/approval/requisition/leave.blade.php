<h3 class=" font-semibold sm:text-lg pl-2 sm:pl-0 my-2"><em>Leave Header</em></h3>
<!--leave-->
<x-grid>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Leave No</x-slot>
            <x-slot name="value">{{$data->Document_No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Employee No</x-slot>
            <x-slot name="value">{{$data->Employee_No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Leave Type</x-slot>
            <x-slot name="value">{{$data->Leave_Type}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Applied Duration</x-slot>
            <x-slot name="value">{{$data->Days_Applied." Days"}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Starting Date</x-slot>
            <x-slot name="value">{{$data->Start_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">End Date</x-slot>
            <x-slot name="value">{{$data->End_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Return Date</x-slot>
            <x-slot name="value">{{$data->Return_to_Work_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Application Date</x-slot>
            <x-slot name="value">{{$data->Application_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Reliever No</x-slot>
            <x-slot name="value">{{$data->Reliever_No}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Reliever Name</x-slot>
            <x-slot name="value">{{$data->Reliever_Name}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Leave Purpose</x-slot>
            <x-slot name="value">{{$data->Purpose}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Date Sent for Approval</x-slot>
            <x-slot name="value">{{$document->Date_Time_Sent_for_Approval}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Due Date</x-slot>
            <x-slot name="value">{{$document->Due_Date}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Last Modified Date</x-slot>
            <x-slot name="value">{{$document->Last_Date_Time_Modified}}</x-slot>
        </x-show-group>
    </x-grid-col>
    <x-grid-col>
        <x-show-group>
            <x-slot name="label">Last User To Modify</x-slot>
            <x-slot name="value">{{$document->Last_Modified_By_User_ID}}</x-slot>
        </x-show-group>
    </x-grid-col>
</x-grid>
