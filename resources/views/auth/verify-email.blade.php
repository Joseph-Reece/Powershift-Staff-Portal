<x-app-layout>
    <x-jet-authentication-card>
        <x-slot name="title">Verify Email</x-slot>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
            <div class="text-center">
                {{-- <h5 class="font-bold">{{config('app.name')}}</h5> --}}
                {{-- <h5 class="font-bold italic">{{config('app.companyName')}}</h5> --}}
            </div>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" id="frmG_Captcha" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-jet-button class="w-full rounded-full uppercase bg-theme2 g-recaptcha"
                        data-sitekey="{{config('app.googleCaptchaKey')}}"
                        data-callback='onSubmit'
                        data-action='submit'
                    >
                        {{ __('Resend Verification Email') }}
                    </x-jet-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Log out') }}
                </button>
            </form>
        </div>
    </x-jet-authentication-card>
    @section('scripts')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endsection
</x-app-layout>
