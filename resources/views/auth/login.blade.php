<x-app-layout class="">
    <x-slot name="title"></x-slot>
        <x-jet-authentication-card>
            <x-slot name="title">STAFF PORTAL LOGIN</x-slot>
            <x-slot name="logo">
                <x-jet-authentication-card-logo />
                <div class="text-center">
                    {{-- <h5 class="font-bold">{{config('app.name')}}</h5> --}}
                    {{-- <h5 class="font-bold italic">{{config('app.companyName')}}</h5> --}}
                </div>
            </x-slot>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" id="frmG_Captcha" action="{{ route('login') }}" class="text-blackx" data-turbo-frame="_top">
                @csrf
                <input type="hidden" name="userCategory" value="staff">
                <div>
                    <x-jet-label for="staffNo" value="Staff No." textColor="text-white font-bold"/>
                    <x-jet-input id="staffNo" class="block mt-1 w-full borderBgTheme2" type="text" name="staffNo" :value="old('staffNo')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-jet-label for="password" value="{{ __('Password') }}" textColor="text-white font-bold"/>
                    <x-jet-input id="password" class="block mt-1 w-full borderBgTheme2" type="password" name="password" required autocomplete="current-password" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-jet-checkbox id="remember_me" name="remember" />
                        <span class="ml-2 text-sm text-white">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-center mt-4" data-turbo="false">
                    @if(\App::environment('local'))
                        <x-jet-button class="w-full rounded-full uppercase bg-theme2 btnBgTheme2 g-recaptchax">
                            {{ __('Login') }}
                        </x-jet-button>
                    @else
                        <x-jet-button class="w-full rounded-full uppercase bg-theme2 btnBgTheme2 g-recaptcha"
                            data-sitekey="{{config('app.googleCaptchaKey')}}"
                            data-callback='onSubmit'
                            data-action='submit'
                        >
                            {{ __('Login') }}
                        </x-jet-button>
                    @endif
                </div>
            </form>
            @if (Route::has('password.request'))
                <div class="text-center mt-2 flex justify-center">
                    <a class="underline text-white" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            @endif
        </x-jet-authentication-card>
    {{-- @section('scripts')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endsection --}}
</x-app-layout>
