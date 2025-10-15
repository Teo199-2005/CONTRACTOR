<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
@media print {
  .no-print { display: none !important; }
  body { margin: 0; }
  .id-card { page-break-after: always; }
  * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
}

.id-card {
  width: 4.5in;
  height: 3in;
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  border-radius: 15px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  margin: 20px auto;
  font-family: 'Arial', sans-serif;
  -webkit-print-color-adjust: exact;
  color-adjust: exact;
}

.id-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 80px;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
}

.card-header {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px 20px;
  color: white;
  gap: 12px;
}

.school-logo {
  width: 55px;
  height: 55px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  padding: 3px;
}

.school-logo img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 50%;
}

.school-name {
  font-family: 'Times New Roman', serif;
  font-size: 16px;
  font-weight: bold;
  margin: 0;
  color: black;
  text-shadow: none;
  text-align: center;
}

.card-body {
  padding: 40px 20px 20px 20px;
  color: white;
  position: relative;
  z-index: 2;
}

.student-photo {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  border: 4px solid white;
  object-fit: cover;
  float: left;
  margin-right: 15px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.no-photo {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  border: 4px solid white;
  background: rgba(255,255,255,0.2);
  float: left;
  margin-right: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 30px;
  color: rgba(255,255,255,0.7);
}

.student-info {
  overflow: hidden;
}

.student-name {
  font-size: 18px;
  font-weight: bold;
  margin: 0 0 6px 0;
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.student-details {
  font-size: 13px;
  line-height: 1.4;
  opacity: 0.95;
}

.student-id {
  font-size: 14px;
  font-weight: bold;
  background: rgba(255,255,255,0.2);
  padding: 4px 8px;
  border-radius: 6px;
  display: inline-block;
  margin-top: 6px;
}

.card-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0,0,0,0.2);
  padding: 6px 20px;
  font-size: 10px;
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
      <?php if (empty($student['lrn'])): ?>
        <button onclick="generateLRN(<?= $student['id'] ?>)" class="btn btn-success me-2">
          <i class="bi bi-card-text me-1"></i>Generate LRN
        </button>
      <?php endif; ?>
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
      <div class="school-logo">
        <img src="<?= base_url('LPHS2.png') ?>" alt="LPHS Logo">
      </div>
      <div class="school-name">LOURDES PROVINCIAL HIGH SCHOOL</div>
    </div>
    
    <div class="card-body">
      <?php if (!empty($student['photo']) && file_exists(FCPATH . 'uploads/enrollment_documents/' . $student['photo'])): ?>
        <img src="<?= base_url('uploads/enrollment_documents/' . $student['photo']) ?>" alt="Student Photo" class="student-photo">
      <?php elseif (!empty($student['photo_path'])): ?>
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
        <div class="student-id"><?= esc($student['lrn'] ?? 'LRN PENDING') ?></div>
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

<script>
function generateLRN(studentId) {
  if (!confirm('Generate an LRN for this student?')) {
    return;
  }
  
  fetch('<?= base_url('admin/id-cards/generate-lrn') ?>/' + studentId, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error: ' + (data.message || 'Failed to generate LRN'));
    }
  })
  .catch(error => {
    alert('Error generating LRN');
  });
}
</script>

<?= $this->endSection() ?>