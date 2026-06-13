<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan SMP 11 Jember</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 shadow-sm backdrop-blur">
        <div class="page-container">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/smp-11-logo.png') }}" alt="Logo SMP Negeri 11 Jember" class="logo-mark">
                    <div>
                        <span class="block text-base font-extrabold tracking-tight text-brand-navy sm:text-lg">Perpustakaan Digital</span>
                        <span class="block text-xs font-semibold text-slate-500">SMP Negeri 11 Jember</span>
                    </div>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('books.index') }}" class="hidden text-sm font-semibold text-slate-600 transition hover:text-brand-blue sm:inline">Katalog</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary px-4 py-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 transition hover:text-brand-blue">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary px-4 py-2">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        <section class="relative overflow-hidden border-b border-slate-200 bg-white">
            <img src="{{ asset('images/smp-11-logo.png') }}" alt="" class="pointer-events-none absolute -right-10 top-8 h-[420px] w-[420px] object-contain opacity-10 sm:right-10 sm:h-[520px] sm:w-[520px] lg:opacity-15">
            <div class="page-container relative py-16 sm:py-20 lg:py-24">
                <div class="max-w-3xl">
                    <p class="page-kicker">Sistem Informasi Perpustakaan</p>
                    <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                        Perpustakaan SMP Negeri 11 Jember
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        Akses katalog, ajukan peminjaman, dan pantau riwayat buku secara digital dengan tampilan yang ringkas untuk siswa dan admin.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('books.index') }}" class="btn-primary">Jelajahi Katalog</a>
                        @guest
                            <a href="{{ route('register') }}" class="btn-secondary">Daftar Sebagai Anggota</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-secondary">Buka Dashboard</a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        <section class="page-container py-12">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="surface p-5">
                    <div class="mb-4 inline-flex rounded-lg bg-blue-50 p-3 text-brand-blue">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-950">Katalog Mudah Dicari</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Filter berdasarkan judul, penulis, ISBN, dan kategori koleksi.</p>
                </div>

                <div class="surface p-5">
                    <div class="mb-4 inline-flex rounded-lg bg-amber-50 p-3 text-amber-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-950">Peminjaman Tercatat</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Pengajuan, persetujuan, tenggat, dan pengembalian tersimpan rapi.</p>
                </div>

                <div class="surface p-5">
                    <div class="mb-4 inline-flex rounded-lg bg-emerald-50 p-3 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Zm0 9.75c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Zm9.75-9.75c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-950">Admin Lebih Efisien</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Kelola buku, kategori, pengguna, dan laporan peminjaman dari satu sistem.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white py-6">
        <div class="page-container text-center text-sm font-medium text-slate-500">
            &copy; {{ date('Y') }} Perpustakaan SMP Negeri 11 Jember.
        </div>
    </footer>
</body>
</html>
