<?php
require 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM santri WHERE id = ?');
$stmt->execute([$id]);
$santri = $stmt->fetch();

if (!$santri) {
  $_SESSION['pesan'] = 'Data santri tidak ditemukan.';
  $_SESSION['tipe']  = 'danger';
  header('Location: index.php');
  exit;
}

// Kita gunakan variabel $data agar cocok saat di-include dengan form.php
$data = $santri;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $kolomForm = ['nama', 'jenis_kelamin', 'tanggal_lahir', 'asal_daerah', 'kamar', 'no_hp_wali', 'angkatan'];
  foreach ($kolomForm as $kolom) {
    $data[$kolom] = trim($_POST[$kolom] ?? '');
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
    $sql = 'UPDATE santri SET
                nama          = :nama,
                jenis_kelamin = :jk,
                tanggal_lahir = :tgl,
                asal_daerah   = :asal,
                kamar         = :kamar,
                no_hp_wali    = :hp,
                angkatan      = :angkatan
              WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':nama'     => $data['nama'],
      ':jk'       => $data['jenis_kelamin'],
      ':tgl'      => $data['tanggal_lahir'] ?: null,
      ':asal'     => $data['asal_daerah'],
      ':kamar'    => $data['kamar'],
      ':hp'       => $data['no_hp_wali'],
      ':angkatan' => (int)$data['angkatan'],
      ':id'       => $id,
    ]);

    $_SESSION['pesan'] = 'Data santri berhasil diperbarui.';
    header('Location: index.php');
    exit;
  }
}

include 'header.php';
?>

<h4 class="mb-3">Edit Santri</h4>

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
  <!-- Panggil form yang sudah dipisah ke form.php -->
  <?php include 'form.php'; ?>

  <div>
    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
  </div>
</form>

<?php include 'footer.php'; ?>