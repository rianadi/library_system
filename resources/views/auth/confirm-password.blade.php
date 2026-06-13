<x-guest-layout>
    <div class="mb-6">
        <p class="page-kicker">Konfirmasi Keamanan</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Masukkan Password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Area ini membutuhkan konfirmasi password sebelum dilanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Konfirmasi
        </x-primary-button>
    </form>
</x-guest-layout>
