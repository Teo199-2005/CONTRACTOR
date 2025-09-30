<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Edit Student</h1>
    <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-secondary">Back to Students</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/students/update/' . $student['id']) ?>">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" name="student_id" id="student_id" 
                               value="<?= esc($student['student_id']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" 
                               value="<?= esc($student['email']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" 
                               value="<?= esc($student['first_name']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" id="middle_name" 
                               value="<?= esc($student['middle_name'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" 
                               value="<?= esc($student['last_name']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="grade_level" class="form-label">Grade Level</label>
                        <select class="form-select" name="grade_level" id="grade_level" required>
                            <option value="7" <?= $student['grade_level'] == 7 ? 'selected' : '' ?>>Grade 7</option>
                            <option value="8" <?= $student['grade_level'] == 8 ? 'selected' : '' ?>>Grade 8</option>
                            <option value="9" <?= $student['grade_level'] == 9 ? 'selected' : '' ?>>Grade 9</option>
                            <option value="10" <?= $student['grade_level'] == 10 ? 'selected' : '' ?>>Grade 10</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="section_id" class="form-label">Section</label>
                        <select class="form-select" name="section_id" id="section_id">
                            <option value="">No Section</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?= $section['id'] ?>" 
                                        <?= $student['section_id'] == $section['id'] ? 'selected' : '' ?>>
                                    <?= esc($section['section_name']) ?> (Grade <?= $section['grade_level'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" name="gender" id="gender" required>
                            <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" 
                               value="<?= esc($student['date_of_birth']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" 
                               value="<?= esc($student['contact_number'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" name="address" id="address" rows="2"><?= esc($student['address'] ?? '') ?></textarea>
            </div>

            <h5 class="mt-4 mb-3">Emergency Contact</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="emergency_contact_name" id="emergency_contact_name" 
                               value="<?= esc($student['emergency_contact_name'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="emergency_contact_number" class="form-label">Number</label>
                        <input type="text" class="form-control" name="emergency_contact_number" id="emergency_contact_number" 
                               value="<?= esc($student['emergency_contact_number'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                        <input type="text" class="form-control" name="emergency_contact_relationship" id="emergency_contact_relationship" 
                               value="<?= esc($student['emergency_contact_relationship'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="enrollment_status" class="form-label">Enrollment Status</label>
                        <select class="form-select" name="enrollment_status" id="enrollment_status" required>
                            <option value="enrolled" <?= $student['enrollment_status'] == 'enrolled' ? 'selected' : '' ?>>Enrolled</option>
                            <option value="suspended" <?= $student['enrollment_status'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="graduated" <?= $student['enrollment_status'] == 'graduated' ? 'selected' : '' ?>>Graduated</option>
                            <option value="transferred" <?= $student['enrollment_status'] == 'transferred' ? 'selected' : '' ?>>Transferred</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>