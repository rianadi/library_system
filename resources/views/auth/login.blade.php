<x-guest-layout>
    <div class="mb-6">
        <p class="page-kicker">Masuk Akun</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Selamat Datang</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Gunakan akun perpustakaan untuk mengakses dashboard dan peminjaman.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-blue shadow-sm focus:ring-brand-blue" name="remember">
                <span class="ms-2 text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-brand-blue hover:text-brand-navy" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="flex flex-col gap-3 pt-2">
            <x-primary-button class="w-full">
                Login
            </x-primary-button>
            <a href="{{ route('register') }}" class="btn-secondary w-full">Buat Akun Baru</a>
        </div>
    </form>
</x-guest-layout>
