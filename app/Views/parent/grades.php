<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-3">Child Grades</h1>
<p class="text-muted">Grades for student ID: <?= esc($studentId) ?> will be displayed here.</p>
<?= $this->endSection() ?> 