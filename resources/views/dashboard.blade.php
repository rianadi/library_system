@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $availabilityRate = $stats['total_copies'] > 0
        ? min(100, round(($stats['available_books'] / $stats['total_copies']) * 100))
        : 0;

    $statusStyles = [
        'approved' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'returned' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
        'rejected' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
        'overdue' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
    ];
@endphp

<div class="page-shell">
    <div class="page-container space-y-6">
        <section class="surface overflow-hidden">
            <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[1.35fr_0.65fr] lg:p-8">
                <div class="flex flex-col justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <img src="{{ asset('images/smp-11-logo.png') }}" alt="Logo SMP Negeri 11 Jember" class="h-16 w-16 rounded-lg bg-white object-contain p-2 shadow-sm ring-1 ring-slate-200">
                        <div>
                            <p class="page-kicker">Dashboard Perpustakaan</p>
                            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                                Selamat datang, {{ Auth::user()->name }}.
                            </h1>
                            <p class="page-subtitle max-w-2xl">
                                {{ now()->translatedFormat('l, d F Y') }}. Pantau koleksi, peminjaman, dan aktivitas anggota dalam satu tampilan yang ringkas.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('books.index') }}" class="btn-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Jelajahi Katalog
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('loans.index') }}" class="btn-secondary">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2" />
                                </svg>
                                Kelola Peminjaman
                            </a>
                        @else
                            <a href="{{ route('loans.my') }}" class="btn-secondary">Lihat Pinjaman Saya</a>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-5 lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Ketersediaan Koleksi</p>
                            <p class="mt-1 text-3xl font-extrabold text-brand-navy">{{ $availabilityRate }}%</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-5 h-2.5 rounded-full bg-slate-200">
                        <div class="h-2.5 rounded-full bg-brand-blue" style="width: {{ $availabilityRate }}%"></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="font-bold text-slate-950">{{ number_format($stats['available_books']) }}</p>
                            <p class="text-slate-500">Salinan tersedia</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-950">{{ number_format($stats['total_copies']) }}</p>
                            <p class="text-slate-500">Total salinan</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Total Buku</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ number_format($stats['total_books']) }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-brand-blue">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Kategori</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ number_format($stats['category_count']) }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3 text-amber-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Anggota</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ number_format($stats['total_members']) }}</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Dipinjam</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ number_format($stats['active_loans']) }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75Zm0 5.25h.008v.008H3.75V12Zm0 5.25h.008v.008H3.75v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Menunggu</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ number_format($stats['pending_loans']) }}</p>
                    </div>
                    <div class="rounded-lg bg-rose-50 p-3 text-rose-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        @if($stats['overdue_loans'] > 0)
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                Ada {{ $stats['overdue_loans'] }} peminjaman aktif yang melewati tenggat.
            </div>
        @endif

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="surface">
                <div class="panel-header flex items-center justify-between gap-3">
                    <div>
                        <p class="page-kicker">Aktivitas</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-950">Peminjaman Terbaru</h2>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('loans.index') }}" class="text-sm font-semibold text-brand-blue hover:text-brand-navy">Lihat semua</a>
                    @endif
                </div>
                <div class="panel-body">
                    <div class="space-y-3">
                        @forelse($recentLoans as $loan)
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $loan->book->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $loan->user->name }} - {{ $loan->loan_date->format('d M Y') }}</p>
                                </div>
                                <span class="status-pill shrink-0 {{ $statusStyles[$loan->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-200' }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </div>
                        @empty
                            <div class="empty-state">Belum ada peminjaman.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="surface">
                <div class="panel-header">
                    <p class="page-kicker">Koleksi</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-950">Buku Terpopuler</h2>
                </div>
                <div class="panel-body">
                    <div class="space-y-3">
                        @forelse($popularBooks as $book)
                            <a href="{{ route('books.show', $book) }}" class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3 transition last:border-0 last:pb-0 hover:text-brand-blue">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $book->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $book->author }}</p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-brand-blue">
                                    {{ $book->loans_count }}x
                                </span>
                            </a>
                        @empty
                            <div class="empty-state">Belum ada buku populer.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="surface">
            <div class="panel-header flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="page-kicker">Masuk Terbaru</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-950">Koleksi yang Baru Ditambahkan</h2>
                </div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('books.create') }}" class="btn-secondary px-3 py-2">Tambah Buku</a>
                @endif
            </div>
            <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-6 lg:grid-cols-4">
                @forelse($latestBooks as $book)
                    <a href="{{ route('books.show', $book) }}" class="group rounded-lg border border-slate-200 bg-white p-3 transition hover:border-blue-200 hover:shadow-sm">
                        <div class="aspect-[3/4] overflow-hidden rounded-lg bg-brand-soft">
                            @if($book->cover_image)
                                <img src="{{ Storage::disk('public')->url($book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-brand-blue">
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <p class="mt-3 line-clamp-2 font-semibold text-slate-900 group-hover:text-brand-blue">{{ $book->title }}</p>
                        <p class="mt-1 truncate text-sm text-slate-500">{{ $book->author }}</p>
                    </a>
                @empty
                    <div class="empty-state sm:col-span-2 lg:col-span-4">Belum ada buku yang ditambahkan.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
