<?php
// jumlah article
$hasil1 = $conn->query("SELECT id FROM article");
$jumlah_article = $hasil1 ? $hasil1->num_rows : 0;

// jumlah gallery
$hasil2 = $conn->query("SELECT id FROM gallery");
$jumlah_gallery = $hasil2 ? $hasil2->num_rows : 0;

// foto user (dari admin.php: $currentUser)
$fotoUser = $currentUser['foto'] ?? '';
$fotoPath = (!empty($fotoUser) && file_exists('img/' . $fotoUser)) ? ('img/' . $fotoUser) : '';
?>

<div class="text-center my-4">
  <div class="fs-4">Selamat Datang,</div>
  <div class="display-6 fw-bold text-danger"><?= htmlspecialchars($_SESSION['username']) ?></div>

  <?php if ($fotoPath): ?>
    <img src="<?= htmlspecialchars($fotoPath) ?>" alt="Foto Profil" style="width:220px;height:220px;object-fit:cover;border-radius:50%;" class="my-3 shadow">
  <?php endif; ?>
</div>

<div class="row row-cols-1 row-cols-md-4 g-4 justify-content-center pt-2">
    <div class="col">
        <div class="card border border-danger mb-3 shadow" style="max-width: 18rem;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="p-3">
                        <h5 class="card-title"><i class="bi bi-newspaper"></i> Article</h5> 
                    </div>
                    <div class="p-3">
                        <span class="badge rounded-pill text-bg-danger fs-2"><?php echo $jumlah_article; ?></span>
                    </div> 
                </div>
            </div>
        </div>
    </div> 
    <div class="col">
        <div class="card border border-danger mb-3 shadow" style="max-width: 18rem;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="p-3">
                        <h5 class="card-title"><i class="bi bi-camera"></i> Gallery</h5> 
                    </div>
                    <div class="p-3">
                        <span class="badge rounded-pill text-bg-danger fs-2"><?php echo $jumlah_gallery; ?></span>
                    </div> 
                </div>
            </div>
        </div>
    </div> 
</div>
