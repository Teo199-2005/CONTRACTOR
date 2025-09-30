<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* Enhanced Analytics Page Styling */
.analytics-page-content {
  min-height: 200vh;
  padding-bottom: 15rem;
}

.analytics-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
  border: 2px solid #e2e8f0;
  transition: all 0.3s ease;
  position: relative;
}

.analytics-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #3b82f6, #1e40af, #3b82f6);
  border-radius: 4px 4px 0 0;
}

.analytics-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
  border-color: #3b82f6;
}

.stat-card {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  border-radius: 12px;
  padding: 1.5rem;
  text-align: center;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 30px rgba(30, 64, 175, 0.25);
}

.stat-card.success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card.warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card.info {
  background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.9;
  font-weight: 500;
}

.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
  background: #f8fafc;
  border-radius: 8px;
  padding: 1rem;
  border: 1px solid #e2e8f0;
}

.performance-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-header {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 12px 12px 0 0;
  margin: -1rem -1rem 1rem -1rem;
  border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.analytics-grid {
  display: grid;
  gap: 1.5rem;
}

.trend-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.trend-up {
  color: #10b981;
  background: rgba(16, 185, 129, 0.1);
  border-color: rgba(16, 185, 129, 0.2);
}

.trend-down {
  color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
  border-color: rgba(239, 68, 68, 0.2);
}

/* Widget containers enhancement */
.widget, .stat-widget, .chart-widget {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  background: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  position: relative;
}

.widget::after {
  content: '';
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  background: linear-gradient(45deg, #3b82f6, #1e40af, #3b82f6, #60a5fa);
  border-radius: 12px;
  z-index: -1;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.widget:hover::after {
  opacity: 0.1;
}

.student-row {
  padding: 0.75rem;
  border-radius: 8px;
  margin-bottom: 0.5rem;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-left: 4px solid #3b82f6;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

.student-row:hover {
  background: #f8fafc;
  border-color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.1);
}

.progress-thin {
  height: 8px;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress-bar {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}
</style>

<div class="analytics-page-content">
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div class="d-flex align-items-center gap-3">
    <div class="text-primary fs-4"><i class="bi bi-graph-up-arrow"></i></div>
    <div>
      <h1 class="h4 mb-0">Class Analytics</h1>
      <small class="text-muted">Comprehensive performance insights for your classes</small>
    </div>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary btn-sm" onclick="exportAnalytics()">
      <i class="bi bi-download me-1"></i>Export Report
    </button>
    <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-warning">
  <i class="bi bi-exclamation-triangle me-2"></i><?= esc($error) ?>
</div>
<?php else: ?>

<!-- Key Statistics Row -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-number"><?= $analytics['totalStudents'] ?? 0 ?></div>
      <div class="stat-label">Total Students</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card success">
      <div class="stat-number"><?= number_format($analytics['classAverage'] ?? 0, 1) ?>%</div>
      <div class="stat-label">Class Average</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card warning">
      <div class="stat-number"><?= number_format($analytics['attendanceRate'] ?? 0, 1) ?>%</div>
      <div class="stat-label">Attendance Rate</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card info">
      <div class="stat-number">+<?= number_format($analytics['improvementRate'] ?? 0, 1) ?>%</div>
      <div class="stat-label">Improvement Rate</div>
    </div>
  </div>
</div>

<!-- Main Analytics Grid -->
<div class="row g-4">
  <!-- Grade Distribution Chart -->
  <div class="col-lg-6">
    <div class="analytics-card h-100">
      <div class="section-header">
        <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Grade Distribution</h5>
      </div>
      <div class="p-3">
        <div class="chart-container">
          <canvas id="gradeDistributionChart"></canvas>
        </div>
        <div class="mt-3">
          <div class="row text-center">
            <div class="col-4">
              <div class="text-success fw-bold"><?= $analytics['gradeDistribution']['excellent'] + $analytics['gradeDistribution']['very_good'] ?></div>
              <small class="text-muted">Excellent/Very Good</small>
            </div>
            <div class="col-4">
              <div class="text-warning fw-bold"><?= $analytics['gradeDistribution']['good'] + $analytics['gradeDistribution']['fair'] ?></div>
              <small class="text-muted">Good/Fair</small>
            </div>
            <div class="col-4">
              <div class="text-danger fw-bold"><?= $analytics['gradeDistribution']['passing'] + $analytics['gradeDistribution']['failing'] ?></div>
              <small class="text-muted">Needs Improvement</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quarter Trends Chart -->
  <div class="col-lg-6">
    <div class="analytics-card h-100">
      <div class="section-header">
        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Quarter Performance Trends</h5>
      </div>
      <div class="p-3">
        <div class="chart-container">
          <canvas id="quarterTrendsChart"></canvas>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="trend-indicator trend-up">
              <i class="bi bi-arrow-up"></i>
              Improving trend this quarter
            </span>
            <small class="text-muted">School Year <?= $schoolYear ?></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Subject Performance and Student Rankings -->
<div class="row g-4 mt-2">
  <!-- Subject Averages -->
  <div class="col-lg-6">
    <div class="analytics-card h-100">
      <div class="section-header">
        <h5 class="mb-0"><i class="bi bi-book me-2"></i>Subject Performance</h5>
      </div>
      <div class="p-3">
        <?php if (!empty($analytics['subjectAverages'])): ?>
          <?php foreach ($analytics['subjectAverages'] as $subject): ?>
            <div class="student-row">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold"><?= esc($subject['subject']) ?></span>
                <span class="performance-badge bg-<?= $subject['average'] >= 85 ? 'success' : ($subject['average'] >= 75 ? 'warning' : 'danger') ?> text-white">
                  <?= number_format($subject['average'], 1) ?>%
                </span>
              </div>
              <div class="progress progress-thin">
                <div class="progress-bar bg-<?= $subject['average'] >= 85 ? 'success' : ($subject['average'] >= 75 ? 'warning' : 'danger') ?>"
                     style="width: <?= min($subject['average'], 100) ?>%"></div>
              </div>
              <small class="text-muted"><?= $subject['count'] ?> students graded</small>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-graph-down text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">No subject data available yet</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Top Performing Students -->
  <div class="col-lg-6">
    <div class="analytics-card h-100">
      <div class="section-header">
        <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Student Performance Rankings</h5>
      </div>
      <div class="p-3">
        <?php if (!empty($analytics['studentPerformance'])): ?>
          <?php
          // Sort students by average (descending)
          usort($analytics['studentPerformance'], function($a, $b) {
            return $b['average'] <=> $a['average'];
          });
          ?>
          <?php foreach (array_slice($analytics['studentPerformance'], 0, 10) as $index => $student): ?>
            <div class="student-row">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                  <div class="badge bg-<?= $index < 3 ? 'warning' : 'secondary' ?> rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <?= $index + 1 ?>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= esc($student['name']) ?></div>
                    <small class="text-muted"><?= $student['grade_count'] ?> subjects</small>
                  </div>
                </div>
                <span class="performance-badge bg-<?= $student['average'] >= 85 ? 'success' : ($student['average'] >= 75 ? 'warning' : 'danger') ?> text-white">
                  <?= number_format($student['average'], 1) ?>%
                </span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">No student performance data available</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Additional Insights -->
<div class="row g-4 mt-2">
  <div class="col-12">
    <div class="analytics-card">
      <div class="section-header">
        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Key Insights & Recommendations</h5>
      </div>
      <div class="p-3">
        <div class="row">
          <div class="col-md-4">
            <div class="border-start border-primary border-4 ps-3 mb-3">
              <h6 class="text-primary mb-1">Performance Trend</h6>
              <p class="mb-0 small">
                <?php if (($analytics['classAverage'] ?? 0) >= 85): ?>
                  <span class="text-success">Excellent!</span> Your class is performing exceptionally well with an average of <?= number_format($analytics['classAverage'], 1) ?>%.
                <?php elseif (($analytics['classAverage'] ?? 0) >= 75): ?>
                  <span class="text-warning">Good progress!</span> Class average is <?= number_format($analytics['classAverage'], 1) ?>%. Consider targeted support for struggling students.
                <?php else: ?>
                  <span class="text-danger">Needs attention.</span> Class average is <?= number_format($analytics['classAverage'], 1) ?>%. Implement intervention strategies.
                <?php endif; ?>
              </p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="border-start border-success border-4 ps-3 mb-3">
              <h6 class="text-success mb-1">Attendance Impact</h6>
              <p class="mb-0 small">
                High attendance rate of <?= number_format($analytics['attendanceRate'], 1) ?>% correlates with better academic performance.
                Continue encouraging regular attendance.
              </p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="border-start border-info border-4 ps-3 mb-3">
              <h6 class="text-info mb-1">Subject Focus</h6>
              <p class="mb-0 small">
                <?php if (!empty($analytics['subjectAverages'])): ?>
                  <?php
                  $lowestSubject = array_reduce($analytics['subjectAverages'], function($carry, $item) {
                    return (!$carry || $item['average'] < $carry['average']) ? $item : $carry;
                  });
                  ?>
                  Consider additional support for <strong><?= esc($lowestSubject['subject']) ?></strong>
                  (<?= number_format($lowestSubject['average'], 1) ?>% average).
                <?php else: ?>
                  Start entering grades to see subject-specific recommendations.
                <?php endif; ?>
              </p>
            </div>
          </div>
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
  report += 'School Year: <?= $schoolYear ?>\n';
  report += 'Quarter: <?= $currentQuarter ?>\n';
  report += 'Generated: ' + new Date().toLocaleDateString() + '\n\n';

  report += 'SUMMARY STATISTICS:\n';
  report += '- Total Students: <?= $analytics["totalStudents"] ?? 0 ?>\n';
  report += '- Class Average: <?= number_format($analytics["classAverage"] ?? 0, 1) ?>%\n';
  report += '- Attendance Rate: <?= number_format($analytics["attendanceRate"] ?? 0, 1) ?>%\n\n';

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

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initGradeDistributionChart();
  initQuarterTrendsChart();
});
</script>

</div> <!-- Close analytics-page-content -->

<?= $this->endSection() ?>



