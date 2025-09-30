<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Class Materials</h1>
  <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<!-- Class Schedule Section -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">My Class Schedule</h5>
    <?php 
      $sectionName = 'Unknown Section';
      if (!empty($student['section_id'])) {
        $db = \Config\Database::connect();
        $section = $db->table('sections')->where('id', $student['section_id'])->get()->getRowArray();
        if ($section && !empty($section['section_name'])) {
          $sectionName = $section['section_name'];
        }
      }
    ?>
    <small class="text-muted">Grade <?= $student['grade_level'] ?? 'N/A' ?> - <?= $sectionName ?></small>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Time</th>
            <th>Subject (Teacher)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="fw-bold">7:15-7:30</td>
            <td><em>Flag Ritual</em></td>
          </tr>
          <?php if (!empty($schedule)): ?>
            <?php foreach ($schedule as $time => $subject): ?>
              <tr>
                <td class="fw-bold"><?= esc($time) ?></td>
                <td><?= esc($subject) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="2" class="text-muted text-center">Schedule not available</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>



<?= $this->endSection() ?>



