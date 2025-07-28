@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Kelola Counter</h1>
<p class="mb-4">Daftar semua counter/gate/line yang ada di sistem.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="{{ route('gates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Counter</a>
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
                        <th>Nama Counter</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gates as $gate)
                        <tr>
                            <td>{{ $loop->iteration + $gates->firstItem() - 1 }}</td>
                            <td>{{ $gate->name }}</td>
                            <td>{{ $gate->location ?? '-' }}</td>
                            <td>
                                @php
                                    $status_class = 'bg-secondary';
                                    if ($gate->status == 'active') $status_class = 'bg-success';
                                    if ($gate->status == 'maintenance') $status_class = 'bg-warning';
                                    if ($gate->status == 'inactive') $status_class = 'bg-danger';
                                @endphp
                                <span class="badge {{ $status_class }} text-white">{{ ucfirst($gate->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('gates.edit', $gate->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('gates.destroy', $gate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data counter belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $gates->links() }}
        </div>
    </div>
</div>
@endsection