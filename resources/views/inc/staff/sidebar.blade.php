<div class="section-bg sidebarx section-bg min-h-screen h-full w-full sm:w-full whitespace-nowrap">
    <div class="text-white sticky top-12 overflow-y-auto z-40 max-h-screen">
        <!--action-->
        <x-jet-dropdown-link href="/dashboard" class="border-b border-t pl-1 flex items-center gap-1 {{\Request::is('dashboard')? 'bg-theme2':''}}">
            <x-heroicon-o-chart-pie/>
            Dashboard
        </x-jet-dropdown-link>
        <!--HR Services-->
        <div class="pl-1 border-b border-t">
            <span class="flex items-center gap-1"><x-heroicon-o-receipt-refund/> HR Services</span>
            <div class="pl-4">
                <x-jet-dropdown-link href="/staff/leave" class="pl-1 flex items-center gap-1  {{\Request::is('*staff/leave*') && !Request::is('*staff/leave/statement')? 'bg-theme2':''}}">
                    <x-heroicon-o-home/>
                    Leave Application
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/leave/statement" class="pl-1 flex items-center gap-1 {{\Request::is('*staff/leave/statement')? 'bg-theme2':''}}">
                    <x-heroicon-o-home/>
                    Leave Statement
                </x-jet-dropdown-link>
                {{-- <x-jet-dropdown-link href="/staff/requisition/training" class="pl-1 flex items-center gap-1  {{\Request::is('*staff/requisition/training*')? 'bg-theme2':''}}">
                    <x-heroicon-o-home/>
                    Training Request
                </x-jet-dropdown-link> --}}
                <x-jet-dropdown-link href="/staff/payslip" class="pl-1 flex items-center gap-1 {{\Request::is('*staff/payslip')? 'bg-theme2':''}}">
                    <x-heroicon-o-currency-dollar/>
                    Pay Slip
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/p-nine" class=" pl-1 flex items-center gap-1 {{\Request::is('*staff/p-nine')? 'bg-theme2':''}}">
                    <x-heroicon-o-cash/>
                    P9
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/attendance" class=" pl-1 flex items-center gap-1 {{\Request::is('*staff/attendance')? 'bg-theme2':''}}">
                    <x-heroicon-o-users/>
                    Attendance
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/appraisal" class=" pl-1 flex items-center gap-1 {{\Request::is('*staff/appraisal*')? 'bg-theme2':''}}">
                    <x-heroicon-o-document/>
                    Appraisals
                </x-jet-dropdown-link>
            </div>
        </div>
        <!--Finance Services-->
        <div class="pl-1 border-b border-t">
            <span class="flex items-center gap-1"><x-heroicon-o-currency-dollar/> Finance Services</span>
            <div class="pl-4">
                <x-jet-dropdown-link href="/staff/requisition/imprest" class="pl-1 flex items-center gap-1 {{\Request::is('*staff/requisition/imprest/*') || \Request::is('*staff/requisition/imprest')? 'bg-theme2':''}}">
                    <x-heroicon-o-cash/>
                    Imprest Requisition
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/requisition/imprest-surrender" class="borderx-t pl-1 flex items-center gap-1 {{\Request::is('*staff/requisition/imprest-surrender*')? 'bg-theme2':''}}">
                    <x-heroicon-o-receipt-refund/>
                    Imprest Surrender
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/requisition/claim" class="pl-1 flex items-center gap-1  {{\Request::is('*staff/requisition/claim*')? 'bg-theme2':''}}">
                    <x-heroicon-o-cash/>
                    Staff Claims
                </x-jet-dropdown-link>
            </div>
        </div>
        <!--Procurement Services-->
        <div class="pl-1 border-b border-t">
            <span class="flex items-center gap-1"><x-heroicon-o-briefcase/> Procurement Services</span>
            <div class="pl-4">
                <x-jet-dropdown-link href="/staff/requisition/purchase" class="pl-1 flex items-center gap-1 {{\Request::is('*staff/requisition/purchase*')? 'bg-theme2':''}}">
                    <x-heroicon-o-shopping-cart/>
                    Purchase Requisition
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/requisition/store" class="pl-1 flex items-center gap-1 {{\Request::is('*staff/requisition/store*')? 'bg-theme2':''}}">
                    <x-heroicon-o-database/>
                    Store Requisition
                </x-jet-dropdown-link>
                {{-- <x-jet-dropdown-link href="/staff/requisition/transport" class="borderx-b pl-1 flex items-center gap-1 {{\Request::is('*staff/requisition/transport*')? 'bg-theme2':''}}">
                    <x-heroicon-o-truck/>
                    Transport Requisition
                </x-jet-dropdown-link> --}}
            </div>
        </div>
        <!--approvals-->
        <div class="pl-1">
            <span class="flex items-center gap-1"><x-heroicon-o-document-duplicate/> Document Approvals</span>
            <div class="pl-4">
                <x-jet-dropdown-link href="/staff/approval/open" data-turbo="false" class="pl-1 flex items-center gap-1 {{\Request::is('*/approval/open')? 'bg-theme2':''}}">
                    <x-heroicon-o-clipboard-copy/>
                    <span class="flex gap-2"><span>Pending Approval</span><span><x-badge class="text-xs bg-red-500" id="totalPendingApproval"></x-badge></span></span>
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/approval/approved" data-turbo="false" class="borderx-t pl-1 flex items-center gap-1  {{\Request::is('*/approval/approved')? 'bg-theme2':''}}">
                    <x-heroicon-o-clipboard-check/>
                    Approved Documents
                </x-jet-dropdown-link>
                <x-jet-dropdown-link href="/staff/approval/rejected" data-turbo="false" class="pl-1 flex items-center gap-1  {{\Request::is('*/approval/rejected')? 'bg-theme2':''}}">
                    <x-heroicon-o-x-circle/>
                    Rejected Documents
                </x-jet-dropdown-link>
            </div>
        </div>
        <!--CEO-->
        {{-- @if(session('authUser')['CEO'])
            <div class="pl-1 border-t">
                <span class="flex items-center gap-1"><x-heroicon-o-user-group/> CEO</span>
                <div class="pl-4">
                    <x-jet-dropdown-link href="/staff/ceo/master-roll" data-turbo="false" class="pl-1 flex items-center gap-1  {{\Request::is('*/ceo/master-roll')? 'bg-theme2':''}}">
                        <x-heroicon-o-users/>
                        Payroll Master Roll
                    </x-jet-dropdown-link>
                </div>
            </div>
        @endif --}}
        <!--HOD-->
        @if(session('authUser')['HOD'] != null)
            <div class="pl-1 border-t">
                <span class="flex items-center gap-1"><x-heroicon-o-user-group/> HOD</span>
                <div class="pl-4">
                    <x-jet-dropdown-link href="/staff/hod/employee" data-turbo="false" class="pl-1 flex items-center gap-1  {{\Request::is('*/hod/staff')? 'bg-theme2':''}}">
                        <x-heroicon-o-users/>
                        Department Staff
                    </x-jet-dropdown-link>
                    <x-jet-dropdown-link href="/staff/hod/employee?status=onLeave" data-turbo="false" class="border-t pl-1 flex items-center gap-1  {{\Request::is('*/hod/staff?status=*')? 'bg-theme2':''}}">
                        <x-heroicon-o-users/>
                        Staff on Leave
                    </x-jet-dropdown-link>
                </div>
            </div>
        @endif
        <x-jet-dropdown-link href="/staff/profile" class="border-b pl-1 flex items-center gap-1  {{\Request::is('*staff/profile')? 'bg-theme2':''}}">
            <x-heroicon-o-user-circle/>
            Profile
        </x-jet-dropdown-link>
        <x-jet-dropdown-link href="/change-password" class="border-b pl-1 flex items-center gap-1  {{\Request::is('*change-password')? 'bg-theme2':''}}">
            <x-heroicon-o-cog/>
            Change Password
        </x-jet-dropdown-link>
        <x-jet-dropdown-link href="/logout" class="border-b pl-1 flex items-center gap-1  sm:hidden">
            <x-heroicon-o-logout/>
            Logout
        </x-jet-dropdown-link>
    </div>
</div>
