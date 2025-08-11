<x-app-layout>
    <x-slot name="title"></x-slot>
        <x-jet-authentication-card>
            <x-slot name="title">REGISTRATION FORM</x-slot>
            <x-slot name="logo">
                <x-jet-authentication-card-logo />
                <div class="text-center">
                    {{-- <h5 class="font-bold">{{config('app.name')}}</h5> --}}
                    {{-- <h5 class="font-bold italic">{{config('app.companyName')}}</h5> --}}
                </div>
            </x-slot>
            {{-- <x-jet-validation-errors class="mb-4" /> --}}

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" id="frmG_Captcha" action="{{ route('register') }}" class="text-black" data-turbo-frame="_top">
                @csrf
                <div>
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>
                <div>
                    <x-jet-label for="name" value="Name" />
                    <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                </div>
                <div class="mt-4">
                    <x-jet-label for="password" value="Password" />
                    <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password"/>
                </div>
                <div class="mt-4">
                    <x-jet-label for="password_confirmation" value="Confirm Password" />
                    <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>
                <div class="flex items-center justify-center mt-4">
                    <x-jet-button class="w-full uppercase rounded-full bg-theme2 g-recaptcha"
                        data-sitekey="{{config('app.googleCaptchaKey')}}"
                        data-callback='onSubmit'
                        data-action='submit'
                    >
                        Register
                    </x-jet-button>
                </div>
                <div class="text-center mt-2 flex justify-between">
                        <a href="/login" class="underline py-1 px-2 text-red-500">
                            Login?
                        </a>
                    </div>
            </form>
        </x-jet-authentication-card>
    @section('scripts')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endsection
</x-app-layout>
