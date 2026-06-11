# 🧺 Loffie Laundry - Sistem Informasi Laundry

Sistem informasi laundry berbasis web dengan implementasi algoritma **Grey Prediction GM(1,4)** untuk prediksi waktu penyelesaian cucian.

## Tech Stack

- **Backend:** Laravel 11 · PHP 8.2+
- **Frontend:** Blade · Tailwind CSS 3 · Alpine.js · Chart.js
- **Database:** MySQL
- **PDF Export:** DomPDF

## Fitur Utama

- **Kelola Pesanan**    : CRUD pesanan + auto-trigger prediksi GM(1,4)
- **Prediksi GM(1,4)**  : Estimasi waktu selesai berdasarkan berat, kompleksitas layanan, dan kapasitas mesin
- **Kelola Status** : Kanban board (proses → selesai → diambil) + auto-hitung MAPE/MAE
- **Laporan**       : Generate laporan per periode + download PDF/CSV
- **Grafik**        : Chart.js interaktif (akurasi prediksi, volume transaksi, tren pelanggan)
- **Tracking Publik** : Pelanggan cek status pesanan tanpa login
- **Multi-role**    : Admin (operasional) dan Owner (monitoring + laporan)
