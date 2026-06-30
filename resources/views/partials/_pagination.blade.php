@if ($paginator->hasPages())
    @php($elements = Illuminate\Pagination\UrlWindow::make($paginator))
    @php($label = $label ?? 'data')

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm font-medium text-slate-600">
            Menampilkan
            <span class="font-bold text-brand-navy">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-bold text-brand-navy">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-bold text-brand-navy">{{ $paginator->total() }}</span>
            {{ $label }}
        </div>

        <div class="inline-flex w-full overflow-hidden rounded-lg border border-blue-100 bg-white shadow-sm ring-1 ring-blue-50 sm:w-auto">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-11 min-w-11 items-center justify-center border-r border-blue-100 bg-slate-50 px-3 text-slate-300" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-11 min-w-11 items-center justify-center border-r border-blue-100 bg-white px-3 text-brand-blue transition hover:bg-blue-50 hover:text-brand-navy focus:z-10 focus:outline-none focus:ring-2 focus:ring-brand-gold" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
                    </svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="hidden h-11 min-w-11 items-center justify-center border-r border-blue-100 bg-white px-3 text-sm font-bold text-slate-400 sm:inline-flex" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-11 min-w-11 items-center justify-center border-r border-brand-blue bg-brand-blue px-3 text-sm font-bold text-white shadow-sm" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="hidden h-11 min-w-11 items-center justify-center border-r border-blue-100 bg-white px-3 text-sm font-bold text-brand-navy transition hover:bg-blue-50 hover:text-brand-blue focus:z-10 focus:outline-none focus:ring-2 focus:ring-brand-gold sm:inline-flex" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-11 min-w-11 items-center justify-center bg-white px-3 text-brand-blue transition hover:bg-blue-50 hover:text-brand-navy focus:z-10 focus:outline-none focus:ring-2 focus:ring-brand-gold" rel="next" aria-label="{{ __('pagination.next') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
                    </svg>
                </a>
            @else
                <span class="inline-flex h-11 min-w-11 items-center justify-center bg-slate-50 px-3 text-slate-300" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
