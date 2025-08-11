<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{isset($title) && $title != ''? $title.' - ':''}}{{config('app.name')}} - {{config('app.company')['name']}}</title>
        <meta name="description" content="{{config('app.metaDescription')}}" />
        <meta name="keywords" content="{{config('app.metaKeywords')}}"/>
        <meta name="author" content="{{config('app.company')['name']}}" />
        <!-- Fonts -->
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> --}}
        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="icon" href="/storage/general/logo.png" type="image/png">
        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        {{-- <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script> --}}
        @livewireStyles
        @yield('styles')
    </head>
    <body class="overflow-x-hidden font-sans" data-turbo="false">
        @include('inc.general.notification')
        @if(session('authUser') == null)
            <div id="app" data-turbo="false">
                <!--navbar-->
                <div class="flex">
                    <!--main-->
                    <main class="font-sans text-gray-900 antialiased whitespace-pre-linex container" id="mainSection">
                        <div class="w-screen">
                            <div class="min-h-screen overflow-hiddenx">
                                @include('inc.general.notification')
                                <turbo-frame id="tfMain">
                                    <div class="block sm:hidden w-full justify-center items-center pb-2">
                                        <h3 class="ml-3 text-sm sm:text-md md:text-lg font-semibold underline text-center"><turbo-frame id="tfNavbar">{{isset($title) && $title != null? $title:''}}</turbo-frame></h3>
                                    </div>
                                    {{ $slot }}
                                </turbo-frame>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        @else
            <div id="app" class="bg-gray-100" data-turbo="false">
                <!--navbar-->
                <header class="z-50">
                    @include('inc.general.navbar')
                </header>
                <div class="relative sm:flex">
                    <!--sidebar-->
                    @if(session('authUser')['userCategory'] == 'staff')
                        <section id="sidebar" class="w-60 sm:w-50 hidden sm:block absolute sm:relative" >
                            @include('inc.staff.sidebar')
                        </section>
                    @else
                        <section id="sidebar" class="w-60 sm:w-50 hidden sm:block absolute sm:relative" >
                            @include('inc.farmer.sidebar')
                        </section>
                    @endif
                    <!--main-->
                    <main class="font-sans text-gray-900 antialiased pt-2 whitespacex-pre-line container {{\Route::currentRouteName() == 'home'? 'mt-10':'mt-12'}}" id="mainSection">
                        <div class="pl-2 pt-0 pr-1">
                            <div class="min-h-screen overflow-hidden w-full">
                                @include('inc.general.notification')
                                <turbo-frame id="tfMain">
                                    <div class="block sm:hidden w-full justify-center items-center pb-2">
                                        <h3 class="ml-3 text-sm sm:text-md  md:text-lg font-semibold underline text-center"><turbo-frame id="tfNavbar">{{isset($title) && $title != null? $title:''}}</turbo-frame></h3>
                                    </div>
                                    {{ $slot }}
                                </turbo-frame>
                            </div>
                            <footer class="mt-4">
                                @include('inc.general.footer')
                            </footer>
                        </div>
                    </main>
                </div>
            </div>
        @endif

        @livewireScripts
        {{-- <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false" data-turbo-eval="false"></script> --}}
        <script>
            var isLoggedIn = "<?php echo session('authUser') == null? false:true;?>";
            window.onload = function(){
                if (typeof fnOnLoad === "function")
                {
                    fnOnLoad();
                }
                if(isLoggedIn == true){
                    getApprovalsCount();
                }
            }
            function toggleSidebar(){
                var sidebar = document.getElementById('sidebar');
                var btnSidebar = document.getElementById('btnSidebar');
                var windowWidth = window.innerWidth;
                if(windowWidth < 640){
                    sidebar.classList.toggle("hidden");
                }else{
                    sidebar.classList.toggle("sm:block");
                    if(sidebar.classList.contains("sm:block") && !sidebar.classList.contains("hidden")){
                        sidebar.classList.add("hidden");
                        btnSidebar.click();
                    }
                }
            }
            function getApprovalsCount(){
                var elTotalPendingApproval = document.getElementById('totalPendingApproval');
                    axios.get('/staff/approval/approvals-count/Pending/Open').then(response =>{
                        var data = response.data;
                        if(data != null && typeof data !== undefined){
                            elTotalPendingApproval.innerHTML = data.totalAll;
                            if(data.totalAll > 0 && data.isNotified == false)
                            {
                                alert('You have '+data.totalAll+' documents pending your approval. Kindly navigate to the approvals section for more details.');
                            }
                        }
                    }).catch((error) => {
                    });
            }
            function loadEditor(id){
                var instance = CKEDITOR.instances[id];
                if(instance){
                    CKEDITOR.destroy(instance);
                }
                var editor = CKEDITOR.replace(id);
            }

            function onSubmit(token) {
                document.getElementById("frmG_Captcha").submit();
            }
        </script>
        @stack('scripts')
    </body>
</html>
