<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Kamu bisa menambahkan logika untuk mengambil data spesifik
        // berdasarkan role di sini jika dibutuhkan.
        // Contoh: if ($user->role === 'admin') { $data = ... }
        return view('dashboard.index');
    }
}