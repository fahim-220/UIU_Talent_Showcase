<?php
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect('/dashboard.php');
}

$error = '';
$old = ['name' => '', 'email' => '', 'department' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $old = ['name' => $name, 'email' => $email, 'department' => $department];

    if (empty($name)) {
        $error = "Name is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Valid email is required.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        } else {
            // insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, department) VALUES (?, ?, ?, 'user', ?)");
            $stmt->execute([$name, $email, $hash, $department]);
            
            set_flash('success', 'Registration successful! Please log in.');
            redirect('/login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - UIU Talent Showcase</title>
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
        <h2>Create an Account</h2>
        <p>Join the UIU Talent Showcase</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" class="form-input" value="<?php echo e($old['name']); ?>" required />
        </div>
        
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" class="form-input" value="<?php echo e($old['email']); ?>" required />
        </div>

        <div class="form-group">
          <label>Department (Optional)</label>
          <input type="text" name="department" class="form-input" value="<?php echo e($old['department']); ?>" />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-input" required minlength="8" />
        </div>

        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" class="form-input" required minlength="8" />
        </div>

        <button type="submit" class="btn-primary auth-btn">Register</button>
      </form>
      
      <p class="auth-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
