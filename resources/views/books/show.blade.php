@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="page-shell">
    <div class="page-container">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('books.index') }}" class="text-sm font-semibold text-brand-blue hover:text-brand-navy">
                Kembali ke katalog
            </a>
            @if(auth()->check() && auth()->user()->isAdmin())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('books.barcode.print', $book) }}" target="_blank" class="btn-secondary px-3 py-2">Cetak Barcode</a>
                    <a href="{{ route('books.edit', $book) }}" class="btn-secondary px-3 py-2">Edit Buku</a>
                </div>
            @endif
        </div>

        <article class="surface overflow-hidden">
            <div class="grid gap-8 p-5 sm:p-6 lg:grid-cols-[360px_1fr] lg:p-8">
                <div>
                    <div class="aspect-[3/4] overflow-hidden rounded-lg bg-brand-soft shadow-sm ring-1 ring-slate-200">
                        @if($book->cover_image)
                            <img src="{{ Storage::disk('public')->url($book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center gap-4 text-brand-blue">
                                <img src="{{ asset('images/smp-11-logo.png') }}" alt="" class="h-24 w-24 object-contain opacity-90">
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Perpustakaan</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($book->category)
                            <span class="status-pill bg-blue-50 text-brand-blue ring-1 ring-blue-200">{{ $book->category->name }}</span>
                        @endif
                        @if($book->isAvailable())
                            <span class="status-pill bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Tersedia</span>
                        @else
                            <span class="status-pill bg-red-50 text-red-700 ring-1 ring-red-200">Tidak tersedia</span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">{{ $book->title }}</h1>
                    <p class="mt-2 text-lg font-medium text-slate-600">oleh {{ $book->author }}</p>

                    <dl class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @if($book->book_code)
                            <div class="surface-soft p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode Buku</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $book->book_code }}</dd>
                            </div>
                        @endif
                        @if($book->isbn)
                            <div class="surface-soft p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">ISBN</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $book->isbn }}</dd>
                            </div>
                        @endif
                        @if($book->publisher)
                            <div class="surface-soft p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Penerbit</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $book->publisher }}</dd>
                            </div>
                        @endif
                        @if($book->year)
                            <div class="surface-soft p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Tahun</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $book->year }}</dd>
                            </div>
                        @endif
                        @if($book->location)
                            <div class="surface-soft p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Lokasi Rak</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $book->location }}</dd>
                            </div>
                        @endif
                        <div class="surface-soft p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Stok</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $book->available_copies }} dari {{ $book->total_copies }} eksemplar</dd>
                        </div>
                    </dl>

                    @if($book->description)
                        <div class="mt-6 border-t border-slate-200 pt-6">
                            <h2 class="text-lg font-bold text-slate-950">Deskripsi</h2>
                            <p class="mt-2 leading-relaxed text-slate-600">{{ $book->description }}</p>
                        </div>
                    @endif

                    <div class="mt-8">
                        @auth
                            @if($userLoan)
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                    Status peminjaman Anda: <strong>{{ ucfirst($userLoan->status) }}</strong>
                                    @if($userLoan->status === 'approved' && $userLoan->due_date)
                                        <span class="block sm:inline">Tenggat: {{ $userLoan->due_date->format('d M Y') }}</span>
                                    @endif
                                </div>
                            @elseif($book->isAvailable())
                                <form method="POST" action="{{ route('loans.store') }}">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <button type="submit" class="btn-primary w-full sm:w-auto">
                                        Pinjam Buku
                                    </button>
                                </form>
                            @else
                                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                                    Buku sedang tidak tersedia.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto">
                                Login untuk Meminjam
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
