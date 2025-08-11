<div>
    <nav class="bg-white fixed w-full z-50 top-0 border-b border-blue-900 h-12 flex sm:block items-center ">
        <div class="flex justify-between items-center px-1 py-0 mt-0 mb-0">
            <!--left side-->
            <div class="flex items-center">
                <button onclick="toggleSidebar()" id="btnSidebar" class="sm:mr-6 inline-flex items-center justify-center p-0 w-8 h-8 rounded-full text-black hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" title="Toggle sidebar">
                    <x-heroicon-o-menu class="h-5 w-5 rounded-full" />
                </button>
                <x-jet-application-mark/>
            </div>
            <div class="hidden sm:block">
                <h3 class="ml-3 text-xs sm:text-sm md:text-md text-black font-semibold"><turbo-frame id="tfNavbar">{{isset($title) && $title != null? $title:''}}</turbo-frame></h3>
            </div>
            <!--right side-->
            @if(session('authUser') != null)
                <div class="hidden sm:flex">
                    <x-jet-dropdown  :active="''">
                        <x-slot name="trigger">
                            <div class="flex items-center px-1">
                                @if(isset(session('authUser')['picture']) && session('authUser')['picture'] != null)
                                    <img class="h-10 w-10 rounded-full mt-0 mb-0" src="data:image/png;base64,{{session('authUser')['picture']}}" alt="photo" />
                                @else
                                    <img class="h-10 w-10 rounded-full mt-0 mb-0" src="/storage/general/avatar.jpg" alt="photo" />
                                @endif
                                <span class="pl-2 text-black text-sm">{{session('authUser')['name']}}</span>
                            </div>
                        </x-slot>
                        <x-slot name="content">
                            <x-jet-dropdown-link href="/dashboard" class="bg-green-500 border-white border-b text-white">
                                Dashboard
                            </x-jet-dropdown-link>
                            <x-jet-dropdown-link href="/logout" class="bg-green-500 text-white">
                                Logout
                            </x-jet-dropdown-link>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            @else
                <div class="">

                </div>
            @endif
        </div>
    </nav>
</div>
