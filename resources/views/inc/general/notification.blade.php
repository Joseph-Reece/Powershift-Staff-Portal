{{-- <div class="text-center text-white fixed top-0 z-50 overflow-hidden opacity-100 flexx left-1/2 items-center justifyx-center justify-items-center"> --}}
<div class="text-center text-white fixed top-0 left-0 right-0 z-50 overflow-hidden bg-grayx-700 opacity-100 flex flex-row w-full items-center justify-center">
    <div>
        @if(session("success"))
            <div class="notification bg-green-600 pl-4 pt-2 pb-2 mb-2 rounded-lg flex items-center justify-between"  x-data="{isOpen:true}" x-show="isOpen"  x-data="{isOpen:true}" x-show="isOpen" x-init="setTimeout(() => {isOpen = false;$wire.updateVariable('xCurrent','session_success')}, 10000)">
                <span class="flex items-center mr-2"><x-heroicon-o-check-circle class="mr-2"/> {{session('success')}}</span>
                <span><x-heroicon-o-x class="mr-2 cursor-pointer" @click="isOpen=!isOpen"/></span>
            </div>
        @endif
        @if(session("error"))
            <div class="notification bg-red-600 pl-4 pt-2 pb-2 mb-2 rounded-lg flex items-center justify-between" x-data="{isOpen:true}" x-show="isOpen" x-init="setTimeout(() => {isOpen = false;$wire.updateVariable('xCurrent','session_error')}, 10000)">
                <span class="flex items-center mr-2"><x-heroicon-o-exclamation-circle class="mr-2"/> {{session('error')}}</span>
                <span><x-heroicon-o-x class="mr-2 cursor-pointer" @click="isOpen=!isOpen"/></span>
            </div>
        @endif
        @if(session("info"))
            <div class="notification bg-blue-600 pl-4 pt-2 pb-2 mb-2 rounded-lg flex items-center justify-between"  x-data="{isOpen:true}" x-show="isOpen" x-init="setTimeout(() => {isOpen = false;$wire.updateVariable('xCurrent','session_info')}, 10000)">
                <span class="flex items-center mr-2"><x-heroicon-o-check-circle class="mr-2"/> {{session('info')}}</span>
                <span><x-heroicon-o-x class="mr-2 cursor-pointer" @click="isOpen=!isOpen"/></span>
            </div>
        @endif
        @if(isset($errors) && count($errors) > 0)
            <div class="notification bg-red-600 pl-4 pt-2 pb-2 mb-2 rounded-lg flex items-center justify-between" x-data="{isOpen:true}" x-show="isOpen"  x-data="{isOpen:true}" x-show="isOpen" x-init="setTimeout(() => {isOpen = false;$wire.updateVariable('xCurrent','errors')}, 10000)">
                <span class="flex items-center mr-2"><x-heroicon-o-check-circle class="mr-2"/> {{$errors->first()}}</span>
                <span><x-heroicon-o-x class="mr-2 cursor-pointer" @click="isOpen=!isOpen"/></span>
            </div>
        @endif
    </div>
</div>
