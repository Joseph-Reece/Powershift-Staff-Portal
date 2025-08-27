{{-- <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex flexx justify-center items-center px-2 py-1 bg-gray-800 border border-transparent font-semibold text-xs text-white tracking-widest hover:bg-gray-700 hover:border-red active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150']) }}>
{{ $slot }}
</button> --}}
{{-- <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-2 bg-[#52A446] border border-transparent font-semibold text-sm text-white uppercase tracking-widest hover:bg-white hover:border-[#52A446] hover:text-black focus:outline-none focus:border-[#52A446] focus:ring focus:ring-[#52A446] focus:ring-opacity-50 active:bg-white disabled:opacity-25 transition ease-in-out duration-150 rounded-full']) }}>
    {{ $slot }}
</button> --}}
{{-- <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-2 bg-theme2 border border-transparent font-semibold text-sm text-white uppercase tracking-widest hover:bg-theme1 hover:borderBgTheme2 hover:text-black focus:bg-theme1 focus:borderBgTheme2 focus:ring focus:ring-[#52A446] focus:ring-opacity-50 active:bg-theme1 active:borderBgTheme2 active:text-black disabled:opacity-25 transition ease-in-out duration-150 rounded-full']) }}>
    {{ $slot }}
</button> --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-3 py-1 btnBgTheme2  font-semibold text-sm text-white uppercase tracking-widest focus:bg-theme1 focus:border-theme2 focus:text-[#52A446] focus:ring focus:ring-[#52A446] focus:ring-opacity-50 active:bg-theme1 active:border-theme2 active:text-[#52A446] disabled:opacity-25 transition ease-in-out duration-150 rounded-full']) }}>
    {{ $slot }}
</button>
