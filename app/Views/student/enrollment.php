<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Next Semester Enrollment</h1>
    <span class="badge bg-success">Eligible for Enrollment</span>
</div>

<!-- Eligibility Status -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="text-success mb-2">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Congratulations! You are eligible to enroll for the next semester.
                </h5>
                <p class="mb-2">Your GWA of <strong>86.19</strong> meets the minimum requirement of <strong>75.00</strong>.</p>
                <div class="row">
                    <div class="col-sm-6">
                        <small class="text-muted">Enrollment requirements: GWA ≥ 75.00</small><br>
                        <small class="text-muted">Current GWA: <strong>86.19</strong></small>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Academic Standing: <strong class="text-warning">Cum Laude</strong></small><br>
                        <small class="text-muted">Next Semester: <strong>2026-2027</strong></small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Enrollment Form -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Enrollment Application Form</h5>
    </div>
    <div class="card-body">
        <form method="post" action="<?= base_url('student/enrollment') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row g-2 mb-3">
                <div class="col-12"><h6 class="text-primary mb-2">Personal Information</h6></div>
                <div class="col-md-3">
                    <label class="form-label small">First Name</label>
                    <input type="text" class="form-control form-control-sm" name="first_name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Last Name</label>
                    <input type="text" class="form-control form-control-sm" name="last_name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Email</label>
                    <input type="email" class="form-control form-control-sm" name="email" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Phone</label>
                    <input type="tel" class="form-control form-control-sm" name="phone" required>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-12"><h6 class="text-primary mb-2">Academic Information</h6></div>
                <div class="col-md-3">
                    <label class="form-label small">LRN</label>
                    <input type="text" class="form-control form-control-sm" name="lrn" placeholder="e.g. 123456789012" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Year Level</label>
                    <select class="form-select form-select-sm" name="year_level" required>
                        <option value="">Select</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Strand</label>
                    <select class="form-select form-select-sm" name="strand" required>
                        <option value="">Select</option>
                        <option value="STEM">STEM</option>
                        <option value="HUMSS">HUMSS</option>
                        <option value="ABM">ABM</option>
                        <option value="GAS">GAS</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Section</label>
                    <select class="form-select form-select-sm" name="section">
                        <option value="">Any</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-12"><h6 class="text-primary mb-2">Required Documents</h6></div>
                <div class="col-md-6">
                    <label class="form-label small">Report Card <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="report_card" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Birth Certificate <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                </div>
            </div>



            <!-- Submit Button -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-outline-secondary me-md-2">Clear Form</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Submit Enrollment Application
                </button>
            </div>
        </form>
    </div>
</div>



<script>
// File upload validation
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }
                
                // Show file name
                const label = this.previousElementSibling;
                const fileName = file.name;
                if (fileName.length > 30) {
                    label.innerHTML = label.innerHTML.split('<span')[0] + 
                        '<span class="text-success ms-2">(' + fileName.substring(0, 30) + '...)</span>';
                } else {
                    label.innerHTML = label.innerHTML.split('<span')[0] + 
                        '<span class="text-success ms-2">(' + fileName + ')</span>';
                }
            }
        });
    });
});
</script>

<?= $this->endSection() ?>