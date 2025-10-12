<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* Admin-style Analytics Styling */
.analytics-page.compact {
  font-size: 14px;
}

.overview-card {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border: 1px solid #cbd5e1;
  border-radius: 8px;
}

.stat-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.stat-chip {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.bg-primary-soft { background: rgba(30, 64, 175, 0.1); color: #1e40af; }
.bg-blue-soft { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.bg-amber-soft { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.bg-slate-soft { background: rgba(100, 116, 139, 0.1); color: #64748b; }
.bg-gray-soft { background: rgba(107, 114, 128, 0.1); color: #6b7280; }
.bg-cyan-soft { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
.bg-indigo-soft { background: rgba(99, 102, 241, 0.1); color: #6366f1; }

.analytics-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1rem;
  margin-top: 1rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.chart-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-container {
  position: relative;
  height: 120px;
}

.chart-canvas {
  max-height: 120px;
}

.metric-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
}

.metric-row:last-child {
  border-bottom: none;
}

.analytics-header {
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .analytics-layout {
    grid-template-columns: 1fr;
  }
  .charts-grid {
    grid-template-columns: 1fr;
  }
  .stat-chips {
    justify-content: center;
  }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3 analytics-header">
  <h1 class="h5 mb-0">Class Analytics</h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('teacher/analytics/export-pdf') ?>" class="btn btn-sm btn-primary" target="_blank">
      <i class="bi bi-file-earmark-pdf"></i> Export PDF Report
    </a>
    <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<?php
  $totalStudents = (int)($analytics['totalStudents'] ?? 0);
  $classAverage = (float)($analytics['classAverage'] ?? 0);
  $attendanceRate = (float)($analytics['attendanceRate'] ?? 0);
  $improvementRate = (float)($analytics['improvementRate'] ?? 0);
?>

<?php if (isset($error)): ?>
<div class="alert alert-warning">
  <i class="bi bi-exclamation-triangle me-2"></i><?= esc($error) ?>
</div>
<?php else: ?>

<div class="analytics-page compact">
  <div class="card overview-card mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
      <div class="d-flex align-items-center gap-2">
        <h6 class="mb-0">Overview</h6>
        <small class="text-muted">Class performance snapshot</small>
      </div>
      <div class="stat-chips">
        <span class="stat-chip bg-primary-soft">Total <strong><?= $totalStudents ?></strong></span>
        <span class="stat-chip bg-blue-soft">Average <strong><?= number_format($classAverage, 1) ?>%</strong></span>
        <span class="stat-chip bg-amber-soft">Attendance <strong><?= number_format($attendanceRate, 1) ?>%</strong></span>
        <span class="stat-chip bg-cyan-soft">Improvement <strong>+<?= number_format($improvementRate, 1) ?>%</strong></span>
      </div>
    </div>
  </div>

  <div class="analytics-layout">
    <!-- Left: 2x2 charts grid (compact) -->
    <div class="analytics-cell">
      <div class="charts-grid">
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Grade Distribution</strong>
            <small class="text-muted d-none d-md-inline">Performance</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-4">
                <div class="text-center">
                  <div class="h6 mb-0 text-success"><?= ($analytics['gradeDistribution']['excellent'] ?? 0) + ($analytics['gradeDistribution']['very_good'] ?? 0) ?></div>
                  <small class="text-muted">Excellent</small>
                </div>
              </div>
              <div class="col-4">
                <div class="text-center">
                  <div class="h6 mb-0 text-warning"><?= ($analytics['gradeDistribution']['good'] ?? 0) + ($analytics['gradeDistribution']['fair'] ?? 0) ?></div>
                  <small class="text-muted">Good</small>
                </div>
              </div>
              <div class="col-4">
                <div class="text-center">
                  <div class="h6 mb-0 text-danger"><?= ($analytics['gradeDistribution']['passing'] ?? 0) + ($analytics['gradeDistribution']['failing'] ?? 0) ?></div>
                  <small class="text-muted">Needs Work</small>
                </div>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="gradeDistributionChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Quarter Trends</strong>
            <small class="text-muted d-none d-md-inline">Progress</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h6 mb-0 text-primary"><?= number_format($classAverage, 1) ?>%</div>
                  <small class="text-muted">Current</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h6 mb-0 text-success">+<?= number_format($improvementRate, 1) ?>%</div>
                  <small class="text-muted">Growth</small>
                </div>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="quarterTrendsChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Performance Trends</strong>
            <small class="text-muted d-none d-md-inline">Monthly</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h6 mb-0 text-primary"><?= number_format($classAverage, 1) ?>%</div>
                  <small class="text-muted">Current</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h6 mb-0 text-success">+<?= number_format($improvementRate, 1) ?>%</div>
                  <small class="text-muted">Growth</small>
                </div>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="performanceChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Attendance Overview</strong>
            <small class="text-muted d-none d-md-inline">This Month</small>
          </div>
          <div class="card-body py-2">
            <?php if (isset($analytics['attendanceStats'])): ?>
              <div class="row mb-2">
                <div class="col-6">
                  <div class="text-center">
                    <div class="h6 mb-0 text-success"><?= $analytics['attendanceStats']['present'] ?? 0 ?></div>
                    <small class="text-muted">Present</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center">
                    <div class="h6 mb-0 text-danger"><?= $analytics['attendanceStats']['absent'] ?? 0 ?></div>
                    <small class="text-muted">Absent</small>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <div class="text-center py-3">
                <small class="text-muted">No attendance data</small>
              </div>
            <?php endif; ?>
            <div class="chart-container">
              <canvas id="attendanceChart" class="chart-canvas"></canvas>
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
          <div class="metric-row"><span>Class Average</span><strong><?= number_format($classAverage, 1) ?>%</strong></div>
          <div class="metric-row"><span>Attendance</span><strong><?= number_format($attendanceRate, 1) ?>%</strong></div>
          <div class="metric-row"><span>Improvement</span><strong>+<?= number_format($improvementRate, 1) ?>%</strong></div>
          <div class="metric-row mb-0"><span>Total Students</span><strong><?= $totalStudents ?></strong></div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
          <strong class="small">Top Performers</strong>
          <small class="text-muted">Latest 5</small>
        </div>
        <div class="card-body p-0">
          <?php if (!empty($analytics['studentPerformance'])): ?>
            <?php
            usort($analytics['studentPerformance'], function($a, $b) {
              return $b['average'] <=> $a['average'];
            });
            ?>
            <ul class="list-group list-group-flush">
              <?php foreach (array_slice($analytics['studentPerformance'], 0, 5) as $s): ?>
                <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                  <span class="small text-truncate" style="max-width: 170px;">
                    <?= esc($s['name']) ?>
                  </span>
                  <small class="text-muted"><?= number_format($s['average'], 1) ?>%</small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted m-2 small">No student data available.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header py-2"><strong class="small">Subject Averages</strong></div>
        <div class="card-body py-2">
          <?php if (!empty($analytics['subjectAverages'])): ?>
            <?php foreach (array_slice($analytics['subjectAverages'], 0, 4) as $subject): ?>
              <div class="metric-row">
                <span><?= esc(substr($subject['subject'], 0, 15)) ?><?= strlen($subject['subject']) > 15 ? '...' : '' ?></span>
                <strong><?= number_format($subject['average'], 1) ?>%</strong>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="metric-row mb-0">
              <span class="text-muted small">No subject data available</span>
              <strong>-</strong>
            </div>
          <?php endif; ?>
          <small class="text-muted d-block mt-1 small">SY: <?= $schoolYear ?? '2024-2025' ?></small>
        </div>
      </div>
    </div>
  </div>
</div>

<?php endif; ?>



<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart data from PHP
const gradeDistribution = <?= json_encode($analytics['gradeDistribution'] ?? []) ?>;
const quarterTrends = <?= json_encode($analytics['quarterTrends'] ?? []) ?>;
const subjectAverages = <?= json_encode($analytics['subjectAverages'] ?? []) ?>;
const attendanceStats = <?= json_encode($analytics['attendanceStats'] ?? []) ?>;
const analytics = <?= json_encode($analytics ?? []) ?>;
const schoolYear = '<?= $schoolYear ?? '2024-2025' ?>';
const currentQuarter = '<?= $currentQuarter ?? '1' ?>';

// Grade Distribution Doughnut Chart
function initGradeDistributionChart() {
  const ctx = document.getElementById('gradeDistributionChart').getContext('2d');

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Excellent (90-100)', 'Very Good (85-89)', 'Good (80-84)', 'Fair (75-79)', 'Passing (70-74)', 'Failing (<70)'],
      datasets: [{
        data: [
          gradeDistribution.excellent || 0,
          gradeDistribution.very_good || 0,
          gradeDistribution.good || 0,
          gradeDistribution.fair || 0,
          gradeDistribution.passing || 0,
          gradeDistribution.failing || 0
        ],
        backgroundColor: [
          '#10b981', // Excellent - Green
          '#22c55e', // Very Good - Light Green
          '#3b82f6', // Good - Blue
          '#f59e0b', // Fair - Yellow
          '#f97316', // Passing - Orange
          '#ef4444'  // Failing - Red
        ],
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverBorderWidth: 4
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
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
              return context.label + ': ' + context.parsed + ' students (' + percentage + '%)';
            }
          }
        }
      },
      cutout: '60%'
    }
  });
}

// Quarter Trends Line Chart
function initQuarterTrendsChart() {
  const ctx = document.getElementById('quarterTrendsChart').getContext('2d');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: quarterTrends.map(q => q.quarter),
      datasets: [{
        label: 'Class Average',
        data: quarterTrends.map(q => q.average),
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 3,
        pointRadius: 6,
        pointHoverRadius: 8
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
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Average: ' + context.parsed.y.toFixed(1) + '%';
            }
          }
        }
      }
    }
  });
}

// Export Analytics Function
function exportAnalytics() {
  // Create a simple text report
  let report = 'CLASS ANALYTICS REPORT\n';
  report += '======================\n\n';
  report += 'School Year: ' + (schoolYear || '2024-2025') + '\n';
  report += 'Quarter: ' + (currentQuarter || '1') + '\n';
  report += 'Generated: ' + new Date().toLocaleDateString() + '\n\n';

  report += 'SUMMARY STATISTICS:\n';
  report += '- Total Students: ' + (analytics.totalStudents || 0) + '\n';
  report += '- Class Average: ' + (analytics.classAverage || 0).toFixed(1) + '%\n';
  report += '- Attendance Rate: ' + (analytics.attendanceRate || 0).toFixed(1) + '%\n\n';

  report += 'GRADE DISTRIBUTION:\n';
  report += '- Excellent (90-100): ' + (gradeDistribution.excellent || 0) + ' students\n';
  report += '- Very Good (85-89): ' + (gradeDistribution.very_good || 0) + ' students\n';
  report += '- Good (80-84): ' + (gradeDistribution.good || 0) + ' students\n';
  report += '- Fair (75-79): ' + (gradeDistribution.fair || 0) + ' students\n';
  report += '- Passing (70-74): ' + (gradeDistribution.passing || 0) + ' students\n';
  report += '- Failing (<70): ' + (gradeDistribution.failing || 0) + ' students\n\n';

  if (subjectAverages.length > 0) {
    report += 'SUBJECT AVERAGES:\n';
    subjectAverages.forEach(subject => {
      report += '- ' + subject.subject + ': ' + subject.average.toFixed(1) + '%\n';
    });
  }

  // Download as text file
  const blob = new Blob([report], { type: 'text/plain' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'class_analytics_report.txt';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  window.URL.revokeObjectURL(url);
}

// Attendance Chart
function initAttendanceChart() {
  const ctx = document.getElementById('attendanceChart').getContext('2d');

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Present', 'Absent', 'Late', 'Excused'],
      datasets: [{
        data: [
          attendanceStats.present || 0,
          attendanceStats.absent || 0,
          attendanceStats.late || 0,
          attendanceStats.excused || 0
        ],
        backgroundColor: [
          '#10b981', // Present - Green
          '#ef4444', // Absent - Red
          '#f59e0b', // Late - Yellow
          '#3b82f6'  // Excused - Blue
        ],
        borderWidth: 2,
        borderColor: '#ffffff'
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
            padding: 10,
            font: {
              size: 10
            }
          }
        }
      },
      cutout: '50%'
    }
  });
}

// Performance Trends Chart (Area Chart)
function initPerformanceChart() {
  const ctx = document.getElementById('performanceChart').getContext('2d');
  
  // Mock monthly performance data
  const monthlyData = [78, 82, 85, 87, 84, 88];
  const months = ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'];
  
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Class Performance',
        data: monthlyData,
        borderColor: '#8b5cf6',
        backgroundColor: 'rgba(139, 92, 246, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#8b5cf6',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: false,
          min: 70,
          max: 95,
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
            display: false
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
  initGradeDistributionChart();
  initQuarterTrendsChart();
  initAttendanceChart();
  initPerformanceChart();
});
</script>

<?= $this->endSection() ?>



