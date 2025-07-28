@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Log Transaksi per Karyawan</h1>
<p class="mb-4">Halaman ini menampilkan ringkasan transaksi makan yang dikelompokkan berdasarkan karyawan.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Ringkasan Log Transaksi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>ID Karyawan (NIK)</th>
                        <th>Transaksi Terakhir</th>
                         <th>Counter / Gate</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration + $logs->firstItem() - 1 }}</td>
                            <td>{{ $log->employee->name ?? 'Data Karyawan Dihapus' }}</td>
                            <td>{{ $log->employee->employee_id ?? '-' }}</td>
                       
                            <td>{{ \Carbon\Carbon::parse($log->last_transaction)->isoFormat('dddd, D MMMM Y - HH:mm') }}</td>
                            <td>{{ $log->counterMenu->gate->name ?? 'Data Gate Dihapus' }}</td>
                            <td>
                                <a href="{{ route('logs.show', $log->employee_id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada transaksi yang tercatat.</td>
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