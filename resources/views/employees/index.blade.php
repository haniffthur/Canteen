@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Kelola Karyawan</h1>
<p class="mb-4">Daftar semua karyawan yang terdaftar di sistem.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Karyawan</a>
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
                         <th>ID Karyawan</th>
                        <th>Nama Karyawan</th>
                         <th>Departemen</th>
                        <th>Nomor Kartu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration + $employees->firstItem() - 1 }}</td>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->department ?? '-' }}</td>
                            <td>{{ $employee->card->card_number ?? '-' }}</td> 
                            <td>
                                <span class="badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                            <td>
                                  <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Data karyawan belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection