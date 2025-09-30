<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Announcements</h1>
<?php if (empty($announcements)): ?>
  <p class="text-muted">No announcements yet.</p>
<?php else: ?>
  <div class="list-group">
    <?php foreach ($announcements as $a): ?>
      <div class="list-group-item">
        <h2 class="h6 mb-1"><?= esc($a['title']) ?></h2>
        <div class="small text-muted">Published: <?= esc($a['published_at'] ?? $a['created_at'] ?? '') ?></div>
        <div><?= esc($a['body']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>

