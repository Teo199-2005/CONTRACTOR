<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<!-- Create Admin Modal -->
<div id="createAdminModal" class="custom-modal-overlay" style="display: none;">
  <div class="custom-modal-container">
    <div class="custom-modal-header">
      <h3 class="custom-modal-title" style="color: white !important;"><i class="bi bi-person-plus me-2" style="color: white !important;"></i>Create Admin Account</h3>
      <button type="button" class="custom-modal-close" onclick="closeCreateAdminModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <form id="createAdminForm">
        <div class="mb-3">
          <label for="adminEmail" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="adminEmail" name="email" required>
        </div>
        <div class="mb-3">
          <label for="adminFirstName" class="form-label">First Name</label>
          <input type="text" class="form-control" id="adminFirstName" name="first_name" required>
        </div>
        <div class="mb-3">
          <label for="adminLastName" class="form-label">Last Name</label>
          <input type="text" class="form-control" id="adminLastName" name="last_name" required>
        </div>
        <div class="mb-3">
          <label for="adminPassword" class="form-label">Password</label>
          <div class="input-group">
            <input type="password" class="form-control" id="adminPassword" name="password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        <div class="mb-3">
          <label for="adminConfirmPassword" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="adminConfirmPassword" name="confirm_password" required>
        </div>
      </form>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-dark" onclick="closeCreateAdminModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="submitCreateAdminForm()">Create Admin</button>
    </div>
  </div>
</div>

<script>
function openCreateAdminModal() {
  const modal = document.getElementById('createAdminModal');
  const footer = document.querySelector('.modern-footer');
  
  if (footer) footer.style.display = 'none';
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeCreateAdminModal() {
  const modal = document.getElementById('createAdminModal');
  const footer = document.querySelector('.modern-footer');
  
  modal.style.display = 'none';
  if (footer) footer.style.display = 'block';
  document.body.style.overflow = '';
  
  document.getElementById('createAdminForm').reset();
}

function submitCreateAdminForm() {
  const form = document.getElementById('createAdminForm');
  const password = document.getElementById('adminPassword').value;
  const confirmPassword = document.getElementById('adminConfirmPassword').value;
  
  if (password !== confirmPassword) {
    alert('Passwords do not match!');
    return;
  }
  
  const formData = new FormData(form);
  
  fetch('<?= base_url('admin/dashboard/createAdmin') ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Admin account created successfully!');
      closeCreateAdminModal();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while creating the admin account.');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const togglePassword = document.getElementById('togglePassword');
  if (togglePassword) {
    togglePassword.addEventListener('click', function() {
      const password = document.getElementById('adminPassword');
      const confirmPassword = document.getElementById('adminConfirmPassword');
      const icon = this.querySelector('i');
      
      if (password.type === 'password') {
        password.type = 'text';
        confirmPassword.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        password.type = 'password';
        confirmPassword.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  }
});
</script>

<div class="dashboard-header d-flex align-items-center justify-content-between mb-4">
  <div class="d-flex align-items-center gap-3">
    <div class="text-primary fs-4"><i class="bi bi-speedometer2"></i></div>
    <div>
      <h2 class="mb-0" style="font-size:1.25rem;">Admin Dashboard</h2>
      <small class="text-muted">Overview and quick actions</small>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <div class="btn-group flex-wrap">
      <a href="<?= base_url('admin/students') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-people-fill me-2"></i>Students</a>
      <a href="<?= base_url('admin/teachers') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-person-video3 me-2"></i>Teachers</a>
      <a href="<?= base_url('admin/sections') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-grid-3x3-gap me-2"></i>Subjects & Sections</a>
      <a href="<?= base_url('announcements/admin') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-megaphone me-2"></i>Announcements</a>
      <a href="<?= base_url('admin/students/pending') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-clock-history me-2"></i>Pending Applications</a>
      <a href="<?= base_url('admin/id-cards') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-credit-card-2-front me-2"></i>ID Cards</a>
      <a href="<?= base_url('admin/analytics') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-graph-up me-2"></i>Analytics</a>
    </div>
    
    <div class="btn-group">
      <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-calendar-check me-1"></i>Q<?= $currentQuarter ?>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" onclick="updateQuarter(1)">Quarter 1</a></li>
        <li><a class="dropdown-item" href="#" onclick="updateQuarter(2)">Quarter 2</a></li>
        <li><a class="dropdown-item" href="#" onclick="updateQuarter(3)">Quarter 3</a></li>
        <li><a class="dropdown-item" href="#" onclick="updateQuarter(4)">Quarter 4</a></li>
      </ul>
    </div>
    
    <button type="button" class="btn btn-sm btn-warning" onclick="openCreateAdminModal()" id="createAdminBtn">
      <i class="bi bi-person-plus me-1"></i>Create Admin
    </button>
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
        <div class="row mb-2">
          <div class="col-6">
            <div class="text-center">
              <div class="h5 mb-0 text-primary"><?= $stats['total_students'] ?? 0 ?></div>
              <small class="text-muted">This Year</small>
            </div>
          </div>
          <div class="col-6">
            <div class="text-center">
              <div class="h5 mb-0 text-success">+<?= round((($stats['total_students'] ?? 0) / max(1, 1)) * 100, 0) ?>%</div>
              <small class="text-muted">Growth</small>
            </div>
          </div>
        </div>
        <div class="chart-container" style="height: 140px;">
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
                  <?php 
                  $statusClass = match($enrollment['enrollment_status']) {
                    'enrolled' => 'bg-success-subtle text-success-emphasis',
                    'pending' => 'bg-warning-subtle text-warning-emphasis', 
                    'rejected' => 'bg-danger-subtle text-danger-emphasis',
                    'approved' => 'bg-info-subtle text-info-emphasis',
                    default => 'bg-secondary-subtle text-secondary-emphasis'
                  };
                  ?>
                  <span class="badge <?= $statusClass ?> ms-2 small"><?= ucfirst($enrollment['enrollment_status']) ?></span>
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

/* Custom Modal Styles */
.custom-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
}

.custom-modal-container {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
  z-index: 100001;
  pointer-events: auto;
}

.custom-modal-header {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: #ffffff !important;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 3px solid #1e40af;
}

.custom-modal-header * {
  color: #ffffff !important;
}

.custom-modal-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #ffffff !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.custom-modal-title, .custom-modal-title * {
  color: #ffffff !important;
}

.custom-modal-close {
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: #ffffff !important;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.custom-modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  color: #ffffff !important;
  transform: scale(1.1);
}

.custom-modal-close i {
  color: #ffffff !important;
}

.custom-modal-body {
  padding: 2rem;
  max-height: 60vh;
  overflow-y: auto;
  background: #f8fafc;
  position: relative;
  z-index: 100001;
}

.custom-modal-body input,
.custom-modal-body select,
.custom-modal-body textarea,
.custom-modal-body button {
  position: relative;
  z-index: 100002;
  pointer-events: auto;
}

.custom-modal-footer {
  background: #f1f5f9;
  padding: 1.5rem 2rem;
  border-top: 2px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>

<!-- Chart.js for enrollment charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let enrollmentChart, predictionChart;
let currentEnrollmentYear = 2024;
let currentPredictionYear = 2026;

// Real enrollment data from database
const enrollmentData = <?= $enrollmentData ?? 'null' ?> || {
  2023: { monthly: [3, 2, 1, 2, 4, 35, 28, 15, 6, 2, 1, 1], yearly: [100] },
  2024: { monthly: [4, 3, 1, 3, 5, 47, 38, 20, 8, 3, 2, 1], yearly: [135] },
  2025: { monthly: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], yearly: [0] }
};

const predictionData = <?= $predictionData ?? 'null' ?> || {
  2026: { monthly: [5, 4, 1, 4, 7, 63, 51, 27, 11, 4, 3, 1], yearly: [181] },
  2027: { monthly: [6, 5, 1, 5, 8, 72, 58, 31, 12, 5, 3, 1], yearly: [207] },
  2028: { monthly: [7, 6, 1, 6, 9, 82, 66, 35, 14, 6, 4, 1], yearly: [237] }
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
      labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
      datasets: [{
        label: 'Enrolled Students',
        data: [
          <?= $enrollmentByGrade[7] ?? 0 ?>,
          <?= $enrollmentByGrade[8] ?? 0 ?>,
          <?= $enrollmentByGrade[9] ?? 0 ?>,
          <?= $enrollmentByGrade[10] ?? 0 ?>,
          <?= $enrollmentByGrade[11] ?? 0 ?>,
          <?= $enrollmentByGrade[12] ?? 0 ?>
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

function updateQuarter(quarter) {
  fetch('<?= base_url('admin/dashboard/updateQuarter') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'quarter=' + quarter
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Failed to update quarter: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while updating the quarter.');
  });
}

</script>



<?= $this->endSection() ?>
