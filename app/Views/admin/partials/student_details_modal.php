<div class="row">
  <div class="col-md-6">
    <div class="student-info-section">
      <div class="student-info-title">
        <i class="bi bi-person-circle"></i>
        Personal Information
      </div>
      <table class="student-details-table">
        <tr><td>Full Name:</td><td><?= esc($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'] . ' ' . ($student['suffix'] ?? '')) ?></td></tr>
        <tr><td>Gender:</td><td><?= esc($student['gender'] ?? 'N/A') ?></td></tr>
        <tr><td>Date of Birth:</td><td><?= $student['date_of_birth'] ? date('M j, Y', strtotime($student['date_of_birth'])) : 'N/A' ?></td></tr>
        <tr><td>Place of Birth:</td><td><?= esc($student['place_of_birth'] ?? 'N/A') ?></td></tr>
        <tr><td>Nationality:</td><td><?= esc($student['nationality'] ?? 'N/A') ?></td></tr>
        <tr><td>Religion:</td><td><?= esc($student['religion'] ?? 'N/A') ?></td></tr>
      </table>
    </div>
  </div>
  <div class="col-md-6">
    <div class="student-info-section">
      <div class="student-info-title">
        <i class="bi bi-telephone"></i>
        Contact Information
      </div>
      <table class="student-details-table">
        <tr><td>Email:</td><td><?= esc($student['email'] ?? 'N/A') ?></td></tr>
        <tr><td>Contact Number:</td><td><?= esc($student['contact_number'] ?? 'N/A') ?></td></tr>
        <tr><td>Address:</td><td><?= esc($student['address'] ?? 'N/A') ?></td></tr>
      </table>
    </div>

    <div class="student-info-section">
      <div class="student-info-title">
        <i class="bi bi-shield-exclamation"></i>
        Emergency Contact
      </div>
      <table class="student-details-table">
        <tr><td>Name:</td><td><?= esc($student['emergency_contact_name'] ?? 'N/A') ?></td></tr>
        <tr><td>Number:</td><td><?= esc($student['emergency_contact_number'] ?? 'N/A') ?></td></tr>
        <tr><td>Relationship:</td><td><?= esc($student['emergency_contact_relationship'] ?? 'N/A') ?></td></tr>
      </table>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="student-info-section">
      <div class="student-info-title">
        <i class="bi bi-mortarboard"></i>
        Enrollment Information
      </div>
      <table class="student-details-table">
        <tr><td>Grade Level:</td><td>Grade <?= esc($student['grade_level']) ?></td></tr>
        <tr><td>Section:</td><td><?= esc($student['section_name'] ?? 'Not assigned') ?></td></tr>
        <tr><td>School Year:</td><td><?= esc($student['school_year'] ?? 'N/A') ?></td></tr>
        <tr><td>Status:</td><td>
          <span class="badge bg-<?= $student['enrollment_status'] === 'pending' ? 'warning' : ($student['enrollment_status'] === 'approved' ? 'info' : ($student['enrollment_status'] === 'enrolled' ? 'success' : 'secondary')) ?>">
            <?= ucfirst(esc($student['enrollment_status'])) ?>
          </span>
        </td></tr>
        <tr><td>Applied Date:</td><td><?= $student['created_at'] ? date('M j, Y', strtotime($student['created_at'])) : 'N/A' ?></td></tr>
      </table>
    </div>
  </div>
</div>

<?php if (!empty($documents)): ?>
<div class="row">
  <div class="col-12">
    <div class="student-info-section">
      <div class="student-info-title">
        <i class="bi bi-file-earmark-text"></i>
        Required Documents
      </div>
      <div class="documents-grid">
        <?php 
        $documentTypes = [
          'birth_certificate' => ['label' => 'Birth Certificate', 'icon' => 'bi-file-earmark-person'],
          'report_card' => ['label' => 'Report Card (Form 138)', 'icon' => 'bi-file-earmark-bar-graph'],
          'good_moral' => ['label' => 'Good Moral Certificate', 'icon' => 'bi-file-earmark-check'],
          'medical_certificate' => ['label' => 'Medical Certificate', 'icon' => 'bi-file-earmark-medical'],
          'photo' => ['label' => '2x2 Photo', 'icon' => 'bi-person-square']
        ];
        ?>
        
        <?php foreach ($documentTypes as $type => $info): ?>
          <div class="document-item">
            <div class="document-header">
              <i class="bi <?= $info['icon'] ?>"></i>
              <span class="document-label"><?= $info['label'] ?></span>
            </div>
            <?php if (isset($documents[$type])): ?>
              <div class="document-preview">
                <?php
                $doc = $documents[$type];
                $fileName = basename($doc['file_path']);

                // Check both possible file locations
                $filePath1 = WRITEPATH . 'uploads/enrollment_documents/' . $fileName;
                $filePath2 = WRITEPATH . $doc['file_path'];

                $fileExists = file_exists($filePath1) || file_exists($filePath2);
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Use the filename for the URL
                $fileUrl = base_url('uploads/enrollment_documents/' . $fileName);
                ?>

                <?php if ($fileExists): ?>
                  <?php if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <div class="image-preview" onclick="showImageModal('<?= $fileUrl ?>', '<?= esc($info['label']) ?>')" style="cursor: pointer;">
                      <img src="<?= $fileUrl ?>"
                           alt="<?= $info['label'] ?>"
                           class="document-thumbnail"
                           onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';">
                      <div class="document-overlay">
                        <i class="bi bi-eye"></i>
                        <span>Click to view</span>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="file-preview">
                      <i class="bi bi-file-earmark-pdf"></i>
                      <span class="file-name"><?= esc($doc['document_name']) ?></span>
                      <a href="<?= $fileUrl ?>"
                         target="_blank"
                         class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Download
                      </a>
                    </div>
                  <?php endif; ?>
                  
                  <div class="document-info">
                    <small class="text-muted">
                      Uploaded: <?= date('M j, Y', strtotime($doc['created_at'])) ?>
                    </small>
                  </div>
                <?php else: ?>
                  <div class="document-missing">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    <span class="text-warning">File not found</span>
                  </div>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <div class="document-missing">
                <i class="bi bi-x-circle text-danger"></i>
                <span class="text-muted">Not submitted</span>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
<div class="row">
  <div class="col-12">
    <div class="student-info-section">
      <div class="student-info-title">
        <i class="bi bi-file-earmark-text"></i>
        Required Documents
      </div>
      <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        No documents have been submitted for this student.
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="document-viewer-modal" style="display: none;">
  <div class="document-viewer-overlay" onclick="closeDocumentModal()"></div>
  <div class="document-viewer-content">
    <div class="document-viewer-header">
      <h5 id="documentViewerTitle">Document Viewer</h5>
      <button type="button" class="document-viewer-close" onclick="closeDocumentModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="document-viewer-body">
      <img id="documentViewerImage" src="" alt="Document" class="document-viewer-image">
    </div>
  </div>
</div>

<style>
/* Documents Grid Styles */
.documents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.document-item {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem;
  background: #ffffff;
  transition: all 0.3s ease;
}

.document-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.document-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #374151;
}

.document-header i {
  font-size: 1.2rem;
  color: #3b82f6;
}

.document-preview {
  position: relative;
}

.image-preview {
  position: relative;
  cursor: pointer;
  border-radius: 8px;
  overflow: hidden;
  background: #f8fafc;
}

.document-thumbnail {
  width: 100%;
  height: 120px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.image-preview:hover .document-thumbnail {
  transform: scale(1.05);
}

.document-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-preview:hover .document-overlay {
  opacity: 1;
}

.document-overlay i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.file-preview {
  text-align: center;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
}

.file-preview i {
  font-size: 2rem;
  color: #dc2626;
  margin-bottom: 0.5rem;
}

.file-name {
  display: block;
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 0.75rem;
  word-break: break-word;
}

.document-missing {
  text-align: center;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 8px;
  color: #6b7280;
}

.document-missing i {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.document-info {
  margin-top: 0.5rem;
  text-align: center;
}

/* Document Viewer Modal */
.document-viewer-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.document-viewer-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.document-viewer-content {
  background: white;
  border-radius: 12px;
  max-width: 90vw;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
}

.document-viewer-header {
  background: #1e40af;
  color: white;
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.document-viewer-header h5 {
  margin: 0;
  color: white;
}

.document-viewer-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.document-viewer-body {
  padding: 1rem;
  text-align: center;
}

.document-viewer-image {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
}
</style>

<!-- JavaScript functions are now defined in the main page for global access -->
