<x-app-layout>
    <x-slot name="title">{{$title}}</x-slot>
    <div>
        <x-tab-holder class="text-xs">
            <x-tab-item href="?" icon="cash" active="{{!\Request::has('docType')? true:false}}"><x-heroicon-o-view-list/> <span class="hidden md:block">All</span>&nbsp;<x-badge class="text-xs"><span id="all"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Leave Application" active="{{\Request::get('docType') == 'Leave Application'? true:false}}"><x-heroicon-o-home/> <span class="hidden md:block">Leave</span>&nbsp;<x-badge class="text-xs"><span id="leaveReqs"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Imprest" active="{{\Request::get('docType') == 'Imprest'? true:false}}"><x-heroicon-o-cash/> <span class="hidden md:block">Imprest</span> &nbsp;<x-badge class="text-xs"> <span id="imprestReqs"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=ImprestSurrender" active="{{\Request::get('docType') == 'ImprestSurrender'? true:false}}"><x-heroicon-o-receipt-refund/> <span class="hidden md:block">Surrender</span><x-badge class="text-xs"> <span id="imprestSurrReqs"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Quote" active="{{\Request::get('docType') == 'Quote'? true:false}}"><x-heroicon-o-shopping-cart/> <span class="hidden md:block">Purchase</span> &nbsp;<x-badge class="text-xs"> <span id="purchaseReqs"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Requisition" active="{{\Request::get('docType') == 'Requisition'? true:false}}"><x-heroicon-o-database/> <span class="hidden md:block">Store</span> &nbsp;<x-badge class="text-xs"> <span id="storeReqs"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Payment Voucher" active="{{\Request::get('docType') == 'Payment Voucher'? true:false}}"><x-heroicon-o-cash/> <span class="hidden md:block">PV</span> &nbsp;<x-badge class="text-xs"> <span id="pvs"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Petty Cash" active="{{\Request::get('docType') == 'Petty Cash'? true:false}}"><x-heroicon-o-cash/> <span class="hidden md:block">PC</span> &nbsp;<x-badge class="text-xs"> <span id="pc"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <x-tab-item href="?docType=Order" active="{{\Request::get('docType') == 'Order'? true:false}}"><x-heroicon-o-cash/> <span class="hidden md:block">Order</span> &nbsp;<x-badge class="text-xs"> <span id="order"><x-loading class="loaders" show/></span></x-badge></x-tab-item>
            <!-- <x-tab-item href="?docType=TransportRequest" active="{{\Request::get('docType') == 'TransportRequest'? true:false}}"><x-heroicon-o-truck/> <span class="hidden md:block">Transport</span> &nbsp;<x-badge class="text-xs"> <span id="transportReqs"><x-loading class="loaders" show/></span></x-badge></x-tab-item> -->
        </x-tab-holder>
        <x-table.table>
            <x-slot name="thead">
                <x-table.th>Document No</x-table.th>
                <!-- <x-table.th>Document Type</x-table.th> -->
                <x-table.th>Sender ID</x-table.th>
                <x-table.th>Date sent</x-table.th>
            </x-slot>
            <x-slot name="tbody">
                @if($approvals != null && count($approvals) > 0)
                    @foreach($approvals as $approval)
                        <x-table.tr isEven="{{$loop->even}}" onClick="location = '/staff/approval/view/{{$approval->Document_No}}'">
                            <x-table.td>{{$approval->Document_No}}</x-table.td>
                            <!-- <x-table.td>{{$approval->Document_Type}}</x-table.td> -->
                            <x-table.td>{{$approval->Sender_ID}}</x-table.td>
                            <x-table.td>{{$approval->Date_Time_Sent_for_Approval}}</x-table.td>
                        </x-table.tr>
                    @endforeach
                @else
                    <tr class="w-full">
                        <td colspan="3" class="text-black text-center pt-4"><em>*** No documents found ***</em></td>
                    </tr>
                @endif
            </x-slot>
        </x-table.table>
        @if($approvals != null && count($approvals) > 0)
            <x-pagination/>
        @endif
    </div>
    <script>
        var status = "<?php echo $status;?>";
        function fnOnLoad(){
            getStatistics();
        }
        function getStatistics(){
            var loaders = document.getElementsByClassName("loaders");
            var elAll = document.getElementById('all');
            var elLeave = document.getElementById('leaveReqs');
            var elImprest = document.getElementById('imprestReqs');
            var elImprestSurr = document.getElementById('imprestSurrReqs');
            var elPurchase = document.getElementById('purchaseReqs');
            var elStore = document.getElementById('storeReqs');
            // var elTransport = document.getElementById('transportReqs');
            var elPv = document.getElementById('pvs');
            var elPc = document.getElementById('pc');
            var elOrder = document.getElementById('order');
            axios.get('/staff/approval/approvals-count/all/'+status).then(response =>{
                var data = response.data;
                elAll.innerHTML = data.totalAll;
                if(data.totalAll > 0){
                    elAll.parentElement.style.backgroundColor = 'red';
                }
                elLeave.innerHTML = data.totalLeave;
                if(data.totalLeave > 0){
                    elLeave.parentElement.style.backgroundColor = 'red';
                }
                elImprest.innerHTML = data.totalImprest;
                if(data.totalImprest > 0){
                    elImprest.parentElement.style.backgroundColor = 'red';
                }
                elImprestSurr.innerHTML = data.totalImprestSurr;
                if(data.totalImprestSurr > 0){
                    elImprestSurr.parentElement.style.backgroundColor = 'red';
                }
                elPurchase.innerHTML = data.totalPurchase;
                if(data.totalPurchase > 0){
                    elPurchase.parentElement.style.backgroundColor = 'red';
                }
                elStore.innerHTML = data.totalStore;
                if(data.totalStore > 0){
                    elStore.parentElement.style.backgroundColor = 'red';
                }
                // elTransport.innerHTML = data.totalTransport;
                // if(data.totalTransport > 0){
                //     elTransport.parentElement.style.backgroundColor = 'red';
                // }
                elPv.innerHTML = data.totalPv;
                if(data.totalPv > 0){
                    elPv.parentElement.style.backgroundColor = 'red';
                }
                elPc.innerHTML = data.totalPc;
                if(data.totalPc > 0){
                    elPc.parentElement.style.backgroundColor = 'red';
                }
                elOrder.innerHTML = data.totalOrder;
                if(data.totalOrder > 0){
                    elOrder.parentElement.style.backgroundColor = 'red';
                }
            }).catch((error)=>{

            })
        }
    </script>
</x-app-layout>
