@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Laporan Konsumsi Makanan</h1>
    <p class="mb-4">Halaman ini menampilkan rekapitulasi jumlah konsumsi untuk setiap menu berdasarkan rentang tanggal.</p>

    {{-- Filter Laporan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            {{-- Beri ID pada form untuk target JS --}}
            <form id="filter-form">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="start_date">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" id="filter-btn" class="btn btn-primary w-100">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Laporan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 id="report-period-header" class="m-0 font-weight-bold text-primary">
                Hasil Laporan Periode {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} s/d
                {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th width="10%">No</th>
                            <th>Nama Menu</th>
                            <th width="20%">Jumlah Konsumsi</th>
                            <th width="20%">Sisa Stok Hari Ini</th>
                        </tr>
                    </thead>
                    {{-- Beri ID pada tbody untuk target JS --}}
                    <tbody id="report-table-body">
                        @forelse ($reportData as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('reports.consumption.detail', ['menu' => $data->menu_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
                                        {{ $data->menu_name }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $data->total_consumed }} Porsi</td>
                                <td class="text-center">{{ $todayStocks[$data->menu_id] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data konsumsi pada rentang tanggal yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const tableBody = document.getElementById('report-table-body');
    const periodHeader = document.getElementById('report-period-header');
    const filterBtn = document.getElementById('filter-btn');
    const spinner = filterBtn.querySelector('.spinner-border');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        spinner.classList.remove('d-none');
        filterBtn.disabled = true;

        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const url = `{{ route('reports.consumption') }}?start_date=${startDate}&end_date=${endDate}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            updateTable(data);

        } catch (error) {
            console.error('Error fetching report data:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>`;
        } finally {
            spinner.classList.add('d-none');
            filterBtn.disabled = false;
        }
    });

    function updateTable(data) {
        // Update header
        periodHeader.textContent = `Hasil Laporan Periode ${data.formattedStartDate} s/d ${data.formattedEndDate}`;
        
        // Clear table body
        tableBody.innerHTML = '';

        if (data.reportData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak ada data konsumsi pada rentang tanggal yang dipilih.</td></tr>`;
            return;
        }

        // Populate table with new data
        data.reportData.forEach((item, index) => {
            const detailUrl = `{{ url('/reports/consumption') }}/${item.menu_id}?start_date=${data.startDate}&end_date=${data.endDate}`;
            const remainingStock = data.todayStocks[item.menu_id] ?? '-';
            
            const row = `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td><a href="${detailUrl}">${item.menu_name}</a></td>
                    <td class="text-center">${item.total_consumed} Porsi</td>
                    <td class="text-center">${remainingStock}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }
});
</script>
@endpush
