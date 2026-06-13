<x-guest-layout>
    <div class="mb-6">
        <p class="page-kicker">Bantuan Akun</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Lupa Password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Masukkan email akun Anda. Sistem akan mengirim tautan untuk membuat password baru.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Kirim Link Reset
        </x-primary-button>
    </form>
</x-guest-layout>
