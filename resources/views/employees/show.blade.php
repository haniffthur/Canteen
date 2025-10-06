@extends('layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Detail Karyawan</h1>
<p class="mb-4">Informasi lengkap untuk karyawan: <strong>{{ $employee->name }}</strong>.</p>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Diri Karyawan</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Nama Lengkap</th>
                            <td>{{ $employee->name }}</td>
                        </tr>
                        <tr>
                            <th>ID Karyawan (NIK)</th>
                            <td>{{ $employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $employee->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Departemen</th>
                            <td>{{ $employee->department ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status Karyawan</th>
                            <td>
                                <span class="badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Terdaftar</th>
                            <td>{{ $employee->created_at->isoFormat('dddd, D MMMM YYYY') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Kartu</h6>
            </div>
            <div class="card-body">
                @if($employee->card)
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="40%">Nomor Kartu</th>
                                <td>{{ $employee->card->card_number }}</td>
                            </tr>
                            <tr>
                                <th>Status Kartu</th>
                                <td>
                                    <span class="badge {{ $employee->card->status == 'active' ? 'bg-success' : 'bg-secondary' }} text-white">
                                        {{ ucfirst($employee->card->status) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Karyawan ini belum memiliki kartu terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<a href="{{ route('employees.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
</a>
<a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning">
    <i class="fas fa-edit"></i> Edit Data
</a>

@endsection