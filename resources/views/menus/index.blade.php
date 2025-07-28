@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Kelola Menu</h1>
<p class="mb-4">Daftar semua menu yang tersedia di sistem.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Menu Baru</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menus as $menu)
                        <tr>
                            <td>{{ $loop->iteration + $menus->firstItem() - 1 }}</td>
                            <td>{{ $menu->name }}</td>
                            <td>
                                {{-- LOGIKA UNTUK MENGUBAH WARNA BADGE --}}
                                <span class="badge text-white
                                    @if($menu->category == 'utama') bg-primary
                                    @elseif($menu->category == 'spesial') bg-success
                                    @elseif($menu->category == 'opsional') bg-secondary
                                    @endif">
                                    {{ ucfirst($menu->category) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Data menu belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $menus->links() }}
        </div>
    </div>
</div>
@endsection