@props(['name', 'label', 'type' => 'text', 'required' => false, 'index' => null, 'value' => null])
@php($fieldName = $index !== null ? "bonsais[{$index}][{$name}]" : $name)
@php($fieldId = $index !== null ? "bonsais-{$index}-{$name}" : $name)
<div>
    <label for="{{ $fieldId }}" class="mb-2 block text-sm font-medium">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
    <input id="{{ $fieldId }}" name="{{ $fieldName }}" type="{{ $type }}" value="{{ $value ?? old($name) }}" @required($required) {{ $attributes->merge(['class' => 'w-full rounded-xl border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-emerald-600']) }}>
    @error($index !== null ? "bonsais.{$index}.{$name}" : $name)<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
</div>
