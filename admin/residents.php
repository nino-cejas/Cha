<?php
declare(strict_types=1);
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/auth.php';
require_role('admin');

$user = $_SESSION['user'];

// ── Search / filter ───────────────────────────────────────────────────────────
$search = trim($_GET['q'] ?? '');

$sql    = "SELECT * FROM residents";
$params = [];

if ($search !== '') {
    $sql   .= " WHERE CONCAT(last_name, ' ', first_name) LIKE ? OR household_id LIKE ?";
    $like   = "%{$search}%";
    $params = [$like, $like];
}

$sql .= " ORDER BY last_name ASC, first_name ASC";

$st = $pdo->prepare($sql);
$st->execute($params);
$residents = $st->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Residents – RBI System</title>
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
        <a class="active" href="<?= BASE_URL ?>/admin/residents.php">👥 Residents List</a>
        <a href="<?= BASE_URL ?>/admin/resident_add.php">➕ Add Resident</a>
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
          <h2>Residents List</h2>
          <div class="muted small">
            <?= count($residents) ?> record<?= count($residents) !== 1 ? 's' : '' ?>
            <?= $search !== '' ? '– search: "' . htmlspecialchars($search) . '"' : '' ?>
          </div>
        </div>
        <div class="actions">
          <a class="btn primary" href="<?= BASE_URL ?>/admin/resident_add.php">➕ Add Resident</a>
        </div>
      </header>

      <!-- Search -->
      <form method="get" class="panel-row">
        <div class="search-box">
          <input
            type="search"
            name="q"
            placeholder="Search by name or household ID…"
            value="<?= htmlspecialchars($search) ?>"
          />
        </div>
        <div style="display:flex;gap:.6rem">
          <button class="btn primary" type="submit">Search</button>
          <?php if ($search !== ''): ?>
            <a class="btn" href="<?= BASE_URL ?>/admin/residents.php">Clear</a>
          <?php endif; ?>
        </div>
      </form>

      <!-- Table -->
      <div class="card">
        <?php if (empty($residents)): ?>
          <p class="muted small">No residents found. <a href="<?= BASE_URL ?>/admin/resident_add.php">Add one</a>.</p>
        <?php else: ?>
          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Sex</th>
                  <th>Age</th>
                  <th>Civil Status</th>
                  <th>Household</th>
                  <th>Tags</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($residents as $i => $r):
                  $diff = date_diff(date_create($r['birthdate']), date_create('today'));
                  $age  = (int) $diff->format('%y');
                ?>
                  <tr>
                    <td class="muted small"><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($r['last_name'] . ', ' . $r['first_name']) ?></strong>
                      <?php if ($r['middle_name']): ?>
                        <span class="muted small"><?= htmlspecialchars($r['middle_name']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['sex']) ?></td>
                    <td><?= $age ?></td>
                    <td><?= htmlspecialchars($r['civil_status']) ?></td>
                    <td><?= $r['household_id'] ? htmlspecialchars($r['household_id']) : '<span class="muted">—</span>' ?></td>
                    <td>
                      <?php if ($r['is_pwd']): ?>
                        <span class="badge pwd">PWD</span>
                      <?php endif; ?>
                      <?php if ($r['is_solo_parent']): ?>
                        <span class="badge solo">Solo</span>
                      <?php endif; ?>
                      <?php if ($r['is_osy']): ?>
                        <span class="badge osy">OSY</span>
                      <?php endif; ?>
                      <?php if (!$r['is_pwd'] && !$r['is_solo_parent'] && !$r['is_osy']): ?>
                        <span class="muted small">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </main>

  </div>
</body>
</html>
