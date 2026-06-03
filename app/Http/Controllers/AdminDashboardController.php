<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pesanan;

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
                'value'       => Pesanan::whereDate('created_at', today())->count(),
                'note'        => null,
                'icon'        => 'clipboard',
                'color'       => 'amber',
            ],
            [
                'label'       => 'Pesanan Aktif',
                'value'       => Pesanan::where('status', 'proses')->count(),
                'note'        => null,
                'icon'        => 'refresh',
                'color'       => 'violet',
            ],
            [
                'label'       => 'Pesanan Selesai',
                'value'       => Pesanan::where('status', 'selesai')->count(),
                'note'        => null,
                'icon'        => 'check-circle',
                'color'       => 'emerald',
            ],
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
