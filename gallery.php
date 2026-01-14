<div class="container">
  <div class="row mb-2">
    <div class="col-md-6">
      <button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#modalTambahGallery">
        <i class="bi bi-plus-lg"></i> Tambah Gallery
      </button>
    </div>
    <div class="col-md-6">
      <div class="input-group">
        <input type="text" id="search_gallery" class="form-control" placeholder="Ketikkan minimal 3 karakter untuk pencarian.." autocomplete="off">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width:70px;">No</th>
            <th>Deskripsi</th>
            <th style="width:520px;">Gambar</th>
            <th style="width:120px;">Aksi</th>
          </tr>
        </thead>
        <tbody id="result_gallery"></tbody>
      </table>
    </div>
  </div>

  <!-- Modal Tambah Gallery -->
  <div class="modal fade" id="modalTambahGallery" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Tambah Gallery</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" placeholder="Tuliskan deskripsi gambar" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Gambar</label>
              <input type="file" class="form-control" name="gambar" required>
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
</div>

<script>
  function loadGallery(keyword = '') {
    $.ajax({
      url: 'gallery_search.php',
      type: 'POST',
      data: { keyword: keyword },
      success: function (data) {
        $('#result_gallery').html(data);
      }
    });
  }

  loadGallery();

  $('#search_gallery').on('input', function () {
    const keyword = $(this).val().trim();
    if (keyword.length >= 3) {
      loadGallery(keyword);
    } else if (keyword.length === 0) {
      loadGallery('');
    } else {
      loadGallery('');
    }
  });
</script>

<?php
include "upload_foto.php";

// SIMPAN / UPDATE
if (isset($_POST['simpan_gallery'])) {
  $deskripsi = $_POST['deskripsi'];
  $tanggal = date("Y-m-d H:i:s");
  $username = $_SESSION['username'];

  $gambar = '';
  $nama_gambar = $_FILES['gambar']['name'] ?? '';

  // file baru
  if ($nama_gambar !== '') {
    $cek_upload = upload_foto($_FILES['gambar']);
    if ($cek_upload['status']) {
      $gambar = $cek_upload['message'];
    } else {
      echo "<script>alert('" . $cek_upload['message'] . "');document.location='admin.php?page=gallery';</script>";
      die;
    }
  }

  // UPDATE id
  if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    if ($nama_gambar === '') {
      $gambar = $_POST['gambar_lama'];
    } else {
      if (!empty($_POST['gambar_lama']) && file_exists('img/' . $_POST['gambar_lama'])) {
        unlink('img/' . $_POST['gambar_lama']);
      }
    }

    $stmt = $conn->prepare("UPDATE gallery SET deskripsi=?, gambar=?, tanggal=?, username=? WHERE id=?");
    $stmt->bind_param("ssssi", $deskripsi, $gambar, $tanggal, $username, $id);
    $ok = $stmt->execute();
  } else {
    // INSERT
    $stmt = $conn->prepare("INSERT INTO gallery (deskripsi, gambar, tanggal, username) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $deskripsi, $gambar, $tanggal, $username);
    $ok = $stmt->execute();
  }

  if ($ok) {
    echo "<script>alert('Simpan data sukses');document.location='admin.php?page=gallery';</script>";
  } else {
    echo "<script>alert('Simpan data gagal');document.location='admin.php?page=gallery';</script>";
  }

  $stmt->close();
}

// HAPUS
if (isset($_POST['hapus_gallery'])) {
  $id = (int)$_POST['id'];
  $gambar = $_POST['gambar'] ?? '';

  if ($gambar !== '' && file_exists('img/' . $gambar)) {
    unlink('img/' . $gambar);
  }

  $stmt = $conn->prepare("DELETE FROM gallery WHERE id=?");
  $stmt->bind_param("i", $id);
  $ok = $stmt->execute();

  if ($ok) {
    echo "<script>alert('Hapus data sukses');document.location='admin.php?page=gallery';</script>";
  } else {
    echo "<script>alert('Hapus data gagal');document.location='admin.php?page=gallery';</script>";
  }

  $stmt->close();
}
?>
