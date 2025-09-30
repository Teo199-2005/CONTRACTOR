<style>
.teacher-info-section {
  margin-bottom: 2rem;
}

.teacher-info-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e40af;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.teacher-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.teacher-info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.teacher-info-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.teacher-info-value {
  font-size: 1rem;
  color: #111827;
  font-weight: 500;
}

.teacher-info-value.empty {
  color: #9ca3af;
  font-style: italic;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status-active {
  background-color: #d1fae5;
  color: #065f46;
}

.status-inactive {
  background-color: #fee2e2;
  color: #991b1b;
}

.status-on_leave {
  background-color: #fef3c7;
  color: #92400e;
}

.sections-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.section-badge {
  background-color: #dbeafe;
  color: #1e40af;
  padding: 0.25rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
}
</style>

<div class="teacher-details-content">
  <!-- Personal Information -->
  <div class="teacher-info-section">
    <div class="teacher-info-title">
      <i class="bi bi-person"></i>
      Personal Information
    </div>
    <div class="teacher-info-grid">
      <div class="teacher-info-item">
        <div class="teacher-info-label">Full Name</div>
        <div class="teacher-info-value">
          <?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Teacher ID</div>
        <div class="teacher-info-value">
          <?= esc($teacher['teacher_id']) ?: 'N/A' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Email Address</div>
        <div class="teacher-info-value">
          <?= esc($teacher['email']) ?: 'N/A' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Gender</div>
        <div class="teacher-info-value">
          <?= esc($teacher['gender']) ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Date of Birth</div>
        <div class="teacher-info-value">
          <?= $teacher['date_of_birth'] ? date('F j, Y', strtotime($teacher['date_of_birth'])) : '—' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Professional Information -->
  <div class="teacher-info-section">
    <div class="teacher-info-title">
      <i class="bi bi-briefcase"></i>
      Professional Information
    </div>
    <div class="teacher-info-grid">
      <div class="teacher-info-item">
        <div class="teacher-info-label">Department</div>
        <div class="teacher-info-value <?= empty($teacher['department']) ? 'empty' : '' ?>">
          <?= esc($teacher['department']) ?: 'Not specified' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Position</div>
        <div class="teacher-info-value <?= empty($teacher['position']) ? 'empty' : '' ?>">
          <?= esc($teacher['position']) ?: 'Not specified' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Specialization</div>
        <div class="teacher-info-value <?= empty($teacher['specialization']) ? 'empty' : '' ?>">
          <?= esc($teacher['specialization']) ?: 'Not specified' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Hire Date</div>
        <div class="teacher-info-value">
          <?= $teacher['hire_date'] ? date('F j, Y', strtotime($teacher['hire_date'])) : '—' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Employment Status</div>
        <div class="teacher-info-value">
          <span class="status-badge status-<?= $teacher['employment_status'] ?>">
            <?= ucfirst(str_replace('_', ' ', $teacher['employment_status'])) ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Information -->
  <div class="teacher-info-section">
    <div class="teacher-info-title">
      <i class="bi bi-telephone"></i>
      Contact Information
    </div>
    <div class="teacher-info-grid">
      <div class="teacher-info-item">
        <div class="teacher-info-label">Contact Number</div>
        <div class="teacher-info-value <?= empty($teacher['contact_number']) ? 'empty' : '' ?>">
          <?= esc($teacher['contact_number']) ?: 'Not provided' ?>
        </div>
      </div>
      <div class="teacher-info-item" style="grid-column: 1 / -1;">
        <div class="teacher-info-label">Address</div>
        <div class="teacher-info-value <?= empty($teacher['address']) ? 'empty' : '' ?>">
          <?= esc($teacher['address']) ?: 'Not provided' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Assigned Sections -->
  <div class="teacher-info-section">
    <div class="teacher-info-title">
      <i class="bi bi-grid-3x3-gap"></i>
      Assigned Sections
    </div>
    <?php if (!empty($sections)): ?>
      <div class="sections-list">
        <?php foreach ($sections as $section): ?>
          <span class="section-badge">
            <?= esc($section['section_name']) ?>
          </span>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="teacher-info-value empty">
        No sections assigned
      </div>
    <?php endif; ?>
  </div>

  <!-- Account Information -->
  <div class="teacher-info-section">
    <div class="teacher-info-title">
      <i class="bi bi-clock-history"></i>
      Account Information
    </div>
    <div class="teacher-info-grid">
      <div class="teacher-info-item">
        <div class="teacher-info-label">Account Created</div>
        <div class="teacher-info-value">
          <?= $teacher['created_at'] ? date('F j, Y g:i A', strtotime($teacher['created_at'])) : '—' ?>
        </div>
      </div>
      <div class="teacher-info-item">
        <div class="teacher-info-label">Last Updated</div>
        <div class="teacher-info-value">
          <?= $teacher['updated_at'] ? date('F j, Y g:i A', strtotime($teacher['updated_at'])) : '—' ?>
        </div>
      </div>
    </div>
  </div>
</div>
