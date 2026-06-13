<x-guest-layout>
    <div class="mb-6">
        <p class="page-kicker">Anggota Baru</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Daftar Akun</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Buat akun untuk mengajukan peminjaman dan memantau riwayat buku.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 pt-2">
            <x-primary-button class="w-full">
                Daftar
            </x-primary-button>
            <a href="{{ route('login') }}" class="btn-secondary w-full">Sudah Punya Akun</a>
        </div>
    </form>
</x-guest-layout>
