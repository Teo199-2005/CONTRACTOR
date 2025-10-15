<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.blue-divider {
  height: 3px;
  background: linear-gradient(90deg, #007bff, #0056b3);
  border-radius: 2px;
  margin: 1rem 0;
}

.profile-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.profile-header {
  background: linear-gradient(135deg, #007bff, #0056b3);
  color: white !important;
  padding: 2rem;
  text-align: center;
}

.profile-header h3,
.profile-header p {
  color: white !important;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 2.5rem;
  font-weight: bold;
}

.form-section {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-section h5 {
  color: #007bff;
  margin-bottom: 1rem;
  font-weight: 600;
}
</style>

<!-- Header Section -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">My Profile</h1>
      <p class="text-muted mb-0 small">Manage your personal information and account settings</p>
    </div>
  </div>
  
  <!-- Blue Divider -->
  <div class="blue-divider"></div>
</div>

<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>
<?php if ($error = session('error')): ?>
  <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row">
  <!-- Profile Information -->
  <div class="col-lg-8">
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-avatar">
          <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
        </div>
        <h3 class="mb-1" style="color: white;"><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></h3>
        <p class="mb-0" style="color: white; opacity: 0.9;"><?= esc($student['email']) ?></p>
      </div>
      
      <div class="card-body p-4">
        <form action="<?= base_url('student/profile/update') ?>" method="post">
          <?= csrf_field() ?>
          
          <div class="form-section">
            <h5><i class="bi bi-person me-2"></i>Personal Information</h5>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       value="<?= esc($student['first_name']) ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" 
                       value="<?= esc($student['middle_name'] ?? '') ?>">
              </div>
              <div class="col-md-4 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       value="<?= esc($student['last_name']) ?>" required>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h5><i class="bi bi-envelope me-2"></i>Contact Information</h5>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= esc($student['email']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="<?= esc($student['phone'] ?? '') ?>">
              </div>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="2"><?= esc($student['address'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>Update Profile
            </button>
            <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Change Password -->
  <div class="col-lg-4">
    <div class="profile-card">
      <div class="card-header bg-warning text-dark">
        <h5 class="card-title mb-0">
          <i class="bi bi-shield-lock me-2"></i>Change Password
        </h5>
      </div>
      <div class="card-body">
        <form action="<?= base_url('student/profile/change-password') ?>" method="post">
          <?= csrf_field() ?>
          
          <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <div style="position: relative;">
              <input type="password" class="form-control" id="current_password" name="current_password" required>
              <button type="button" id="toggleCurrentPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="currentEyeIcon"></i>
              </button>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <div style="position: relative;">
              <input type="password" class="form-control" id="new_password" name="new_password" 
                     minlength="6" required>
              <button type="button" id="toggleNewPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="newEyeIcon"></i>
              </button>
            </div>
            <div class="form-text">Minimum 6 characters</div>
          </div>
          
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <div style="position: relative;">
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              <button type="button" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="confirmEyeIcon"></i>
              </button>
            </div>
          </div>
          
          <button type="submit" class="btn btn-warning w-100">
            <i class="bi bi-key me-2"></i>Change Password
          </button>
        </form>
      </div>
    </div>

    <!-- Profile Stats -->
    <div class="profile-card mt-3">
      <div class="card-header bg-info text-white">
        <h5 class="card-title mb-0">
          <i class="bi bi-graph-up me-2"></i>Student Info
        </h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">LRN</span>
          <span class="fw-bold"><?= esc($student['lrn']) ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">Grade Level</span>
          <span class="fw-bold">Grade <?= esc($student['grade_level']) ?></span>
        </div>
        <?php if (!empty($student['section_name'])): ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">Section</span>
          <span class="fw-bold"><?= esc($student['section_name']) ?></span>
        </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted">Status</span>
          <span class="badge bg-primary"><?= ucfirst(esc($student['enrollment_status'])) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Password toggle functionality
document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
  const passwordInput = document.getElementById('current_password');
  const eyeIcon = document.getElementById('currentEyeIcon');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.className = 'bi bi-eye-slash';
  } else {
    passwordInput.type = 'password';
    eyeIcon.className = 'bi bi-eye';
  }
});

document.getElementById('toggleNewPassword').addEventListener('click', function() {
  const passwordInput = document.getElementById('new_password');
  const eyeIcon = document.getElementById('newEyeIcon');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.className = 'bi bi-eye-slash';
  } else {
    passwordInput.type = 'password';
    eyeIcon.className = 'bi bi-eye';
  }
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
  const passwordInput = document.getElementById('confirm_password');
  const eyeIcon = document.getElementById('confirmEyeIcon');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.className = 'bi bi-eye-slash';
  } else {
    passwordInput.type = 'password';
    eyeIcon.className = 'bi bi-eye';
  }
});
</script>

<?= $this->endSection() ?>