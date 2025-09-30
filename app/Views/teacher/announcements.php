<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Post Announcements</h1>
  <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>
<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="post" action="<?= base_url('teacher/announcements') ?>">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Body</label>
        <textarea name="body" rows="6" class="form-control" required></textarea>
      </div>
      <button class="btn btn-primary" type="submit"><i class="bi bi-send me-2"></i>Post</button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>



