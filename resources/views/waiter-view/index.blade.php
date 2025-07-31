<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiter View - {{ $gate->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #212529; font-family: 'Poppins', sans-serif; }
        .order-display {
            background-color: #fff;
            border-radius: 1rem;
            animation: popIn 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .order-header { border-bottom: 3px dashed #333; }
        .order-body ul { list-style-type: none; padding-left: 0; font-size: 1.5rem; }
        .order-body li { border-bottom: 1px solid #eee; }
        .order-body li:last-child { border-bottom: none; }
        .main-header { color: white; }
        .waiting-message { color: #6c757d; }
        @keyframes popIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>
<div class="container vh-100 d-flex flex-column py-3">
    <div class="main-header d-flex justify-content-between align-items-center mb-3">
        <h2 class="font-weight-bold">WAITER VIEW - {{ strtoupper($gate->name) }}</h2>
        <h3 id="clock" class="font-weight-bold"></h3>
    </div>
    
    {{-- Area untuk menampilkan satu pesanan --}}
    <div id="order-display-container" class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="text-center waiting-message">
            <i class="fas fa-receipt fa-4x mb-3"></i>
            <h2 class="font-weight-bold">Menunggu Transaksi...</h2>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderDisplayContainer = document.getElementById('order-display-container');
    const logsApiUrl = `{{ route('api.waiter-view.logs', $gate->id) }}`;
    let lastOrderId = null; // Untuk melacak ID pesanan terakhir yang ditampilkan

    const fetchAndUpdateDisplay = async () => {
        try {
            const response = await fetch(logsApiUrl);
            const order = await response.json();

            // Hanya update tampilan jika ada pesanan BARU
            if (order && order.id !== lastOrderId) {
                lastOrderId = order.id;
                const orderHtml = createOrderElement(order);
                orderDisplayContainer.innerHTML = orderHtml;
            } else if (!order && lastOrderId !== null) {
                // Jika tidak ada pesanan lagi, kembali ke tampilan awal
                lastOrderId = null;
                orderDisplayContainer.innerHTML = `
                    <div class="text-center waiting-message">
                        <i class="fas fa-receipt fa-4x mb-3"></i>
                        <h2 class="font-weight-bold">Menunggu Transaksi...</h2>
                    </div>
                `;
            }

        } catch (error) {
            console.error("Gagal mengambil data pesanan:", error);
        }
    };

    const createOrderElement = (order) => {
        let menuHtml = '<ul>';
        order.menus.forEach(menu => {
            const isMain = ['utama', 'spesial'].includes(menu.category);
            menuHtml += `<li class="py-2 ${isMain ? 'font-weight-bold text-primary' : ''}">${menu.name}</li>`;
        });
        menuHtml += '</ul>';

        return `
            <div class="order-display p-4 w-100">
                <div class="order-header pb-3 mb-3 text-center">
                    <h1 class="font-weight-bold mb-1" style="font-family: 'Roboto Mono', monospace;">${order.employee_name}</h1>
                    <h4 class="text-muted">${order.tapped_at}</h4>
                </div>
                <div class="order-body">
                    ${menuHtml}
                </div>
            </div>
        `;
    };

    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }

    // Jalankan pertama kali & atur interval lebih cepat
    fetchAndUpdateDisplay();
    setInterval(fetchAndUpdateDisplay, 3000); // Refresh setiap 3 detik
    setInterval(updateClock, 1000);
    updateClock();
});
</script>
</body>
</html>
