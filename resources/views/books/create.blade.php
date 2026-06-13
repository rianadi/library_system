@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
<div class="page-shell">
    <div class="page-container-narrow">
        <div class="mb-6">
            <p class="page-kicker">Koleksi Baru</p>
            <h1 class="page-title mt-2">Tambah Buku</h1>
            <p class="page-subtitle">Lengkapi informasi buku agar koleksi mudah dicari dan dikelola.</p>
        </div>

        <div class="surface p-5 sm:p-6">
            @include('partials._errors')

            <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="field-label">Judul Buku *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="field-control">
                    </div>

                    <div>
                        <label class="field-label">Penulis *</label>
                        <input type="text" name="author" value="{{ old('author') }}" required class="field-control">
                    </div>

                    <div>
                        <label class="field-label">ISBN</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" class="field-control">
                    </div>

                    <div>
                        <label class="field-label">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}" class="field-control">
                    </div>

                    <div>
                        <label class="field-label">Tahun</label>
                        <input type="number" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}" class="field-control">
                    </div>

                    <div>
                        <label class="field-label">Kategori</label>
                        <select name="category_id" class="field-control">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="field-label">Jumlah Eksemplar *</label>
                        <input type="number" name="total_copies" value="{{ old('total_copies', 1) }}" min="1" required class="field-control">
                    </div>

                    <div>
                        <label class="field-label">Lokasi Rak</label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Rak A-12" class="field-control">
                    </div>

                    <div>
                        <label class="field-label">Cover Buku</label>
                        <input type="file" name="cover_image" accept="image/*" class="field-control">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="field-label">Deskripsi</label>
                        <textarea name="description" rows="4" class="field-control">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('books.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
