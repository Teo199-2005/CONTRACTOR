<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Manage Announcements</h1>
  <a href="<?= base_url('announcements/create') ?>" class="btn btn-primary">New Announcement</a>
</div>
<?php if (session('message')): ?>
  <div class="alert alert-success"><?= esc(session('message')) ?></div>
<?php endif; ?>
<?php if (empty($announcements)): ?>
  <p class="text-muted">No announcements yet.</p>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Title</th>
          <th>Targets</th>
          <th>Published</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($announcements as $a): ?>
          <tr>
            <td><?= esc($a['title']) ?></td>
            <td><?= esc($a['target_roles']) ?></td>
            <td><?= esc($a['published_at']) ?></td>
            <td><?= esc($a['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>

