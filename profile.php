<?php

include "upload_foto.php";

if (isset($_POST['simpan_profile'])) {
  $username = $_SESSION['username'];
  $password_baru = $_POST['password'] ?? '';
  $password_baru = trim($password_baru);

  // foto
  $foto_baru = '';
  $nama_foto = $_FILES['foto']['name'] ?? '';
  if ($nama_foto !== '') {
    $cek_upload = upload_foto($_FILES['foto']);
    if ($cek_upload['status']) {
      $foto_baru = $cek_upload['message'];
    } else {
      echo "<script>alert('" . $cek_upload['message'] . "');document.location='admin.php?page=profile';</script>";
      die;
    }
  }

  $stmt = $conn->prepare("SELECT password, foto FROM user WHERE username=? LIMIT 1");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!$user) {
    echo "<script>alert('User tidak ditemukan');document.location='admin.php?page=profile';</script>";
    die;
  }

  $password_final = $user['password'];
  $foto_final = $user['foto'] ?? '';

  if ($password_baru !== '') {
    $password_final = password_hash($password_baru, PASSWORD_DEFAULT);
  }

  // jika upload foto baru
  if ($foto_baru !== '') {
    if (!empty($foto_final) && file_exists('img/' . $foto_final)) {
      unlink('img/' . $foto_final);
    }
    $foto_final = $foto_baru;
  }

  $stmt = $conn->prepare("UPDATE user SET password=?, foto=? WHERE username=?");
  $stmt->bind_param("sss", $password_final, $foto_final, $username);
  $ok = $stmt->execute();
  $stmt->close();

  if ($ok) {
    echo "<script>alert('Profile berhasil diperbarui');document.location='admin.php?page=profile';</script>";
  } else {
    echo "<script>alert('Gagal memperbarui profile');document.location='admin.php?page=profile';</script>";
  }
}

$stmt = $conn->prepare("SELECT username, foto FROM user WHERE username=? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<div class="container" style="max-width: 720px;">

  <form method="post" action="" enctype="multipart/form-data">

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($currentUser['username']) ?>" readonly>
    </div>

    <div class="mb-3">
      <label class="form-label">Ganti Password</label>
      <input type="password" name="password" class="form-control" placeholder="Tuliskan Password Baru Jika Ingin Mengganti Password Saja">
      <small class="text-muted">Kosongkan jika tidak ingin mengganti password.</small>
    </div>

    <div class="mb-3">
      <label class="form-label">Ganti Foto Profil</label>
      <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.gif">
      <small class="text-muted">Opsional. Maks 500KB.</small>
    </div>

    <div class="mb-3">
      <label class="form-label">Foto Profil Saat Ini</label><br>
      <?php if (!empty($currentUser['foto']) && file_exists('img/' . $currentUser['foto'])): ?>
        <img src="img/<?= htmlspecialchars($currentUser['foto']) ?>" alt="Foto Profil" style="width:120px;height:120px;object-fit:cover;border-radius:14px;">
      <?php else: ?>
        <div class="text-muted">Belum ada foto.</div>
      <?php endif; ?>
    </div>

    <button type="submit" name="simpan_profile" class="btn btn-primary">simpan</button>
  </form>
</div>
