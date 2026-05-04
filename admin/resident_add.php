<?php
declare(strict_types=1);
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/auth.php';
require_role('admin');

$user = $_SESSION['user'];
$msg  = '';
$msgClass = '';

// ── Handle form submission ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastName    = trim($_POST['last_name']    ?? '');
    $firstName   = trim($_POST['first_name']   ?? '');
    $middleName  = trim($_POST['middle_name']  ?? '') ?: null;
    $sex         = $_POST['sex']               ?? '';
    $birthdate   = $_POST['birthdate']         ?? '';
    $civilStatus = $_POST['civil_status']      ?? 'Single';
    $householdId = trim($_POST['household_id'] ?? '') ?: null;
    $isPwd       = isset($_POST['is_pwd'])         ? 1 : 0;
    $isSolo      = isset($_POST['is_solo_parent'])  ? 1 : 0;
    $isOsy       = isset($_POST['is_osy'])          ? 1 : 0;

    if ($lastName === '' || $firstName === '' || $sex === '' || $birthdate === '') {
        $msg = 'Last name, first name, sex, and birthdate are required.';
        $msgClass = 'alert err';
    } else {
        $st = $pdo->prepare(
            'INSERT INTO residents
               (last_name, first_name, middle_name, sex, birthdate, civil_status,
                household_id, is_pwd, is_solo_parent, is_osy, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $lastName, $firstName, $middleName, $sex, $birthdate, $civilStatus,
            $householdId, $isPwd, $isSolo, $isOsy, $user['id'],
        ]);

        $msg = 'Resident "' . htmlspecialchars("$firstName $lastName") . '" added successfully.';
        $msgClass = 'alert ok';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Resident – RBI System</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css" />
</head>
<body>
  <div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-badge">🏛️</div>
        <div>
          <div class="brand-title">RBI SYSTEM</div>
          <div class="brand-sub">Barangay Bonbon</div>
        </div>
      </div>

      <nav class="nav">
        <a href="<?= BASE_URL ?>/admin/dashboard.php">📊 Dashboard</a>
        <a href="<?= BASE_URL ?>/admin/residents.php">👥 Residents List</a>
        <a class="active" href="<?= BASE_URL ?>/admin/resident_add.php">➕ Add Resident</a>
      </nav>

      <div class="sidebar-footer">
        <div class="muted small">
          👤 <?= htmlspecialchars($user['full_name']) ?><br>
          <span style="font-size:.8rem">Administrator</span>
        </div>
        <a class="btn danger" href="<?= BASE_URL ?>/api/logout.php?redirect=1" style="text-align:center">Logout</a>
      </div>
    </aside>

    <!-- Main content -->
    <main class="main">
      <header class="topbar">
        <div>
          <h2>Add Resident</h2>
          <div class="muted small">Register a new inhabitant</div>
        </div>
        <div class="actions">
          <a class="btn" href="<?= BASE_URL ?>/admin/residents.php">← Back to List</a>
        </div>
      </header>

      <div class="card" style="max-width:800px">
        <?php if ($msg !== ''): ?>
          <div class="<?= $msgClass ?>"><?= $msg ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="form-grid">

            <div class="form-group">
              <label for="last_name">Last Name <span style="color:red">*</span></label>
              <input id="last_name" name="last_name" type="text" placeholder="Dela Cruz" required />
            </div>

            <div class="form-group">
              <label for="first_name">First Name <span style="color:red">*</span></label>
              <input id="first_name" name="first_name" type="text" placeholder="Juan" required />
            </div>

            <div class="form-group full">
              <label for="middle_name">Middle Name</label>
              <input id="middle_name" name="middle_name" type="text" placeholder="Santos (optional)" />
            </div>

            <div class="form-group">
              <label for="sex">Sex <span style="color:red">*</span></label>
              <select id="sex" name="sex" required>
                <option value="">— Select —</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>

            <div class="form-group">
              <label for="birthdate">Birthdate <span style="color:red">*</span></label>
              <input id="birthdate" name="birthdate" type="date" required />
            </div>

            <div class="form-group">
              <label for="civil_status">Civil Status</label>
              <select id="civil_status" name="civil_status">
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Divorced">Divorced</option>
                <option value="Separated">Separated</option>
              </select>
            </div>

            <div class="form-group">
              <label for="household_id">Household ID</label>
              <input id="household_id" name="household_id" type="text" placeholder="e.g., HH-001 (optional)" />
            </div>

            <div class="form-group full">
              <label>Special Classifications</label>
              <div class="check-group">
                <label>
                  <input type="checkbox" name="is_pwd" value="1" />
                  Person with Disability (PWD)
                </label>
                <label>
                  <input type="checkbox" name="is_solo_parent" value="1" />
                  Solo Parent
                </label>
                <label>
                  <input type="checkbox" name="is_osy" value="1" />
                  Out-of-School Youth (OSY)
                </label>
              </div>
            </div>

            <div class="form-group full" style="margin-top:.5rem">
              <button type="submit" class="btn primary" style="max-width:220px">➕ Add Resident</button>
            </div>

          </div>
        </form>
      </div>
    </main>

  </div>
</body>
</html>
