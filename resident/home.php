<?php
declare(strict_types=1);
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/auth.php';
require_role('resident');

$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Portal – Barangay Bonbon</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css" />
  <style>
    .welcome-card {
      max-width: 640px;
      margin: 0 auto;
      padding: 2rem;
      text-align: center;
    }
    .welcome-card .avatar {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff;
      font-size: 2rem;
      display: grid;
      place-items: center;
      margin: 0 auto 1rem;
    }
    .welcome-card h2 { font-size: 1.5rem; margin-bottom: .3rem; }
    .welcome-card .role-tag {
      display: inline-block;
      background: #dbeafe;
      color: #1e40af;
      padding: .2rem .75rem;
      border-radius: 999px;
      font-size: .8rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
    .service-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: .85rem;
      margin-top: 1.5rem;
      text-align: left;
    }
    .service-item {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: .9rem;
      padding: 1rem;
      display: flex;
      align-items: flex-start;
      gap: .75rem;
    }
    .service-item .icon { font-size: 1.5rem; }
    .service-item .s-title { font-weight: 700; font-size: .9rem; }
    .service-item .s-desc  { font-size: .82rem; color: var(--muted); margin-top: .2rem; }
  </style>
</head>
<body>
  <div style="min-height:100vh; display:flex; flex-direction:column;">

    <!-- Top bar -->
    <header style="background:#fff; border-bottom:1px solid var(--border); padding:.9rem 1.5rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
      <div style="display:flex; align-items:center; gap:.7rem;">
        <span style="font-size:1.5rem;">🏛️</span>
        <div>
          <div style="font-weight:800; font-size:1.05rem; color:var(--primary);">Barangay Bonbon</div>
          <div style="font-size:.8rem; color:var(--muted);">Resident Portal</div>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:.85rem;">
        <span style="font-size:.9rem; color:var(--muted);">
          👤 <?= htmlspecialchars($user['full_name']) ?>
        </span>
        <a class="btn danger" href="<?= BASE_URL ?>/api/logout.php?redirect=1">Logout</a>
      </div>
    </header>

    <!-- Content -->
    <main style="flex:1; padding:2rem 1rem; display:flex; align-items:flex-start; justify-content:center;">
      <div class="card welcome-card">
        <div class="avatar">
          <?= mb_strtoupper(mb_substr($user['full_name'], 0, 1)) ?>
        </div>
        <h2>Welcome, <?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?>!</h2>
        <div class="role-tag">Resident</div>

        <p class="muted small">
          You are logged in to the Barangay Bonbon Resident Portal.<br>
          Below are the available services you can request.
        </p>

        <div class="service-list">
          <div class="service-item">
            <div class="icon">📋</div>
            <div>
              <div class="s-title">Barangay Clearance</div>
              <div class="s-desc">Request a clearance certificate for employment, school, or other purposes.</div>
            </div>
          </div>
          <div class="service-item">
            <div class="icon">📄</div>
            <div>
              <div class="s-title">Barangay Certificate</div>
              <div class="s-desc">Proof of residency issued by the barangay office.</div>
            </div>
          </div>
          <div class="service-item">
            <div class="icon">📑</div>
            <div>
              <div class="s-title">Certificate of Indigency</div>
              <div class="s-desc">For residents who need assistance or financial support.</div>
            </div>
          </div>
          <div class="service-item">
            <div class="icon">🆔</div>
            <div>
              <div class="s-title">Cedula / CTC</div>
              <div class="s-desc">Community Tax Certificate required for various transactions.</div>
            </div>
          </div>
        </div>

        <div style="margin-top:1.5rem; font-size:.85rem; color:var(--muted);">
          For document requests, please visit the Barangay Hall or contact the office directly.
        </div>
      </div>
    </main>

  </div>
</body>
</html>
