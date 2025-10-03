<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiter View - {{ $gate->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Mengadopsi gaya dasar dari Tapping Interface */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
            color: #1a202c;
            height: 100%;
            overflow: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            height: calc(100vh - 4rem);
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            color: #667eea;
        }

        .clock {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            color: #4a5568;
            background: #f7fafc;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }
        
        .clock i {
             color: #667eea;
        }

        .main-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        /* Gaya untuk state menunggu */
        .waiting-state {
            text-align: center;
            color: #718096;
        }

        .waiting-icon i {
            font-size: 4rem;
            color: #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .waiting-text {
            font-size: 1.75rem;
            font-weight: 600;
            color: #4a5568;
        }

        .waiting-subtext {
            font-size: 1rem;
            color: #a0aec0;
            margin-top: 0.5rem;
        }

        /* Gaya untuk kartu pesanan yang muncul */
        .order-card {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .order-card-header {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 2rem;
        }
        
        .employee-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .order-time {
            font-size: 1.1rem;
            color: #718096;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.25rem;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
        }
        
        .menu-item.main-menu {
            font-weight: 600;
            color: #667eea;
            border-color: #c3dafe;
        }
        
        .menu-item.optional-menu {
            color: #4a5568;
        }
        
        .menu-icon {
            font-size: 1rem;
        }

        /* Animasi */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @media (max-width: 768px) {
            body { padding: 1rem; }
            .container { height: calc(100vh - 2rem); }
            .header { flex-direction: column; gap: 1rem; }
            .employee-name { font-size: 2rem; }
            .menu-item { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-concierge-bell"></i>
                <span>Waiter View - {{ $gate->name }}</span>
            </h1>
            <div class="clock">
                <i class="far fa-clock"></i>
                <span id="clock">--:--:--</span>
            </div>
        </div>
        
        <div class="main-content">
            <div id="order-display-container" class="w-100">
                </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderDisplayContainer = document.getElementById('order-display-container');
    const logsApiUrl = `{{ route('api.waiter-view.logs', $gate->id) }}`;
    let lastOrderId = null;

    const fetchAndUpdateDisplay = async () => {
        try {
            const response = await fetch(logsApiUrl);
            const order = await response.json();

            if (order && order.id !== lastOrderId) {
                lastOrderId = order.id;
                const orderHtml = createOrderElement(order);
                orderDisplayContainer.innerHTML = orderHtml;
                
                // Hapus pesanan secara otomatis setelah 15 detik
                setTimeout(() => {
                    if (lastOrderId === order.id) {
                        showWaitingState();
                        lastOrderId = null;
                    }
                }, 15000);
                
            } else if (!order && lastOrderId !== null) {
                // Jika API mengembalikan null, kembali ke waiting state
                showWaitingState();
                lastOrderId = null;
            }

        } catch (error) {
            console.error("Gagal mengambil data pesanan:", error);
            showWaitingState(); // Jika error, kembali ke waiting state
        }
    };

    const showWaitingState = () => {
        orderDisplayContainer.innerHTML = `
            <div class="waiting-state fade-in">
                <div class="waiting-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="waiting-text">Menunggu Transaksi</div>
                <div class="waiting-subtext">Pesanan akan muncul di sini secara otomatis</div>
            </div>
        `;
    };

    const createOrderElement = (order) => {
        let menuHtml = '<ul class="menu-list">';
        order.menus.forEach(menu => {
            const isMain = ['utama', 'spesial'].includes(menu.category);
            const menuClass = isMain ? 'main-menu' : 'optional-menu';
            const menuIcon = isMain ? 'fas fa-star' : 'fas fa-plus';
            
            menuHtml += `
                <li class="menu-item ${menuClass}">
                    <i class="menu-icon ${menuIcon}"></i>
                    <span class="menu-text">${menu.name}</span>
                </li>
            `;
        });
        menuHtml += '</ul>';

        return `
            <div class="order-card fade-in">
                <div class="order-card-header">
                    <div class="employee-name">${order.employee_name}</div>
                    <div class="order-time">
                        <i class="far fa-clock"></i>
                        <span>${order.tapped_at}</span>
                    </div>
                </div>
                <div class="order-card-body">
                    ${menuHtml}
                </div>
            </div>
        `;
    };

    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        document.getElementById('clock').textContent = timeString.replace(/\./g, ':');
    }

    // Panggilan awal dan interval
    showWaitingState(); // Tampilkan waiting state saat pertama kali load
    setInterval(fetchAndUpdateDisplay, 3000); // Cek pesanan baru setiap 3 detik
    setInterval(updateClock, 1000);
    updateClock();
});
</script>
</body>
</html>