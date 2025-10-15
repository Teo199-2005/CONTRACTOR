<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3">Announcements</h1>
    <p class="text-muted mb-0">View announcements from admin and post to students</p>
  </div>
  <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
  </a>
</div>

<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>
<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<!-- Post New Announcement -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="bi bi-plus-circle me-2"></i>Post New Announcement
    </h5>
  </div>
  <div class="card-body">
    <form method="post" action="<?= base_url('teacher/announcements') ?>">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Body</label>
        <textarea name="body" rows="4" class="form-control" required></textarea>
      </div>
      <button class="btn btn-primary" type="submit">
        <i class="bi bi-send me-2"></i>Post to Students
      </button>
    </form>
  </div>
</div>

<!-- Announcements List -->
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="bi bi-megaphone me-2"></i>Recent Announcements
    </h5>
  </div>
  <div class="card-body">
    <?php if (!empty($announcements)): ?>
      <?php foreach ($announcements as $announcement): ?>
        <div class="announcement-item border-bottom pb-3 mb-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-1"><?= esc($announcement['title']) ?></h6>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-<?= $announcement['target_roles'] === 'admin' ? 'primary' : ($announcement['target_roles'] === 'all' ? 'success' : 'info') ?>">
                <?= ucfirst(esc($announcement['target_roles'])) ?>
              </span>
              <small class="text-muted">
                <?= date('M j, Y', strtotime($announcement['created_at'])) ?>
              </small>
            </div>
          </div>
          <p class="text-muted mb-0">
            <?= esc(substr($announcement['body'], 0, 200)) ?><?= strlen($announcement['body']) > 200 ? '...' : '' ?>
          </p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-4">
        <i class="bi bi-megaphone fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No Announcements</h5>
        <p class="text-muted mb-0">No announcements have been posted yet.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
.announcement-item:last-child {
  border-bottom: none !important;
  margin-bottom: 0 !important;
  padding-bottom: 0 !important;
}
</style>

<?= $this->endSection() ?>



