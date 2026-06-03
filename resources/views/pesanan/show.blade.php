@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')
@section('page-description', 'Rincian nota transaksi ' . $pesanan->kode_pesanan)

@section('content')
<div class="max-w-4xl space-y-6 print:w-full print:max-w-none print:space-y-4">
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('admin.pesanan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-sky-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Pesanan
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white text-sm font-semibold rounded-lg hover:bg-sky-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Nota
        </button>
    </div>

    {{-- Order Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $pesanan->kode_pesanan }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Dibuat oleh {{ $pesanan->user?->name ?? 'Admin' }} pada {{ $pesanan->created_at->format('d M Y H:i') }}</p>
                </div>
                @if ($pesanan->status === 'proses')
                    <span class="inline-flex px-3 py-1.5 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-xl">Proses</span>
                @elseif ($pesanan->status === 'selesai')
                    <span class="inline-flex px-3 py-1.5 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-xl">Selesai</span>
                @else
                    <span class="inline-flex px-3 py-1.5 bg-blue-100 text-blue-800 text-sm font-semibold rounded-xl">Diambil</span>
                @endif
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $pesanan->tanggal_masuk->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</p>
                <p class="text-sm font-bold text-sky-700 mt-1">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                <p class="text-sm font-semibold text-gray-900 mt-1 capitalize">{{ $pesanan->status }}</p>
            </div>
        </div>
    </div>

    {{-- Estimasi Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Estimasi Waktu Selesai (GM Prediction)</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Selesai</p>
                <p class="text-sm font-bold text-amber-700 mt-1">{{ $pesanan->estimasi_selesai ? $pesanan->estimasi_selesai->format('d M Y H:i') : '— Belum dihitung —' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Aktual Selesai</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $pesanan->actual_selesai ? $pesanan->actual_selesai->format('d M Y H:i') : '— Belum selesai —' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Waktu</p>
                @if ($pesanan->status === 'proses' && $pesanan->estimasi_selesai)
                    @php $isPast = now()->gt($pesanan->estimasi_selesai); @endphp
                    @if ($isPast)
                        <p class="text-sm font-semibold text-red-600 mt-1">Melewati estimasi</p>
                    @else
                        @php $diff = now()->diff($pesanan->estimasi_selesai); @endphp
                        <p class="text-sm font-semibold text-emerald-600 mt-1">{{ $diff->d > 0 ? $diff->d . ' hari ' : '' }}{{ $diff->h }} jam {{ $diff->i }} menit</p>
                    @endif
                @else
                    <p class="text-sm font-semibold text-gray-400 mt-1">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Items --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-cyan-50 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Rincian Item Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Layanan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase">Bahan</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Berat</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Harga/kg</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Subtotal</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase">Kap. Mesin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($pesanan->detailTransaksi as $i => $detail)
                    <tr class="hover:bg-sky-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-6 py-4"><p class="font-medium text-gray-900">{{ $detail->pelanggan?->nama ?? '—' }}</p><p class="text-xs text-gray-400">{{ $detail->pelanggan?->kode_pelanggan }}</p></td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 bg-sky-100 text-sky-700 text-xs font-semibold rounded-lg">{{ $detail->layanan?->jenis_layanan ?? '—' }}</span></td>
                        <td class="px-6 py-4 text-gray-600">{{ $detail->bahan?->nama_bahan ?? 'Tanpa bahan' }}</td>
                        <td class="px-6 py-4 text-right text-gray-700">{{ $detail->berat }} kg</td>
                        <td class="px-6 py-4 text-right text-gray-700">Rp {{ number_format($detail->harga_per_berat, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right"><span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-lg">{{ number_format($detail->kapasitas_mesin, 2) }}%</span></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-200">
                        <td colspan="6" class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase">Total</td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-sky-700">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if ($pesanan->status === 'proses')
    <div class="flex items-center gap-3 print:hidden">
        <a href="{{ route('admin.pesanan.edit', $pesanan) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold rounded-xl shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Pesanan
        </a>
    </div>
    @endif
</div>
@endsection
