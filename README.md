# Sistem Kantin Karyawan (Employee Canteen System)

Sistem Kantin Karyawan adalah aplikasi web berbasis Laravel 10 yang dirancang untuk mengelola dan memonitor proses pengambilan jatah makan karyawan di sebuah perusahaan. Sistem ini menggantikan proses manual dengan sistem tapping kartu RFID/NFC yang terintegrasi, menyediakan data real-time, dan mempermudah proses pelaporan.

![Tampilan Antarmuka Tapping](https://imgur.com/a/XeY9lYX) 
*Ganti dengan screenshot antarmuka tappingmu*

---

## ✨ Fitur Utama

Sistem ini dilengkapi dengan berbagai fitur untuk memenuhi kebutuhan operasional kantin modern:

### Panel Admin & HR
- **Sistem Login Berbasis Role:** Memisahkan hak akses antara Admin dan HR.
- **Dashboard Interaktif:** Menampilkan statistik penting secara real-time seperti jumlah makanan yang disajikan, jumlah karyawan aktif, jadwal aktif, dan peringatan stok kritis. Data diperbarui secara otomatis menggunakan AJAX.
- **Manajemen Master Data (CRUD):**
    - **Kelola Menu:** Menambah, mengubah, dan menghapus data menu dengan kategori (Utama, Spesial, Opsional).
    - **Kelola Karyawan:** Mengelola data karyawan, termasuk menautkan kartu akses saat pendaftaran.
    - **Kelola Kartu:** Mengelola aset kartu RFID/NFC, menugaskannya ke karyawan, dan mengubah statusnya (hilang, rusak).
    - **Kelola Counter:** Mendaftarkan counter/gate fisik dan mengatur jam operasionalnya.
- **Manajemen Jadwal Makan:**
    - Membuat jadwal harian (Makan Siang/Malam) dengan tipe hari (Normal/Spesial).
    - Menugaskan beberapa menu ke satu atau banyak counter sekaligus dalam satu form.
    - Aturan bisnis yang kompleks untuk validasi menu di hari spesial.
- **Fitur Operasional Real-time:**
    - **Pindah Menu:** Memindahkan stok menu dari satu counter ke counter lain saat operasional sedang berlangsung.
- **Pelaporan:**
    - **Log Transaksi:** Melihat riwayat tapping yang dikelompokkan per karyawan, dengan halaman detail untuk setiap karyawan.
    - **Laporan Konsumsi:** Melihat rekapitulasi total konsumsi per menu dengan filter rentang tanggal. Laporan ini juga interaktif dengan AJAX dan memiliki halaman detail untuk melihat siapa saja yang mengonsumsi menu tertentu.

### Antarmuka Tapping Publik
- **Halaman Tapping Tanpa Login:** Didesain untuk diakses langsung dari perangkat di setiap counter.
- **Tampilan Menu Dinamis:** Secara otomatis menampilkan menu yang aktif sesuai jadwal dan counter. Halaman ini juga melakukan auto-update setiap 30 detik.
- **Proses Tapping Efisien:** Staff dapat memilih menu opsional terlebih dahulu, lalu karyawan melakukan tap kartu untuk mencatat semua menu (utama + opsional) dalam satu kali transaksi.
- **Validasi Real-time:** Sistem akan langsung memberikan notifikasi (sukses atau gagal) jika kartu tidak valid, karyawan sudah makan, stok habis, atau counter sedang tidak aktif.

---

## 🚀 Teknologi yang Digunakan

- **Backend:** PHP 8.1+, Laravel 10
- **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5 (via SB Admin 2)
- **Database:** MySQL / MariaDB
- **Fitur Frontend Lanjutan:**
    - **AJAX (Fetch API):** Digunakan di Dashboard, Laporan, dan Antarmuka Tapping untuk pengalaman pengguna yang dinamis tanpa refresh.
    - **Chart.js:** Untuk menampilkan grafik statistik di dashboard.
    - **SweetAlert2:** Untuk notifikasi yang modern dan interaktif.
    - **Tom Select:** Untuk input multi-select yang canggih di form jadwal.

---

## ⚙️ Panduan Instalasi

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal:

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/your-username/canteen-system.git](https://github.com/your-username/canteen-system.git)
    cd canteen-system
    ```

2.  **Install Dependensi**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Lingkungan**
    - Salin file `.env.example` menjadi `.env`.
      ```bash
      cp .env.example .env
      ```
    - Buat *application key* baru.
      ```bash
      php artisan key:generate
      ```
    - Atur koneksi database di dalam file `.env`.
      ```
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=canteen_db
      DB_USERNAME=root
      DB_PASSWORD=
      ```

4.  **Migrasi & Seeding Database**
    Jalankan migrasi untuk membuat semua tabel, lalu jalankan seeder untuk mengisi data awal (user admin, counter, dan menu).
    ```bash
    php artisan migrate --seed
    ```

5.  **Compile Aset Frontend**
    ```bash
    npm run dev
    ```

6.  **Jalankan Server Development**
    ```bash
    php artisan serve
    ```
    Aplikasi sekarang bisa diakses di `http://127.0.0.1:8000`.

---

## 👨‍💻 Cara Penggunaan

Setelah instalasi selesai, Anda bisa login ke panel admin menggunakan kredensial default yang dibuat oleh `UserSeeder`.

- **URL Login:** `http://127.0.0.1:8000/login`
- **Email:** `admin@canteen.com`
- **Password:** `password`

### Endpoint API
Sistem ini memiliki beberapa endpoint API publik yang digunakan oleh antarmuka tapping:
- `GET /api/tapping/{gate}/menu`: Mengambil daftar menu aktif untuk sebuah counter.
- `POST /api/tap`: Memproses transaksi tapping kartu.

---

Terima kasih telah menggunakan sistem ini!
