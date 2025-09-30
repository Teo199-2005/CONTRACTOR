<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Announcements</h1>
<div class="list-group">
  <?php if (!empty($announcements)): ?>
    <?php foreach ($announcements as $a): ?>
      <div class="list-group-item">
        <div class="d-flex w-100 justify-content-between">
          <h5 class="mb-1"><?= esc($a['title']) ?></h5>
          <small class="text-muted"><?= esc($a['published_at']) ?></small>
        </div>
        <p class="mb-1"><?= nl2br(esc($a['body'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="text-muted">No announcements.</div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?> 