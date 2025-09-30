<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<!-- Header Section -->
<div class="dashboard-header d-flex align-items-center justify-content-between mb-4">
  <div class="d-flex align-items-center gap-3">
    <div class="text-primary fs-4"><i class="bi bi-person-video3"></i></div>
    <div>
      <h2 class="mb-0" style="font-size:1.25rem;">Teacher Dashboard</h2>
      <small class="text-muted">Class management and student progress</small>
    </div>
  </div>
  <div class="btn-group flex-wrap">
    <a href="<?= base_url('teacher/grades') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pen me-2"></i>Enter Grades</a>
    <a href="<?= base_url('teacher/students') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-people me-2"></i>My Students</a>
    <a href="<?= base_url('teacher/schedule') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-calendar-week me-2"></i>My Schedule</a>
    <a href="<?= base_url('teacher/announcements') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-megaphone me-2"></i>Announcements</a>
    <a href="<?= base_url('teacher/materials') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-folder2-open me-2"></i>Materials</a>
    <a href="<?= base_url('teacher/analytics') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-graph-up me-2"></i>Analytics</a>
  </div>
</div>

<!-- Compact Dashboard Grid Layout -->
<div class="row g-3 mb-3" style="display: flex; flex-wrap: nowrap;">
  <!-- Top Row: 2 Large Charts Side by Side -->
  <div class="col-6" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Grade Distribution</h6>
          <small class="text-muted">Current year</small>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="chart-container" style="height: 180px;">
          <canvas id="gradeDistributionChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Class Performance by Quarter</h6>
          <small class="text-muted">2024-2025</small>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="chart-container" style="height: 180px;">
          <canvas id="quarterPerformanceChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bottom Row: 3 Equal Columns -->
<div class="row g-3" style="display: flex; flex-wrap: nowrap;">
  <!-- Recent Grades Entered -->
  <div class="col-4" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <h6 class="card-title mb-0 fw-semibold">Recent Grades Entered</h6>
      </div>
      <div class="card-body py-2" style="max-height: 280px; overflow-y: auto;">
        <?php if (!empty($recentGrades)): ?>
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($recentGrades, 0, 5) as $grade): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.85rem;">
                      <?= esc($grade['first_name'] . ' ' . $grade['last_name']) ?>
                    </div>
                    <div class="text-muted small"><?= esc($grade['subject_name']) ?></div>
                  </div>
                  <span class="badge bg-<?= $grade['grade'] >= 85 ? 'success' : ($grade['grade'] >= 75 ? 'warning' : 'danger') ?> ms-2 small" style="color: white !important;">
                    <?= number_format($grade['grade'], 1) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0 small">No grades entered yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Class Averages by Subject -->
  <div class="col-4" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <h6 class="card-title mb-0 fw-semibold">Class Averages by Subject</h6>
      </div>
      <div class="card-body py-2" style="max-height: 280px; overflow-y: auto;">
        <?php if (!empty($classAverages)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($classAverages as $average): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1">
                    <div class="fw-semibold text-dark small"><?= esc($average['subject']) ?></div>
                    <div class="progress mt-1" style="height: 6px;">
                      <div class="progress-bar bg-<?= $average['average'] >= 85 ? 'success' : ($average['average'] >= 75 ? 'warning' : 'danger') ?>"
                           style="width: <?= $average['average'] ?>%"></div>
                    </div>
                  </div>
                  <span class="badge bg-<?= $average['average'] >= 85 ? 'success' : ($average['average'] >= 75 ? 'warning' : 'danger') ?> ms-2 small" style="color: white !important;">
                    <?= number_format($average['average'], 1) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0 small">No class data available.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- My Students -->
  <div class="col-4" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">My Students</h6>
          <a href="<?= base_url('teacher/students') ?>" class="btn btn-sm btn-outline-primary px-2 py-1">
            <i class="bi bi-eye"></i> View All
          </a>
        </div>
      </div>
      <div class="card-body py-2" style="max-height: 280px; overflow-y: auto;">
        <?php if (!empty($myStudents)): ?>
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($myStudents, 0, 6) as $student): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1">
                    <div class="fw-semibold text-dark small">
                      <?= esc($student['first_name'] . ' ' . $student['last_name']) ?>
                    </div>
                    <div class="text-muted small">
                      Grade <?= esc($student['grade_level']) ?>
                      <?= !empty($student['section_name']) ? '- ' . esc($student['section_name']) : '' ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($myStudents) > 6): ?>
            <div class="text-center mt-2">
              <small class="text-muted">And <?= count($myStudents) - 6 ?> more students...</small>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <p class="text-muted mb-0 small">No students assigned yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Grade Distribution Chart
function initializeGradeDistributionChart() {
  const ctx = document.getElementById('gradeDistributionChart').getContext('2d');
  const gradeData = <?= json_encode($gradeDistribution ?? []) ?>;

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Excellent (90-100)', 'Very Good (85-89)', 'Good (80-84)', 'Fair (75-79)', 'Passing (70-74)', 'Failing (<70)'],
      datasets: [{
        data: [
          gradeData.excellent || 0,
          gradeData.very_good || 0,
          gradeData.good || 0,
          gradeData.fair || 0,
          gradeData.passing || 0,
          gradeData.failing || 0
        ],
        backgroundColor: [
          '#28a745',  // Excellent - Green
          '#20c997',  // Very Good - Teal
          '#17a2b8',  // Good - Info
          '#ffc107',  // Fair - Warning
          '#fd7e14',  // Passing - Orange
          '#dc3545'   // Failing - Danger
        ],
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 15,
            font: {
              size: 11
            }
          }
        }
      }
    }
  });
}

// Quarter Performance Chart
function initializeQuarterPerformanceChart() {
  const ctx = document.getElementById('quarterPerformanceChart').getContext('2d');
  const quarterData = <?= json_encode($quarterPerformance ?? [0, 0, 0, 0]) ?>;

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Quarter 1', 'Quarter 2', 'Quarter 3', 'Quarter 4'],
      datasets: [{
        label: 'Class Average',
        data: quarterData,
        borderColor: '#007bff',
        backgroundColor: 'rgba(0, 123, 255, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#007bff',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          grid: {
            color: 'rgba(0,0,0,0.1)'
          },
          ticks: {
            callback: function(value) {
              return value + '%';
            }
          }
        },
        x: {
          grid: {
            color: 'rgba(0,0,0,0.1)'
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeGradeDistributionChart();
  initializeQuarterPerformanceChart();
});
</script>

<?= $this->endSection() ?>