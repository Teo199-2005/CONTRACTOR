<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* Force white text on "Not recorded" badges */
.badge.bg-secondary.text-white {
  color: #ffffff !important;
  background-color: #6c757d !important;
}
</style>

<!-- Compact Header Section with Blue Divider -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">My Academic Grades</h1>
      <p class="text-muted mb-0 small">Track your academic progress and performance</p>
    </div>
    <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
    </a>
  </div>

  <!-- Blue Divider Line -->
  <div class="blue-divider"></div>
</div>

<!-- Compact Stats Cards -->
<div class="d-flex gap-3 mb-4">
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-trophy-fill text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-success mb-1 small"><?= $gwa !== null ? number_format($gwa, 2) : 'N/A' ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">General Weighted Average</p>
    </div>
  </div>

  <?php for ($q = 1; $q <= 4; $q++): ?>
    <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
      <div class="card-body text-center p-3">
        <div class="stats-icon bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
          <i class="bi bi-calendar-check text-white fs-5"></i>
        </div>
        <h4 class="stats-number text-primary mb-1 small">
          <?= isset($allQuarterGrades[$q]) && $allQuarterGrades[$q] !== null
              ? number_format($allQuarterGrades[$q], 1)
              : '--' ?>
        </h4>
        <p class="stats-label text-muted fw-medium mb-0 small">Quarter <?= $q ?></p>
      </div>
    </div>
  <?php endfor; ?>
</div>

<!-- Blue Divider -->
<div class="blue-divider mb-4"></div>

<!-- Quarter Selection -->
<div class="card bg-white border-0 shadow-sm rounded-3 mb-4">
  <div class="card-body p-3">
    <form class="row g-2" method="get">
      <div class="col-md-4">
        <label class="form-label small fw-medium">School Year</label>
        <select name="school_year" class="form-select form-select-sm">
          <option value="2023-2024" <?= $schoolYear === '2023-2024' ? 'selected' : '' ?>>2023-2024</option>
          <option value="2024-2025" <?= $schoolYear === '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
          <option value="2025-2026" <?= $schoolYear === '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
          <option value="2026-2027" <?= $schoolYear === '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-medium">Quarter</label>
        <select name="quarter" class="form-select form-select-sm">
          <?php for ($q=1; $q<=4; $q++): ?>
            <option value="<?= $q ?>" <?= ((int)$quarter===$q?'selected':'') ?>>
              Quarter <?= $q ?>
            </option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-primary btn-sm w-100">
          <i class="bi bi-search me-2"></i>View Grades
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Grades Table -->
<div class="card bg-white border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-transparent border-0 p-3">
    <h4 class="card-title mb-0 small">
      <i class="bi bi-list-check me-2 text-primary"></i>
      Quarter <?= esc($quarter) ?> Grades
    </h4>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="border-0 fw-medium small">Subject</th>
            <th class="border-0 fw-medium small">Code</th>
            <th class="border-0 fw-medium small">Units</th>
            <th class="border-0 fw-medium small text-center">Grade</th>
            <th class="border-0 fw-medium small">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($grades)): ?>
            <?php foreach ($grades as $row): ?>
              <tr>
                <td class="py-2">
                  <div class="fw-medium text-dark small"><?= esc($row['subject']['subject_name']) ?></div>
                </td>
                <td class="py-2">
                  <span class="badge bg-light text-dark small"><?= esc($row['subject']['subject_code']) ?></span>
                </td>
                <td class="py-2">
                  <span class="text-muted small"><?= esc($row['subject']['units']) ?> units</span>
                </td>
                <td class="py-2 text-center">
                  <?php if ($row['grade'] && $row['grade']['grade'] !== null): ?>
                    <?php
                      $grade = $row['grade']['grade'];
                      $badgeClass = $grade >= 90 ? 'success' : ($grade >= 85 ? 'info' : ($grade >= 75 ? 'warning' : 'danger'));
                    ?>
                    <span class="badge bg-<?= $badgeClass ?> small" style="color: white !important;">
                      <?= number_format($grade, 2) ?>
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary small" style="color: white !important;">Not recorded</span>
                  <?php endif; ?>
                </td>
                <td class="py-2">
                  <?php if ($row['grade'] && isset($row['grade']['remarks'])): ?>
                    <span class="text-muted small"><?= esc($row['grade']['remarks']) ?></span>
                  <?php else: ?>
                    <span class="text-muted small">--</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                <small>No grades recorded for this quarter yet.</small>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Quarter Summary -->
  <div class="card-footer bg-light border-0 p-3">
    <div class="row align-items-center">
      <div class="col">
        <strong class="text-primary small">Quarter <?= esc($quarter) ?> Average:</strong>
        <span class="ms-2 fw-bold">
          <?= $quarterAverage !== null ? number_format($quarterAverage, 2) : 'N/A' ?>
        </span>
      </div>
      <div class="col-auto">
        <?php if ($quarterAverage !== null): ?>
          <?php
            $avgBadgeClass = $quarterAverage >= 90 ? 'success' : ($quarterAverage >= 85 ? 'info' : ($quarterAverage >= 75 ? 'warning' : 'danger'));
            $avgMessage = $quarterAverage >= 90 ? 'Excellent' : ($quarterAverage >= 85 ? 'Very Good' : ($quarterAverage >= 75 ? 'Good' : 'Needs Improvement'));
          ?>
          <span class="badge bg-<?= $avgBadgeClass ?> small" style="color: white !important;">
            <?= $avgMessage ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Next Semester Enrollment -->
<div class="card bg-white border-0 shadow-sm rounded-3 <?= $canEnrollNextSemester ? 'border-success' : 'border-danger' ?>">
  <div class="card-body p-3">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h4 class="card-title mb-2 small">
          <i class="bi bi-calendar-plus me-2 text-primary"></i>
          Next Semester Enrollment
        </h4>

        <?php if ($canEnrollNextSemester): ?>
          <p class="text-success mb-2 small">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Congratulations!</strong> You are eligible to enroll for the next semester.
            Your GWA of <strong><?= number_format($gwa, 2) ?></strong> meets the minimum requirement of 75.00.
          </p>

          <div class="mb-2">
            <small class="text-muted">
              <i class="bi bi-info-circle me-1"></i>
              Enrollment requirements: GWA ≥ 75.00 | Current GWA: <?= number_format($gwa, 2) ?>
            </small>
          </div>
        <?php else: ?>
          <p class="text-danger mb-2 small">
            <i class="bi bi-x-circle-fill me-2"></i>
            <strong>Enrollment Restricted:</strong> Your current GWA of
            <strong><?= $gwa !== null ? number_format($gwa, 2) : 'N/A' ?></strong>
            is below the minimum requirement of 75.00 for next semester enrollment.
          </p>

          <div class="mb-2">
            <small class="text-muted">
              <i class="bi bi-exclamation-triangle me-1"></i>
              You need to improve your grades to at least 75.00 GWA to be eligible for enrollment.
            </small>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4 text-lg-end">
        <?php if ($canEnrollNextSemester): ?>
          <button class="btn btn-success btn-sm px-3" onclick="enrollNextSemester()">
            <i class="bi bi-check-circle me-2"></i>
            Enroll for Next Semester
          </button>
        <?php else: ?>
          <button class="btn btn-secondary btn-sm px-3" disabled>
            <i class="bi bi-lock me-2"></i>
            Enrollment Unavailable
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Additional Info -->
    <div class="row mt-2 pt-2 border-top">
      <div class="col-md-6">
        <small class="text-muted">
          <strong>Academic Standing:</strong>
          <?php if ($gwa !== null): ?>
            <?php if ($gwa >= 95): ?>
              <span class="text-success">Summa Cum Laude</span>
            <?php elseif ($gwa >= 90): ?>
              <span class="text-success">Magna Cum Laude</span>
            <?php elseif ($gwa >= 85): ?>
              <span class="text-info">Cum Laude</span>
            <?php elseif ($gwa >= 75): ?>
              <span class="text-warning">Good Standing</span>
            <?php else: ?>
              <span class="text-danger">Academic Probation</span>
            <?php endif; ?>
          <?php else: ?>
            <span class="text-muted">Not Available</span>
          <?php endif; ?>
        </small>
      </div>
      <div class="col-md-6 text-md-end">
        <small class="text-muted">
          <strong>Next Semester:</strong>
          <?php
            $currentYear = date('Y');
            $nextSemester = ($currentYear + 1) . '-' . ($currentYear + 2);
          ?>
          <?= $nextSemester ?>
        </small>
      </div>
    </div>
  </div>
</div>

<script>
function enrollNextSemester() {
  if (confirm('Are you sure you want to enroll for the next semester?')) {
    // Here you would typically make an AJAX call to handle enrollment
    alert('Enrollment request submitted! Please wait for admin approval.');
    // You can add actual enrollment logic here
  }
}
</script>

<?= $this->endSection() ?>