@extends('layouts.app')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Selamat Datang, {{ Auth::user()->name }}!</h6>
            </div>
            <div class="card-body">
                <p>Anda login sebagai: <strong>{{ strtoupper(Auth::user()->role) }}</strong></p>
                
                @if(Auth::user()->role === 'admin')
                    <p>Anda memiliki akses penuh untuk mengelola sistem kantin, termasuk mengatur jadwal dan data master.</p>
                @elseif(Auth::user()->role === 'hr')
                    <p>Anda dapat melihat dan mengunduh laporan konsumsi makanan karyawan.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection