@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="page-shell" x-data="{ addOpen: false, editOpen: false, category: { id: null, name: '', description: '' } }">
    <div class="page-container space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-kicker">Manajemen Koleksi</p>
                <h1 class="page-title mt-2">Kategori Buku</h1>
                <p class="page-subtitle">Kelompokkan koleksi agar pencarian buku lebih cepat dan rapi.</p>
            </div>
            <button type="button" @click="addOpen = !addOpen" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Kategori
            </button>
        </div>

        <div x-show="addOpen" x-cloak class="surface p-4 sm:p-5">
            <form method="POST" action="{{ route('categories.store') }}" class="grid gap-3 lg:grid-cols-[1fr_1fr_auto]">
                @csrf
                <input type="text" name="name" placeholder="Nama kategori" required class="field-control">
                <input type="text" name="description" placeholder="Deskripsi singkat (opsional)" class="field-control">
                <button type="submit" class="btn-primary">Simpan</button>
            </form>
        </div>

        <div class="surface overflow-hidden">
            <div class="panel-header">
                <h2 class="text-lg font-bold text-slate-950">Daftar Kategori</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $cat->name }}</td>
                                <td>{{ $cat->slug }}</td>
                                <td>
                                    <span class="status-pill bg-blue-50 text-brand-blue ring-1 ring-blue-200">{{ $cat->books_count }} buku</span>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            @click="category = { id: @js($cat->id), name: @js($cat->name), description: @js($cat->description ?? '') }; editOpen = true"
                                            class="text-xs font-bold text-brand-blue hover:text-brand-navy"
                                        >
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-bold text-red-600 hover:text-red-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate-500">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 p-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div @click.away="editOpen = false" class="w-full max-w-md rounded-lg bg-white p-5 shadow-xl">
            <div class="mb-4">
                <p class="page-kicker">Edit Kategori</p>
                <h2 class="mt-1 text-xl font-bold text-slate-950" x-text="category.name"></h2>
            </div>
            <form method="POST" :action="'/categories/' + category.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="field-label">Nama</label>
                    <input x-model="category.name" name="name" required class="field-control">
                </div>
                <div>
                    <label class="field-label">Deskripsi</label>
                    <input x-model="category.description" name="description" class="field-control">
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <button type="button" @click="editOpen = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
