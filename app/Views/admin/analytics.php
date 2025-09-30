<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3 analytics-header">
  <h1 class="h5 mb-0">Analytics Dashboard</h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('admin/analytics/export-pdf') ?>" class="btn btn-sm btn-primary" target="_blank">
      <i class="bi bi-file-earmark-pdf"></i> Export PDF Report
    </a>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<?php
  $maleCount = (int)($genderDistribution['male'] ?? 0);
  $femaleCount = (int)($genderDistribution['female'] ?? 0);
  $enrolledTotal = (int)($statusDistribution['enrolled'] ?? 0);
  $pendingTotal = (int)($statusDistribution['pending'] ?? 0);
  $approvedTotal = (int)($statusDistribution['approved'] ?? 0);
  $rejectedTotal = (int)($statusDistribution['rejected'] ?? 0);
  $totalStudents = $enrolledTotal + $pendingTotal + $approvedTotal + $rejectedTotal;
?>

<div class="analytics-page compact">
  <div class="card overview-card mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
      <div class="d-flex align-items-center gap-2">
        <h6 class="mb-0">Overview</h6>
        <small class="text-muted">This school year's quick snapshot</small>
      </div>
      <div class="stat-chips">
        <span class="stat-chip bg-primary-soft">Total <strong><?= $totalStudents ?></strong></span>
        <span class="stat-chip bg-blue-soft">Enrolled <strong><?= $enrolledTotal ?></strong></span>
        <span class="stat-chip bg-amber-soft">Pending <strong><?= $pendingTotal ?></strong></span>
        <span class="stat-chip bg-slate-soft">Approved <strong><?= $approvedTotal ?></strong></span>
        <span class="stat-chip bg-gray-soft">Rejected <strong><?= $rejectedTotal ?></strong></span>
        <span class="stat-chip bg-cyan-soft">Male <strong><?= $maleCount ?></strong></span>
        <span class="stat-chip bg-indigo-soft">Female <strong><?= $femaleCount ?></strong></span>
      </div>
    </div>
  </div>

  <div class="analytics-layout">
    <!-- Left: 2x2 charts grid (compact) -->
    <div class="analytics-cell">
      <div class="charts-grid">
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Enrolled Students</strong>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="changeYear(2023)">2023</button>
              <button type="button" class="btn btn-primary btn-sm" onclick="changeYear(2024)">2024</button>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="changeYear(2025)">2025</button>
            </div>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-primary"><?= array_sum(array_column($enrollmentTrends ?? [], 'count')) ?></div>
                  <small class="text-muted">This Year</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-success">+<?= round(((array_sum(array_column($enrollmentTrends ?? [], 'count')) - array_sum(array_column($enrollmentTrendsPrev ?? [], 'count'))) / max(array_sum(array_column($enrollmentTrendsPrev ?? [], 'count')), 1)) * 100, 1) ?>%</div>
                  <small class="text-muted">Growth</small>
                </div>
              </div>
            </div>
            <div class="chart-container line-chart" style="height: 50px;">
              <canvas id="trendChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Grade Level Distribution</strong>
            <small class="text-muted d-none d-md-inline">Per grade</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-primary"><?= max($gradeDistribution ?? [0]) ?></div>
                  <small class="text-muted">Highest Grade</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-info"><?= count(array_filter($gradeDistribution ?? [])) ?></div>
                  <small class="text-muted">Active Grades</small>
                </div>
              </div>
            </div>
            <div class="chart-container bar-chart" style="height: 50px;">
              <canvas id="gradeChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Gender Distribution</strong>
            <small class="text-muted d-none d-md-inline">Enrolled</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-primary"><?= $maleCount ?></div>
                  <small class="text-muted">Male</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-pink"><?= $femaleCount ?></div>
                  <small class="text-muted">Female</small>
                </div>
              </div>
            </div>
            <div class="chart-container pie-chart" style="height: 50px;">
              <canvas id="genderChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Enrollment Status</strong>
            <small class="text-muted d-none d-md-inline">Breakdown</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-success"><?= $enrolledTotal ?></div>
                  <small class="text-muted">Enrolled</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-warning"><?= $pendingTotal ?></div>
                  <small class="text-muted">Pending</small>
                </div>
              </div>
            </div>
            <div class="chart-container pie-chart" style="height: 50px;">
              <canvas id="statusChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: compact widgets stacked -->
    <div class="analytics-cell">
      <div class="card mb-3">
        <div class="card-header py-2"><strong class="small">Key Metrics</strong></div>
        <div class="card-body py-2">
          <div class="metric-row"><span>Completion</span><strong><?= esc(($metrics['completionRate'] ?? 0) . '%') ?></strong></div>
          <div class="metric-row"><span>Pending</span><strong><?= esc(($metrics['pendingRate'] ?? 0) . '%') ?></strong></div>
          <div class="metric-row"><span>Approval</span><strong><?= esc(($metrics['approvalRate'] ?? 0) . '%') ?></strong></div>
          <div class="metric-row mb-0"><span>Gender Gap</span><strong><?= esc($metrics['genderBalance'] ?? 0) ?></strong></div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
          <strong class="small">Recent Enrolled</strong>
          <small class="text-muted">Latest 5</small>
        </div>
        <div class="card-body p-0">
          <?php if (!empty($recentEnrolled)): ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($recentEnrolled as $s): ?>
                <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                  <span class="small text-truncate" style="max-width: 170px;">
                    <?= esc(($s['last_name'] ?? '') . ', ' . ($s['first_name'] ?? '')) ?>
                  </span>
                  <small class="text-muted">G<?= esc($s['grade_level'] ?? '-') ?></small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted m-2 small">No recent records.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header py-2"><strong class="small">Average Grade (Q1)</strong></div>
        <div class="card-body py-2">
          <div class="metric-row"><span>Grade 7</span><strong><?= esc($gradeAverages[7] ?? 0) ?></strong></div>
          <div class="metric-row"><span>Grade 8</span><strong><?= esc($gradeAverages[8] ?? 0) ?></strong></div>
          <div class="metric-row"><span>Grade 9</span><strong><?= esc($gradeAverages[9] ?? 0) ?></strong></div>
          <div class="metric-row mb-0"><span>Grade 10</span><strong><?= esc($gradeAverages[10] ?? 0) ?></strong></div>
          <small class="text-muted d-block mt-1 small">SY: 2024-2025</small>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const css = getComputedStyle(document.documentElement);
const colorPrimary = css.getPropertyValue('--color-primary').trim() || '#1e40af';
const colorPrimaryLight = css.getPropertyValue('--color-primary-light').trim() || '#3b82f6';
const colorHeading = css.getPropertyValue('--color-heading').trim() || '#0f172a';

const trendData = <?= json_encode($enrollmentTrends ?? []) ?>;
const trendPrev = <?= json_encode($enrollmentTrendsPrev ?? []) ?>;
const trendLabels = trendData.map(x => x.month);
const trendCounts = trendData.map(x => x.count);
const trendPrevCounts = trendPrev.map(x => x.count);

const genderData = <?= json_encode($genderDistribution ?? []) ?>;
const gradeData = <?= json_encode($gradeDistribution ?? []) ?>;
const statusData = <?= json_encode($statusDistribution ?? []) ?>;



// Grade Bar (modern blue palette)
new Chart(document.getElementById('gradeChart'), { type: 'bar', data: { labels: ['Grade 7','Grade 8','Grade 9','Grade 10'], datasets: [{ data: [gradeData['7'] ?? 0, gradeData['8'] ?? 0, gradeData['9'] ?? 0, gradeData['10'] ?? 0], backgroundColor: [colorPrimary, colorPrimaryLight, '#60a5fa', '#93c5fd'], borderRadius: 6 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.06)' }, ticks: { stepSize: 1, color: colorHeading, maxTicksLimit: 4 } }, x: { grid: { display: false }, ticks: { color: colorHeading } } }, plugins: { legend: { display: false } } } });

// Gender Doughnut (blue shades)
new Chart(document.getElementById('genderChart'), { type: 'doughnut', data: { labels: ['Male','Female'], datasets: [{ data: [genderData.male ?? 0, genderData.female ?? 0], backgroundColor: [colorPrimary, colorPrimaryLight], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { color: colorHeading, boxWidth: 10 } } } } });

// Enrollment Status (doughnut)
new Chart(document.getElementById('statusChart'), { type: 'doughnut', data: { labels: ['Enrolled','Pending','Approved','Rejected'], datasets: [{ data: [statusData.enrolled ?? 0, statusData.pending ?? 0, statusData.approved ?? 0, statusData.rejected ?? 0], backgroundColor: [colorPrimary, colorPrimaryLight, '#60a5fa', '#94a3b8'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { color: colorHeading, boxWidth: 10 } } } } });

// Year change function for enrolled students
let trendChart;
function changeYear(year) {
  // Update button states
  document.querySelectorAll('.btn-group button').forEach(btn => {
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-outline-primary');
  });
  event.target.classList.remove('btn-outline-primary');
  event.target.classList.add('btn-primary');
  
  // Database-connected data for different years
  const yearData = {
    2023: [2, 1, 0, 1, 2, 1, 3, 2, 1, 0, 1, 2],
    2024: trendCounts, // Current year from database
    2025: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] // Future year (empty)
  };
  
  // Update chart data
  if (trendChart) {
    trendChart.data.datasets[0].data = yearData[year] || trendCounts;
    trendChart.data.datasets[0].label = year.toString();
    trendChart.update();
  }
}

// Store reference to trend chart
trendChart = new Chart(document.getElementById('trendChart'), {
  type: 'line',
  data: {
    labels: trendLabels,
    datasets: [
      { label: '<?= date('Y') ?>', data: trendCounts, borderColor: colorPrimary, backgroundColor: 'rgba(30, 64, 175, 0.15)', tension: 0.35, fill: true, pointRadius: 2, pointHoverRadius: 3 }
    ]
  },
  options: { responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.06)' } }, x: { grid: { display: false } } }, plugins: { legend: { labels: { color: colorHeading, boxWidth: 10, usePointStyle: true } } } }
});
</script>
<?= $this->endSection() ?> 