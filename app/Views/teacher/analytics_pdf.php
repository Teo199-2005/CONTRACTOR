<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LPHS Teacher Analytics Report</title>
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
        <div class="report-title">Teacher Class Analytics Report</div>
        <div class="report-info">Teacher: <?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
        <div class="report-info">School Year: <?= $schoolYear ?> | Quarter: <?= $currentQuarter ?></div>
        <div class="report-info">Report Generated: <?= $reportDate ?></div>
    </div>

    <div class="section">
        <div class="section-title">Class Overview</div>
        <div class="summary-box">
            <p><strong>Total Students:</strong> <?= $analytics['totalStudents'] ?></p>
            <p><strong>Total Subjects:</strong> <?= $analytics['totalSubjects'] ?></p>
            <p><strong>Class Average:</strong> <?= number_format($analytics['classAverage'], 1) ?>%</p>
            <p><strong>Attendance Rate:</strong> <?= number_format($analytics['attendanceRate'], 1) ?>%</p>
            <p><strong>Improvement Rate:</strong> +<?= number_format($analytics['improvementRate'], 1) ?>%</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Grade Distribution</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Grade Range</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Excellent (90-100)</td>
                    <td><?= $analytics['gradeDistribution']['excellent'] ?></td>
                    <td><?= $analytics['totalStudents'] > 0 ? round(($analytics['gradeDistribution']['excellent'] / $analytics['totalStudents']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Very Good (85-89)</td>
                    <td><?= $analytics['gradeDistribution']['very_good'] ?></td>
                    <td><?= $analytics['totalStudents'] > 0 ? round(($analytics['gradeDistribution']['very_good'] / $analytics['totalStudents']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Good (80-84)</td>
                    <td><?= $analytics['gradeDistribution']['good'] ?></td>
                    <td><?= $analytics['totalStudents'] > 0 ? round(($analytics['gradeDistribution']['good'] / $analytics['totalStudents']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Fair (75-79)</td>
                    <td><?= $analytics['gradeDistribution']['fair'] ?></td>
                    <td><?= $analytics['totalStudents'] > 0 ? round(($analytics['gradeDistribution']['fair'] / $analytics['totalStudents']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Passing (70-74)</td>
                    <td><?= $analytics['gradeDistribution']['passing'] ?></td>
                    <td><?= $analytics['totalStudents'] > 0 ? round(($analytics['gradeDistribution']['passing'] / $analytics['totalStudents']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Failing (<70)</td>
                    <td><?= $analytics['gradeDistribution']['failing'] ?></td>
                    <td><?= $analytics['totalStudents'] > 0 ? round(($analytics['gradeDistribution']['failing'] / $analytics['totalStudents']) * 100, 1) : 0 ?>%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (!empty($analytics['subjectAverages'])): ?>
    <div class="section">
        <div class="section-title">Subject Performance</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Average Grade</th>
                    <th>Students Graded</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($analytics['subjectAverages'] as $subject): ?>
                <tr>
                    <td><?= esc($subject['subject']) ?></td>
                    <td><?= number_format($subject['average'], 1) ?>%</td>
                    <td><?= $subject['count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if (!empty($analytics['studentPerformance'])): ?>
    <div class="section">
        <div class="section-title">Top Performing Students</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Student Name</th>
                    <th>Average Grade</th>
                    <th>Subjects</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Sort students by average (descending)
                usort($analytics['studentPerformance'], function($a, $b) {
                    return $b['average'] <=> $a['average'];
                });
                ?>
                <?php foreach (array_slice($analytics['studentPerformance'], 0, 10) as $index => $student): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($student['name']) ?></td>
                    <td><?= number_format($student['average'], 1) ?>%</td>
                    <td><?= $student['grade_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-title">Quarter Performance Trends</div>
        <div class="metric-grid">
            <?php foreach ($analytics['quarterTrends'] as $trend): ?>
            <div class="metric-row">
                <div class="metric-label"><?= esc($trend['quarter']) ?></div>
                <div class="metric-value"><?= number_format($trend['average'], 1) ?>%</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Recommendations</div>
        <div class="summary-box">
            <?php if (($analytics['classAverage'] ?? 0) >= 85): ?>
                <p><strong>Performance Status:</strong> Excellent! Your class is performing exceptionally well with an average of <?= number_format($analytics['classAverage'], 1) ?>%.</p>
            <?php elseif (($analytics['classAverage'] ?? 0) >= 75): ?>
                <p><strong>Performance Status:</strong> Good progress! Class average is <?= number_format($analytics['classAverage'], 1) ?>%. Consider targeted support for struggling students.</p>
            <?php else: ?>
                <p><strong>Performance Status:</strong> Needs attention. Class average is <?= number_format($analytics['classAverage'], 1) ?>%. Implement intervention strategies.</p>
            <?php endif; ?>
            
            <p><strong>Attendance Impact:</strong> High attendance rate of <?= number_format($analytics['attendanceRate'], 1) ?>% correlates with better academic performance.</p>
            
            <?php if (!empty($analytics['subjectAverages'])): ?>
                <?php
                $lowestSubject = array_reduce($analytics['subjectAverages'], function($carry, $item) {
                    return (!$carry || $item['average'] < $carry['average']) ? $item : $carry;
                });
                ?>
                <p><strong>Subject Focus:</strong> Consider additional support for <?= esc($lowestSubject['subject']) ?> (<?= number_format($lowestSubject['average'], 1) ?>% average).</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>Lourdes Provincial High School - Teacher Analytics Report | Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        <p>This report contains confidential information. Distribution is restricted to authorized personnel only.</p>
    </div>
</body>
</html>