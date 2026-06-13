@extends('layouts.app')

@section('title', 'Kelola Peminjaman')

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
                <p class="page-kicker">Sirkulasi</p>
                <h1 class="page-title mt-2">Kelola Peminjaman</h1>
                <p class="page-subtitle">Pantau pengajuan, persetujuan, pengembalian, dan denda buku.</p>
            </div>
            <a href="{{ route('loans.print', request()->query()) }}" target="_blank" class="btn-muted">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 9V4.5h10.5V9m-10.5 6.75H5.25A2.25 2.25 0 0 1 3 13.5v-3A2.25 2.25 0 0 1 5.25 8.25h13.5A2.25 2.25 0 0 1 21 10.5v3a2.25 2.25 0 0 1-2.25 2.25h-1.5m-10.5 0v3.75h10.5v-3.75H6.75Z" />
                </svg>
                Cetak / PDF
            </a>
        </div>

        <div class="surface p-4 sm:p-5">
            <form method="GET" action="{{ route('loans.index') }}" class="grid gap-3 lg:grid-cols-[220px_1fr_auto_auto]">
                <select name="status" class="field-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau judul buku..." class="field-control">
                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->anyFilled(['status', 'search']))
                    <a href="{{ route('loans.index') }}" class="btn-secondary">Reset</a>
                @endif
            </form>
        </div>

        <div class="surface overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $loan->user->email }}</div>
                                </td>
                                <td class="font-medium text-slate-900">{{ $loan->book->title }}</td>
                                <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td>
                                    {{ $loan->due_date->format('d/m/Y') }}
                                    @if($loan->isOverdue())
                                        <span class="mt-1 block text-xs font-bold text-red-600">Terlambat</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-pill {{ $statusStyles[$loan->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-200' }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        @if($loan->status === 'pending')
                                            <form method="POST" action="{{ route('loans.approve', $loan) }}">
                                                @csrf
                                                <button class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('loans.reject', $loan) }}">
                                                @csrf
                                                <button class="text-xs font-bold text-red-600 hover:text-red-700">Tolak</button>
                                            </form>
                                        @elseif(in_array($loan->status, ['approved', 'overdue']))
                                            <form method="POST" action="{{ route('loans.return', $loan) }}">
                                                @csrf
                                                <button class="text-xs font-bold text-brand-blue hover:text-brand-navy">Kembalikan</button>
                                            </form>
                                            @if($loan->status === 'approved')
                                                <form method="POST" action="{{ route('loans.extend', $loan) }}">
                                                    @csrf
                                                    <button class="text-xs font-bold text-amber-700 hover:text-amber-800">Perpanjang</button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400">Selesai</span>
                                        @endif
                                    </div>
                                    @if($loan->fine_amount > 0)
                                        <p class="mt-1 text-xs font-bold text-red-600">Denda: Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-slate-500">Tidak ada data peminjaman.</td>
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
