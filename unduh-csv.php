<?php
require 'koneksi.php';

// Atur header agar browser mendownloadnya sebagai file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_santri_' . date('Y-m-d') . '.csv');

// Buka output stream untuk menulis file CSV
$output = fopen('php://output', 'w');

// Tulis baris judul kolom (Header CSV)
fputcsv($output, ['No', 'Nama Lengkap', 'Jenis Kelamin', 'Tanggal Lahir', 'Asal Daerah', 'Kamar', 'No HP Wali', 'Angkatan']);

// Ambil semua data dari database
$stmt = $pdo->query("SELECT * FROM santri ORDER BY id DESC");
$daftar = $stmt->fetchAll();

$no = 1;
foreach ($daftar as $row) {
    // Ubah format L/P menjadi Laki-laki / Perempuan agar rapi di CSV
    $jk = ($row['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan';

    // Tulis data baris per baris ke file CSV
    fputcsv($output, [
        $no++,
        $row['nama'],
        $jk,
        $row['tanggal_lahir'],
        $row['asal_daerah'],
        $row['kamar'],
        $row['no_hp_wali'],
        $row['angkatan']
    ]);
}

fclose($output);
exit();
?>