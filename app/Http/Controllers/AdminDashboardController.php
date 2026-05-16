<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin dengan stat cards.
     */
    public function index()
    {
        $stats = [
            [
                'label'       => 'Total Pelanggan',
                'value'       => Pelanggan::count(),
                'note'        => null,
                'icon'        => 'users',
                'color'       => 'sky',
            ],
            [
                'label'       => 'Total Pesanan Hari Ini',
                'value'       => 0,
                'note'        => '(Menunggu Sprint 2)',
                'icon'        => 'clipboard',
                'color'       => 'amber',
            ],
            [
                'label'       => 'Pesanan Aktif',
                'value'       => 0,
                'note'        => '(Menunggu Sprint 2)',
                'icon'        => 'refresh',
                'color'       => 'violet',
            ],
            [
                'label'       => 'Pesanan Selesai',
                'value'       => 0,
                'note'        => '(Menunggu Sprint 2)',
                'icon'        => 'check-circle',
                'color'       => 'emerald',
            ],
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
