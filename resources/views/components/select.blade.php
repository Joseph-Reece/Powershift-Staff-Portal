{{-- @php
    if(isset($onChange)){
        $onChange = $onChange[0]."('".$onChange[1]."')";
    }
@endphp
<select {{isset($id)? 'id = '.$id:''}} {{isset($model)? 'wire:model.defer = '.$model:''}} {{isset($name)? 'name = '.$name:''}} class="cursor-pointer shadow appearance-none border w-full text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:opacity-40
{{isset($class)? $class:''}}" {{isset($properties)? $properties:''}} {{isset($required) && !$required? '':'required'}} {!!isset($onChange)? 'onChange = '.$onChange:''!!}>
    {{$slot}}
</select> --}}
<select {{isset($id)? 'id = '.$id:''}}  {{ $attributes->merge(['class' => "cursor-pointer shadow appearance-none border w-full text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:opacity-40"])}} {{!isset($required) || isset($required) && $required? 'required':''}}>{{$slot}}</select>
