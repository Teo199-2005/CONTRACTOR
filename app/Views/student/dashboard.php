<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* Force white text on badges */
.badge.text-white {
  color: #ffffff !important;
}

.badge.bg-success.text-white {
  background-color: #198754 !important;
  color: #ffffff !important;
}

.badge.bg-warning.text-white {
  background-color: #ffc107 !important;
  color: #ffffff !important;
}

.badge.bg-info.text-white {
  background-color: #0dcaf0 !important;
  color: #ffffff !important;
}
</style>

<!-- Compact Header Section with Blue Divider -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Student Dashboard</h1>
      <p class="text-muted mb-0 small">Welcome back, <span class="fw-semibold text-dark"><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></span></p>
      <p class="text-muted small">Last login: <?= date('M j, Y \a\t g:i A') ?></p>
    </div>
  </div>
  
  <!-- Blue Divider Line -->
  <div class="blue-divider"></div>
</div>

<!-- Compact Stats Cards -->
<div class="d-flex gap-3 mb-4">
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-wallet2 text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-primary mb-1 small"><?= esc($student['student_id'] ?? 'Pending') ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">Student ID</p>
    </div>
  </div>
  
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-mortarboard-fill text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-success mb-1 small">Grade <?= esc($student['grade_level']) ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">Current Grade Level</p>
    </div>
  </div>
  
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-graph-up-arrow text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-info mb-1 small"><?= $quarterAverage ? number_format($quarterAverage, 2) : 'N/A' ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">Q<?= $currentQuarter ?> Average</p>
    </div>
  </div>
  
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-person-check-fill text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-warning mb-1 small"><?= ucfirst(esc($student['enrollment_status'])) ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">Enrollment Status</p>
    </div>
  </div>
</div>

<!-- Blue Divider -->
<div class="blue-divider mb-4"></div>

<!-- Academic Progress Section -->
<div class="row mb-4">
  <div class="col-lg-8">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-transparent border-0 p-3">
        <h4 class="card-title mb-0 small">Academic Progress Overview</h4>
      </div>
      <div class="card-body p-3">
        <div class="d-flex align-items-center mb-3">
          <div class="me-3">
            <h5 class="mb-1 small">Current Quarter Performance</h5>
            <div class="progress mb-2" style="height: 8px;">
              <div class="progress-bar bg-success" role="progressbar" style="width: <?= $quarterAverage ?>%" aria-valuenow="<?= $quarterAverage ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="mb-0 small text-muted"><?= $quarterAverage ?>%</p>
          </div>
          <div class="performance-message ms-auto">
            <span class="badge <?= $performanceMessage['class'] ?> small"><?= $performanceMessage['message'] ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <!-- Notifications Widget -->
    <div class="card bg-white border-0 shadow-sm rounded-3 mb-3">
      <div class="card-header bg-transparent border-0 p-3">
        <h4 class="card-title mb-0 small d-flex align-items-center">
          <i class="bi bi-bell me-2 text-primary"></i>Notifications
          <?php if ($unreadCount > 0): ?>
            <span class="badge bg-danger ms-auto"><?= $unreadCount ?></span>
          <?php endif; ?>
        </h4>
      </div>
      <div class="card-body p-3">
        <?php if (!empty($notifications)): ?>
          <?php foreach (array_slice($notifications, 0, 3) as $notification): ?>
            <div class="notification-item d-flex align-items-start mb-2">
              <div class="notification-icon bg-<?= $notification['type'] === 'grade' ? 'success' : ($notification['type'] === 'assignment' ? 'warning' : 'info') ?> bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                <i class="bi bi-<?= $notification['type'] === 'grade' ? 'check-circle' : ($notification['type'] === 'assignment' ? 'exclamation-triangle' : 'info-circle') ?> text-<?= $notification['type'] === 'grade' ? 'success' : ($notification['type'] === 'assignment' ? 'warning' : 'info') ?>" style="font-size: 10px;"></i>
              </div>
              <div class="notification-content flex-grow-1">
                <p class="mb-0" style="font-size: 11px;"><?= esc($notification['title']) ?></p>
                <p class="mb-0 text-muted" style="font-size: 10px;"><?= time_ago($notification['created_at']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted small mb-0">No notifications yet.</p>
        <?php endif; ?>

      </div>
    </div>

  </div>
</div>



<!-- Recent Data Section -->
<div class="row mb-4">
  <div class="col-lg-12">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-transparent border-0 p-3">
        <h4 class="card-title mb-0 small">Recent Announcements</h4>
      </div>
      <div class="card-body p-3">
        <?php if (!empty($announcements)): ?>
          <?php foreach ($announcements as $announcement): ?>
            <div class="announcement-item mb-3">
              <h6 class="mb-1 small"><?= esc($announcement['title']) ?></h6>
              <p class="mb-1 small text-muted"><?= esc(substr($announcement['body'] ?? '', 0, 100)) ?>...</p>
              <p class="mb-0 small text-muted"><?= date('M j, Y', strtotime($announcement['published_at'])) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted small mb-0">No recent announcements.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>



<?= $this->endSection() ?>
