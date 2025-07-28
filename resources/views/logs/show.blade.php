@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Detail Log Transaksi</h1>
        <p class="mb-0">Karyawan: <strong>{{ $employee->name }} ({{ $employee->employee_id }})</strong></p>
    </div>
    <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Ringkasan</a>
</div>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengambilan Makan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Waktu Transaksi</th>
                        <th>Menu yang Diambil</th>
                        <th>Kategori</th>
                        <th>Counter / Gate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->tapped_at)->isoFormat('dddd, D MMMM YYYY - HH:mm:ss') }}</td>
                            <td>{{ $log->counterMenu->menu->name ?? 'Data Menu Dihapus' }}</td>
                            <td>{{ $log->counterMenu->menu->category ?? '-' }}</td>
                            <td>{{ $log->counterMenu->gate->name ?? 'Data Gate Dihapus' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada transaksi untuk karyawan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Link untuk Paginasi --}}
            <div class="d-flex justify-content-center">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection