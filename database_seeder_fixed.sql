-- LPHS SMS Database Seeder (Fixed for duplicates)
USE lphs_sms;

-- Insert Sections (ignore duplicates)
INSERT IGNORE INTO `sections` (`section_name`, `grade_level`, `school_year`, `max_capacity`, `current_enrollment`, `is_active`, `created_at`, `updated_at`) VALUES
('Bower', 8, '2024-2025', 40, 25, 1, NOW(), NOW()),
('Rose', 8, '2024-2025', 40, 28, 1, NOW(), NOW()),
('Lily', 9, '2024-2025', 40, 30, 1, NOW(), NOW()),
('Jasmine', 9, '2024-2025', 40, 27, 1, NOW(), NOW()),
('Orchid', 10, '2024-2025', 40, 32, 1, NOW(), NOW());

-- Insert Subjects (ignore duplicates)
INSERT IGNORE INTO `subjects` (`subject_code`, `subject_name`, `grade_level`, `units`, `is_core`, `is_active`, `created_at`, `updated_at`) VALUES
('MATH8', 'Mathematics 8', 8, 1.0, 1, 1, NOW(), NOW()),
('ENG8', 'English 8', 8, 1.0, 1, 1, NOW(), NOW()),
('SCI8', 'Science 8', 8, 1.0, 1, 1, NOW(), NOW()),
('AP8', 'Aral Pan 8', 8, 1.0, 1, 1, NOW(), NOW()),
('FIL8', 'Filipino 8', 8, 1.0, 1, 1, NOW(), NOW()),
('TLE8', 'TLE FSC 8', 8, 1.0, 1, 1, NOW(), NOW()),
('VALUES8', 'Values Ed. 8', 8, 1.0, 1, 1, NOW(), NOW()),
('MAPEH8', 'MAPEH 8', 8, 1.0, 1, 1, NOW(), NOW());

-- Insert Teachers (ignore duplicates)
INSERT IGNORE INTO `teachers` (`teacher_id`, `first_name`, `middle_name`, `last_name`, `gender`, `contact_number`, `email`, `department`, `position`, `specialization`, `date_hired`, `employment_status`, `created_at`, `updated_at`) VALUES
('T-001', 'Elisa', 'M.', 'Ereno', 'Female', '09111222333', 'elisa.ereno@lphs.edu.ph', 'Mathematics', 'Teacher III', 'Mathematics Education', '2020-06-15', 'active', NOW(), NOW()),
('T-002', 'Nathan', 'D.', 'Dolorical', 'Male', '09222333444', 'nathan.dolorical@lphs.edu.ph', 'Social Studies', 'Teacher II', 'History Education', '2019-08-20', 'active', NOW(), NOW()),
('T-003', 'Midlyn', 'C.', 'Castillo', 'Female', '09333444555', 'midlyn.castillo@lphs.edu.ph', 'Filipino', 'Teacher III', 'Filipino Literature', '2018-06-10', 'active', NOW(), NOW());

-- Insert Sample Students (ignore duplicates)
INSERT IGNORE INTO `students` (`student_id`, `first_name`, `middle_name`, `last_name`, `gender`, `date_of_birth`, `place_of_birth`, `nationality`, `religion`, `contact_number`, `email`, `address`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_relationship`, `enrollment_status`, `grade_level`, `section_id`, `school_year`, `created_at`, `updated_at`) VALUES
('2024-001', 'Juan', 'Santos', 'Cruz', 'Male', '2010-03-15', 'Tagbilaran City', 'Filipino', 'Catholic', '09123456789', 'juan.cruz@student.lphs.edu.ph', 'Brgy. Poblacion, Panglao, Bohol', 'Maria Cruz', '09987654321', 'Mother', 'enrolled', 8, 1, '2024-2025', NOW(), NOW()),
('2024-002', 'Maria', 'Garcia', 'Reyes', 'Female', '2010-05-22', 'Panglao, Bohol', 'Filipino', 'Catholic', '09234567890', 'maria.reyes@student.lphs.edu.ph', 'Brgy. Lourdes, Panglao, Bohol', 'Pedro Reyes', '09876543210', 'Father', 'enrolled', 8, 1, '2024-2025', NOW(), NOW()),
('2024-003', 'Jose', 'Mendoza', 'Torres', 'Male', '2010-01-10', 'Dauis, Bohol', 'Filipino', 'Catholic', '09345678901', 'jose.torres@student.lphs.edu.ph', 'Brgy. Dauis, Dauis, Bohol', 'Ana Torres', '09765432109', 'Mother', 'enrolled', 8, 1, '2024-2025', NOW(), NOW());

-- Insert Sample Grades (ignore duplicates)
INSERT IGNORE INTO `grades` (`student_id`, `subject_id`, `teacher_id`, `school_year`, `quarter`, `grade`, `remarks`, `date_recorded`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-2025', 1, 85.50, 'Good', NOW(), NOW(), NOW()),
(1, 2, 1, '2024-2025', 1, 88.00, 'Very Good', NOW(), NOW(), NOW()),
(1, 3, 1, '2024-2025', 1, 82.75, 'Good', NOW(), NOW(), NOW()),
(1, 1, 1, '2024-2025', 2, 99.00, 'Excellent', NOW(), NOW(), NOW()),
(2, 1, 1, '2024-2025', 1, 92.00, 'Excellent', NOW(), NOW(), NOW()),
(2, 2, 1, '2024-2025', 1, 89.50, 'Very Good', NOW(), NOW(), NOW());

-- Insert Sample Announcements (ignore duplicates)
INSERT IGNORE INTO `announcements` (`title`, `slug`, `body`, `target_roles`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES
('Welcome to School Year 2024-2025', 'welcome-sy-2024-2025', 'Welcome back students! We are excited to start another year of learning and growth at LPHS.', 'all', NOW(), 1, NOW(), NOW()),
('Enrollment Period Extended', 'enrollment-extended', 'The enrollment period for new students has been extended until June 30, 2024.', 'all', NOW(), 1, NOW(), NOW());

SELECT 'Database seeded successfully!' as message;