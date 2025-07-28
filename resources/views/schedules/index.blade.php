@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Daftar Jadwal Makan</h1>
    <p class="mb-4">Halaman ini menampilkan semua jadwal makan yang telah dibuat.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="{{ route('schedules.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Buat Jadwal
                Baru</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Sesi Makan</th>
                            <th>Tipe Hari</th>
                            <th>Jumlah Menu Ditugaskan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td>{{ $loop->iteration + $schedules->firstItem() - 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->meal_date)->isoFormat('dddd, D MMMM Y') }}</td>
                                <td>{{ ucfirst($schedule->meal_type) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $schedule->day_type == 'special' ? 'bg-success' : 'bg-info' }} text-white">
                                        {{ ucfirst($schedule->day_type) }}
                                    </span>
                                </td>
                                <td>{{ $schedule->counter_menus_count }} Menu</td>
                                <td>
                                    <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-info btn-sm"><i
                                            class="fas fa-eye"></i> Detail</a>
                                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus jadwal ini? Semua data penugasan menu di dalamnya juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada jadwal yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
@endsection