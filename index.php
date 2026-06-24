<?php
declare(strict_types=1);
require __DIR__ . '/config/db.php';
require __DIR__ . '/config/auth.php';

// Redirect if already logged in
if (!empty($_SESSION['user'])) {
    if (($_SESSION['user']['role'] ?? '') === 'admin') {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . '/resident/home.php');
    }
    exit;
}

$msg      = '';
$msgClass = '';
$activeTab = 'login';

// ── Handle Register ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $activeTab = 'register';
    $fullName  = trim($_POST['full_name'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '') {
        $msg = 'Please complete all fields.';
        $msgClass = 'alert-danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Invalid email address.';
        $msgClass = 'alert-danger';
    } elseif (strlen($password) < 8) {
        $msg = 'Password must be at least 8 characters.';
        $msgClass = 'alert-danger';
    } elseif ($password !== $confirm) {
        $msg = 'Passwords do not match.';
        $msgClass = 'alert-danger';
    } else {
        $st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $st->execute([$email]);

        if ($st->fetch()) {
            $msg = 'Email already registered. Please log in.';
            $msgClass = 'alert-danger';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $st   = $pdo->prepare(
                'INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)'
            );
            $st->execute([$fullName, $email, $hash, 'resident']);

            $msg = 'Registered successfully! You can now log in.';
            $msgClass = 'alert-success';
            $activeTab = 'login';
        }
    }
}

// ── Handle Login ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $activeTab = 'login';
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $msg = 'Email and password are required.';
        $msgClass = 'alert-danger';
    } else {
        $st = $pdo->prepare(
            'SELECT id, role, full_name, email, password_hash FROM users WHERE email = ?'
        );
        $st->execute([$email]);
        $u = $st->fetch();

        if (!$u || !password_verify($password, $u['password_hash'])) {
            $msg = 'Invalid email or password.';
            $msgClass = 'alert-danger';
        } else {
            $_SESSION['user'] = [
                'id'        => (int) $u['id'],
                'role'      => $u['role'],
                'full_name' => $u['full_name'],
                'email'     => $u['email'],
            ];

            // Backward-compat keys used by existing api/auth.php / app.js
            $_SESSION['user_id']    = (int) $u['id'];
            $_SESSION['user_name']  = $u['full_name'];
            $_SESSION['user_email'] = $u['email'];
            $_SESSION['user_role']  = $u['role'];

            if ($u['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/resident/home.php');
            }
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Barangay Bonbon Portal - Management System" />
  <title>Barangay Bonbon Portal</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/styles.css" />
  <style>
    body.auth-bg {
      background-image: url("<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/muni.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
    body.auth-bg::before {
      background: rgba(0, 0, 0, 0.45);
    }
  </style>
</head>
<body class="auth-bg">
  <main class="container">

    <!-- LOGIN PAGE -->
    <section id="login-page" class="auth-page<?= $activeTab === 'login' ? ' active' : '' ?>">
      <div class="auth-card">
        <div class="auth-header">
          <h1>🏛️ Barangay Bonbon</h1>
          <p>Management Portal</p>
        </div>
        <h2>Welcome Back</h2>

        <?php if ($activeTab === 'login' && $msg !== ''): ?>
          <div class="alert <?= htmlspecialchars($msgClass) ?>"><?= htmlspecialchars($msg) ?></div>
        <?php else: ?>
          <div id="login-error" class="alert alert-danger hidden"></div>
        <?php endif; ?>

        <form id="login-form" method="post" action="<?= BASE_URL ?>/index.php">
          <input type="hidden" name="action" value="login" />
          <div class="form-group">
            <label for="login-email">Email Address</label>
            <input
              id="login-email"
              name="email"
              type="email"
              placeholder="your@example.com"
              required
              autocomplete="email"
            />
          </div>
          <div class="form-group">
            <label for="login-password">Password</label>
            <input
              id="login-password"
              name="password"
              type="password"
              placeholder="Enter your password"
              required
              autocomplete="current-password"
            />
          </div>
          <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="auth-footer">
          Don't have an account? <a id="switch-to-register" href="#">Register here</a>
        </div>
      </div>
    </section>

    <!-- REGISTER PAGE -->
    <section id="register-page" class="auth-page<?= $activeTab === 'register' ? ' active' : '' ?>">
      <div class="auth-card">
        <div class="auth-header">
          <h1>🏛️ Barangay Bonbon</h1>
          <p>Create Your Account</p>
        </div>
        <h2>Register</h2>

        <?php if ($activeTab === 'register' && $msg !== ''): ?>
          <div class="alert <?= htmlspecialchars($msgClass) ?>"><?= htmlspecialchars($msg) ?></div>
        <?php else: ?>
          <div id="register-error" class="alert alert-danger hidden"></div>
        <?php endif; ?>

        <form id="register-form" method="post" action="<?= BASE_URL ?>/index.php">
          <input type="hidden" name="action" value="register" />
          <div class="form-group">
            <label for="register-name">Full Name</label>
            <input
              id="register-name"
              name="full_name"
              type="text"
              placeholder="Juan Dela Cruz"
              required
              autocomplete="name"
            />
          </div>
          <div class="form-group">
            <label for="register-email">Email Address</label>
            <input
              id="register-email"
              name="email"
              type="email"
              placeholder="your@example.com"
              required
              autocomplete="email"
            />
          </div>
          <div class="form-group">
            <label for="register-password">Password</label>
            <input
              id="register-password"
              name="password"
              type="password"
              placeholder="Create a strong password (min 8 chars)"
              required
              minlength="8"
              autocomplete="new-password"
            />
          </div>
          <div class="form-group">
            <label for="register-confirm">Confirm Password</label>
            <input
              id="register-confirm"
              name="confirm_password"
              type="password"
              placeholder="Confirm your password"
              required
              autocomplete="new-password"
            />
          </div>
          <button type="submit" class="btn-submit">Register</button>
        </form>

        <div class="auth-footer">
          Already have an account? <a id="switch-to-login" href="#">Login here</a>
        </div>
      </div>
    </section>

  </main>

  <script>
    const loginPage    = document.getElementById('login-page');
    const registerPage = document.getElementById('register-page');
    const switchToReg  = document.getElementById('switch-to-register');
    const switchToLog  = document.getElementById('switch-to-login');

    function showLogin() {
      loginPage.classList.add('active');
      registerPage.classList.remove('active');
    }
    function showRegister() {
      registerPage.classList.add('active');
      loginPage.classList.remove('active');
    }

    switchToReg.addEventListener('click', (e) => { e.preventDefault(); showRegister(); });
    switchToLog.addEventListener('click', (e) => { e.preventDefault(); showLogin(); });
  </script>
</body>
</html>
