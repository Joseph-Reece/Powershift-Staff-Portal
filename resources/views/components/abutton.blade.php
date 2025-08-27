{{-- @props(['bg' => 'bg-theme2']) --}}
<a {{ $attributes->merge(['class' => "inline-flex flexx btnBgTheme2  rounded-full justify-center items-center px-2 py-1 border border-transparent font-semibold text-xs text-white tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 "])}}>{{$slot}}</a>
