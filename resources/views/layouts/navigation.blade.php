<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 shadow-sm backdrop-blur">
    <div class="page-container">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('images/smp-11-logo.png') }}" alt="Logo SMP Negeri 11 Jember" class="logo-mark">
                <div class="min-w-0">
                    <span class="block truncate text-base font-extrabold tracking-tight text-brand-navy sm:text-lg">Perpustakaan Digital</span>
                    <span class="block truncate text-xs font-semibold text-slate-500">SMP Negeri 11 Jember</span>
                </div>
            </a>

            <div class="hidden items-center gap-6 sm:flex">
                @auth
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                @endauth

                <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.index', 'books.show')">
                    Katalog
                </x-nav-link>

                @auth
                    <x-nav-link :href="route('loans.my')" :active="request()->routeIs('loans.my')">
                        Pinjaman Saya
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                        <div class="relative" x-data="{ menuOpen: false }">
                            <button
                                type="button"
                                @click="menuOpen = !menuOpen"
                                class="inline-flex h-16 items-center gap-1 border-b-2 px-1 text-sm font-semibold transition {{ request()->routeIs('users.*', 'categories.*', 'loans.index', 'books.create', 'books.edit') ? 'border-brand-gold text-brand-navy' : 'border-transparent text-slate-500 hover:border-blue-200 hover:text-brand-blue' }}"
                            >
                                Admin
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            <div
                                x-show="menuOpen"
                                x-cloak
                                @click.away="menuOpen = false"
                                class="absolute left-0 mt-2 w-56 rounded-lg border border-slate-200 bg-white py-2 shadow-lg"
                            >
                                <a href="{{ route('users.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-blue">Pengguna</a>
                                <a href="{{ route('categories.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-blue">Kategori</a>
                                <a href="{{ route('loans.index') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-blue">Peminjaman</a>
                                <a href="{{ route('books.create') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-blue">Tambah Buku</a>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                @auth
                    <div class="relative" x-data="{ userOpen: false }">
                        <button type="button" @click="userOpen = !userOpen" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-brand-blue">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-soft text-xs font-bold text-brand-blue">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-show="userOpen"
                            x-cloak
                            @click.away="userOpen = false"
                            class="absolute right-0 mt-2 w-52 rounded-lg border border-slate-200 bg-white py-2 shadow-lg"
                        >
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-blue">Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-blue">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 transition hover:text-brand-blue">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary px-4 py-2">Daftar</a>
                @endauth
            </div>

            <button type="button" @click="open = !open" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white p-2 text-slate-500 shadow-sm transition hover:text-brand-blue sm:hidden">
                <span class="sr-only">Buka menu</span>
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="border-t border-slate-200 bg-white sm:hidden">
        <div class="space-y-1 py-2">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            @endauth
            <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.index', 'books.show')">Katalog</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('loans.my')" :active="request()->routeIs('loans.my')">Pinjaman Saya</x-responsive-nav-link>

                @if(auth()->user()->isAdmin())
                    <div class="mx-4 my-2 border-t border-slate-200"></div>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">Pengguna</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">Kategori</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('loans.index')" :active="request()->routeIs('loans.index')">Peminjaman</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('books.create')" :active="request()->routeIs('books.create')">Tambah Buku</x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <div class="border-t border-slate-200 px-4 py-4">
            @auth
                <div class="mb-3">
                    <div class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="grid gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn-secondary w-full">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-muted w-full">Logout</button>
                    </form>
                </div>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
