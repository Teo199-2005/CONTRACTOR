<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Messages</h1>
  <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<div class="alert alert-info">Internal messaging UI will be added here.</div>
<?= $this->endSection() ?>



