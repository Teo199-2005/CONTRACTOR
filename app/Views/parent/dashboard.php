<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Parent Dashboard</h1>
<div class="row g-3">
  <div class="col-auto"><a class="btn btn-outline-primary" href="<?= base_url('parent/children') ?>">My Children</a></div>
  <div class="col-auto"><a class="btn btn-outline-primary" href="<?= base_url('parent/announcements') ?>">Announcements</a></div>
</div>
<p class="text-muted mt-3">Welcome! You will be able to view your children's grades and school announcements.</p>
<?= $this->endSection() ?> 