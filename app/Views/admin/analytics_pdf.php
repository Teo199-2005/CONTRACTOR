<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LPHS Analytics Report</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            color: #000;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 5px 0;
        }
        
        .report-info {
            font-size: 12px;
            margin: 5px 0;
        }
        
        .section {
            margin: 25px 0;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .metric-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        
        .metric-row {
            display: table-row;
        }
        
        .metric-label,
        .metric-value {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #000;
        }
        
        .metric-label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 60%;
        }
        
        .metric-value {
            text-align: right;
            width: 40%;
        }
        
        .summary-box {
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
            background-color: #f9f9f9;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <?php
            $logoPath = FCPATH . 'LPHS2.png';
            if (file_exists($logoPath) && function_exists('imagecreatefrompng')) {
                $imageData = file_get_contents($logoPath);
                $base64 = base64_encode($imageData);
                echo '<img src="data:image/png;base64,' . $base64 . '" alt="LPHS Logo" style="width: 80px; height: 80px; margin: 0 auto; display: block;">';
            } else {
                echo '<div style="width: 80px; height: 80px; border: 3px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: #f0f0f0;"><strong style="font-size: 18px;">LPHS</strong></div>';
            }
            ?>
        </div>
        <div class="school-name">Lourdes Provincial High School</div>
        <div class="report-title">Student Management System Analytics Report</div>
        <div class="report-info">School Year: <?= $schoolYear ?></div>
        <div class="report-info">Report Generated: <?= $reportDate ?></div>
    </div>

    <div class="section">
        <div class="section-title">Executive Summary</div>
        <div class="summary-box">
            <p><strong>Total Students:</strong> <?= array_sum($statusDistribution) ?></p>
            <p><strong>Enrolled Students:</strong> <?= $statusDistribution['enrolled'] ?></p>
            <p><strong>Pending Applications:</strong> <?= $statusDistribution['pending'] ?></p>
            <p><strong>Completion Rate:</strong> <?= $metrics['completionRate'] ?>%</p>
            <p><strong>Gender Balance:</strong> <?= $genderDistribution['male'] ?> Male, <?= $genderDistribution['female'] ?> Female</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Enrollment Status Distribution</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Enrolled</td>
                    <td><?= $statusDistribution['enrolled'] ?></td>
                    <td><?= array_sum($statusDistribution) > 0 ? round(($statusDistribution['enrolled'] / array_sum($statusDistribution)) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Pending</td>
                    <td><?= $statusDistribution['pending'] ?></td>
                    <td><?= array_sum($statusDistribution) > 0 ? round(($statusDistribution['pending'] / array_sum($statusDistribution)) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Approved</td>
                    <td><?= $statusDistribution['approved'] ?></td>
                    <td><?= array_sum($statusDistribution) > 0 ? round(($statusDistribution['approved'] / array_sum($statusDistribution)) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Rejected</td>
                    <td><?= $statusDistribution['rejected'] ?></td>
                    <td><?= array_sum($statusDistribution) > 0 ? round(($statusDistribution['rejected'] / array_sum($statusDistribution)) * 100, 1) : 0 ?>%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Grade Level Distribution</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Grade Level</th>
                    <th>Enrolled Students</th>
                    <th>Average Grade (Q1)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Grade 7</td>
                    <td><?= $gradeDistribution[7] ?></td>
                    <td><?= $gradeAverages[7] > 0 ? $gradeAverages[7] : 'N/A' ?></td>
                </tr>
                <tr>
                    <td>Grade 8</td>
                    <td><?= $gradeDistribution[8] ?></td>
                    <td><?= $gradeAverages[8] > 0 ? $gradeAverages[8] : 'N/A' ?></td>
                </tr>
                <tr>
                    <td>Grade 9</td>
                    <td><?= $gradeDistribution[9] ?></td>
                    <td><?= $gradeAverages[9] > 0 ? $gradeAverages[9] : 'N/A' ?></td>
                </tr>
                <tr>
                    <td>Grade 10</td>
                    <td><?= $gradeDistribution[10] ?></td>
                    <td><?= $gradeAverages[10] > 0 ? $gradeAverages[10] : 'N/A' ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Gender Distribution</div>
        <div class="metric-grid">
            <div class="metric-row">
                <div class="metric-label">Male Students</div>
                <div class="metric-value"><?= $genderDistribution['male'] ?></div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Female Students</div>
                <div class="metric-value"><?= $genderDistribution['female'] ?></div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Total Students</div>
                <div class="metric-value"><?= $genderDistribution['male'] + $genderDistribution['female'] ?></div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Gender Balance Gap</div>
                <div class="metric-value"><?= $metrics['genderBalance'] ?></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Key Performance Metrics</div>
        <div class="metric-grid">
            <div class="metric-row">
                <div class="metric-label">Enrollment Completion Rate</div>
                <div class="metric-value"><?= $metrics['completionRate'] ?>%</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Pending Application Rate</div>
                <div class="metric-value"><?= $metrics['pendingRate'] ?>%</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Approval Rate</div>
                <div class="metric-value"><?= $metrics['approvalRate'] ?>%</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Lourdes Provincial High School - Student Management System | Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        <p>This report contains confidential information. Distribution is restricted to authorized personnel only.</p>
    </div>
</body>
</html>