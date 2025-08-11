<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-jet-authentication-card>
        <x-slot name="title">Confirm Password</x-slot>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
            <div class="text-center"></div>
        </x-slot>
        <form method="POST" id="frmG_Captcha" action="{{ route('password.confirm') }}">
            @csrf
            <!-- Password -->
            <div>
                <x-jet-label for="password" :value="__('Password')" />

                <x-jet-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password"/>
            </div>
            <div class="flex justify-end mt-4">
                <x-jet-button class="w-full rounded-full uppercase bg-theme2 g-recaptcha"
                    data-sitekey="{{config('app.googleCaptchaKey')}}"
                    data-callback='onSubmit'
                    data-action='submit'
                >
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-jet-authentication-card>
    @section('scripts')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endsection
</x-app-layout>
