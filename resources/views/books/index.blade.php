@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<div class="page-shell">
    <div class="page-container space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-kicker">Katalog Perpustakaan</p>
                <h1 class="page-title mt-2">Temukan Buku Pilihanmu</h1>
                <p class="page-subtitle max-w-2xl">Cari koleksi berdasarkan judul, penulis, ISBN, atau kategori yang tersedia di Perpustakaan SMP Negeri 11 Jember.</p>
            </div>
            @if(auth()->check() && auth()->user()->isAdmin())
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                    <form id="book-import-form" method="POST" action="{{ route('books.import') }}" enctype="multipart/form-data" class="w-full sm:w-auto">
                        @csrf
                        <label class="sr-only" for="excel_file">File Excel</label>
                        <input id="excel_file" type="file" name="excel_file" accept=".xlsx" required class="sr-only" data-book-import-input>
                        <button type="button" class="btn-secondary w-full border-brand-blue/20 bg-white/95 text-brand-blue hover:border-brand-gold hover:bg-amber-50 sm:w-auto" data-book-import-button>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16.5v-12m0 0L7.5 9M12 4.5 16.5 9M4.5 19.5h15" />
                            </svg>
                            <span data-book-import-label>Import Excel</span>
                        </button>
                    </form>

                    <a href="{{ route('books.create') }}" class="btn-primary w-full sm:w-auto">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Buku
                    </a>
                </div>
            @endif
        </div>

        @include('partials._errors')

        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <p class="font-semibold">Beberapa baris tidak dapat diimport:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="surface p-4 sm:p-5">
            <form method="GET" action="{{ route('books.index') }}" class="grid gap-3 lg:grid-cols-[1fr_220px_auto_auto]">
                <label class="sr-only" for="search">Cari buku</label>
                <input id="search" type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari judul, penulis, ISBN, atau kode buku..."
                    class="field-control">

                <label class="sr-only" for="category">Kategori</label>
                <select id="category" name="category" class="field-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Cari
                </button>

                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('books.index') }}" class="btn-secondary">Reset</a>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($books as $book)
                <article class="group flex min-h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                    <a href="{{ route('books.show', $book) }}" class="block">
                        <div class="relative aspect-[3/4] overflow-hidden bg-brand-soft">
                            @if($book->cover_image)
                                <img src="{{ Storage::disk('public')->url($book->cover_image) }}" alt="{{ $book->title }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full flex-col items-center justify-center gap-3 text-brand-blue">
                                    <img src="{{ asset('images/smp-11-logo.png') }}" alt="" class="h-16 w-16 object-contain opacity-80">
                                    <span class="px-4 text-center text-xs font-semibold uppercase tracking-widest text-slate-400">Perpustakaan</span>
                                </div>
                            @endif

                            <div class="absolute left-2 top-2">
                                @if($book->available_copies > 0)
                                    <span class="status-pill bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Tersedia</span>
                                @else
                                    <span class="status-pill bg-red-50 text-red-700 ring-1 ring-red-200">Habis</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <div class="flex flex-1 flex-col p-3 sm:p-4">
                        <a href="{{ route('books.show', $book) }}" class="group/link">
                            <h3 class="line-clamp-2 text-sm font-bold leading-5 text-slate-900 transition group-hover/link:text-brand-blue">
                                {{ $book->title }}
                            </h3>
                        </a>
                        <p class="mt-1 truncate text-xs font-medium text-slate-500">{{ $book->author }}</p>

                        @if($book->category)
                            <p class="mt-2 inline-flex w-fit rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-brand-blue">
                                {{ $book->category->name }}
                            </p>
                        @endif

                        @if($book->book_code)
                            <p class="mt-2 text-xs font-semibold text-slate-500">Kode: {{ $book->book_code }}</p>
                        @endif

                        <div class="mt-auto flex items-center justify-between gap-2 pt-4">
                            <span class="text-xs font-semibold text-slate-400">{{ $book->year ?? 'Tanpa tahun' }}</span>
                            <div class="flex flex-wrap justify-end gap-2">
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <a href="{{ route('books.barcode.print', $book) }}" target="_blank" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:border-brand-blue hover:text-brand-blue">
                                        Cetak Barcode
                                    </a>
                                @endif

                                @auth
                                    @if($book->isAvailable() && !$book->loans->where('user_id', auth()->id())->whereIn('status', ['pending', 'approved'])->count())
                                        <form method="POST" action="{{ route('loans.store') }}">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <button type="submit" class="rounded-lg bg-brand-blue px-3 py-1.5 text-xs font-bold text-white transition hover:bg-brand-navy">
                                                Pinjam
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state col-span-full">
                    Buku tidak ditemukan. Coba ubah kata kunci atau kategori pencarian.
                </div>
            @endforelse
        </div>

        <div>
            @include('partials._pagination', ['paginator' => $books, 'label' => 'buku'])
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('book-import-form');

            if (!form) {
                return;
            }

            const input = form.querySelector('[data-book-import-input]');
            const button = form.querySelector('[data-book-import-button]');
            const label = form.querySelector('[data-book-import-label]');

            button.addEventListener('click', () => input.click());

            input.addEventListener('change', () => {
                if (!input.files.length) {
                    return;
                }

                label.textContent = 'Mengimport...';
                button.disabled = true;

                if (form.requestSubmit) {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            });
        });
    </script>
@endpush
