@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="page-shell">
    <div class="page-container-narrow space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-kicker">Profil Pengguna</p>
                <h1 class="page-title mt-2">{{ $user->name }}</h1>
                <p class="page-subtitle">{{ $user->email }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn-secondary">Kembali</a>
        </div>

        <div class="surface p-5 sm:p-6">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Role</dt>
                    <dd class="mt-1">
                        <span class="status-pill {{ $user->role == 'admin' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'bg-blue-50 text-brand-blue ring-1 ring-blue-200' }}">
                            {{ $user->role == 'admin' ? 'Admin' : 'Siswa' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Telepon</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $user->phone ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Alamat</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $user->address ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Terdaftar</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="surface overflow-hidden">
            <div class="panel-header">
                <h2 class="text-lg font-bold text-slate-950">Riwayat Peminjaman Terakhir</h2>
            </div>
            <div class="panel-body">
                @if($user->loans->count())
                    <div class="space-y-3">
                        @foreach($user->loans->take(10) as $loan)
                            <div class="flex flex-col gap-2 border-b border-slate-100 pb-3 last:border-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $loan->book->title }}</p>
                                    <p class="text-sm text-slate-500">{{ $loan->loan_date->format('d/m/Y') }}</p>
                                </div>
                                <span class="status-pill {{ $loan->status == 'returned' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Belum ada peminjaman.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
