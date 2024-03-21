@props(['value'])

{{-- <label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label> --}}

{{-- <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del artículo</label> --}}

<label {{ $attributes->merge(['class' => 'block mb-1 text-sm font-medium text-gray-900 dark:text-white']) }}>
    {{ $value ?? $slot }}
</label> 