<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex flexx justify-center items-center px-2 py-1 bg-gray-800 border border-transparent font-semibold text-xs text-white tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150']) }}>
{{ $slot }}
</button>
