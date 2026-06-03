<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $laporan->judul }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }

        .header { text-align: center; margin-bottom: 24px; border-bottom: 3px solid #0ea5e9; padding-bottom: 16px; }
        .header h1 { font-size: 20px; font-weight: 800; color: #0c4a6e; margin-bottom: 4px; }
        .header p { font-size: 12px; color: #6b7280; }

        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { padding: 4px 8px; vertical-align: top; }
        .meta-label { font-weight: 700; color: #374151; width: 140px; }
        .meta-value { color: #4b5563; }

        .summary-grid { width: 100%; margin-bottom: 24px; border-collapse: collapse; }
        .summary-grid td { padding: 12px 16px; text-align: center; border: 1px solid #e5e7eb; }
        .summary-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; font-weight: 600; }
        .summary-value { font-size: 18px; font-weight: 800; color: #111827; margin-top: 4px; }
        .summary-sub { font-size: 9px; color: #6b7280; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .data-table thead th { background: #f3f4f6; border: 1px solid #d1d5db; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #4b5563; }
        .data-table tbody td { border: 1px solid #e5e7eb; padding: 6px 10px; font-size: 10px; }
        .data-table tbody tr:nth-child(even) { background: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-proses { background: #fef3c7; color: #92400e; }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .badge-diambil { background: #dbeafe; color: #1e40af; }

        .badge-mape-good { background: #d1fae5; color: #065f46; }
        .badge-mape-ok { background: #dbeafe; color: #1e40af; }
        .badge-mape-fair { background: #fef3c7; color: #92400e; }
        .badge-mape-bad { background: #fee2e2; color: #991b1b; }

        .footer { text-align: center; margin-top: 24px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; }
        .text-emerald { color: #059669; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>LOFFIE LAUNDRY</h1>
        <p>{{ $laporan->judul }}</p>
    </div>

    {{-- Report Metadata --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">Tipe Laporan</td>
            <td class="meta-value">{{ ucfirst($laporan->tipe) }}</td>
            <td class="meta-label">Dibuat Oleh</td>
            <td class="meta-value">{{ $laporan->creator?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Periode</td>
            <td class="meta-value">{{ $laporan->start_date->format('d M Y') }} — {{ $laporan->end_date->format('d M Y') }}</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-value">{{ now()->format('d M Y H:i') }}</td>
        </tr>
    </table>

    {{-- Summary --}}
    <table class="summary-grid">
        <tr>
            <td>
                <div class="summary-label">Total Pesanan</div>
                <div class="summary-value">{{ number_format($laporan->total_pesanan) }}</div>
            </td>
            <td>
                <div class="summary-label">Total Pendapatan</div>
                <div class="summary-value text-emerald">Rp {{ number_format($laporan->total_pendapatan, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-label">Rata-rata MAPE</div>
                <div class="summary-value">{{ $laporan->avg_mape !== null ? number_format((float)$laporan->avg_mape, 2) . '%' : '—' }}</div>
                @if($laporan->avg_mape !== null)
                    @php
                        $m = (float)$laporan->avg_mape;
                        $lbl = $m < 10 ? 'Sangat Baik' : ($m < 20 ? 'Baik' : ($m < 50 ? 'Layak' : 'Buruk'));
                    @endphp
                    <div class="summary-sub">{{ $lbl }}</div>
                @endif
            </td>
            <td>
                <div class="summary-label">Rata-rata MAE</div>
                <div class="summary-value">{{ $laporan->avg_mae !== null ? number_format((float)$laporan->avg_mae, 2) . ' jam' : '—' }}</div>
            </td>
        </tr>
    </table>

    {{-- Transaction Details --}}
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th class="text-right">Berat (kg)</th>
                <th class="text-right">Total Biaya</th>
                <th class="text-right">Prediksi (jam)</th>
                <th class="text-right">Aktual (jam)</th>
                <th class="text-center">MAPE (%)</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pesanans as $i => $p)
                @php $d = $p->detailTransaksi->first(); $pr = $p->prediksiLogs->first(); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $p->kode_pesanan }}</td>
                    <td>{{ $d?->pelanggan?->nama ?? '-' }}</td>
                    <td>{{ $d?->layanan?->jenis_layanan ?? '-' }}</td>
                    <td class="text-right">{{ $d?->berat ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $pr?->prediksi_jam ? number_format((float)$pr->prediksi_jam, 2) : '—' }}</td>
                    <td class="text-right">{{ $pr?->actual_jam ? number_format((float)$pr->actual_jam, 2) : '—' }}</td>
                    <td class="text-center">
                        @if($pr?->mape !== null)
                            @php
                                $mv = (float)$pr->mape;
                                $cls = $mv < 10 ? 'badge-mape-good' : ($mv < 20 ? 'badge-mape-ok' : ($mv < 50 ? 'badge-mape-fair' : 'badge-mape-bad'));
                            @endphp
                            <span class="badge {{ $cls }}">{{ number_format($mv, 2) }}%</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">
                        @if($p->status === 'proses')
                            <span class="badge badge-proses">Proses</span>
                        @elseif($p->status === 'selesai')
                            <span class="badge badge-selesai">Selesai</span>
                        @elseif($p->status === 'diambil')
                            <span class="badge badge-diambil">Diambil</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #9ca3af;">Tidak ada transaksi dalam periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dicetak dari Sistem Informasi Loffie Laundry — {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
