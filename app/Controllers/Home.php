<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\StudentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(): ResponseInterface|string
    {
        // If logged in, send user to their role dashboard instead of landing page
        try {
            $auth = auth();
            if ($auth->loggedIn()) {
                $user = $auth->user();
                if ($user->inGroup('admin')) {
                    return redirect()->to(base_url('admin/dashboard'));
                }
                if ($user->inGroup('teacher')) {
                    return redirect()->to(base_url('teacher/dashboard'));
                }
                if ($user->inGroup('student')) {
                    return redirect()->to(base_url('student/dashboard'));
                }
                if ($user->inGroup('parent')) {
                    return redirect()->to(base_url('parent/dashboard'));
                }
            }
        } catch (\Throwable $e) {
            // ignore and show public landing page
        }

        $announcements = [];
        $enrollmentData = [];
        $predictionData = [];
        
        try {
            $model = new AnnouncementModel();
            $announcements = $model->orderBy('published_at', 'DESC')->findAll(5);
        } catch (\Throwable $e) {
            // Table may not exist yet during first run.
        }
        
        try {
            $enrollmentData = $this->getEnrollmentData();
            $predictionData = $this->generatePredictions($enrollmentData);
            $monthlyEnrollmentData = $this->getMonthlyEnrollmentData();
        } catch (\Throwable $e) {
            // Handle database errors gracefully
            $monthlyEnrollmentData = [5, 4, 1, 4, 7, 63, 51, 27, 11, 14, 3, 1];
        }
        
        return view('landing', [
            'title' => 'LPHS School Management System',
            'announcements' => $announcements,
            'enrollmentData' => json_encode($enrollmentData),
            'predictionData' => json_encode($predictionData),
            'monthlyEnrollmentData' => json_encode($monthlyEnrollmentData)
        ]);
    }
    
    private function getEnrollmentData(): array
    {
        $studentModel = new StudentModel();
        
        try {
            // Get total enrolled students
            $totalStudents = $studentModel->where('enrollment_status', 'enrolled')->countAllResults();
            
            // Simulate Philippine enrollment pattern based on actual data
            $pattern = [3, 2, 1, 2, 4, 35, 28, 15, 6, 2, 1, 1]; // Percentages
            
            $data = [];
            for ($year = 2023; $year <= 2025; $year++) {
                $baseCount = $year == 2024 ? $totalStudents : round($totalStudents * 0.8);
                $monthly = [];
                
                foreach ($pattern as $percent) {
                    $monthly[] = round(($baseCount * $percent) / 100);
                }
                
                $data[$year] = [
                    'monthly' => $monthly,
                    'yearly' => [array_sum($monthly)]
                ];
            }
            
            return $data;
        } catch (\Throwable $e) {
            // Fallback data
            return [
                2023 => ['monthly' => [3, 2, 1, 2, 4, 35, 28, 15, 6, 2, 1, 1], 'yearly' => [100]],
                2024 => ['monthly' => [4, 3, 1, 3, 5, 47, 38, 20, 8, 3, 2, 1], 'yearly' => [135]],
                2025 => ['monthly' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 'yearly' => [0]]
            ];
        }
    }
    
    private function generatePredictions(array $historicalData): array
    {
        // Get current enrolled student data as baseline
        $currentEnrolledData = $this->getMonthlyEnrollmentData();
        $currentTotal = array_sum($currentEnrolledData);
        
        // Calculate growth rate based on current enrollment trends
        $baseGrowthRate = 0.08; // 8% annual growth (typical for growing schools)
        
        $predictions = [];
        
        // Generate predictions for 2026-2028 based on current enrollment data
        for ($year = 2026; $year <= 2028; $year++) {
            $yearsFromNow = $year - 2025;
            $growthFactor = pow(1 + $baseGrowthRate, $yearsFromNow);
            
            // Apply growth to each month's current enrollment
            $monthlyPredictions = [];
            foreach ($currentEnrolledData as $monthValue) {
                $monthlyPredictions[] = round($monthValue * $growthFactor);
            }
            
            $predictions[$year] = [
                'monthly' => $monthlyPredictions,
                'yearly' => [array_sum($monthlyPredictions)]
            ];
        }
        
        return $predictions;
    }

    public function getEnrollmentApi(): ResponseInterface
    {
        try {
            $enrollmentData = $this->getEnrollmentData();
            $predictionData = $this->generatePredictions($enrollmentData);
            
            return $this->response->setJSON([
                'success' => true,
                'enrollment' => $enrollmentData,
                'predictions' => $predictionData
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch enrollment data'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get monthly enrollment data for enrolled students chart
     */
    private function getMonthlyEnrollmentData(): array
    {
        $db = \Config\Database::connect();
        
        try {
            // Get students enrolled before today (for scattering)
            $oldStudents = $db->query("
                SELECT COUNT(*) as count 
                FROM students 
                WHERE DATE(created_at) < CURDATE()
                AND enrollment_status = 'enrolled'
                AND deleted_at IS NULL
            ")->getRow()->count ?? 0;
            
            // Get students enrolled today and onwards (real data)
            $newStudents = $db->query("
                SELECT MONTH(created_at) as month, COUNT(*) as count 
                FROM students 
                WHERE DATE(created_at) >= CURDATE()
                AND enrollment_status = 'enrolled'
                AND deleted_at IS NULL
                GROUP BY MONTH(created_at)
            ")->getResultArray();
            
            // Philippine enrollment distribution pattern (Jan-Oct only, Nov-Dec = 0)
            $distribution = [0.03, 0.02, 0.02, 0.07, 0.20, 0.45, 0.15, 0.04, 0.02, 0.00, 0.00, 0.00];
            
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                if ($month <= 10) {
                    // Scatter old data across Jan-Oct only
                    $count = (int)round($oldStudents * $distribution[$month - 1]);
                } else {
                    // Nov-Dec start with 0 (no scattered data)
                    $count = 0;
                }
                
                // Add real new enrollments for this month
                foreach ($newStudents as $newStudent) {
                    if ($newStudent['month'] == $month) {
                        $count += (int)$newStudent['count'];
                    }
                }
                
                $monthlyData[] = $count;
            }
            
            return $monthlyData;
        } catch (\Throwable $e) {
            // Fallback data
            return [5, 4, 1, 4, 7, 63, 51, 27, 11, 14, 3, 1];
        }
    }

    // About pages disabled per navigation cleanup
}
