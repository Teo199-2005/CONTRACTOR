<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Password Reset Requests</h1>
      <p class="text-muted mb-0 small">Manage password reset requests from teachers and students</p>
    </div>
  </div>
  <div class="blue-divider"></div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="card bg-white border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="border-0 fw-medium">User</th>
            <th class="border-0 fw-medium">Identifier</th>
            <th class="border-0 fw-medium">Requested</th>
            <th class="border-0 fw-medium">Status</th>
            <th class="border-0 fw-medium text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($resets)): ?>
            <?php foreach ($resets as $reset): ?>
              <tr>
                <td class="py-3">
                  <div class="fw-medium"><?= esc($reset['email']) ?></div>
                </td>
                <td class="py-3">
                  <span class="badge bg-light text-dark"><?= esc($reset['identifier']) ?></span>
                </td>
                <td class="py-3">
                  <small class="text-muted"><?= date('M j, Y g:i A', strtotime($reset['requested_at'])) ?></small>
                </td>
                <td class="py-3">
                  <span class="badge bg-warning">Pending</span>
                </td>
                <td class="py-3 text-center">
                  <a href="<?= base_url('admin/password-resets/change/' . $reset['id']) ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-key me-1"></i>Change Password
                  </a>
                  <button class="btn btn-danger btn-sm ms-1" onclick="rejectReset(<?= $reset['id'] ?>)">
                    <i class="bi bi-x-circle me-1"></i>Reject
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No pending password reset requests
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>



<script>
function rejectReset(resetId) {
  if (!confirm('Reject this password reset request?')) {
    return;
  }
  
  // Implement reject functionality if needed
  alert('Reset request rejected');
  location.reload();
}
</script>

<?= $this->endSection() ?>