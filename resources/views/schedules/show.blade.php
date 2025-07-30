@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Jadwal Makan</h1>
    <a href="{{ route('schedules.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar Jadwal
    </a>
</div>

{{-- Card Informasi Utama Jadwal (tidak berubah) --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Jadwal untuk: {{ \Carbon\Carbon::parse($schedule->meal_date)->isoFormat('dddd, D MMMM Y') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Sesi Makan:</strong> {{ ucfirst($schedule->meal_type) }}</div>
            <div class="col-md-4">
                <strong>Tipe Hari:</strong>
                <span class="badge {{ $schedule->day_type == 'special' ? 'bg-success' : 'bg-info' }} text-white">
                    {{ ucfirst($schedule->day_type) }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Card Daftar Counter (Struktur Baru dengan Tabel) --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Ringkasan Menu per Counter</h6>
    </div>
    <div class="card-body">
        @if($groupedCounterMenus->isEmpty())
            <p class="text-center">Tidak ada menu yang ditugaskan untuk jadwal ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama Counter</th>
                            <th>Jumlah Menu</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedCounterMenus as $gateName => $counterMenus)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $gateName }}</td>
                                <td class="text-center">{{ $counterMenus->count() }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#gateDetailModal_{{ $loop->iteration }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- MODALS UNTUK SETIAP COUNTER -->
@foreach($groupedCounterMenus as $gateName => $counterMenus)
<div class="modal fade" id="gateDetailModal_{{ $loop->iteration }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel_{{ $loop->iteration }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_{{ $loop->iteration }}">Detail Menu di: {{ $gateName }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Kategori</th>
                                <th>Tipe Opsi</th>
                                <th>Stok Awal</th>
                                <th>Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($counterMenus as $item)
                                <tr>
                                    <td>{{ $item->menu->name }}</td>
                                    <td>{{ ucfirst($item->menu->category) }}</td>
                                    <td>{{ ucfirst($item->meal_option_type) }}</td>
                                    <td>{{ $item->supply_qty ?? 'Tak Terbatas' }}</td>
                                    <td>{{ $item->balance_qty ?? 'Tak Terbatas' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
