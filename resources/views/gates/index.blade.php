@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Kelola Counter</h1>
<p class="mb-4">Daftar semua counter/gate/line yang ada di sistem.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('gates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Counter</a>
        </div>
        <div>
            {{-- TOMBOL BARU UNTUK MEMBUKA MODAL --}}
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#timeSettingsModal">
                <i class="fas fa-clock"></i> Atur Jam Operasional Massal
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama Counter</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Jam Operasional</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gates as $gate)
                        <tr>
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
                            <td>{{ $gate->start_time ? \Carbon\Carbon::parse($gate->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($gate->stop_time)->format('H:i') : '-' }}</td>
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

<!-- MODAL UNTUK PENGATURAN JAM OPERASIONAL -->
<div class="modal fade" id="timeSettingsModal" tabindex="-1" role="dialog" aria-labelledby="timeSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('gates.bulkUpdateTime') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="timeSettingsModalLabel">Atur Jam Operasional Massal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            Harap periksa kembali input Anda.
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time">Jam Mulai Aktif</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stop_time">Jam Selesai Aktif</label>
                            <input type="time" name="stop_time" class="form-control" required>
                        </div>
                    </div>
                    <hr>
                    <p class="font-weight-bold">Pilih Counter yang akan Diterapkan:</p>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkAllGates">
                            <label class="custom-control-label" for="checkAllGates">Pilih Semua Counter</label>
                        </div>
                    </div>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                        @foreach($allGates as $gate)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input gate-checkbox" name="gate_ids[]" value="{{ $gate->id }}" id="gate_{{ $gate->id }}">
                                <label class="custom-control-label" for="gate_{{ $gate->id }}">{{ $gate->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logika untuk checkbox "Pilih Semua"
    const checkAll = document.getElementById('checkAllGates');
    const gateCheckboxes = document.querySelectorAll('.gate-checkbox');

    checkAll.addEventListener('change', function() {
        gateCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Jika ada error validasi, modal akan tetap terbuka saat halaman reload
    @if($errors->any())
        var myModal = new bootstrap.Modal(document.getElementById('timeSettingsModal'));
        myModal.show();
    @endif
});
</script>
@endpush
