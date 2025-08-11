<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel maxWidth="max-w-lg">
            <x-slot name="title">Reset Password</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{route('password.change')}}" class="text-black w-full" data-turbo-frame="_top">
                 @csrf
                    <x-grid cols="">
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Current Password</x-slot>
                                <x-slot name="value">
                                    <x-input :type="'password'" :name="'currentPassword'" :id="'currentPassword'"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">New Password</x-slot>
                                <x-slot name="value">
                                    <x-input :type="'password'" :name="'newPassword'" :id="'newPassword'"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Confirm Password</x-slot>
                                <x-slot name="value">
                                    <x-input :type="'password'" :name="'confirmPassword'" :id="'confirmPassword'"/>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="p-2 flex justify-center">
                        <x-jet-button type="submit" class="rounded-full bg-blue-800" data-turbo="false">Submit</x-jet-button>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
</x-app-layout>
