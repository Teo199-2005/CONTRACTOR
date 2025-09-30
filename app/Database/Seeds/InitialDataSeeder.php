<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Seed Sections
        $this->seedSections();
        
        // Seed Subjects
        $this->seedSubjects();
        
        // Seed FAQ
        $this->seedFaq();
        
        // Seed Sample Announcements
        $this->seedAnnouncements();
    }
    
    private function seedSections()
    {
        $sections = [
            // Grade 7
            ['section_name' => 'St. Francis', 'grade_level' => 7, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Clare', 'grade_level' => 7, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Anthony', 'grade_level' => 7, 'school_year' => '2024-2025', 'max_capacity' => 40],
            
            // Grade 8
            ['section_name' => 'St. Joseph', 'grade_level' => 8, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Mary', 'grade_level' => 8, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Peter', 'grade_level' => 8, 'school_year' => '2024-2025', 'max_capacity' => 40],
            
            // Grade 9
            ['section_name' => 'St. Paul', 'grade_level' => 9, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. John', 'grade_level' => 9, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Luke', 'grade_level' => 9, 'school_year' => '2024-2025', 'max_capacity' => 40],
            
            // Grade 10
            ['section_name' => 'St. Matthew', 'grade_level' => 10, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Mark', 'grade_level' => 10, 'school_year' => '2024-2025', 'max_capacity' => 40],
            ['section_name' => 'St. Thomas', 'grade_level' => 10, 'school_year' => '2024-2025', 'max_capacity' => 40],
        ];
        
        $this->db->table('sections')->insertBatch($sections);
    }
    
    private function seedSubjects()
    {
        $subjects = [
            // Grade 7 Core Subjects
            ['subject_code' => 'ENG7', 'subject_name' => 'English 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'FIL7', 'subject_name' => 'Filipino 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MATH7', 'subject_name' => 'Mathematics 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'SCI7', 'subject_name' => 'Science 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'AP7', 'subject_name' => 'Araling Panlipunan 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MAPEH7', 'subject_name' => 'MAPEH 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'TLE7', 'subject_name' => 'Technology and Livelihood Education 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'ESP7', 'subject_name' => 'Edukasyon sa Pagpapakatao 7', 'grade_level' => 7, 'units' => 1.0, 'is_core' => true],
            
            // Grade 8 Core Subjects
            ['subject_code' => 'ENG8', 'subject_name' => 'English 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'FIL8', 'subject_name' => 'Filipino 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MATH8', 'subject_name' => 'Mathematics 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'SCI8', 'subject_name' => 'Science 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'AP8', 'subject_name' => 'Araling Panlipunan 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MAPEH8', 'subject_name' => 'MAPEH 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'TLE8', 'subject_name' => 'Technology and Livelihood Education 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'ESP8', 'subject_name' => 'Edukasyon sa Pagpapakatao 8', 'grade_level' => 8, 'units' => 1.0, 'is_core' => true],
            
            // Grade 9 Core Subjects
            ['subject_code' => 'ENG9', 'subject_name' => 'English 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'FIL9', 'subject_name' => 'Filipino 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MATH9', 'subject_name' => 'Mathematics 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'SCI9', 'subject_name' => 'Science 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'AP9', 'subject_name' => 'Araling Panlipunan 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MAPEH9', 'subject_name' => 'MAPEH 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'TLE9', 'subject_name' => 'Technology and Livelihood Education 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'ESP9', 'subject_name' => 'Edukasyon sa Pagpapakatao 9', 'grade_level' => 9, 'units' => 1.0, 'is_core' => true],
            
            // Grade 10 Core Subjects
            ['subject_code' => 'ENG10', 'subject_name' => 'English 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'FIL10', 'subject_name' => 'Filipino 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MATH10', 'subject_name' => 'Mathematics 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'SCI10', 'subject_name' => 'Science 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'AP10', 'subject_name' => 'Araling Panlipunan 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'MAPEH10', 'subject_name' => 'MAPEH 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'TLE10', 'subject_name' => 'Technology and Livelihood Education 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
            ['subject_code' => 'ESP10', 'subject_name' => 'Edukasyon sa Pagpapakatao 10', 'grade_level' => 10, 'units' => 1.0, 'is_core' => true],
        ];
        
        $this->db->table('subjects')->insertBatch($subjects);
    }
    
    private function seedFaq()
    {
        $faqs = [
            [
                'question' => 'What are the enrollment requirements?',
                'answer' => 'Required documents include: Birth Certificate (PSA), Report Card/Form 138, Certificate of Good Moral Character, Medical Certificate, and 2x2 ID photos.',
                'keywords' => 'enrollment,requirements,documents,birth certificate,report card,good moral,medical certificate,photos',
                'category' => 'enrollment'
            ],
            [
                'question' => 'When is the enrollment period?',
                'answer' => 'Enrollment for the upcoming school year typically starts in March and ends in June. Please check our announcements for specific dates.',
                'keywords' => 'enrollment,period,when,dates,march,june,school year',
                'category' => 'enrollment'
            ],
            [
                'question' => 'How can I check my grades?',
                'answer' => 'Students and parents can log in to the SMS portal using their credentials to view grades and academic performance.',
                'keywords' => 'grades,check,view,academic,performance,login,portal',
                'category' => 'academics'
            ],
            [
                'question' => 'What is the school contact information?',
                'answer' => 'You can reach Lourdes Provincial High School via email at 302002@deped.gov.ph or phone at 0951-683-5105. Office hours are Monday to Friday, 8:00 AM to 5:00 PM.',
                'keywords' => 'contact,phone,email,office hours,information,lphs',
                'category' => 'general'
            ],
            [
                'question' => 'How do I reset my password?',
                'answer' => 'Click on "Forgot Password" on the login page and enter your email address. You will receive instructions to reset your password.',
                'keywords' => 'password,reset,forgot,login,email',
                'category' => 'technical'
            ]
        ];
        
        $this->db->table('faq')->insertBatch($faqs);
    }
    
    private function seedAnnouncements()
    {
        $announcements = [
            [
                'title' => 'Welcome to School Year 2024-2025',
                'slug' => 'welcome-sy-2024-2025',
                'body' => 'We welcome all students, parents, and faculty to the new school year. Let us work together for academic excellence.',
                'target_roles' => 'all',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Enrollment Period Extended',
                'slug' => 'enrollment-period-extended',
                'body' => 'Due to high demand, the enrollment period has been extended until June 30, 2024. Don\'t miss this opportunity!',
                'target_roles' => 'all',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('announcements')->insertBatch($announcements);
    }
}
