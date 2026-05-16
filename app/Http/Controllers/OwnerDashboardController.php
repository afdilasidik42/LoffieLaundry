<?php

namespace App\Http\Controllers;

class OwnerDashboardController extends Controller
{
    /**
     * Tampilkan dashboard owner dengan overview cards.
     */
    public function index()
    {
        // Nilai dummy — akan diganti data riil di Sprint 3
        $overview = [
            [
                'label' => 'Revenue Bulan Ini',
                'value' => 'Rp0',
                'note'  => 'Menunggu Sprint 2',
                'icon'  => 'currency',
                'color' => 'emerald',
            ],
            [
                'label' => 'Total Transaksi',
                'value' => '0',
                'note'  => 'Menunggu Sprint 2',
                'icon'  => 'receipt',
                'color' => 'sky',
            ],
            [
                'label' => 'Akurasi Prediksi MAPE%',
                'value' => '0%',
                'note'  => 'Menunggu Sprint 3',
                'icon'  => 'chart',
                'color' => 'violet',
            ],
        ];

        return view('owner.dashboard', compact('overview'));
    }
}
