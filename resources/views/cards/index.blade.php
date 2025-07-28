@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Kelola Kartu</h1>
<p class="mb-4">Daftar semua kartu RFID/NFC yang terdaftar dan penugasannya ke karyawan.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="{{ route('cards.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Daftarkan Kartu Baru</a>
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
                        <th>Nomor Kartu</th>
                        <th>Ditugaskan ke Karyawan</th>
                        <th>Status Kartu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cards as $card)
                        <tr>
                            <td>{{ $loop->iteration + $cards->firstItem() - 1 }}</td>
                            <td>{{ $card->card_number }}</td>
                            <td>{{ $card->employee->name ?? 'Belum Ditugaskan' }}</td>
                            <td>
                                <span class="badge {{ $card->status == 'active' ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ ucfirst($card->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('cards.edit', $card->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('cards.destroy', $card->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kartu ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data kartu belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $cards->links() }}
        </div>
    </div>
</div>
@endsection