<?php
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect('/dashboard.php');
}

$error = '';
$success = get_flash('success');
$old_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old_email = $email;

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] === 'blocked') {
                $error = "Your account has been blocked. Please contact an administrator.";
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                
                if ($user['role'] === 'admin') {
                    redirect('/admin/index.php');
                } else {
                    redirect('/dashboard.php');
                }
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - UIU Talent Showcase</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/pages/auth.css" />
</head>
<body class="auth-page">
  <?php include 'includes/header.php'; ?>

  <main class="auth-main">
    <div class="auth-card">
      <div class="auth-header">
        <h2>Welcome Back</h2>
        <p>Log in to your account</p>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e($success); ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" class="form-input" value="<?php echo e($old_email); ?>" required />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-input" required />
        </div>

        <button type="submit" class="btn-primary auth-btn">Log In</button>
      </form>
      
      <p class="auth-link">Don't have an account? <a href="register.php">Register</a></p>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
