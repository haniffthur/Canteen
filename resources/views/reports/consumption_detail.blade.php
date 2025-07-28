@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Detail Laporan Konsumsi</h1>
        <p class="mb-0">Menu: <strong>{{ $menu->name }}</strong></p>
        <p class="mb-0 text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}</p>
    </div>
    <a href="{{ route('reports.consumption', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Laporan</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Karyawan Pengonsumsi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu Transaksi</th>
                        <th>Nama Karyawan</th>
                        <th>ID Karyawan (NIK)</th>
                        <th>Departemen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration + $logs->firstItem() - 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->tapped_at)->isoFormat('D MMM Y, HH:mm:ss') }}</td>
                            <td>{{ $log->employee->name ?? 'N/A' }}</td>
                            <td>{{ $log->employee->employee_id ?? 'N/A' }}</td>
                            <td>{{ $log->employee->department ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada karyawan yang mengonsumsi menu ini pada periode yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{-- Menambahkan parameter query string ke link paginasi --}}
                {{ $logs->appends(['start_date' => $startDate, 'end_date' => $endDate])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
