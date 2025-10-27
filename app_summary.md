# Struktur Data Utama (Entitas dan Atribut)

## User
- Menyimpan data seluruh pengguna sistem.
- Atribut: user_id, username, password, role, nama_lengkap, email.

## Barang
- Menyimpan data barang yang dikelola dalam gudang.
- Atribut: barang_id, nama_barang, jenis_barang, stok, satuan, harga_satuan.

## BarangMasuk
- Menyimpan catatan barang yang masuk ke gudang.
- Atribut: barang_masuk_id, barang_id, user_id, tanggal_masuk, jumlah.

## BarangKeluar
- Menyimpan catatan barang yang keluar dari gudang.
- Atribut: barang_keluar_id, barang_id, user_id, tanggal_keluar, jumlah.

## QualityCheck (QC)
- Menyimpan hasil pengecekan kualitas barang.
- Atribut: qc_id, barang_id, user_id, tanggal_pengecekan, status_qc, keterangan.

## PesananMassal
- Menyimpan data pesanan produksi massal.
- Atribut: pesanan_id, user_id, nama_pemesan, tanggal_pesan, status_pesanan.

## DetailPesanan
- Menyimpan rincian setiap pesanan massal.
- Atribut: detail_id, pesanan_id, nama_produk, jenis_kain, warna, ukuran, jumlah.

## TransaksiPenjualan
- Menyimpan data transaksi penjualan produk atau operasional keuangan.
- Atribut: transaksi_id, user_id, barang_id, tanggal_transaksi, jumlah_terjual, total_harga, keterangan.


# Relasi Antar Entitas
- User memiliki banyak data BarangMasuk, BarangKeluar, QualityCheck, PesananMassal, dan TransaksiPenjualan.
- Barang dapat muncul di banyak BarangMasuk, BarangKeluar, QualityCheck, dan TransaksiPenjualan.
- PesananMassal memiliki banyak DetailPesanan (relasi one-to-many).
- Setiap DetailPesanan, BarangMasuk, BarangKeluar, QualityCheck, dan TransaksiPenjualan terhubung dengan User melalui user_id untuk melacak siapa yang melakukan aksi tersebut.