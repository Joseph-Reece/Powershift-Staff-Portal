<x-jet-authentication-card>
            <x-slot name="title">Enquiry Form</x-slot>
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" id="frmG_Captcha" action="{{ route('enquiryStore') }}" class="text-black" data-turbo-frame="_top">
                @csrf

                <div>
                    <x-jet-label for="subject" value="Subject" />
                    <select id="subject" name="subject" class="block mt-1 w-full">
                        <option value="">--select--</option>
                        @foreach($subjects as $subject)
                            <option value="{{$subject['id']}}" {{old('subject') == $subject['id']? 'selected':''}}>{{$subject['description']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-1">
                    <x-jet-label for="name" value="Your Name" />
                    <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{old('name')}}" required/>
                </div>
                <div class="mt-1">
                    <x-jet-label for="email" value="Your Email" />
                    <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{old('email')}}" required/>
                </div>
                <div class="mt-1">
                    <x-jet-label for="phoneNo" value="Your Phone Number" />
                    <x-jet-input id="phoneNo" class="block mt-1 w-full" type="number" name="phoneNo" value="{{old('phoneNo')}}" required/>
                </div>
                <div class="mt-1">
                    <x-jet-label for="message" value="Message" />
                    <textarea id="message" class="block mt-1 w-full h-20" name="message" required>{{old('message')}}</textarea>
                </div>
                <div class="flex items-center justify-center mt-2" data-turbo="false">
                    <x-jet-button class="g-recaptcha content-center w-full rounded-full uppercase bg-theme2"
                        data-sitekey="{{config('app.googleCaptchaKey')}}"
                        data-callback='onSubmit'
                        data-action='submit'>
                        Submit
                    </x-jet-button>
                </div>
            </form>
    </x-jet-authentication-card>
    @section('scripts')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endsection
