@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-brand-gold bg-blue-50 ps-4 pe-4 py-2.5 text-start text-sm font-semibold text-brand-navy focus:outline-none focus:text-brand-navy focus:bg-blue-50 focus:border-brand-gold transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent ps-4 pe-4 py-2.5 text-start text-sm font-semibold text-slate-600 hover:text-brand-blue hover:bg-slate-50 hover:border-blue-200 focus:outline-none focus:text-brand-blue focus:bg-slate-50 focus:border-blue-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
