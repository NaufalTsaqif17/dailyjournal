<?php
include "koneksi.php";

$keyword = $_POST['keyword'] ?? '';

$sql = "SELECT * FROM gallery
        WHERE deskripsi LIKE ? OR tanggal LIKE ? OR username LIKE ?
        ORDER BY tanggal DESC";

$stmt = $conn->prepare($sql);
$search = "%" . $keyword . "%";
$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();
$hasil = $stmt->get_result();

$no = 1;
while ($row = $hasil->fetch_assoc()) {
?>
  <tr>
    <td><?= $no++ ?></td>
    <td>
      <strong><?= htmlspecialchars($row['deskripsi']) ?></strong>
      <br>pada : <?= $row['tanggal'] ?>
      <br>oleh : <?= htmlspecialchars($row['username']) ?>
    </td>
    <td>
      <?php
        if (!empty($row['gambar']) && file_exists('img/' . $row['gambar'])) {
          echo '<img src="img/' . $row['gambar'] . '" class="img-fluid" style="max-height:220px;" alt="Gambar Gallery">';
        }
      ?>
    </td>
    <td>
      <a href="#" title="edit" class="badge rounded-pill text-bg-success" data-bs-toggle="modal" data-bs-target="#modalEditGallery<?= $row['id'] ?>"><i class="bi bi-pencil"></i></a>
      <a href="#" title="delete" class="badge rounded-pill text-bg-danger" data-bs-toggle="modal" data-bs-target="#modalHapusGallery<?= $row['id'] ?>"><i class="bi bi-x-circle"></i></a>

      <!-- Modal Edit -->
      <div class="modal fade" id="modalEditGallery<?= $row['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5">Edit Gallery</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Deskripsi</label>
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <textarea class="form-control" name="deskripsi" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Ganti Gambar</label>
                  <input type="file" class="form-control" name="gambar">
                </div>
                <div class="mb-3">
                  <label class="form-label">Gambar Lama</label>
                  <?php
                    if (!empty($row['gambar']) && file_exists('img/' . $row['gambar'])) {
                      echo '<br><img src="img/' . $row['gambar'] . '" class="img-fluid" style="max-height:220px;" alt="Gambar Lama">';
                    }
                  ?>
                  <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($row['gambar']) ?>">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <input type="submit" value="simpan" name="simpan_gallery" class="btn btn-primary">
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal Hapus -->
      <div class="modal fade" id="modalHapusGallery<?= $row['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5">Konfirmasi Hapus Gallery</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
              <div class="modal-body">
                Yakin akan menghapus gambar "<strong><?= htmlspecialchars($row['deskripsi']) ?></strong>"?
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="gambar" value="<?= htmlspecialchars($row['gambar']) ?>">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">batal</button>
                <input type="submit" value="hapus" name="hapus_gallery" class="btn btn-primary">
              </div>
            </form>
          </div>
        </div>
      </div>
    </td>
  </tr>
<?php
}

$stmt->close();
$conn->close();
?>
