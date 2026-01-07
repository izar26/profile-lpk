@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])

<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <input type="{{ $type }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"
           class="w-full rounded-lg border-gray-300 focus:border-gold-500 focus:ring-gold-500 text-gray-700 font-medium disabled:bg-gray-100 disabled:text-gray-500 transition-colors duration-200"
           {{ $required ? 'required' : '' }}
           {{ $attributes }}
    >
    @error($name) <p class="text-red-500 text-xs mt-1 font-bold animate-pulse">{{ $message }}</p> @enderror
</div>
