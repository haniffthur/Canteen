@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Kelola Departemen</h1>
<p class="mb-4">Daftar semua departemen yang ada di perusahaan.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Departemen</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Departemen</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td>{{ $loop->iteration + $departments->firstItem() - 1 }}</td>
                            <td>{{ $department->name }}</td>
                            <td>
                                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus departemen ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Data departemen belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $departments->links() }}
        </div>
    </div>
</div>
@endsection