@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Jadwal Makan</h1>
    <a href="{{ route('schedules.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar Jadwal
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Jadwal untuk: {{ \Carbon\Carbon::parse($schedule->meal_date)->isoFormat('dddd, D MMMM Y') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Sesi Makan:</strong> {{ ucfirst($schedule->meal_type) }}
            </div>
            <div class="col-md-4">
                <strong>Tipe Hari:</strong>
                <span class="badge {{ $schedule->day_type == 'special' ? 'bg-success' : 'bg-info' }} text-white">
                    {{ ucfirst($schedule->day_type) }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Menu per Counter</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Counter/Gate</th>
                        <th>Menu</th>
                        <th>Kategori</th>
                        <th>Tipe Opsi</th>
                        <th>Stok Awal</th>
                        <th>Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedule->counterMenus as $counterMenu)
                        <tr>
                            <td>{{ $counterMenu->gate->name }}</td>
                            <td>{{ $counterMenu->menu->name }}</td>
                            <td>{{ ucfirst($counterMenu->menu->category) }}</td>
                            <td>{{ ucfirst($counterMenu->meal_option_type) }}</td>
                            <td>{{ $counterMenu->supply_qty ?? 'Tak Terbatas' }}</td>
                            <td>{{ $counterMenu->balance_qty ?? 'Tak Terbatas' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada menu yang ditugaskan untuk jadwal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection