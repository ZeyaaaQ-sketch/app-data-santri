<?php
require 'koneksi.php';

$cari  = trim($_GET['cari'] ?? '');
$kamar = trim($_GET['kamar'] ?? '');

// Membangun query dinamis untuk Pencarian dan Filter Kamar
$sql = 'SELECT * FROM santri WHERE 1=1';
$params = [];

if ($cari !== '') {
    $sql .= ' AND nama LIKE :cari';
    $params[':cari'] = "%$cari%";
}

if ($kamar !== '') {
    $sql .= ' AND kamar = :kamar';
    $params[':kamar'] = $kamar;
}

$sql .= ' ORDER BY id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$daftar = $stmt->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Daftar Santri</h4>
  <div>
    <a href="unduh-csv.php" class="btn btn-outline-success me-1">Unduh CSV</a>
    <a href="tambah.php" class="btn btn-success">+ Tambah Santri</a>
  </div>
</div>

<!-- FORM PENCARIAN & FILTER KAMAR -->
<form method="get" class="row g-2 mb-3 align-items-center">
  <div class="col-sm-5 col-md-4">
    <input type="text" name="cari" class="form-control"
      placeholder="Cari nama santri..." value="<?= e($cari) ?>">
  </div>
  <div class="col-sm-4 col-md-3">
    <select name="kamar" class="form-select">
      <option value="">-- Semua Kamar --</option>
      <option value="1" <?= $kamar === '1' ? 'selected' : '' ?>>Kamar 1</option>
      <option value="2" <?= $kamar === '2' ? 'selected' : '' ?>>Kamar 2</option>
      <option value="B1" <?= $kamar === 'B1' ? 'selected' : '' ?>>Kamar B1</option>
    </select>
  </div>
  <div class="col-auto">
    <button class="btn btn-outline-success">Filter & Cari</button>
    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>

<p class="text-muted">Menampilkan <?= count($daftar) ?> santri.</p>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
      <thead class="table-success">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>L/P</th>
          <th>Umur</th>
          <th>Tanggal Lahir</th>
          <th>Asal Daerah</th>
          <th>Kamar</th>
          <th>No. HP Wali</th>
          <th>Angkatan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($daftar as $no => $s): ?>
          <?php
              $today = new DateTime('today');
              $birthDate = !empty($s['tanggal_lahir']) ? new DateTime($s['tanggal_lahir']) : null;
              $umur = $birthDate ? $birthDate->diff($today)->y . ' Tahun' : '-';
          ?>
          <tr>
              <td><?= $no + 1 ?></td>
              <td><?= e($s['nama']) ?></td>
              <td><?= ($s['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan' ?></td>
              <td><?= $umur ?></td>
              <td><?= e($s['tanggal_lahir']) ?></td>
              <td><?= e($s['asal_daerah']) ?></td>
              <td><?= e($s['kamar']) ?></td>
              <td><?= e($s['no_hp_wali']) ?></td>
              <td><?= e($s['angkatan']) ?></td>
              <td>
                  <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <button type="button" class="btn btn-sm btn-danger"
                    data-bs-toggle="modal" data-bs-target="#modalHapus"
                    data-id="<?= $s['id'] ?>" data-nama="<?= e($s['nama']) ?>">Hapus</button>
              </td>
          </tr>
        <?php endforeach; ?>

        <?php if (count($daftar) === 0): ?>
          <tr>
            <td colspan="10" class="text-center text-muted">Belum ada data santri.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL HAPUS DATA -->
<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="hapus.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Yakin ingin menghapus data <strong id="namaHapus"></strong>?
        <input type="hidden" name="id" id="idHapus">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById('modalHapus').addEventListener('show.bs.modal', function(event) {
    var tombol = event.relatedTarget;
    document.getElementById('idHapus').value = tombol.getAttribute('data-id');
    document.getElementById('namaHapus').textContent = tombol.getAttribute('data-nama');
  });
</script>

<?php include 'footer.php'; ?>