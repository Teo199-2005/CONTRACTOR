-- LPHS School Management System Sample Data
-- Run this after database_setup.sql

USE lphs_sms;

-- Insert Sections
INSERT INTO `sections` (`section_name`, `grade_level`, `school_year`, `max_capacity`, `current_enrollment`, `is_active`, `created_at`, `updated_at`) VALUES
('St. Francis', 7, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Clare', 7, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Anthony', 7, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Joseph', 8, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Mary', 8, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Peter', 8, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Paul', 9, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. John', 9, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Luke', 9, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Matthew', 10, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Mark', 10, '2024-2025', 40, 0, 1, NOW(), NOW()),
('St. Thomas', 10, '2024-2025', 40, 0, 1, NOW(), NOW());

-- Insert Subjects
INSERT INTO `subjects` (`subject_code`, `subject_name`, `grade_level`, `units`, `is_core`, `is_active`, `created_at`, `updated_at`) VALUES
-- Grade 7 Subjects
('ENG7', 'English 7', 7, 1.0, 1, 1, NOW(), NOW()),
('FIL7', 'Filipino 7', 7, 1.0, 1, 1, NOW(), NOW()),
('MATH7', 'Mathematics 7', 7, 1.0, 1, 1, NOW(), NOW()),
('SCI7', 'Science 7', 7, 1.0, 1, 1, NOW(), NOW()),
('AP7', 'Araling Panlipunan 7', 7, 1.0, 1, 1, NOW(), NOW()),
('MAPEH7', 'MAPEH 7', 7, 1.0, 1, 1, NOW(), NOW()),
('TLE7', 'Technology and Livelihood Education 7', 7, 1.0, 1, 1, NOW(), NOW()),
('ESP7', 'Edukasyon sa Pagpapakatao 7', 7, 1.0, 1, 1, NOW(), NOW()),

-- Grade 8 Subjects
('ENG8', 'English 8', 8, 1.0, 1, 1, NOW(), NOW()),
('FIL8', 'Filipino 8', 8, 1.0, 1, 1, NOW(), NOW()),
('MATH8', 'Mathematics 8', 8, 1.0, 1, 1, NOW(), NOW()),
('SCI8', 'Science 8', 8, 1.0, 1, 1, NOW(), NOW()),
('AP8', 'Araling Panlipunan 8', 8, 1.0, 1, 1, NOW(), NOW()),
('MAPEH8', 'MAPEH 8', 8, 1.0, 1, 1, NOW(), NOW()),
('TLE8', 'Technology and Livelihood Education 8', 8, 1.0, 1, 1, NOW(), NOW()),
('ESP8', 'Edukasyon sa Pagpapakatao 8', 8, 1.0, 1, 1, NOW(), NOW()),

-- Grade 9 Subjects
('ENG9', 'English 9', 9, 1.0, 1, 1, NOW(), NOW()),
('FIL9', 'Filipino 9', 9, 1.0, 1, 1, NOW(), NOW()),
('MATH9', 'Mathematics 9', 9, 1.0, 1, 1, NOW(), NOW()),
('SCI9', 'Science 9', 9, 1.0, 1, 1, NOW(), NOW()),
('AP9', 'Araling Panlipunan 9', 9, 1.0, 1, 1, NOW(), NOW()),
('MAPEH9', 'MAPEH 9', 9, 1.0, 1, 1, NOW(), NOW()),
('TLE9', 'Technology and Livelihood Education 9', 9, 1.0, 1, 1, NOW(), NOW()),
('ESP9', 'Edukasyon sa Pagpapakatao 9', 9, 1.0, 1, 1, NOW(), NOW()),

-- Grade 10 Subjects
('ENG10', 'English 10', 10, 1.0, 1, 1, NOW(), NOW()),
('FIL10', 'Filipino 10', 10, 1.0, 1, 1, NOW(), NOW()),
('MATH10', 'Mathematics 10', 10, 1.0, 1, 1, NOW(), NOW()),
('SCI10', 'Science 10', 10, 1.0, 1, 1, NOW(), NOW()),
('AP10', 'Araling Panlipunan 10', 10, 1.0, 1, 1, NOW(), NOW()),
('MAPEH10', 'MAPEH 10', 10, 1.0, 1, 1, NOW(), NOW()),
('TLE10', 'Technology and Livelihood Education 10', 10, 1.0, 1, 1, NOW(), NOW()),
('ESP10', 'Edukasyon sa Pagpapakatao 10', 10, 1.0, 1, 1, NOW(), NOW());

-- Insert FAQ
INSERT INTO `faq` (`question`, `answer`, `keywords`, `category`, `is_active`, `view_count`, `created_at`, `updated_at`) VALUES
('What are the enrollment requirements?', 'Required documents include: Birth Certificate (PSA), Report Card/Form 138, Certificate of Good Moral Character, Medical Certificate, and 2x2 ID photos.', 'enrollment,requirements,documents,birth certificate,report card,good moral,medical certificate,photos', 'enrollment', 1, 0, NOW(), NOW()),
('When is the enrollment period?', 'Enrollment for the upcoming school year typically starts in March and ends in June. Please check our announcements for specific dates.', 'enrollment,period,when,dates,march,june,school year', 'enrollment', 1, 0, NOW(), NOW()),
('How can I check my grades?', 'Students and parents can log in to the SMS portal using their credentials to view grades and academic performance.', 'grades,check,view,academic,performance,login,portal', 'academics', 1, 0, NOW(), NOW()),
('What is the school contact information?', 'You can reach us at (000) 123-4567 or email info@lphs.edu. Our office hours are Monday to Friday, 8:00 AM to 5:00 PM.', 'contact,phone,email,office hours,information', 'general', 1, 0, NOW(), NOW()),
('How do I reset my password?', 'Click on "Forgot Password" on the login page and enter your email address. You will receive instructions to reset your password.', 'password,reset,forgot,login,email', 'technical', 1, 0, NOW(), NOW()),
('What grade levels does LPHS offer?', 'Lourdes Provincial High School offers Junior High School education from Grade 7 to Grade 10.', 'grade levels,junior high school,grade 7,grade 8,grade 9,grade 10', 'general', 1, 0, NOW(), NOW()),
('How do I upload enrollment documents?', 'During the online registration process, you will find upload sections for each required document. Accepted formats are PDF and JPG files.', 'upload,documents,enrollment,pdf,jpg,registration', 'enrollment', 1, 0, NOW(), NOW()),
('Can parents view their child\'s grades?', 'Yes, parents can create an account and link it to their child\'s student record to view grades and academic progress.', 'parents,grades,view,child,academic,progress', 'academics', 1, 0, NOW(), NOW());

-- Insert Sample Announcements
INSERT INTO `announcements` (`title`, `slug`, `body`, `target_roles`, `published_at`, `created_at`, `updated_at`) VALUES
('Welcome to School Year 2024-2025', 'welcome-sy-2024-2025', 'We welcome all students, parents, and faculty to the new school year. Let us work together for academic excellence and character formation.', 'all', NOW(), NOW(), NOW()),
('Enrollment Period Extended', 'enrollment-period-extended', 'Due to high demand, the enrollment period has been extended until June 30, 2024. Don\'t miss this opportunity to be part of the LPHS family!', 'all', NOW(), NOW(), NOW()),
('New Online Learning Management System', 'new-online-lms', 'We are excited to announce the launch of our new School Management System that will streamline enrollment, grade viewing, and communication between school and families.', 'all', NOW(), NOW(), NOW()),
('Parent-Teacher Conference Schedule', 'ptc-schedule', 'Parent-Teacher Conferences will be held on the first Friday of every month. Please check your notifications for specific appointment times.', 'parent,teacher', NOW(), NOW(), NOW());
