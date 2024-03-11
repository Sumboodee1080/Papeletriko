<?php
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $loginSuccess = handleLogin($username, $password);

  if ($loginSuccess) {
    echo '<script>window.location.reload();</script>';
    exit();
  } else {
    $errorMessage = 'Invalid username or password.';
  }
}
?>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
  <div class="card login-card">
    <div class="login-image">
      <img src="<?= SITE_DOMAIN ?>/media/img/assets/login-image.jpg" class="card-img-top" alt="...">
    </div>
    <div class="card-body">
      <div class="logo">
        <img src="<?= SITE_DOMAIN ?>/media/img/assets/logo.png" class="card-img-top" alt="...">
      </div>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="mb-3">
          <!-- <label for="username" class="form-label">Username</label> -->
          <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="mb-3">
          <!-- <label for="password" class="form-label">Password</label> -->
          <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
        </div>
        <?php if (!empty($errorMessage)) : ?>
          <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Pumasok</button>
      </form>
    </div>
  </div>
</div>