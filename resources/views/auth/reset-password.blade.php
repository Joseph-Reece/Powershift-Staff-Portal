<x-app-layout>
    <x-jet-authentication-card>
        <x-slot name="title">Reset Password</x-slot>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
            <div class="text-center">
                {{-- <h5 class="font-bold">{{config('app.name')}}</h5> --}}
                {{-- <h5 class="font-bold italic">{{config('app.companyName')}}</h5> --}}
            </div>
        </x-slot>
        <!-- Validation Errors -->
        {{-- <x-auth-validation-errors class="mb-4" :errors="$errors" /> --}}

        <form method="POST" action="{{ route('password.update') }}"  data-turbo-frame="_top">
            @csrf
            <!-- Password Reset Token -->
            <input type="hidden" name="staffNo" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-jet-label for="resetToken" value="Reset Token" textColor="text-white"/>

                <x-jet-input id="resetToken" class="block mt-1 w-full text-black borderBgTheme2" type="text" name="resetToken" value="{{old('resetToken')}}" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-jet-label for="password" value="New Password (At least 8 characters)" textColor="text-white"/>

                <x-jet-input id="password" class="block mt-1 w-full text-black borderBgTheme2" type="password" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-jet-label for="password_confirmation" :value="__('Confirm Password')" textColor="text-white"/>

                <x-jet-input id="password_confirmation" class="block mt-1 w-full text-black borderBgTheme2"
                                    type="password"
                                    name="password_confirmation" required />
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-jet-button class="w-full rounded-full uppercase bg-theme1 btnBgTheme2 btnBgTheme2">
                    {{ __('Reset Password') }}
                </x-jet-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
