@extends('layouts.app')

@section('title', 'Pinjaman Saya')

@section('content')
@php
    $statusStyles = [
        'approved' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'rejected' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
        'returned' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
        'overdue' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
    ];
@endphp

<div class="page-shell">
    <div class="page-container space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-kicker">Aktivitas Saya</p>
                <h1 class="page-title mt-2">Riwayat Peminjaman</h1>
                <p class="page-subtitle">Lihat status buku yang sedang diajukan, dipinjam, atau sudah dikembalikan.</p>
            </div>
            <a href="{{ route('books.index') }}" class="btn-primary">Cari Buku</a>
        </div>

        <div class="surface overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $loan->book->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $loan->book->author }}</div>
                                </td>
                                <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="status-pill {{ $statusStyles[$loan->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-200' }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($loan->fine_amount > 0)
                                        <span class="font-bold text-red-600">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate-500">Belum ada riwayat peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 p-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
