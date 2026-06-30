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
    $offlineLoanFields = ['user_id', 'book_id', 'loan_date', 'due_date', 'notes'];
    $openOfflineLoanModal = session('open_offline_loan_modal') || collect($offlineLoanFields)->contains(fn ($field) => $errors->has($field));
    $selectedOfflineBook = old('book_id')
        ? \App\Models\Book::select('id', 'book_code', 'title', 'author', 'available_copies', 'total_copies', 'location')->find(old('book_id'))
        : null;
@endphp

<div class="page-shell">
    <div class="page-container space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-kicker">Sirkulasi</p>
                <h1 class="page-title mt-2">Kelola Peminjaman</h1>
                <p class="page-subtitle">Pantau pengajuan, persetujuan, pengembalian, dan denda buku.</p>
            </div>
             <div class="flex gap-2">
                <button
                    onclick="openLoanModal()"
                    class="btn-primary">
                    + Peminjaman Offline
                </button>

                <a href="{{ route('loans.print', request()->query()) }}"
                target="_blank"
                class="btn-muted">
                    Cetak / PDF
                </a>
            </div>
        </div>

        @include('partials._errors')

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

<div
    id="loan-modal"
    data-loan-modal
    data-open-on-load="{{ $openOfflineLoanModal ? 'true' : 'false' }}"
    data-lookup-url="{{ url('/books/barcode') }}"
    class="{{ $openOfflineLoanModal ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="loan-modal-title"
    role="dialog"
    aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 py-6">
        <button type="button" class="fixed inset-0 cursor-default bg-slate-950/55" aria-label="Tutup modal" onclick="closeLoanModal()"></button>

        <div class="relative w-full max-w-3xl rounded-lg bg-white shadow-xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 id="loan-modal-title" class="text-lg font-bold text-slate-950">Peminjaman Offline</h2>
                    <p class="mt-1 text-sm text-slate-500">Catat peminjaman langsung dari meja admin.</p>
                </div>
                <button type="button" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" onclick="closeLoanModal()" aria-label="Tutup">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('loans.offline.store') }}" class="space-y-5 px-5 py-5">
                @csrf
                <input id="book_id" type="hidden" name="book_id" value="{{ old('book_id', $selectedOfflineBook?->id) }}" data-book-id>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="user_id" class="field-label">Peminjam *</label>
                        <select id="user_id" name="user_id" required class="field-control">
                            <option value="">Pilih peminjam</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ (string) old('user_id') === (string) $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} - {{ $member->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="barcode" class="field-label">Kode Buku / Barcode *</label>
                        <div class="flex gap-2">
                            <input
                                id="barcode"
                                type="text"
                                value="{{ old('book_code', $selectedOfflineBook?->book_code) }}"
                                class="field-control"
                                placeholder="Contoh: BK000001"
                                autocomplete="off"
                                data-book-code-input>
                            <button type="button" class="btn-secondary shrink-0 px-3" data-scan-button title="Scan dengan kamera laptop" aria-label="Scan dengan kamera laptop">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7V5a1 1 0 0 1 1-1h2m10 0h2a1 1 0 0 1 1 1v2M4 17v2a1 1 0 0 0 1 1h2m10 0h2a1 1 0 0 0 1-1v-2M7 12h10" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs font-medium text-slate-500" data-book-lookup-message></p>
                    </div>
                </div>

                <div class="hidden rounded-lg border border-slate-200 bg-slate-50 p-3" data-scanner-panel>
                    <div class="overflow-hidden rounded-lg bg-slate-950">
                        <div id="scanner-video-container" style="width: 100%; height: 250px; position: relative;"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <p class="text-xs font-medium text-slate-500" data-scanner-message>Kamera siap memindai barcode.</p>
                        <button type="button" class="btn-secondary px-3 py-2" data-stop-scan-button>Stop</button>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="title" class="field-label">Judul Buku</label>
                        <input id="title" type="text" value="{{ $selectedOfflineBook?->title }}" readonly class="field-control bg-slate-50" data-book-title>
                    </div>

                    <div>
                        <label for="author" class="field-label">Penulis</label>
                        <input id="author" type="text" value="{{ $selectedOfflineBook?->author }}" readonly class="field-control bg-slate-50" data-book-author>
                    </div>

                    <div>
                        <label class="field-label">Stok</label>
                        <input type="text" value="{{ $selectedOfflineBook ? $selectedOfflineBook->available_copies.' dari '.$selectedOfflineBook->total_copies.' tersedia' : '' }}" readonly class="field-control bg-slate-50" data-book-stock>
                    </div>

                    <div>
                        <label class="field-label">Lokasi Rak</label>
                        <input type="text" value="{{ $selectedOfflineBook?->location }}" readonly class="field-control bg-slate-50" data-book-location>
                    </div>

                    <div>
                        <label for="loan_date" class="field-label">Tanggal Pinjam *</label>
                        <input id="loan_date" type="date" name="loan_date" value="{{ old('loan_date', now()->toDateString()) }}" required class="field-control">
                    </div>

                    <div>
                        <label for="due_date" class="field-label">Tanggal Kembali *</label>
                        <input id="due_date" type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" required class="field-control">
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="field-label">Catatan</label>
                        <textarea id="notes" name="notes" rows="3" class="field-control">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" class="btn-secondary" onclick="closeLoanModal()">Batal</button>
                    <button type="submit" class="btn-primary" data-offline-loan-submit>Simpan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
