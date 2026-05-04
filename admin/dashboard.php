<?php
declare(strict_types=1);
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/auth.php';
require_role('admin');

$user = $_SESSION['user'];

// ── All statistics in two queries ─────────────────────────────────────────────
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(sex = 'Male')   AS male,
        SUM(sex = 'Female') AS female,
        SUM(TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18)            AS children,
        SUM(TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 59) AS adults,
        SUM(TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60)           AS seniors,
        SUM(is_pwd = 1)         AS pwd,
        SUM(is_solo_parent = 1) AS solo,
        SUM(is_osy = 1)         AS osy
    FROM residents
")->fetch();

$households = (int) $pdo->query("
    SELECT COUNT(DISTINCT household_id)
    FROM residents
    WHERE household_id IS NOT NULL AND household_id <> ''
")->fetchColumn();

$male     = (int) ($stats['male']     ?? 0);
$female   = (int) ($stats['female']   ?? 0);
$total    = (int) ($stats['total']    ?? 0);
$children = (int) ($stats['children'] ?? 0);
$adults   = (int) ($stats['adults']   ?? 0);
$seniors  = (int) ($stats['seniors']  ?? 0);
$pwd      = (int) ($stats['pwd']      ?? 0);
$solo     = (int) ($stats['solo']     ?? 0);
$osy      = (int) ($stats['osy']      ?? 0);

// ── Recent residents ──────────────────────────────────────────────────────────
$recent = $pdo->query(
    "SELECT last_name, first_name, sex, birthdate, household_id FROM residents ORDER BY created_at DESC LIMIT 5"
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard – RBI System</title>
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
        <a class="active" href="<?= BASE_URL ?>/admin/dashboard.php">📊 Dashboard</a>
        <a href="<?= BASE_URL ?>/admin/residents.php">👥 Residents List</a>
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
          <h2>Registry of Inhabitants</h2>
          <div class="muted small">Demographics overview – Barangay Bonbon</div>
        </div>
        <div class="actions">
          <button class="btn" onclick="window.print()">🖨️ Print</button>
          <a class="btn primary" href="<?= BASE_URL ?>/admin/resident_add.php">➕ Add Resident</a>
        </div>
      </header>

      <!-- Main stat cards -->
      <section class="cards">
        <div class="stat blue">
          <div>
            <div class="s-label">Male</div>
            <div class="s-num"><?= $male ?></div>
          </div>
          <div class="s-icon">👨</div>
        </div>
        <div class="stat red">
          <div>
            <div class="s-label">Female</div>
            <div class="s-num"><?= $female ?></div>
          </div>
          <div class="s-icon">👩</div>
        </div>
        <div class="stat green">
          <div>
            <div class="s-label">Total Population</div>
            <div class="s-num"><?= $total ?></div>
          </div>
          <div class="s-icon">👥</div>
        </div>
        <div class="stat sky">
          <div>
            <div class="s-label">Households</div>
            <div class="s-num"><?= $households ?></div>
          </div>
          <div class="s-icon">🏠</div>
        </div>
      </section>

      <!-- Quick stats -->
      <section class="quick">
        <div class="q">
          <div class="q-label">Children (&lt;18)</div>
          <div class="q-num"><?= $children ?></div>
        </div>
        <div class="q">
          <div class="q-label">Adults (18–59)</div>
          <div class="q-num"><?= $adults ?></div>
        </div>
        <div class="q">
          <div class="q-label">Seniors (60+)</div>
          <div class="q-num"><?= $seniors ?></div>
        </div>
        <div class="q">
          <div class="q-label">PWD</div>
          <div class="q-num"><?= $pwd ?></div>
        </div>
        <div class="q">
          <div class="q-label">Solo Parent</div>
          <div class="q-num"><?= $solo ?></div>
        </div>
        <div class="q">
          <div class="q-label">OSY</div>
          <div class="q-num"><?= $osy ?></div>
        </div>
      </section>

      <!-- Recent records -->
      <div class="card">
        <div class="panel-row">
          <div>
            <strong>Recently Added</strong>
            <div class="muted small">Last 5 residents</div>
          </div>
          <a class="btn" href="<?= BASE_URL ?>/admin/residents.php">View All</a>
        </div>

        <?php if (empty($recent)): ?>
          <p class="muted small">No residents recorded yet. <a href="<?= BASE_URL ?>/admin/resident_add.php">Add one now</a>.</p>
        <?php else: ?>
          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Household</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['last_name'] . ', ' . $r['first_name']) ?></td>
                    <td><?= htmlspecialchars($r['sex']) ?></td>
                    <td><?= htmlspecialchars($r['birthdate']) ?></td>
                    <td><?= $r['household_id'] ? htmlspecialchars($r['household_id']) : '<span class="muted">—</span>' ?></td>
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
