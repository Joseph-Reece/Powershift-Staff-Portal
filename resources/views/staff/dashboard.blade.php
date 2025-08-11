<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <div>
        <h4 class="text-center text-blue-500 mb-4 text-xs"><em>Hi {{session('authUser')['name']}}, welcome to {{config('app.company')['name']}}'s {{config('app.name')}}</em></h4>
        <h5 class="text-center text-blue-500 mb-4">{{date('Y')}} Summary</h5>
        <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-4 gap-4">
            <x-tile class="bg-red-500 border-red-700">
                <x-slot name="icon"><x-heroicon-o-clipboard-copy class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="pendingApproval"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/approval/open" class="underline">Pending Approval</a></x-slot>
            </x-tile>
            <x-tile class="bg-blue-500 border-blue-700">
                <x-slot name="icon"><x-heroicon-o-clipboard-check class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="approvedDocs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/approval/approved" class="underline">Approved Documents</a></x-slot>
            </x-tile>
            <x-tile class="bg-green-500 border-green-700">
                <x-slot name="icon"><x-heroicon-o-x-circle class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="rejectedDocs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/approval/rejected" class="underline">Rejected Documents</a></x-slot>
            </x-tile>
            <x-tile class="bg-yellow-500 border-yellow-700">
                <x-slot name="icon"><x-heroicon-o-home class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="leaveAppllications"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/leave" class="underline">Leave Applications</a></x-slot>
            </x-tile>
            <x-tile class=" bg-green-700 border-green-900">
                <x-slot name="icon"><x-heroicon-o-cash class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="claims"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/requisition/claim" class="underline">Staff Claims</a></x-slot>
            </x-tile>
            <x-tile class=" bg-gray-500 border-gray-700">
                <x-slot name="icon"><x-heroicon-o-cash class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="imprestReqs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/requisition/imprest" class="underline">Imprest Requisitions</a></x-slot>
            </x-tile>
            <x-tile class=" bg-red-500 border-red-700">
                <x-slot name="icon"><x-heroicon-o-receipt-refund class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="imprestSurrenderReqs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/requisition/imprest-surrender" class="underline">Imprest Surrenders</a></x-slot>
            </x-tile>
            <x-tile class=" bg-yellow-900 border-yellow-700">
                <x-slot name="icon"><x-heroicon-o-shopping-cart class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="purchaseReqs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/requisition/purchase" class="underline">Purchase Requisitions</a></x-slot>
            </x-tile>
            <x-tile class="bg-pink-600 border-pink-800">
                <x-slot name="icon"><x-heroicon-o-database class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="storeReqs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/requisition/store" class="underline">Store Requisitions</a></x-slot>
            </x-tile>
            {{-- <x-tile class="bg-gray-900 border-gray-700">
                <x-slot name="icon"><x-heroicon-o-truck class="h-6 w-6"/></x-slot>
                <x-slot name="value"><span id="transportReqs"><x-loading class="loaders" show/></span></x-slot>
                <x-slot name="label"><a href="/staff/requisition/transport" class="underline">Transport Requisitions</a></x-slot>
            </x-tile> --}}
        </div>
    </div>
    <script>
        function fnOnLoad(){
            getStatistics();
            //
        }
        function getStatistics(){
            var loaders = document.getElementsByClassName("loaders");
            var elPendingApproval = document.getElementById('pendingApproval');
            var elApprovedDocs = document.getElementById('approvedDocs');
            var elRejectedDocs = document.getElementById('rejectedDocs');
            var elLeaveAppllications = document.getElementById('leaveAppllications');
            var elClaims = document.getElementById('claims');
            var elImprestReqs = document.getElementById('imprestReqs');
            var elImprestSurrenderReqs = document.getElementById('imprestSurrenderReqs');
            var elPurchaseReqs = document.getElementById('purchaseReqs');
            var elStoreReqs = document.getElementById('storeReqs');
            var elTransportReqs = document.getElementById('transportReqs');
            axios.get('/staff/dashboard/statistics/').then(response =>{
                var data = response.data;
                elPendingApproval.innerHTML = data.totalPendingApproval;
                elApprovedDocs.innerHTML = data.totalApproved;
                elRejectedDocs.innerHTML = data.totalRejected;
                elLeaveAppllications.innerHTML = data.totalLeaveReqs;
                elClaims.innerHTML = data.totalClaims;
                elImprestReqs.innerHTML = data.totalImprestReqs;
                elImprestSurrenderReqs.innerHTML = data.totalImprestSurrenderReqs;
                elPurchaseReqs.innerHTML = data.totalPurchaseReqs;
                elStoreReqs.innerHTML = data.totalStoreReqs;
                elTransportReqs.innerHTML = data.totalTransportReqs;
            }).catch((error)=>{

            })
        }
    </script>
</x-app-layout>
