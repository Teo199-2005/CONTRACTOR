<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
@media print {
  .no-print { display: none !important; }
  body { margin: 0; }
  .id-card { page-break-after: always; }
}

.id-card {
  width: 3.375in;
  height: 2.125in;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  border-radius: 12px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  margin: 20px auto;
  font-family: 'Arial', sans-serif;
}

.id-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
}

.card-header {
  position: relative;
  z-index: 2;
  text-align: center;
  padding: 8px 15px;
  color: white;
}

.school-logo {
  width: 35px;
  height: 35px;
  background: white;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #1e40af;
  font-size: 14px;
  margin-bottom: 2px;
}

.school-name {
  font-size: 11px;
  font-weight: bold;
  margin: 0;
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.card-body {
  padding: 15px;
  color: white;
  position: relative;
  z-index: 2;
}

.student-photo {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  border: 3px solid white;
  object-fit: cover;
  float: left;
  margin-right: 12px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.no-photo {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  border: 3px solid white;
  background: rgba(255,255,255,0.2);
  float: left;
  margin-right: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: rgba(255,255,255,0.7);
}

.student-info {
  overflow: hidden;
}

.student-name {
  font-size: 14px;
  font-weight: bold;
  margin: 0 0 4px 0;
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.student-details {
  font-size: 10px;
  line-height: 1.3;
  opacity: 0.95;
}

.student-id {
  font-size: 11px;
  font-weight: bold;
  background: rgba(255,255,255,0.2);
  padding: 2px 6px;
  border-radius: 4px;
  display: inline-block;
  margin-top: 4px;
}

.card-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0,0,0,0.2);
  padding: 4px 15px;
  font-size: 8px;
  text-align: center;
  color: rgba(255,255,255,0.8);
}

.preview-container {
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
}
</style>

<div class="no-print">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0"><i class="bi bi-person-badge me-2"></i>Student ID Card Preview</h1>
    <div>
      <button onclick="window.print()" class="btn btn-primary me-2">
        <i class="bi bi-printer me-1"></i>Print ID Card
      </button>
      <a href="<?= base_url('admin/id-cards') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
      </a>
    </div>
  </div>
</div>

<div class="preview-container">
  <div class="id-card">
    <div class="card-header">
      <div class="school-logo">LPHS</div>
      <div class="school-name">LOURDES PROVINCIAL HIGH SCHOOL</div>
    </div>
    
    <div class="card-body">
      <?php if ($student['photo_path']): ?>
        <img src="<?= base_url($student['photo_path']) ?>" alt="Student Photo" class="student-photo">
      <?php else: ?>
        <div class="no-photo">
          <i class="bi bi-person"></i>
        </div>
      <?php endif; ?>
      
      <div class="student-info">
        <div class="student-name"><?= strtoupper(esc($student['first_name'] . ' ' . $student['last_name'])) ?></div>
        <div class="student-details">
          Grade <?= esc($student['grade_level']) ?><?= $student['section_name'] ? ' - ' . esc($student['section_name']) : '' ?><br>
          S.Y. <?= esc($student['school_year'] ?? '2024-2025') ?>
        </div>
        <div class="student-id"><?= esc($student['student_id'] ?? 'ID PENDING') ?></div>
      </div>
    </div>
    
    <div class="card-footer">
      Panglao, Bohol • Valid for Current School Year Only
    </div>
  </div>
  
  <div class="no-print mt-4">
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>
      <strong>Print Instructions:</strong> Use high-quality cardstock paper and ensure your printer is set to actual size (100% scale) for best results.
    </div>
  </div>
</div>

<?= $this->endSection() ?>