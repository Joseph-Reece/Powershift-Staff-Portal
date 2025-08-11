<textarea  {{ $attributes->merge(['class' => "shadow appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:opacity-40 "])}} {{!isset($required) || isset($required) && $required? 'required':''}}>
    {{$slot}}
</textarea>
