@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="page-shell" x-data="{ addOpen: false, editOpen: false, user: { id: null, name: '', email: '', role: 'member', phone: '', address: '' } }">
    <div class="page-container space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-kicker">Administrasi</p>
                <h1 class="page-title mt-2">Data Pengguna</h1>
                <p class="page-subtitle">Kelola akun admin dan anggota perpustakaan dalam satu daftar.</p>
            </div>
            <button type="button" @click="addOpen = !addOpen" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Pengguna
            </button>
        </div>

        <div x-show="addOpen" x-cloak class="surface p-4 sm:p-5">
            <form method="POST" action="{{ route('users.store') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="field-label">Nama</label>
                    <input type="text" name="name" required class="field-control">
                </div>
                <div>
                    <label class="field-label">Email</label>
                    <input type="email" name="email" required class="field-control">
                </div>
                <div>
                    <label class="field-label">Password</label>
                    <input type="password" name="password" required class="field-control">
                </div>
                <div>
                    <label class="field-label">Role</label>
                    <select name="role" class="field-control">
                        <option value="member">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Telepon</label>
                    <input type="text" name="phone" class="field-control">
                </div>
                <div>
                    <label class="field-label">Alamat</label>
                    <input type="text" name="address" class="field-control">
                </div>
                <div class="md:col-span-2 flex flex-col-reverse gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" @click="addOpen = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>

        <div class="surface p-4 sm:p-5">
            <form method="GET" class="grid gap-3 lg:grid-cols-[220px_1fr_auto_auto]">
                <select name="role" class="field-control">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Siswa</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email" class="field-control">
                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->anyFilled(['role', 'search']))
                    <a href="{{ route('users.index') }}" class="btn-secondary">Reset</a>
                @endif
            </form>
        </div>

        <div class="surface overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Pinjaman</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $listedUser)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $listedUser->name }}</td>
                                <td>{{ $listedUser->email }}</td>
                                <td>
                                    <span class="status-pill {{ $listedUser->role == 'admin' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'bg-blue-50 text-brand-blue ring-1 ring-blue-200' }}">
                                        {{ $listedUser->role == 'admin' ? 'Admin' : 'Siswa' }}
                                    </span>
                                </td>
                                <td>{{ $listedUser->loans_count }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('users.show', $listedUser) }}" class="text-xs font-bold text-brand-blue hover:text-brand-navy">Detail</a>
                                        <button
                                            type="button"
                                            @click="user = { id: @js($listedUser->id), name: @js($listedUser->name), email: @js($listedUser->email), role: @js($listedUser->role), phone: @js($listedUser->phone ?? ''), address: @js($listedUser->address ?? '') }; editOpen = true"
                                            class="text-xs font-bold text-amber-700 hover:text-amber-800"
                                        >
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('users.destroy', $listedUser) }}" onsubmit="return confirm('Yakin hapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-bold text-red-600 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-40" {{ $listedUser->id == auth()->id() ? 'disabled' : '' }}>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate-500">Tidak ada pengguna yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 p-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div @click.away="editOpen = false" class="w-full max-w-lg rounded-lg bg-white p-5 shadow-xl">
            <div class="mb-4">
                <p class="page-kicker">Edit Pengguna</p>
                <h2 class="mt-1 text-xl font-bold text-slate-950" x-text="user.name"></h2>
            </div>
            <form method="POST" :action="'/users/' + user.id" class="grid gap-4 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <label class="field-label">Nama</label>
                    <input x-model="user.name" name="name" required class="field-control">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label">Email</label>
                    <input x-model="user.email" type="email" name="email" required class="field-control">
                </div>
                <div>
                    <label class="field-label">Role</label>
                    <select x-model="user.role" name="role" class="field-control">
                        <option value="member">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Password Baru</label>
                    <input type="password" name="password" class="field-control" placeholder="Opsional">
                </div>
                <div>
                    <label class="field-label">Telepon</label>
                    <input x-model="user.phone" name="phone" class="field-control">
                </div>
                <div>
                    <label class="field-label">Alamat</label>
                    <input x-model="user.address" name="address" class="field-control">
                </div>
                <div class="sm:col-span-2 flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button type="button" @click="editOpen = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
