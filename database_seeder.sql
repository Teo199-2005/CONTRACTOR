-- LPHS SMS Database Seeder
-- Run this after database_setup.sql to populate with sample data

USE lphs_sms;

-- Insert Sections
INSERT INTO `sections` (`section_name`, `grade_level`, `school_year`, `max_capacity`, `current_enrollment`, `is_active`, `created_at`, `updated_at`) VALUES
('Bower', 8, '2024-2025', 40, 25, 1, NOW(), NOW()),
('Rose', 8, '2024-2025', 40, 28, 1, NOW(), NOW()),
('Lily', 9, '2024-2025', 40, 30, 1, NOW(), NOW()),
('Jasmine', 9, '2024-2025', 40, 27, 1, NOW(), NOW()),
('Orchid', 10, '2024-2025', 40, 32, 1, NOW(), NOW());

-- Insert Subjects
INSERT INTO `subjects` (`subject_code`, `subject_name`, `grade_level`, `units`, `is_core`, `is_active`, `created_at`, `updated_at`) VALUES
('MATH8', 'Mathematics 8', 8, 1.0, 1, 1, NOW(), NOW()),
('ENG8', 'English 8', 8, 1.0, 1, 1, NOW(), NOW()),
('SCI8', 'Science 8', 8, 1.0, 1, 1, NOW(), NOW()),
('AP8', 'Aral Pan 8', 8, 1.0, 1, 1, NOW(), NOW()),
('FIL8', 'Filipino 8', 8, 1.0, 1, 1, NOW(), NOW()),
('TLE8', 'TLE FSC 8', 8, 1.0, 1, 1, NOW(), NOW()),
('VALUES8', 'Values Ed. 8', 8, 1.0, 1, 1, NOW(), NOW()),
('MAPEH8', 'MAPEH 8', 8, 1.0, 1, 1, NOW(), NOW());

-- Insert Sample Students
INSERT INTO `students` (`student_id`, `first_name`, `middle_name`, `last_name`, `gender`, `date_of_birth`, `place_of_birth`, `nationality`, `religion`, `contact_number`, `email`, `address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_relationship`, `enrollment_status`, `grade_level`, `section_id`, `school_year`, `created_at`, `updated_at`) VALUES
('2024-001', 'Juan', 'Santos', 'Cruz', 'Male', '2010-03-15', 'Tagbilaran City', 'Filipino', 'Catholic', '09123456789', 'juan.cruz@student.lphs.edu.ph', 'Brgy. Poblacion, Panglao, Bohol', 'Maria Cruz', '09987654321', 'Mother', 'enrolled', 8, 1, '2024-2025', NOW(), NOW()),
('2024-002', 'Maria', 'Garcia', 'Reyes', 'Female', '2010-05-22', 'Panglao, Bohol', 'Filipino', 'Catholic', '09234567890', 'maria.reyes@student.lphs.edu.ph', 'Brgy. Lourdes, Panglao, Bohol', 'Pedro Reyes', '09876543210', 'Father', 'enrolled', 8, 1, '2024-2025', NOW(), NOW()),
('2024-003', 'Jose', 'Mendoza', 'Torres', 'Male', '2010-01-10', 'Dauis, Bohol', 'Filipino', 'Catholic', '09345678901', 'jose.torres@student.lphs.edu.ph', 'Brgy. Dauis, Dauis, Bohol', 'Ana Torres', '09765432109', 'Mother', 'enrolled', 8, 1, '2024-2025', NOW(), NOW()),
('2024-004', 'Ana', 'Lopez', 'Flores', 'Female', '2010-07-08', 'Tagbilaran City', 'Filipino', 'Catholic', '09456789012', 'ana.flores@student.lphs.edu.ph', 'Brgy. Tawala, Panglao, Bohol', 'Carlos Flores', '09654321098', 'Father', 'enrolled', 8, 2, '2024-2025', NOW(), NOW()),
('2024-005', 'Miguel', 'Rivera', 'Santos', 'Male', '2009-11-30', 'Panglao, Bohol', 'Filipino', 'Catholic', '09567890123', 'miguel.santos@student.lphs.edu.ph', 'Brgy. Bolod, Panglao, Bohol', 'Rosa Santos', '09543210987', 'Mother', 'enrolled', 9, 3, '2024-2025', NOW(), NOW());

-- Insert Sample Grades
INSERT INTO `grades` (`student_id`, `subject_id`, `teacher_id`, `school_year`, `quarter`, `grade`, `remarks`, `date_recorded`, `created_at`, `updated_at`) VALUES
-- Juan Cruz grades
(1, 1, 1, '2024-2025', 1, 85.50, 'Good', NOW(), NOW(), NOW()),
(1, 2, 1, '2024-2025', 1, 88.00, 'Very Good', NOW(), NOW(), NOW()),
(1, 3, 1, '2024-2025', 1, 82.75, 'Good', NOW(), NOW(), NOW()),
(1, 1, 1, '2024-2025', 2, 87.25, 'Very Good', NOW(), NOW(), NOW()),
(1, 2, 1, '2024-2025', 2, 90.50, 'Excellent', NOW(), NOW(), NOW()),
(1, 3, 1, '2024-2025', 2, 85.00, 'Good', NOW(), NOW(), NOW()),

-- Maria Reyes grades
(2, 1, 1, '2024-2025', 1, 92.00, 'Excellent', NOW(), NOW(), NOW()),
(2, 2, 1, '2024-2025', 1, 89.50, 'Very Good', NOW(), NOW(), NOW()),
(2, 3, 1, '2024-2025', 1, 91.25, 'Excellent', NOW(), NOW(), NOW()),
(2, 1, 1, '2024-2025', 2, 94.75, 'Excellent', NOW(), NOW(), NOW()),
(2, 2, 1, '2024-2025', 2, 92.00, 'Excellent', NOW(), NOW(), NOW()),
(2, 3, 1, '2024-2025', 2, 93.50, 'Excellent', NOW(), NOW(), NOW());

-- Insert Sample Announcements
INSERT INTO `announcements` (`title`, `slug`, `body`, `target_roles`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES
('Welcome to School Year 2024-2025', 'welcome-sy-2024-2025', 'Welcome back students! We are excited to start another year of learning and growth at LPHS. Please check your schedules and prepare for the first day of classes.', 'all', NOW(), 1, NOW(), NOW()),
('Enrollment Period Extended', 'enrollment-extended', 'The enrollment period for new students has been extended until June 30, 2024. Please complete your requirements and submit them online through our SMS portal.', 'all', NOW(), 1, NOW(), NOW()),
('Grade 8 Orientation Schedule', 'grade-8-orientation', 'All Grade 8 students are required to attend the orientation program on August 15, 2024, at 8:00 AM in the school gymnasium. Parents are also invited to attend.', 'student', NOW(), 1, NOW(), NOW());

-- Insert Sample Teachers (for reference)
INSERT INTO `teachers` (`teacher_id`, `first_name`, `middle_name`, `last_name`, `gender`, `contact_number`, `email`, `department`, `position`, `specialization`, `date_hired`, `employment_status`, `created_at`, `updated_at`) VALUES
('T-001', 'Elisa', 'M.', 'Ereno', 'Female', '09111222333', 'elisa.ereno@lphs.edu.ph', 'Mathematics', 'Teacher III', 'Mathematics Education', '2020-06-15', 'active', NOW(), NOW()),
('T-002', 'Nathan', 'D.', 'Dolorical', 'Male', '09222333444', 'nathan.dolorical@lphs.edu.ph', 'Social Studies', 'Teacher II', 'History Education', '2019-08-20', 'active', NOW(), NOW()),
('T-003', 'Midlyn', 'C.', 'Castillo', 'Female', '09333444555', 'midlyn.castillo@lphs.edu.ph', 'Filipino', 'Teacher III', 'Filipino Literature', '2018-06-10', 'active', NOW(), NOW()),
('T-004', 'Christine', 'L.', 'Lumabe', 'Female', '09444555666', 'christine.lumabe@lphs.edu.ph', 'TLE', 'Teacher I', 'Food Service', '2021-03-01', 'active', NOW(), NOW()),
('T-005', 'Charito', 'M.', 'Malapo', 'Female', '09555666777', 'charito.malapo@lphs.edu.ph', 'Values Education', 'Teacher II', 'Guidance Counseling', '2017-07-15', 'active', NOW(), NOW());

-- Insert FAQ Data
INSERT INTO `faq` (`question`, `answer`, `keywords`) VALUES
('How do I enroll at LPHS?', 'To enroll at LPHS: 1) Create an account on our SMS portal, 2) Fill out the online enrollment form, 3) Upload required documents, 4) Wait for admin approval.', 'enrollment,enroll,register,admission'),
('What documents are required for enrollment?', 'Required documents: Birth Certificate (PSA), Report Card/Form 138, Good Moral Certificate, Medical Certificate, 2x2 ID Photo.', 'documents,requirements,papers,certificate'),
('When is the enrollment period?', 'Enrollment for SY 2024-2025: Early (Jan-Mar), Regular (Apr-May), Late (until June). Enroll early to secure your slot.', 'enrollment,period,when,schedule'),
('How do I check my grades?', 'Log into Student Portal > Click "My Grades" > Select school year and quarter. Grades are updated in real-time.', 'grades,check,view,marks'),
('What is the school contact information?', 'Address: Brgy. Lourdes, Panglao, Bohol | Email: info@lphs.edu.ph | Phone: +63 38 502 9000 | Hours: Mon-Fri 7AM-5PM', 'contact,phone,email,address,location');

-- Update student section assignments
UPDATE `students` SET `section_id` = 1 WHERE `id` IN (1, 2, 3);
UPDATE `students` SET `section_id` = 2 WHERE `id` = 4;
UPDATE `students` SET `section_id` = 3 WHERE `id` = 5;

-- Update section enrollment counts
UPDATE `sections` SET `current_enrollment` = 3 WHERE `id` = 1;
UPDATE `sections` SET `current_enrollment` = 1 WHERE `id` = 2;
UPDATE `sections` SET `current_enrollment` = 1 WHERE `id` = 3;

SELECT 'Database seeded successfully!' as message;