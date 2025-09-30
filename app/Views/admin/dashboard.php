<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="dashboard-header d-flex align-items-center justify-content-between mb-4">
  <div class="d-flex align-items-center gap-3">
    <div class="text-primary fs-4"><i class="bi bi-speedometer2"></i></div>
    <div>
      <h2 class="mb-0" style="font-size:1.25rem;">Admin Dashboard</h2>
      <small class="text-muted">Overview and quick actions</small>
    </div>
  </div>
  <div class="btn-group flex-wrap">
    <a href="<?= base_url('admin/students') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-people-fill me-2"></i>Students</a>
    <a href="<?= base_url('admin/teachers') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-person-video3 me-2"></i>Teachers</a>
    <a href="<?= base_url('admin/sections') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-grid-3x3-gap me-2"></i>Subjects & Sections</a>
    <a href="<?= base_url('announcements/admin') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-megaphone me-2"></i>Announcements</a>
    <a href="<?= base_url('admin/enrollments') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-person-check me-2"></i>Enrollments</a>
    <a href="<?= base_url('admin/id-cards') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-credit-card-2-front me-2"></i>ID Cards</a>
    <a href="<?= base_url('admin/analytics') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-graph-up me-2"></i>Analytics</a>
    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-people me-2"></i>Users & Roles</a>

    <a href="<?= base_url('admin/notifications') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-bell me-2"></i>Notifications</a>
  </div>
</div>

<?php /* Removed feature tiles grid as requested */ ?>

<!-- Compact Dashboard Grid Layout -->
<div class="row g-3 mb-3" style="display: flex; flex-wrap: nowrap;">
  <!-- Top Row: 2 Large Charts Side by Side -->
  <div class="col-6" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Students Enrolled This Year</h6>
          <div class="chart-controls d-flex align-items-center gap-1">
            <button class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="changeEnrollmentPeriod(-1)"><i class="bi bi-chevron-left"></i></button>
            <span id="enrollmentPeriod" class="mx-1 fw-semibold text-primary">2024</span>
            <button class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="changeEnrollmentPeriod(1)"><i class="bi bi-chevron-right"></i></button>
            <select id="enrollmentView" class="form-select form-select-sm ms-1" style="width: auto;" onchange="updateEnrollmentChart()">
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="chart-container" style="height: 180px;">
          <canvas id="enrollmentTrendChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Enrollment Predictions</h6>
          <div class="chart-controls d-flex align-items-center gap-1">
            <button class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="changePredictionPeriod(-1)"><i class="bi bi-chevron-left"></i></button>
            <span id="predictionPeriod" class="mx-1 fw-semibold text-success">2026</span>
            <button class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="changePredictionPeriod(1)"><i class="bi bi-chevron-right"></i></button>
            <select id="predictionView" class="form-select form-select-sm ms-1" style="width: auto;" onchange="updatePredictionChart()">
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="chart-container" style="height: 180px;">
          <canvas id="predictionChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bottom Row: 3 Equal Columns -->
<div class="row g-3" style="display: flex; flex-wrap: nowrap;">
  <!-- Recent Enrollment Applications -->
  <div class="col-4" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <h6 class="card-title mb-0 fw-semibold">Recent Enrollment Applications</h6>
      </div>
      <div class="card-body py-2" style="max-height: 280px; overflow-y: auto;">
        <?php if (!empty($recentEnrollments)): ?>
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($recentEnrollments, 0, 5) as $enrollment): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.85rem;">
                      <?= esc(($enrollment['last_name'] ?? '') . ', ' . ($enrollment['first_name'] ?? '')) ?>
                    </div>
                    <div class="text-muted small">Grade <?= esc($enrollment['grade_level'] ?? '-') ?></div>
                  </div>
                  <span class="badge bg-warning-subtle text-warning-emphasis ms-2 small">Pending</span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0 small">No recent enrollment applications.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Enrollment by Grade Level -->
  <div class="col-4" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Enrollment by Grade Level</h6>
          <small class="text-muted">Current year totals</small>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="chart-container" style="height: 240px;">
          <canvas id="gradeChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Announcements -->
  <div class="col-4" style="flex: 1; min-width: 0;">
    <div class="card h-100">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Recent Announcements</h6>
          <a href="<?= base_url('admin/announcements') ?>" class="btn btn-sm btn-outline-primary px-2 py-1">
            <i class="bi bi-gear"></i> Manage
          </a>
        </div>
      </div>
      <div class="card-body py-2" style="max-height: 280px; overflow-y: auto;">
        <?php if (!empty($recentAnnouncements)): ?>
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($recentAnnouncements, 0, 4) as $announcement): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                  <h6 class="mb-1 fw-semibold text-dark" style="font-size: 0.85rem;"><?= esc($announcement['title']) ?></h6>
                  <small class="text-muted ms-2"><?= $announcement['created_at'] ? date('M j, Y', strtotime($announcement['created_at'])) : 'N/A' ?></small>
                </div>
                <p class="mb-1 small text-muted"><?= esc(substr($announcement['body'], 0, 70)) ?>...</p>
                <small class="text-primary">Target: <?= esc($announcement['target_roles']) ?></small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0 small">No recent announcements.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>











<style>
/* Clean modal styling */
.modal-xl {
  max-width: 1200px;
}

.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.announcement-item {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  background: #f8f9fa;
  transition: all 0.2s ease;
}

.announcement-item:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transform: translateY(-1px);
}

.announcement-title {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
}

.announcement-body {
  color: #6c757d;
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.announcement-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  color: #6c757d;
}

.target-badge {
  background: #e9ecef;
  color: #495057;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}
</style>

<!-- Chart.js for enrollment charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let enrollmentChart, predictionChart;
let currentEnrollmentYear = 2024;
let currentPredictionYear = 2026;

// Sample data with database integration
const enrollmentData = {
  2023: { monthly: [45, 52, 48, 65, 78, 85, 92, 88, 95, 102, 98, 105], yearly: [945] },
  2024: { monthly: [2, 3, 2, 4, 1, 2, 1, 0, 1, 0, 0, 0], yearly: [<?= isset($stats) ? $stats['total_students'] : 16 ?>] },
  2025: { monthly: [50, 58, 55, 72, 85, 92, 99, 95, 102, 109, 105, 112], yearly: [1034] }
};

const predictionData = {
  2025: { monthly: [2, 3, 4, 5, 6, 8, 10, 12, 15, 18, 20, 22], yearly: [<?= isset($stats) ? round($stats['total_students'] * 1.25) : 20 ?>] },
  2026: { monthly: [3, 4, 5, 6, 8, 10, 12, 15, 18, 22, 25, 28], yearly: [<?= isset($stats) ? round($stats['total_students'] * 1.56) : 25 ?>] },
  2027: { monthly: [4, 5, 6, 8, 10, 12, 15, 18, 22, 26, 30, 35], yearly: [<?= isset($stats) ? round($stats['total_students'] * 1.94) : 31 ?>] }
};



function initializeCharts() {
  const root = getComputedStyle(document.documentElement);
  const colorPrimary = '#3b82f6';
  const colorSuccess = '#10b981';
  const colorHeading = '#0f172a';

  // Enrollment Chart
  const enrollmentCtx = document.getElementById('enrollmentTrendChart').getContext('2d');
  enrollmentChart = new Chart(enrollmentCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Students Enrolled',
        data: enrollmentData[currentEnrollmentYear].monthly,
        borderColor: colorPrimary,
        backgroundColor: colorPrimary + '20',
        borderWidth: 3,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true, ticks: { color: colorHeading } },
        x: { ticks: { color: colorHeading } }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });

  // Prediction Chart
  const predictionCtx = document.getElementById('predictionChart').getContext('2d');
  predictionChart = new Chart(predictionCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Predicted Enrollments',
        data: predictionData[currentPredictionYear].monthly,
        borderColor: colorSuccess,
        backgroundColor: colorSuccess + '20',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        borderDash: [5, 5]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true, ticks: { color: colorHeading } },
        x: { ticks: { color: colorHeading } }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
}

function changeEnrollmentPeriod(direction) {
  currentEnrollmentYear += direction;
  if (currentEnrollmentYear < 2023) currentEnrollmentYear = 2023;
  if (currentEnrollmentYear > 2025) currentEnrollmentYear = 2025;
  document.getElementById('enrollmentPeriod').textContent = currentEnrollmentYear;
  updateEnrollmentChart();
}

function changePredictionPeriod(direction) {
  currentPredictionYear += direction;
  if (currentPredictionYear < 2025) currentPredictionYear = 2025;
  if (currentPredictionYear > 2027) currentPredictionYear = 2027;
  document.getElementById('predictionPeriod').textContent = currentPredictionYear;
  updatePredictionChart();
}

function updateEnrollmentChart() {
  const view = document.getElementById('enrollmentView').value;
  const data = enrollmentData[currentEnrollmentYear];
  
  if (view === 'monthly') {
    enrollmentChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    enrollmentChart.data.datasets[0].data = data.monthly;
  } else {
    enrollmentChart.data.labels = [currentEnrollmentYear.toString()];
    enrollmentChart.data.datasets[0].data = data.yearly;
  }
  enrollmentChart.update();
}

function updatePredictionChart() {
  const view = document.getElementById('predictionView').value;
  const data = predictionData[currentPredictionYear];
  
  if (view === 'monthly') {
    predictionChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    predictionChart.data.datasets[0].data = data.monthly;
  } else {
    predictionChart.data.labels = [currentPredictionYear.toString()];
    predictionChart.data.datasets[0].data = data.yearly;
  }
  predictionChart.update();
}

// Initialize Grade Level Chart
function initializeGradeChart() {
  const gradeCtx = document.getElementById('gradeChart').getContext('2d');
  const colorPrimary = '#3b82f6';
  const colorHeading = '#0f172a';
  
  const gradient = gradeCtx.createLinearGradient(0, 0, 0, 300);
  gradient.addColorStop(0, 'rgba(59, 130, 246, 0.9)');
  gradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

  new Chart(gradeCtx, {
    type: 'bar',
    data: {
      labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
      datasets: [{
        label: 'Enrolled Students',
        data: [
          <?= $enrollmentByGrade[7] ?? 0 ?>,
          <?= $enrollmentByGrade[8] ?? 0 ?>,
          <?= $enrollmentByGrade[9] ?? 0 ?>,
          <?= $enrollmentByGrade[10] ?? 0 ?>
        ],
        backgroundColor: gradient,
        borderColor: colorPrimary,
        borderWidth: 1,
        borderRadius: 8,
        maxBarThickness: 40,
        categoryPercentage: 0.6,
        barPercentage: 0.7,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, color: colorHeading },
          grid: { color: 'rgba(15,23,42,0.06)' }
        },
        x: {
          ticks: { color: colorHeading },
          grid: { display: false }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ` ${ctx.raw} students`
          }
        }
      }
    }
  });
}

// Initialize all charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeCharts();
  initializeGradeChart();
});



















function formatDateTime(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

</script>

<?= $this->endSection() ?>
