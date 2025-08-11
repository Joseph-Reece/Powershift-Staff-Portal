<x-app-layout>
    <x-slot name="title"></x-slot>
    {{-- <div class="flex items-center justify-center min-h-screen bg-indigo-500 bg-fixed bg-transparent opacity-25  bg-cover bg-bottom error-bg"
	style="background-image: url('storage/general/royal.jpg')">
	<div class="container z-50">
		<div class="row">
			<div class="col-sm-8 offset-sm-2 text-gray-50 text-center -mt-20">
				<h5 class="text-gray-800 font-bold italic -mr-10 -mt-3 text-xs md:text-md lg:text-2xl">Welcome to {{config('app.company')['name']}}'s Farmer & Staff Portal</h5>
				<div class="flex justify-center gap-4 mt-4">
                    <x-abutton href="/farmer/login" class="sm:text-xl sm:px-6" bg="bg-green-600">Farmer</x-abutton>
                    <x-abutton href="/login" class="sm:text-xl sm:px-6" bg="bg-yellow-500">Staff</x-abutton>
                </div>
			</div>
		</div>
	</div>
</div> --}}
<div class="relative w-screen">
  <img src="storage/general/royal.jpg" alt="royal" class="opacity-25 w-full max-h-screen min-h-screen">
  {{-- <div class="opacity-25 w-full max-h-screen min-h-screen"></div> --}}
  <div class="top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex justify-center text-center items-center opacityx-0 absolute text-black w-full">
  		<div class="px-2 flex flex-col justify-center">
            <img src="/storage/general/logo.jpg" class="" alt="{{config('app.company')['name']}}" style="width:100%; max-width:100%; padding-right:2px; padding-left:2px;">
            <div class="flex justify-center gap-4 mt-4 sm:mt-6 flex-grow">
                <x-abutton href="/login" class="text-md xs:text-lg sm:text-xl px-4 sm:px-6" bg="bg-yellow-500"><x-heroicon-o-briefcase class="mr-2"/> Staff Login</x-abutton>
                <x-abutton href="/farmer/login" class="text-md xs:text-lg sm:text-xl px-4 sm:px-6" bg="bg-green-600"><x-heroicon-o-archive class="mr-2"/> Farmer Login</x-abutton>
            </div>
        </div>
  </div>
</div>

</x-app-layout>
