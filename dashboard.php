<?php
require_once 'includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Dashboard - UIU Talent Showcase</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main style="padding: 100px 20px; text-align: center; min-height: 60vh;">
    <h2>User Dashboard</h2>
    <p style="color: var(--text3); margin-top: 10px;">Coming in Phase 6</p>
  </main>

  <?php include 'includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
