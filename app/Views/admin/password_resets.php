<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status-pending {
  background-color: #fef3c7;
  color: #92400e;
}

.status-approved {
  background-color: #d1fae5;
  color: #065f46;
}

.status-rejected {
  background-color: #fee2e2;
  color: #991b1b;
}

.status-used {
  background-color: #e0e7ff;
  color: #3730a3;
}

.status-expired {
  background-color: #f3f4f6;
  color: #374151;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

.table-responsive {
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table {
  margin-bottom: 0;
}

.table th {
  background-color: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
  font-weight: 600;
  color: #374151;
  padding: 1rem;
}

.table td {
  padding: 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #e2e8f0;
}

.table tbody tr:hover {
  background-color: #f8fafc;
}

.modal-header {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  border-bottom: none;
}

.modal-title {
  font-weight: 600;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #e2e8f0;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  font-weight: 600;
  color: #374151;
}

.info-value {
  color: #6b7280;
}

.notes-section {
  margin-top: 1rem;
}

.notes-section label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  display: block;
}

.notes-section textarea {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 0.75rem;
  font-size: 0.875rem;
  resize: vertical;
  min-height: 80px;
}

.alert {
  border-radius: 0.5rem;
  border: none;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
}

.alert-info {
  background-color: #dbeafe;
  color: #1e40af;
}

.alert-warning {
  background-color: #fef3c7;
  color: #92400e;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Password Reset Requests</h1>
</div>

<?php if (isset($table_missing) && $table_missing): ?>
  <div class="alert alert-warning">
    <h5><i class="bi bi-exclamation-triangle"></i> Database Table Missing</h5>
    <p>The password reset requests table hasn't been created yet. To enable password reset functionality, please run the following SQL command in your database:</p>
    <div class="bg-light p-3 rounded mt-3">
      <small class="text-muted">You can find the complete SQL script in: <code>create_password_reset_table.sql</code></small>
    </div>
    <p class="mt-3 mb-0">After creating the table, refresh this page to manage password reset requests.</p>
  </div>
  <?= $this->endSection() ?>
  <?php return; ?>
<?php endif; ?>

<?php 
$pendingCount = 0;
foreach ($requests as $request) {
  if ($request['status'] === 'pending') $pendingCount++;
}
?>

<?php if ($pendingCount > 0): ?>
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    You have <?= $pendingCount ?> pending password reset request<?= $pendingCount > 1 ? 's' : '' ?> that require your attention.
  </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Email</th>
            <th>Student ID</th>
            <th>Requested</th>
            <th>Status</th>
            <th>Expires</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($requests)): ?>
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                <i class="bi bi-inbox"></i><br>
                No password reset requests found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($requests as $request): ?>
              <tr>
                <td>
                  <div class="fw-semibold"><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></div>
                </td>
                <td><?= esc($request['user_email']) ?></td>
                <td>
                  <span class="badge bg-secondary"><?= esc($request['student_id'] ?? 'N/A') ?></span>
                </td>
                <td>
                  <small class="text-muted">
                    <?= date('M j, Y g:i A', strtotime($request['created_at'])) ?>
                  </small>
                </td>
                <td>
                  <span class="status-badge status-<?= $request['status'] ?>">
                    <?= ucfirst($request['status']) ?>
                  </span>
                </td>
                <td>
                  <small class="text-muted">
                    <?= date('M j, Y g:i A', strtotime($request['expires_at'])) ?>
                  </small>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-outline-primary btn-sm" onclick="viewRequest(<?= $request['id'] ?>)">
                      <i class="bi bi-eye"></i> View
                    </button>
                    <?php if ($request['status'] === 'pending'): ?>
                      <button class="btn btn-success btn-sm" onclick="approveRequest(<?= $request['id'] ?>)">
                        <i class="bi bi-check"></i> Approve
                      </button>
                      <button class="btn btn-danger btn-sm" onclick="rejectRequest(<?= $request['id'] ?>)">
                        <i class="bi bi-x"></i> Reject
                      </button>
                    <?php elseif ($request['status'] === 'approved'): ?>
                      <button class="btn btn-info btn-sm" onclick="getResetLink(<?= $request['id'] ?>)">
                        <i class="bi bi-link"></i> Get Link
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Password Reset Request Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="requestDetails">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Approve Password Reset</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to approve this password reset request?</p>
        <div class="notes-section">
          <label for="approveNotes">Admin Notes (Optional)</label>
          <textarea id="approveNotes" placeholder="Add any notes about this approval..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="confirmApprove()">Approve Request</button>
      </div>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject Password Reset</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to reject this password reset request?</p>
        <div class="notes-section">
          <label for="rejectNotes">Reason for Rejection</label>
          <textarea id="rejectNotes" placeholder="Please provide a reason for rejecting this request..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject Request</button>
      </div>
    </div>
  </div>
</div>

<script>
let currentRequestId = null;

function viewRequest(requestId) {
  fetch(`<?= base_url('admin/password-resets/details') ?>/${requestId}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        alert('Error: ' + data.error);
        return;
      }
      
      const modal = document.getElementById('requestModal');
      const details = document.getElementById('requestDetails');
      
      details.innerHTML = `
        <div class="info-row">
          <span class="info-label">Student Name:</span>
          <span class="info-value">${data.first_name} ${data.last_name}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Student ID:</span>
          <span class="info-value">${data.student_id || 'N/A'}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Email:</span>
          <span class="info-value">${data.user_email}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Contact Number:</span>
          <span class="info-value">${data.contact_number || 'N/A'}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Request Date:</span>
          <span class="info-value">${new Date(data.created_at).toLocaleString()}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Expires:</span>
          <span class="info-value">${new Date(data.expires_at).toLocaleString()}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Status:</span>
          <span class="info-value">
            <span class="status-badge status-${data.status}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>
          </span>
        </div>
        ${data.approved_by_email ? `
        <div class="info-row">
          <span class="info-label">Processed By:</span>
          <span class="info-value">${data.approved_by_email}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Processed Date:</span>
          <span class="info-value">${new Date(data.approved_at).toLocaleString()}</span>
        </div>
        ` : ''}
        ${data.admin_notes ? `
        <div class="info-row">
          <span class="info-label">Admin Notes:</span>
          <span class="info-value">${data.admin_notes}</span>
        </div>
        ` : ''}
      `;
      
      new bootstrap.Modal(modal).show();
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to load request details');
    });
}

function approveRequest(requestId) {
  currentRequestId = requestId;
  document.getElementById('approveNotes').value = '';
  new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectRequest(requestId) {
  currentRequestId = requestId;
  document.getElementById('rejectNotes').value = '';
  new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function confirmApprove() {
  const notes = document.getElementById('approveNotes').value;
  
  fetch(`<?= base_url('admin/password-resets/approve') ?>/${currentRequestId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ notes: notes })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to approve request');
  });
}

function confirmReject() {
  const notes = document.getElementById('rejectNotes').value;
  
  if (!notes.trim()) {
    alert('Please provide a reason for rejection');
    return;
  }
  
  fetch(`<?= base_url('admin/password-resets/reject') ?>/${currentRequestId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ notes: notes })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to reject request');
  });
}

function getResetLink(requestId) {
  fetch(`<?= base_url('admin/password-resets/reset-link') ?>/${requestId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const message = `Reset Link: ${data.reset_link}\n\nExpires: ${new Date(data.expires_at).toLocaleString()}\n\nPlease share this link with the student.`;
        alert(message);
        
        // Copy to clipboard
        navigator.clipboard.writeText(data.reset_link).then(() => {
          alert('Reset link copied to clipboard!');
        });
      } else {
        alert('Error: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to generate reset link');
    });
}
</script>

<?= $this->endSection() ?>
