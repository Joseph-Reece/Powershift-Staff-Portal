<x-app-layout>
    <x-slot name="title"></x-slot>
    <x-jet-authentication-card>
        <x-slot name="title">Forgot/Change Password</x-slot>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
            <div class="text-center"></div>
        </x-slot>
        <div class="mb-4 text-sm text-black">
            {{ __('Let us know your staff no. and we will Email/SMS you a password reset token that will allow you to set a new password.') }}
        </div>
        <!-- Validation Errors -->
        {{-- <x-auth-validation-errors class="mb-4" :errors="$errors" /> --}}
        <form method="POST" id="frmG_Captcha" action="{{ route('password.email') }}" data-turbo-frame="_top">
            @csrf
            <!-- Email Address -->
            <div>
                <x-jet-label for="staffNo" value="Staff No." textColor="text-black"/>

                <x-jet-input id="staffNo" class="block mt-1 w-full borderBgTheme2" type="text" name="staffNo" value="{{old('staffNo')}}" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-jet-button class="w-full rounded-full uppercase bg-theme2 btnBgTheme2">
                    Send Reset Token
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-app-layout>
