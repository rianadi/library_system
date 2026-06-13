<x-guest-layout>
    <div class="mb-6">
        <p class="page-kicker">Verifikasi Email</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Cek Email Anda</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Klik tautan verifikasi yang sudah dikirim. Jika belum masuk, Anda bisa meminta tautan baru.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            Tautan verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <div class="grid gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full">
                Kirim Ulang Verifikasi
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary w-full">
                Logout
            </button>
        </form>
    </div>
</x-guest-layout>
