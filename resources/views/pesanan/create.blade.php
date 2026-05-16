@extends('layouts.admin')

@section('title', 'Buat Pesanan Baru')
@section('page-title', 'Buat Pesanan Baru')
@section('page-description', 'Formulir pembuatan pesanan laundry baru')

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Form Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-cyan-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Formulir Pesanan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi semua field yang bertanda <span class="text-red-500">*</span></p>
            </div>

            <form method="POST" action="{{ route('admin.pesanan.store') }}" class="p-6 space-y-5" id="form-create-pesanan">
                @csrf

                {{-- Tanggal Masuk --}}
                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                           value="{{ old('tanggal_masuk', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('tanggal_masuk') border-red-400 @enderror"
                           required>
                    @error('tanggal_masuk')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pelanggan --}}
                <div>
                    <label for="pelanggan_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <select name="pelanggan_id" id="pelanggan_id"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('pelanggan_id') border-red-400 @enderror"
                            required>
                        <option value="">— Pilih Pelanggan —</option>
                        @foreach ($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->id }}" {{ old('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                                {{ $pelanggan->kode_pelanggan }} — {{ $pelanggan->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('pelanggan_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Layanan --}}
                <div>
                    <label for="layanan_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Jenis Layanan <span class="text-red-500">*</span>
                    </label>
                    <select name="layanan_id" id="layanan_id"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('layanan_id') border-red-400 @enderror"
                            required>
                        <option value="" data-harga="0">— Pilih Layanan —</option>
                        @foreach ($layanans as $layanan)
                            <option value="{{ $layanan->id }}"
                                    data-harga="{{ $layanan->harga }}"
                                    {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>
                                {{ $layanan->kode_layanan }} — {{ $layanan->jenis_layanan }} (Rp {{ number_format($layanan->harga, 0, ',', '.') }}/kg)
                            </option>
                        @endforeach
                    </select>
                    @error('layanan_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bahan (optional) --}}
                <div>
                    <label for="bahan_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Bahan Tambahan
                    </label>
                    <select name="bahan_id" id="bahan_id"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('bahan_id') border-red-400 @enderror">
                        <option value="" data-biaya="0">— Tanpa Bahan Tambahan —</option>
                        @foreach ($bahans as $bahan)
                            <option value="{{ $bahan->id }}"
                                    data-biaya="{{ $bahan->biaya_per_kg }}"
                                    {{ old('bahan_id') == $bahan->id ? 'selected' : '' }}>
                                {{ $bahan->kode_bahan }} — {{ $bahan->nama_bahan }} (Rp {{ number_format($bahan->biaya_per_kg, 0, ',', '.') }}/kg)
                            </option>
                        @endforeach
                    </select>
                    @error('bahan_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Berat --}}
                <div>
                    <label for="berat" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Berat (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="berat" id="berat"
                           value="{{ old('berat') }}"
                           placeholder="Contoh: 3.5"
                           step="0.01" min="0.1" max="9999.99"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors @error('berat') border-red-400 @enderror"
                           required>
                    @error('berat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estimasi Subtotal (Real-time) --}}
                <div class="bg-gradient-to-r from-sky-50 to-cyan-50 rounded-xl p-4 border border-sky-200/60">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Subtotal</p>
                            <p class="text-xs text-gray-400 mt-0.5">Harga layanan × Berat + Biaya bahan × Berat</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-sky-700" id="display-subtotal">Rp 0</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-sky-200/60 grid grid-cols-3 gap-4 text-xs text-gray-500">
                        <div>
                            <span class="block font-medium">Harga Layanan</span>
                            <span id="display-harga-layanan" class="text-gray-700">Rp 0</span>
                        </div>
                        <div>
                            <span class="block font-medium">Biaya Bahan</span>
                            <span id="display-biaya-bahan" class="text-gray-700">Rp 0</span>
                        </div>
                        <div>
                            <span class="block font-medium">Berat</span>
                            <span id="display-berat" class="text-gray-700">0 kg</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="btn-simpan-pesanan"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pesanan
                    </button>
                    <a href="{{ route('admin.pesanan.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const layananSelect = document.getElementById('layanan_id');
        const bahanSelect   = document.getElementById('bahan_id');
        const beratInput    = document.getElementById('berat');

        const displaySubtotal      = document.getElementById('display-subtotal');
        const displayHargaLayanan  = document.getElementById('display-harga-layanan');
        const displayBiayaBahan    = document.getElementById('display-biaya-bahan');
        const displayBerat         = document.getElementById('display-berat');

        function formatRupiah(number) {
            return 'Rp ' + Math.round(number).toLocaleString('id-ID');
        }

        function calculateSubtotal() {
            const selectedLayanan = layananSelect.options[layananSelect.selectedIndex];
            const selectedBahan   = bahanSelect.options[bahanSelect.selectedIndex];
            const berat           = parseFloat(beratInput.value) || 0;

            const hargaLayanan = parseFloat(selectedLayanan.getAttribute('data-harga')) || 0;
            const biayaBahan   = parseFloat(selectedBahan.getAttribute('data-biaya'))   || 0;

            const subTotal = (berat * hargaLayanan) + (biayaBahan * berat);

            displayHargaLayanan.textContent = formatRupiah(hargaLayanan) + '/kg';
            displayBiayaBahan.textContent   = formatRupiah(biayaBahan)   + '/kg';
            displayBerat.textContent        = berat.toFixed(2) + ' kg';
            displaySubtotal.textContent     = formatRupiah(subTotal);
        }

        layananSelect.addEventListener('change', calculateSubtotal);
        bahanSelect.addEventListener('change', calculateSubtotal);
        beratInput.addEventListener('input', calculateSubtotal);

        // Trigger initial calculation in case of old() values
        calculateSubtotal();
    });
</script>
@endpush
