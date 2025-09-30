<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Learning Materials</h1>
  <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($error = session('error')): ?>
  <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" action="<?= base_url('teacher/materials/upload') ?>">
      <?= csrf_field() ?>
      <div class="row g-3 align-items-end">
        <div class="col-md-8">
          <label class="form-label">Upload Material</label>
          <input type="file" name="material" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.png,.jpg" required />
        </div>
        <div class="col-md-4">
          <button class="btn btn-primary" type="submit"><i class="bi bi-upload me-2"></i>Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="alert alert-info">Uploaded materials are stored under <code>write/uploads/materials</code>.</div>
<?= $this->endSection() ?>



