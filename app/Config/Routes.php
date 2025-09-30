<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
// $routes->get('about', 'Home::about'); // disabled
// $routes->get('about-school', 'Home::aboutSchool'); // disabled

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/forgot', 'Auth::forgot');
// Demo quick-login (for development/demo only)
$routes->get('login/demo/(:alpha)', 'Auth::demo/$1');
$routes->get('login/demo/(:segment)', 'Auth::demo/$1'); // Support for newstudent
// Generic dashboard redirector
$routes->get('dashboard', 'Auth::dashboard');

// File serving routes
$routes->get('uploads/enrollment_documents/(:any)', 'FileController::serveEnrollmentDocumentForAdmin/$1');

// Password reset routes
$routes->get('forgot-password', 'PasswordReset::forgotPassword');
$routes->post('forgot-password', 'PasswordReset::sendResetInstructions');
$routes->get('reset-password/(:any)', 'PasswordReset::resetPassword/$1');
$routes->post('reset-password', 'PasswordReset::updatePassword');

// CodeIgniter Shield routes (for additional auth features)
// Temporarily disabled to avoid conflicts with custom auth
// service('auth')->routes($routes);

// Admin Dashboard Routes
$routes->group('admin', [], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('enrollments', 'Admin\Dashboard::enrollments');
    $routes->post('enrollments/approve/(:num)', 'Admin\Dashboard::approveEnrollment/$1');
    $routes->post('enrollments/reject/(:num)', 'Admin\Dashboard::rejectEnrollment/$1');
    $routes->get('enrollments/student/(:num)', 'Admin\Dashboard::getStudentDetails/$1');
    $routes->get('students', 'Admin\Students::index');
    $routes->get('students/create', 'Admin\Students::create');
    $routes->post('students/store', 'Admin\Students::store');
    $routes->get('students/edit/(:num)', 'Admin\Students::edit/$1');
    $routes->post('students/update/(:num)', 'Admin\Students::update/$1');
    $routes->delete('students/delete/(:num)', 'Admin\Students::delete/$1');
    $routes->get('students/details/(:num)', 'Admin\Students::getStudentDetails/$1');
    $routes->get('teachers', 'Admin\Teachers::index');
    $routes->get('teachers/create', 'Admin\Teachers::create');
    $routes->post('teachers/store', 'Admin\Teachers::store');
    $routes->get('teachers/edit/(:num)', 'Admin\Teachers::edit/$1');
    $routes->post('teachers/update/(:num)', 'Admin\Teachers::update/$1');
    $routes->delete('teachers/delete/(:num)', 'Admin\Teachers::delete/$1');
    $routes->get('teachers/details/(:num)', 'Admin\Teachers::getTeacherDetails/$1');
    $routes->get('teachers/edit-form/(:num)', 'Admin\Teachers::editForm/$1');
    $routes->get('sections', 'Admin\Dashboard::sections');
    $routes->post('sections/assign-adviser/(:num)', 'Admin\Dashboard::assignAdviser/$1');
    $routes->post('sections/remove-adviser/(:num)', 'Admin\Dashboard::removeAdviser/$1');
    $routes->get('sections/students/(:num)', 'Admin\Dashboard::getSectionStudents/$1');
    $routes->post('sections/update/(:num)', 'Admin\Dashboard::updateSection/$1');
    $routes->get('analytics', 'Admin\Dashboard::analytics');
    $routes->get('analytics/export-pdf', 'Admin\Dashboard::exportPdf');
    // Additional Admin Features
    $routes->get('notifications', 'Admin\Notifications::index');
    $routes->post('notifications/send', 'Admin\Notifications::send');
    $routes->get('notifications/show/(:num)', 'Admin\Notifications::show/$1');
    $routes->get('notifications/edit/(:num)', 'Admin\Notifications::edit/$1');
    $routes->post('notifications/update/(:num)', 'Admin\Notifications::update/$1');
    $routes->post('notifications/delete/(:num)', 'Admin\Notifications::delete/$1');
    $routes->post('notifications/markAsRead', 'Admin\Notifications::markAsRead');
    $routes->post('notifications/getStats', 'Admin\Notifications::getStats');

    // Announcements CRUD
    $routes->get('announcements', 'Admin\Announcements::index');
    $routes->get('announcements/create', 'Admin\Announcements::create');
    $routes->post('announcements/store', 'Admin\Announcements::store');
    $routes->get('announcements/show/(:num)', 'Admin\Announcements::show/$1');
    $routes->get('announcements/edit/(:num)', 'Admin\Announcements::edit/$1');
    $routes->post('announcements/update/(:num)', 'Admin\Announcements::update/$1');
    $routes->post('announcements/delete/(:num)', 'Admin\Announcements::delete/$1');
    $routes->post('announcements/getStats', 'Admin\Announcements::getStats');

    // Password reset management
    $routes->get('password-resets', 'Admin\PasswordResets::index');
    $routes->get('password-resets/count', 'Admin\PasswordResets::getCount');
    $routes->get('password-resets/details/(:num)', 'Admin\PasswordResets::getRequestDetails/$1');
    $routes->post('password-resets/approve/(:num)', 'Admin\PasswordResets::approve/$1');
    $routes->post('password-resets/reject/(:num)', 'Admin\PasswordResets::reject/$1');
    $routes->get('password-resets/reset-link/(:num)', 'Admin\PasswordResets::generateResetLink/$1');

    // Announcements AJAX management
    $routes->get('announcements/list', 'Announcements::listAjax');
    $routes->post('announcements/store', 'Announcements::storeAjax');
    $routes->get('announcements/get/(:num)', 'Announcements::getAjax/$1');
    $routes->post('announcements/update/(:num)', 'Announcements::updateAjax/$1');
    $routes->delete('announcements/delete/(:num)', 'Announcements::deleteAjax/$1');
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/create', 'Admin\Users::create');
    $routes->post('users/create', 'Admin\Users::create');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->get('users/delete/(:num)', 'Admin\Users::delete/$1');
    $routes->post('users/bulkDelete', 'Admin\Users::bulkDelete');

    $routes->get('id-cards', 'Admin\IdCards::index');
});

// Student Dashboard Routes
$routes->group('student', [], static function ($routes) {
    $routes->get('dashboard', 'Student\Dashboard::index');
    $routes->get('profile', 'Student\Dashboard::profile');
    $routes->post('profile', 'Student\Dashboard::updateProfile');
    $routes->get('grades', 'Student\Dashboard::grades');
    $routes->get('schedule', 'Student\Dashboard::schedule');
    $routes->get('announcements', 'Student\Dashboard::announcements');
    // Additional Student Features
    $routes->get('materials', 'Student\Materials::index');
    $routes->get('events', 'Student\Events::index');
    $routes->get('notifications', 'Student\Notifications::index');

});

// Teacher Dashboard Routes
$routes->group('teacher', [], static function ($routes) {
    $routes->get('dashboard', 'Teacher\Dashboard::index');
    $routes->get('grades', 'Teacher\Dashboard::grades');
    $routes->post('grades', 'Teacher\Dashboard::saveGrades');
    $routes->get('students', 'Teacher\Dashboard::students');
    $routes->get('schedule', 'Teacher\Dashboard::schedule');
    // Additional Teacher Features
    $routes->get('announcements', 'Teacher\Announcements::index');
    $routes->post('announcements', 'Teacher\Announcements::post');
    $routes->get('materials', 'Teacher\Materials::index');
    $routes->post('materials/upload', 'Teacher\Materials::upload');
    $routes->get('messages', 'Teacher\Messages::index');
    $routes->get('analytics', 'Teacher\Analytics::index');
});

// Parent Dashboard Routes
$routes->group('parent', [], static function ($routes) {
    $routes->get('dashboard', 'Parent\Dashboard::index');
    $routes->get('children', 'Parent\Dashboard::children');
    $routes->get('grades/(:num)', 'Parent\Dashboard::childGrades/$1');
    $routes->get('announcements', 'Parent\Dashboard::announcements');
});

// Public Announcements
// $routes->get('announcements', 'Announcements::index'); // disabled public announcements page

// Admin Announcements Management
$routes->group('announcements', [], static function ($routes) {
    $routes->get('admin', 'Announcements::admin');
    $routes->get('create', 'Announcements::create');
    $routes->post('/', 'Announcements::store');
    $routes->get('edit/(:num)', 'Announcements::edit/$1');
    $routes->post('update/(:num)', 'Announcements::update/$1');
    $routes->post('delete/(:num)', 'Announcements::delete/$1');
});

// FAQ Chatbot (Public Access)
$routes->get('faq', 'Chatbot::index');
$routes->post('faq/ask', 'Chatbot::ask');

// Debug route to check authentication state
$routes->get('debug/auth', 'Auth::debugAuth');

// Simple test route without authentication
$routes->get('test/simple', 'Auth::testSimple');

// Simple student dashboard test
$routes->get('test/student', 'Student\Dashboard::testSimple');

// Test auth service directly
$routes->get('debug/auth-service', 'Auth::testAuthService');
