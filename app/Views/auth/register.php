<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.register-container {
  min-height: calc(100vh - 200px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  position: relative;
  margin: 0 -15px;
}

.register-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 1;
}

.register-card {
  background: rgba(30, 64, 175, 0.95);
  backdrop-filter: blur(25px);
  border: 1px solid rgba(59, 130, 246, 0.3);
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.2);
  width: 100%;
  max-width: 800px;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.register-header {
  text-align: center;
  padding: 3rem 2.5rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.1);
}

.register-title {
  font-size: 2rem;
  font-weight: 800;
  font-family: 'Times New Roman', serif;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
  letter-spacing: -0.025em;
}

.register-subtitle {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
}

.register-form {
  padding: 1.5rem 2rem 2rem;
}

.form-control, .form-select {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.5rem 0.75rem;
  font-size: 0.85rem;
  background: #f8fafc;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-weight: 500;
  color: #000000;
}

.form-control:focus, .form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
  background: white;
}

.form-label {
  color: white;
  font-weight: 600;
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
}

.section-title {
  font-size: 1rem;
  font-weight: 700;
  color: white;
  margin-bottom: 0.75rem;
  margin-top: 0;
  padding-bottom: 0.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.section-title::before {
  display: none;
}

.section-title:first-of-type {
  margin-top: 0;
}

.register-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  border: none;
  border-radius: 10px;
  color: white;
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: 0.025em;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.register-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px rgba(251, 191, 36, 0.4);
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
}

.btn-outline-secondary {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem 2rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.alert {
  border: none;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.alert-danger {
  background: #fef2f2;
  color: #dc2626;
  border-left: 4px solid #dc2626;
}

.form-text, .text-muted {
  color: rgba(255, 255, 255, 0.6) !important;
  font-size: 0.75rem;
}

.form-step {
  display: none;
}

.form-step.active {
  display: block;
}

.step-navigation {
  display: flex;
  justify-content: space-between;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.step-navigation .right-buttons {
  display: flex;
  gap: 10px;
}

.btn-step {
  padding: 0.6rem 1.2rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  background: transparent;
  color: white;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.btn-step:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.step-indicator {
  text-align: center;
  margin-bottom: 1.5rem;
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.85rem;
}

.custom-alert {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  max-width: 400px;
  width: 90%;
}

.alert-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

.alert-title {
  color: #dc2626;
  font-weight: 700;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.alert-message {
  color: #374151;
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.alert-close {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  float: right;
}

.demo-float-button {
  position: fixed;
  top: 100px;
  right: 20px;
  z-index: 1000;
  animation: float 3s ease-in-out infinite;
}

.demo-float-button .btn {
  background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
  border: none;
  color: white;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
  font-size: 0.9rem;
}

.demo-float-button .btn:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
  background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
}

.demo-float-button .btn:active {
  transform: translateY(0) scale(1);
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-5px);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .demo-float-button {
    top: 60px;
    right: 10px;
  }

  .demo-float-button .btn {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
  }
}
</style>

<div class="register-container">
  <!-- Floating Demo Button -->
  <div class="demo-float-button">
    <button type="button" class="btn btn-info btn-sm" onclick="fillDemoData()" title="Auto-fill form with demo data">
      <i class="bi bi-magic"></i> Demo Fill
    </button>
  </div>

  <div class="register-card">
    <div class="register-header">
      <h1 class="register-title">Student Registration</h1>
      <p class="register-subtitle">Register for enrollment at Lourdes Provincial High School</p>
    </div>

    <div class="register-form">
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('register') ?>" enctype="multipart/form-data" id="registrationForm">
          <?= csrf_field() ?>

          <div class="step-indicator">
            <span id="stepText">Step 1 of 5: Personal Information</span>
          </div>

          <!-- Step 1: Personal Information -->
          <div class="form-step active" id="step1">
            <h5 class="section-title">Personal Information</h5>
          <div class="row g-2">
            <div class="col-md-3">
              <label class="form-label">First Name *</label>
              <input type="text" class="form-control" name="first_name" value="<?= old('first_name') ?>" required />
            </div>
            <div class="col-md-3">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="middle_name" value="<?= old('middle_name') ?>" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Last Name *</label>
              <input type="text" class="form-control" name="last_name" value="<?= old('last_name') ?>" required />
            </div>
            <div class="col-md-3">
              <label class="form-label">Suffix</label>
              <input type="text" class="form-control" name="suffix" value="<?= old('suffix') ?>" placeholder="Jr., Sr., III" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Gender *</label>
              <select class="form-select" name="gender" required>
                <option value="">Select</option>
                <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Date of Birth *</label>
              <input type="date" class="form-control" name="date_of_birth" value="<?= old('date_of_birth') ?>" required />
            </div>
            <div class="col-md-3">
              <label class="form-label">Grade Level *</label>
              <select class="form-select" name="grade_level" required>
                <option value="">Select</option>
                <option value="7" <?= old('grade_level') === '7' ? 'selected' : '' ?>>Grade 7</option>
                <option value="8" <?= old('grade_level') === '8' ? 'selected' : '' ?>>Grade 8</option>
                <option value="9" <?= old('grade_level') === '9' ? 'selected' : '' ?>>Grade 9</option>
                <option value="10" <?= old('grade_level') === '10' ? 'selected' : '' ?>>Grade 10</option>
                <option value="11" <?= old('grade_level') === '11' ? 'selected' : '' ?>>Grade 11</option>
                <option value="12" <?= old('grade_level') === '12' ? 'selected' : '' ?>>Grade 12</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">LRN</label>
              <input type="text" class="form-control" name="lrn" value="<?= old('lrn') ?>" placeholder="e.g. 123456789012" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Student Type *</label>
              <select class="form-select" name="student_type" required>
                <option value="">Select</option>
                <option value="New Student" <?= old('student_type') === 'New Student' ? 'selected' : '' ?>>New Student</option>
                <option value="Transferee" <?= old('student_type') === 'Transferee' ? 'selected' : '' ?>>Transferee</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Place of Birth</label>
              <input type="text" class="form-control" name="place_of_birth" value="<?= old('place_of_birth') ?>" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Nationality</label>
              <input type="text" class="form-control" name="nationality" value="<?= old('nationality', 'Filipino') ?>" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Religion</label>
              <input type="text" class="form-control" name="religion" value="<?= old('religion') ?>" />
            </div>
          </div>

          </div>

          <!-- Step 2: Contact Information -->
          <div class="form-step" id="step2">
            <h5 class="section-title">Contact Information</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Email Address *</label>
              <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="contact_number" value="<?= old('contact_number') ?>" />
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2"><?= old('address') ?></textarea>
            </div>
          </div>

          </div>

          <!-- Step 3: Emergency Contact -->
          <div class="form-step" id="step3">
            <h5 class="section-title">Emergency Contact</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Emergency Contact Name</label>
              <input type="text" class="form-control" name="emergency_contact_name" value="<?= old('emergency_contact_name') ?>" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Emergency Contact Number</label>
              <input type="text" class="form-control" name="emergency_contact_number" value="<?= old('emergency_contact_number') ?>" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Relationship</label>
              <input type="text" class="form-control" name="emergency_contact_relationship" value="<?= old('emergency_contact_relationship') ?>" placeholder="Parent, Guardian, etc." />
            </div>
          </div>

          </div>

          <!-- Step 4: Documents -->
          <div class="form-step" id="step4">
            <h5 class="section-title">Required Documents (PDF/JPG/PNG)</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Birth Certificate</label>
              <input type="file" class="form-control" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Report Card (Form 138)</label>
              <input type="file" class="form-control" name="report_card" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Good Moral Certificate</label>
              <input type="file" class="form-control" name="good_moral" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Medical Certificate</label>
              <input type="file" class="form-control" name="medical_certificate" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
            <div class="col-md-6">
              <label class="form-label">2x2 Photo</label>
              <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png" />
            </div>
          </div>

          </div>

          <!-- Step 5: Account Information -->
          <div class="form-step" id="step5">
            <h5 class="section-title">Account Information</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Password *</label>
              <div class="position-relative">
                <input type="password" class="form-control" name="password" id="password" required />
                <button type="button" class="btn btn-sm position-absolute" style="right: 8px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #6c757d;" onclick="togglePassword('password')">
                  <i class="bi bi-eye" id="password-icon"></i>
                </button>
              </div>
              <div class="form-text">Minimum 8 characters</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password *</label>
              <div class="position-relative">
                <input type="password" class="form-control" name="password_confirm" id="password_confirm" required />
                <button type="button" class="btn btn-sm position-absolute" style="right: 8px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #6c757d;" onclick="togglePassword('password_confirm')">
                  <i class="bi bi-eye" id="password_confirm-icon"></i>
                </button>
              </div>
            </div>
          </div>

          </div>

          <div class="step-navigation">
            <button type="button" class="btn-step" id="prevBtn" onclick="changeStep(-1)" style="display: none;">Previous</button>
            <button type="button" class="btn-step" id="nextBtn" onclick="validateAndNext()">Next</button>
            <button class="register-btn" type="submit" id="submitBtn" style="display: none;">SUBMIT REGISTRATION</button>
          </div>



          <div class="mt-3">
            <small class="text-muted">
              * Required fields. Your registration will be reviewed by school administrators before approval.
            </small>
          </div>
        </form>

        <script>
        let currentStep = 1;
        const totalSteps = 5;
        const stepTitles = [
          'Personal Information',
          'Contact Information', 
          'Emergency Contact',
          'Required Documents',
          'Account Information'
        ];

        function showStep(step) {
          document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
          document.getElementById('step' + step).classList.add('active');
          document.getElementById('stepText').textContent = `Step ${step} of ${totalSteps}: ${stepTitles[step-1]}`;
          
          document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
          document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
          document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
        }

        function changeStep(direction) {
          const newStep = currentStep + direction;
          if (newStep >= 1 && newStep <= totalSteps) {
            currentStep = newStep;
            showStep(currentStep);
          }
        }

        function showCustomAlert(message) {
          const overlay = document.createElement('div');
          overlay.className = 'alert-overlay';
          
          const alertBox = document.createElement('div');
          alertBox.className = 'custom-alert';
          alertBox.innerHTML = `
            <div class="alert-title"><i class="bi bi-exclamation-triangle"></i> Required Fields Missing</div>
            <div class="alert-message">${message}</div>
            <button class="alert-close" onclick="closeCustomAlert()">OK</button>
          `;
          
          document.body.appendChild(overlay);
          document.body.appendChild(alertBox);
        }
        
        function closeCustomAlert() {
          document.querySelector('.alert-overlay')?.remove();
          document.querySelector('.custom-alert')?.remove();
        }

        function validateAndNext() {
          const currentStepElement = document.getElementById('step' + currentStep);
          const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
          const emptyFields = [];

          requiredFields.forEach(field => {
            if (!field.value.trim()) {
              emptyFields.push(field.previousElementSibling.textContent.replace(' *', ''));
            }
          });

          if (emptyFields.length > 0) {
            showCustomAlert('Please fill in the following required fields:<br><br><strong>' + emptyFields.join('<br>') + '</strong>');
            return;
          }

          changeStep(1);
        }

        function togglePassword(fieldId) {
          const field = document.getElementById(fieldId);
          const icon = document.getElementById(fieldId + '-icon');
          
          if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'bi bi-eye-slash';
          } else {
            field.type = 'password';
            icon.className = 'bi bi-eye';
          }
        }

        function fillDemoData() {
          // Demo data arrays
          const firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Carlos', 'Sofia', 'Miguel', 'Isabella', 'Luis', 'Carmen', 'Pedro', 'Lucia', 'Antonio', 'Elena', 'Francisco'];
          const middleNames = ['Santos', 'Cruz', 'Reyes', 'Garcia', 'Lopez', 'Martinez', 'Gonzalez', 'Rodriguez', 'Fernandez', 'Morales', 'Jimenez', 'Herrera', 'Medina', 'Castro', 'Ortiz'];
          const lastNames = ['Dela Cruz', 'Santos', 'Garcia', 'Reyes', 'Lopez', 'Martinez', 'Gonzalez', 'Rodriguez', 'Fernandez', 'Morales', 'Jimenez', 'Herrera', 'Medina', 'Castro', 'Ortiz'];
          const suffixes = ['', '', '', 'Jr.', 'Sr.', 'III', ''];
          const genders = ['Male', 'Female'];
          const gradeLevels = ['7', '8', '9', '10', '11', '12'];
          const studentTypes = ['New Student', 'Transferee'];
          const places = ['Tagbilaran City, Bohol', 'Panglao, Bohol', 'Dauis, Bohol', 'Baclayon, Bohol', 'Loboc, Bohol', 'Carmen, Bohol', 'Tubigon, Bohol'];
          const religions = ['Catholic', 'Protestant', 'Iglesia ni Cristo', 'Baptist', 'Methodist', 'Born Again', 'Seventh-day Adventist'];
          const relationships = ['Mother', 'Father', 'Guardian', 'Aunt', 'Uncle', 'Grandmother', 'Grandfather'];

          // Helper function to get random item from array
          const getRandom = (arr) => arr[Math.floor(Math.random() * arr.length)];

          // Helper function to generate random date between ages 11-17
          const getRandomBirthDate = () => {
            const today = new Date();
            const age = Math.floor(Math.random() * 7) + 11; // 11-17 years old
            const birthYear = today.getFullYear() - age;
            const birthMonth = Math.floor(Math.random() * 12) + 1;
            const birthDay = Math.floor(Math.random() * 28) + 1;
            return `${birthYear}-${birthMonth.toString().padStart(2, '0')}-${birthDay.toString().padStart(2, '0')}`;
          };

          // Helper function to generate random phone number
          const getRandomPhone = () => {
            const prefixes = ['0917', '0918', '0919', '0920', '0921', '0922', '0923', '0924', '0925', '0926', '0927', '0928', '0929'];
            const prefix = getRandom(prefixes);
            const suffix = Math.floor(Math.random() * 10000000).toString().padStart(7, '0');
            return prefix + suffix;
          };

          // Generate random data
          const firstName = getRandom(firstNames);
          const middleName = getRandom(middleNames);
          const lastName = getRandom(lastNames);
          const suffix = getRandom(suffixes);
          const gender = getRandom(genders);
          const gradeLevel = getRandom(gradeLevels);
          const studentType = getRandom(studentTypes);
          const birthDate = getRandomBirthDate();
          const placeOfBirth = getRandom(places);
          const religion = getRandom(religions);
          const contactNumber = getRandomPhone();
          const emergencyContactNumber = getRandomPhone();
          const relationship = getRandom(relationships);
          const lrn = Math.floor(Math.random() * 900000000000) + 100000000000; // Generate 12-digit LRN

          // Generate email based on name
          const emailUsername = (firstName + lastName).toLowerCase().replace(/\s+/g, '');
          const emailDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
          const email = emailUsername + Math.floor(Math.random() * 999) + '@' + getRandom(emailDomains);

          // Generate address
          const barangays = ['Poblacion', 'Tawala', 'Bolod', 'Danao', 'Tangnan', 'Libaong', 'Lourdes'];
          const address = `Purok ${Math.floor(Math.random() * 10) + 1}, Barangay ${getRandom(barangays)}, Panglao, Bohol`;

          // Fill Step 1: Personal Information
          document.querySelector('input[name="first_name"]').value = firstName;
          document.querySelector('input[name="middle_name"]').value = middleName;
          document.querySelector('input[name="last_name"]').value = lastName;
          document.querySelector('input[name="suffix"]').value = suffix;
          document.querySelector('select[name="gender"]').value = gender;
          document.querySelector('input[name="date_of_birth"]').value = birthDate;
          document.querySelector('select[name="grade_level"]').value = gradeLevel;
          document.querySelector('input[name="lrn"]').value = lrn;
          document.querySelector('select[name="student_type"]').value = studentType;
          document.querySelector('input[name="place_of_birth"]').value = placeOfBirth;
          document.querySelector('input[name="nationality"]').value = 'Filipino';
          document.querySelector('input[name="religion"]').value = religion;

          // Fill Step 2: Contact Information
          document.querySelector('input[name="email"]').value = email;
          document.querySelector('input[name="contact_number"]').value = contactNumber;
          document.querySelector('textarea[name="address"]').value = address;

          // Fill Step 3: Emergency Contact
          const emergencyContactName = getRandom(firstNames) + ' ' + getRandom(lastNames);
          document.querySelector('input[name="emergency_contact_name"]').value = emergencyContactName;
          document.querySelector('input[name="emergency_contact_number"]').value = emergencyContactNumber;
          document.querySelector('input[name="emergency_contact_relationship"]').value = relationship;

          // Fill Step 5: Account Information
          const password = 'Demo123!';
          document.querySelector('input[name="password"]').value = password;
          document.querySelector('input[name="password_confirm"]').value = password;

          // Show success message
          showCustomAlert('✅ Demo data filled successfully!<br><br><strong>Password:</strong> Demo123!<br><br>Navigate through the steps to review all the auto-filled information.');
        }
        </script>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

