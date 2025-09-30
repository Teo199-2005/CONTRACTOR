<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Class Schedule</h1>
<div class="card">
  <div class="card-body p-0">
    <?php if (!empty($subjects)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Code</th>
              <th>Units</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($subjects as $s): ?>
              <tr>
                <td><?= esc($s['subject_name']) ?></td>
                <td><?= esc($s['subject_code']) ?></td>
                <td><?= esc($s['units']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="p-4 text-center text-muted">No subjects for your grade level.</div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?> 