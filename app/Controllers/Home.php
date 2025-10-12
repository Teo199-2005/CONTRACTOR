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
        } catch (\Throwable $e) {
            // Handle database errors gracefully
        }
        
        return view('landing', [
            'title' => 'LPHS School Management System',
            'announcements' => $announcements,
            'enrollmentData' => json_encode($enrollmentData),
            'predictionData' => json_encode($predictionData)
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
        $predictions = [];
        
        // Philippine school enrollment pattern (percentages by month)
        // June-July: Peak enrollment (start of school year)
        // Aug-Sep: Late enrollments
        // Oct-May: Minimal enrollments, transfers
        $philippinePattern = [
            0.04, // Jan - Mid-year transfers
            0.03, // Feb - Final enrollments before cutoff
            0.02, // Mar - Very few
            0.03, // Apr - Some transfers
            0.05, // May - Pre-enrollment preparation
            0.35, // Jun - PEAK: School year starts
            0.28, // Jul - HIGH: Late enrollments
            0.15, // Aug - Moderate: Final late enrollments
            0.04, // Sep - Few stragglers
            0.01, // Oct - Minimal
            0.00, // Nov - Almost none
            0.00  // Dec - None (Christmas break)
        ];
        
        // Calculate growth trend from historical data
        $growth2023to2024 = ($historicalData[2024]['yearly'][0] - $historicalData[2023]['yearly'][0]) / $historicalData[2023]['yearly'][0];
        $baseGrowthRate = max(0.05, min(0.15, $growth2023to2024)); // Cap between 5-15%
        
        // Generate predictions for 2026-2028
        for ($year = 2026; $year <= 2028; $year++) {
            $yearsFromBase = $year - 2024;
            $growthFactor = pow(1 + $baseGrowthRate, $yearsFromBase);
            $predictedTotal = round($historicalData[2024]['yearly'][0] * $growthFactor);
            
            // Apply Philippine enrollment pattern
            $monthlyPredictions = [];
            foreach ($philippinePattern as $ratio) {
                $monthlyPredictions[] = round($predictedTotal * $ratio);
            }
            
            $predictions[$year] = [
                'monthly' => $monthlyPredictions,
                'yearly' => [$predictedTotal]
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

    // About pages disabled per navigation cleanup
}
