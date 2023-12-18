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
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
  <div class="card" style="width: 300px;">
    <img src="../../media/img/undraw_book_lover_re_rwjy.svg" class="card-img-top" alt="...">
    <div class="card-body">
      <h5 class="card-title text-center">Login</h5>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <?php if (!empty($errorMessage)) : ?>
          <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>
</div>