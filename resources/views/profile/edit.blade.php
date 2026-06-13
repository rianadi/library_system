<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Akun Saya</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Profil</h1>
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-container-narrow space-y-6">
            <div class="surface p-5 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="surface p-5 sm:p-6">
                @include('profile.partials.update-password-form')
            </div>

            <div class="surface p-5 sm:p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
