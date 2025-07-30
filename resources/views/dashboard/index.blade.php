@extends('layouts.app')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row - Widgets -->
<div class="row">

    <!-- Widget: Makanan Disajikan Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Makanan Disajikan (Sesi Ini)</div>
                        <div id="widget-meals-today" class="h5 mb-0 font-weight-bold text-gray-800">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-utensils fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget: Karyawan Aktif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Karyawan Aktif</div>
                        <div id="widget-active-employees" class="h5 mb-0 font-weight-bold text-gray-800">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget: Jadwal Aktif -->
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jadwal Aktif Saat Ini</div>
                        <div id="widget-active-schedule">
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><i class="fas fa-spinner fa-spin"></i></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Chart & Stok Kritis -->
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Konsumsi Makanan (7 Hari Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="consumptionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Kritis -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-exclamation-triangle"></i> Peringatan Stok Kritis</h6>
            </div>
            <div id="widget-low-stock" class="card-body" style="max-height: 320px; overflow-y: auto;">
                <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiUrl = "{{ route('api.dashboard.data') }}";
    let consumptionChart; // Variabel untuk menyimpan instance chart

    // --- Fungsi untuk mengambil data dan memperbarui UI ---
    const fetchAndUpdateDashboard = async () => {
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            // 1. Update Widgets
            document.getElementById('widget-meals-today').textContent = `${data.widgets.mealsToday} Porsi`;
            document.getElementById('widget-active-employees').textContent = `${data.widgets.activeEmployees} Orang`;
            
            const scheduleWidget = document.getElementById('widget-active-schedule');
            if (data.widgets.activeSchedule) {
                scheduleWidget.innerHTML = `
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${data.widgets.activeSchedule.meal_type} - ${data.widgets.activeSchedule.meal_date}
                    </div>
                    <small class="text-muted">Tipe Hari: ${data.widgets.activeSchedule.day_type}</small>
                `;
            } else {
                scheduleWidget.innerHTML = `<div class="h5 mb-0 font-weight-bold text-gray-800">Tidak Ada Jadwal Aktif</div>`;
            }

            // 2. Update Stok Kritis
            const lowStockWidget = document.getElementById('widget-low-stock');
            if (data.widgets.lowStockMenus && data.widgets.lowStockMenus.length > 0) {
                let listHtml = '<ul>';
                data.widgets.lowStockMenus.forEach(item => {
                    listHtml += `<li>
                        <strong>${item.menu.name}</strong> di ${item.gate.name}:
                        <span class="font-weight-bold text-danger">${item.balance_qty} Porsi</span>
                    </li>`;
                });
                listHtml += '</ul>';
                lowStockWidget.innerHTML = listHtml;
            } else {
                lowStockWidget.innerHTML = '<p class="text-center text-muted">Tidak ada menu dengan stok kritis saat ini.</p>';
            }

            // 3. Update Chart
            updateChart(data.chart.labels, data.chart.data);

        } catch (error) {
            console.error("Gagal memuat data dashboard:", error);
            // Tampilkan pesan error di widget jika gagal
        }
    };

    // --- Fungsi untuk membuat atau memperbarui chart ---
    const updateChart = (labels, data) => {
        const ctx = document.getElementById('consumptionChart').getContext('2d');
        if (consumptionChart) {
            // Jika chart sudah ada, update datanya
            consumptionChart.data.labels = labels;
            consumptionChart.data.datasets[0].data = data;
            consumptionChart.update();
        } else {
            // Jika belum ada, buat chart baru
            consumptionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Jumlah Konsumsi",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: data,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 10 // Atur kelipatan sumbu Y jika perlu
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    };

    // Panggil fungsi saat halaman dimuat
    fetchAndUpdateDashboard();
    // Atur agar data di-refresh setiap 60 detik
    setInterval(fetchAndUpdateDashboard, 60000); // 60000 ms = 1 menit
});
</script>
@endpush
