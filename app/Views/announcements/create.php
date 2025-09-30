<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Create Announcement</h1>
<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<form method="post" action="<?= base_url('announcements') ?>">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" required value="<?= old('title') ?>" />
  </div>
  <div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control" required value="<?= old('slug') ?>" />
  </div>
  <div class="mb-3">
    <label class="form-label">Body</label>
    <textarea name="body" class="form-control" rows="5" required><?= old('body') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Target Roles</label>
    <select name="target_roles" class="form-select" required>
      <option value="all">All</option>
      <option value="admin">Admin</option>
      <option value="teacher">Teacher</option>
      <option value="student">Student</option>
      <option value="parent">Parent</option>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Publish At</label>
    <input type="datetime-local" name="published_at" class="form-control" value="<?= old('published_at') ?>" />
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Create</button>
    <a href="<?= base_url('announcements/admin') ?>" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>
<?= $this->endSection() ?>

