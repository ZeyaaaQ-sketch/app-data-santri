<?php
require 'koneksi.php';

$errors = [];
$data = [
  'nama'          => '',
  'jenis_kelamin' => '',
  'tanggal_lahir' => '',
  'asal_daerah'   => '',
  'kamar'         => '',
  'no_hp_wali'    => '',
  'angkatan'      => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($data as $kolom => $nilai) {
    $data[$kolom] = trim($_POST[$kolom] ?? '');
  }

  if (mb_strlen($data['nama']) > 100) {
    $errors[] = 'Nama maksimal 100 karakter.';
  }

  if ($data['no_hp_wali'] !== '' && !preg_match('/^[0-9+]{8,15}$/', $data['no_hp_wali'])) {
    $errors[] = 'No. HP wali hanya boleh angka (8-15 digit).';
  }

  if ($data['tanggal_lahir'] !== '') {
    $tgl = DateTime::createFromFormat('Y-m-d', $data['tanggal_lahir']);
    if (!$tgl || $tgl->format('Y-m-d') !== $data['tanggal_lahir']) {
      $errors[] = 'Format tanggal lahir tidak valid.';
    }
  }

  if ($data['nama'] === '') {
    $errors[] = 'Nama wajib diisi.';
  }

  if (!in_array($data['jenis_kelamin'], ['L', 'P'])) {
    $errors[] = 'Jenis kelamin wajib dipilih.';
  }

  if ($data['angkatan'] === '' || !is_numeric($data['angkatan'])) {
    $errors[] = 'Angkatan wajib diisi dengan angka.';
  }

  if (count($errors) === 0) {
    $sql = 'INSERT INTO santri
                (nama, jenis_kelamin, tanggal_lahir, asal_daerah, kamar, no_hp_wali, angkatan)
              VALUES
                (:nama, :jk, :tgl, :asal, :kamar, :hp, :angkatan)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':nama'     => $data['nama'],
      ':jk'       => $data['jenis_kelamin'],
      ':tgl'      => $data['tanggal_lahir'] ?: null,
      ':asal'     => $data['asal_daerah'],
      ':kamar'    => $data['kamar'],
      ':hp'       => $data['no_hp_wali'],
      ':angkatan' => (int)$data['angkatan'],
    ]);

    $_SESSION['pesan'] = 'Data santri berhasil ditambahkan.';
    header('Location: index.php');
    exit;
  }
}

include 'header.php';
?>

<h4 class="mb-3">Tambah Santri</h4>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $pesan): ?>
        <li><?= e($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="card card-body shadow-sm">
  <!-- Panggil form satu kali saja di sini -->
  <?php include 'form.php'; ?>

  <div class="mt-3">
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
  </div>
</form>

<?php include 'footer.php'; ?>