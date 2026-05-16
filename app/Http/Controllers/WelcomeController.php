<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Tampilkan halaman welcome / landing page.
     */
    public function index()
    {
        return view('welcome');
    }
}
