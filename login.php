<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "koneksi.php";

    $username = $_POST['user'] ?? '';
    $password = $_POST['pass'] ?? '';

    $stmt = $conn->prepare("SELECT username, password FROM user WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $valid = false;

    if ($user) {
  
        if (password_verify($password, $user['password'])) {
            $valid = true;

        } elseif (md5($password) === $user['password']) {
            $valid = true;

            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $up = $conn->prepare("UPDATE user SET password=? WHERE username=?");
            $up->bind_param("ss", $newHash, $username);
            $up->execute();
            $up->close();

        } elseif ($password === $user['password']) {
            $valid = true;

            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $up = $conn->prepare("UPDATE user SET password=? WHERE username=?");
            $up->bind_param("ss", $newHash, $username);
            $up->execute();
            $up->close();
        }
    }

    if ($valid) {
        $_SESSION['username'] = $username;
        header('Location: admin.php');
        exit();
    } else {
        $error = 'Username atau Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | My Daily Journal</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />
    <link rel="icon" href="img/logo.png" />
    <style>
      body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .login-container {
        background: white;
        border-radius: 20px;
        padding: 40px 50px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div class="login-container">
      <div class="text-center mb-4">
        <i class="bi bi-person-circle display-4 text-primary"></i>
        <h3 class="mt-3">My Daily Journal</h3>
        <hr />
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($error) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form action="" method="post">
        <div class="mb-3">
          <input
            type="text"
            name="user"
            class="form-control py-2 rounded-4"
            placeholder="Username"
            required
            autofocus
          />
        </div>
        <div class="mb-4">
          <input
            type="password"
            name="pass"
            class="form-control py-2 rounded-4"
            placeholder="Password"
            required
          />
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary rounded-4 py-2">Login</button>
        </div>
      </form>

      <small class="text-center d-block mt-3 text-muted">
        Password: admin/123456 dan april/april
      </small>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
