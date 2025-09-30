<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Generate Student ID Cards</h1>
  <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
      <?php foreach ($students as $s): ?>
      <div class="col">
        <div class="border rounded p-3 bg-light">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong><?= esc($s['last_name'] . ', ' . $s['first_name']) ?></strong>
            <span class="badge bg-primary">Grade <?= esc($s['grade_level']) ?></span>
          </div>
          <div class="small text-muted">Student ID: <?= esc($s['student_id'] ?? 'Pending') ?></div>
          <div class="mt-2">
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>



