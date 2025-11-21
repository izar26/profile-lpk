@extends('layouts.app')

@section('header', 'Pengaturan Profile')

@section('content')

{{-- 
  Konten di bawah ini akan langsung dimuat di dalam <main class="bg-white shadow-soft...">
  dari layout app.blade.php Anda.
--}}

<div class="max-w-4xl mx-auto">
    
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 font-serif">
                Informasi Profil
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Perbarui informasi profil akun Anda dan alamat email.
            </p>
        </header>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('patch')

            <div x-data="{
                    newPhotoUrl: '{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=D97706&color=FFF' }}',
                    photoFilename: '',
                    previewPhoto(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.photoFilename = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.newPhotoUrl = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            this.photoFilename = '';
                            this.newPhotoUrl = '{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=D97706&color=FFF' }}';
                        }
                    }
                 }">
                
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                <div class="flex items-center gap-4">
                    
                    <img :src="newPhotoUrl" 
                         alt="{{ $user->name }}" 
                         class="h-20 w-20 rounded-full object-cover border-2 border-gold-200">
                    
                    <div>
                        <label for="foto" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition-all duration-200">
                            <i class="fa-solid fa-upload mr-2 text-gold-600"></i>
                            Pilih Foto...
                        </label>
                        <input @change="previewPhoto($event)" 
                               name="foto" type="file" id="foto" class="hidden"/>
                        <span x-text="photoFilename" class="ml-3 text-sm text-gray-600"></span>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, atau JPEG (Maks. 2MB).</p>
                        <x-input-error class="mt-2" :messages="$errors->get('foto')" />
                    </div>
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input id="name" name="name" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" name="email" type="email" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('email', $user->email) }}" required autocomplete="username">
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 text-sm text-gray-600">
                        <p>
                            Alamat email Anda belum terverifikasi.
                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Klik di sini untuk mengirim ulang email verifikasi.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                Link verifikasi baru telah dikirim ke alamat email Anda.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-md hover:bg-gold-600 transition-all font-semibold">
                    Simpan Perubahan
                </button>

                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                       class="text-sm text-green-600 font-medium">
                        Tersimpan.
                    </p>
                @endif
            </div>
        </form>
    </section>

    <hr class="border-gray-200 my-8">

    <section>
        <header class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 font-serif">
                Ubah Password
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
            </p>
        </header>

        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <div>
                <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" autocomplete="current-password">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input id="update_password_password" name="password" type="password" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-md hover:bg-gold-600 transition-all font-semibold">
                    Simpan Password
                </button>

                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                       class="text-sm text-green-600 font-medium">
                        Tersimpan.
                    </p>
                @endif
            </div>
        </form>
    </section>

    <hr class="border-gray-200 my-8">

    <section>
        <header class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 font-serif">
                Hapus Akun
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Setelah akun Anda dihapus, semua data dan sumber dayanya akan dihapus secara permanen.
            </p>
        </header>

        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-5 py-2.5 bg-red-600 text-white rounded-xl shadow-md hover:bg-red-700 transition-all font-semibold"
        >Hapus Akun</button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    Apakah Anda yakin ingin menghapus akun Anda?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Setelah akun Anda dihapus, semua data akan dihapus permanen. Masukkan password Anda untuk mengonfirmasi.
                </p>

                <div class="mt-6">
                    <label for="password" class="sr-only">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4 border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300"
                        placeholder="Password"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition mr-3">
                        Batal
                    </button>

                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-xl shadow-md hover:bg-red-700 transition-all font-semibold">
                        Hapus Akun
                    </button>
                </div>
            </form>
        </x-modal>
    </section>

</div> @endsection