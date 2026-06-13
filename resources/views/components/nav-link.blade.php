@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex h-16 items-center border-b-2 border-brand-gold px-1 text-sm font-semibold leading-5 text-brand-navy focus:outline-none focus:border-brand-gold transition duration-150 ease-in-out'
            : 'inline-flex h-16 items-center border-b-2 border-transparent px-1 text-sm font-semibold leading-5 text-slate-500 hover:text-brand-blue hover:border-blue-200 focus:outline-none focus:text-brand-blue focus:border-blue-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
