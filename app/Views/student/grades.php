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

<!-- Next School Year Enrollment -->
<?php 
// Check if student has completed all quarters (Q1-Q4) with passing grades
$hasAllQuarters = true;
$allQuartersPassed = true;
for ($q = 1; $q <= 4; $q++) {
    if (!isset($allQuarterGrades[$q]) || $allQuarterGrades[$q] === null) {
        $hasAllQuarters = false;
        break;
    }
    if ($allQuarterGrades[$q] < 75) {
        $allQuartersPassed = false;
    }
}

$canEnrollNextYear = $hasAllQuarters && $allQuartersPassed && $gwa >= 75;
$nextGradeLevel = isset($student['grade_level']) ? $student['grade_level'] + 1 : null;
?>

<div class="card bg-white border-0 shadow-sm rounded-3 mb-4 <?= $canEnrollNextYear ? 'border-success' : 'border-warning' ?>">
  <div class="card-body p-3">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h4 class="card-title mb-2 small">
          <i class="bi bi-calendar-plus me-2 text-primary"></i>
          Next School Year Enrollment
        </h4>

        <?php if ($canEnrollNextYear): ?>
          <p class="text-success mb-2 small">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Congratulations!</strong> You have completed all quarters with passing grades.
            You are eligible to enroll for Grade <?= $nextGradeLevel ?> next school year.
          </p>

          <div class="mb-2">
            <small class="text-muted">
              <i class="bi bi-info-circle me-1"></i>
              Requirements: Complete Q1-Q4 with GWA ≥ 75.00 | Your GWA: <?= number_format($gwa, 2) ?>
            </small>
          </div>
        <?php elseif (!$hasAllQuarters): ?>
          <p class="text-warning mb-2 small">
            <i class="bi bi-clock-fill me-2"></i>
            <strong>Enrollment Pending:</strong> You need to complete all quarters (Q1-Q4) before you can enroll for the next school year.
          </p>

          <div class="mb-2">
            <small class="text-muted">
              <i class="bi bi-info-circle me-1"></i>
              Missing quarters: 
              <?php 
              $missing = [];
              for ($q = 1; $q <= 4; $q++) {
                  if (!isset($allQuarterGrades[$q]) || $allQuarterGrades[$q] === null) {
                      $missing[] = "Q$q";
                  }
              }
              echo implode(', ', $missing);
              ?>
            </small>
          </div>
        <?php else: ?>
          <p class="text-danger mb-2 small">
            <i class="bi bi-x-circle-fill me-2"></i>
            <strong>Enrollment Restricted:</strong> You have failing grades in some quarters.
            Your GWA of <strong><?= number_format($gwa, 2) ?></strong> is below the minimum requirement of 75.00.
          </p>

          <div class="mb-2">
            <small class="text-muted">
              <i class="bi bi-exclamation-triangle me-1"></i>
              You need to retake failed subjects or improve your grades to be eligible for promotion.
            </small>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4 text-lg-end">
        <?php 
        // Check if already applied
        $db = \Config\Database::connect();
        $studentId = $student['id'] ?? 1;
        $hasApplied = false;
        $applicationStatus = '';
        
        if ($db->tableExists('next_year_applications')) {
            $existing = $db->table('next_year_applications')
                ->where('student_id', $studentId)
                ->where('school_year', '2026-2027')
                ->get()->getRow();
            if ($existing) {
                $hasApplied = true;
                $applicationStatus = $existing->status;
            }
        }
        ?>
        
        <?php if ($hasApplied): ?>
          <div class="text-center">
            <span class="badge bg-info px-3 py-2 mb-2">
              <i class="bi bi-clock-history me-2"></i>
              Application Submitted
            </span>
            <div class="small text-muted">
              Status: <strong><?= ucfirst($applicationStatus) ?></strong><br>
              Waiting for admin approval
            </div>
          </div>
        <?php elseif ($canEnrollNextYear): ?>
          <button class="btn btn-success px-3 py-2" onclick="submitNextYearEnrollment()" style="position: relative; z-index: 999; font-weight: 600;">
            <i class="bi bi-check-circle me-2"></i>
            Apply for Grade <?= $nextGradeLevel ?>
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
      <div class="col-md-12 text-center">
        <small class="text-muted">
          <strong>Next School Year:</strong>
          <?php
            $currentYear = date('Y');
            $nextSchoolYear = ($currentYear + 1) . '-' . ($currentYear + 2);
          ?>
          <?= $nextSchoolYear ?>
        </small>
      </div>
    </div>
  </div>
</div>

<script>
function submitNextYearEnrollment() {
    console.log('Starting application submission...');
    
    <?php if ($hasApplied): ?>
        alert('You have already submitted an application for next school year.');
        return;
    <?php endif; ?>
    
    if (confirm('Submit your application for Grade <?= $nextGradeLevel ?> enrollment for the next school year?')) {
        const requestData = {
            next_grade_level: <?= $nextGradeLevel ?>,
            current_gwa: <?= $gwa ?>
        };
        
        console.log('Request data:', requestData);
        console.log('URL:', '<?= base_url('student/apply-next-year') ?>');
        
        fetch('<?= base_url('student/apply-next-year') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                alert(data.message || 'Your application for Grade <?= $nextGradeLevel ?> has been submitted to the admin for review!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to submit application. Please try again.'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Network error: ' + error.message + '. Please check your connection and try again.');
        });
    }
}
</script>

<!-- Quarter Selection -->
<div class="card bg-white border-0 shadow-sm rounded-3 mb-4">
  <div class="card-body p-3">
    <form class="row g-2" method="get">
      <div class="col-md-4">
        <label class="form-label small fw-medium">School Year</label>
        <select name="school_year" class="form-select form-select-sm">
          <option value="2024-2025" <?= $schoolYear === '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
          <option value="2025-2026" <?= $schoolYear === '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
          <option value="2026-2027" <?= $schoolYear === '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
          <option value="2027-2028" <?= $schoolYear === '2027-2028' ? 'selected' : '' ?>>2027-2028</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-medium">Quarter</label>
        <select name="quarter" class="form-select form-select-sm" onchange="this.form.submit()">
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
              <td colspan="4" class="text-center py-4 text-muted">
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







<?= $this->endSection() ?>