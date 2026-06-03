@extends('layouts.owner')

@section('title', 'Edit Staf Admin')
@section('page-title', 'Edit Staf Admin')
@section('page-description', 'Perbarui data akun Staf Admin.')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('owner.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Staf
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Edit: {{ $user->name }}</h3>
                    <p class="text-xs text-gray-500">Perbarui informasi staf admin di bawah ini</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('owner.users.update', $user->id) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('name') border-red-300 ring-1 ring-red-300 @enderror">
                @error('name')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Masukkan username unik" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('username') border-red-300 ring-1 ring-red-300 @enderror">
                @error('username')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('password') border-red-300 ring-1 ring-red-300 @enderror">
                @error('password')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-400">Biarkan kosong untuk mempertahankan password lama.</p>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-medium text-amber-800">Informasi</p>
                    <p class="text-xs text-amber-700 mt-0.5">Role pengguna ini dikunci sebagai <strong>Admin</strong> dan tidak dapat diubah.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-700 shadow-md shadow-emerald-500/20 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Perbarui
                </button>
                <a href="{{ route('owner.users.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
