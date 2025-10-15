<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge text-primary"></i>
                        <?= esc($student['first_name'] . ' ' . $student['last_name']) ?> - Academic Record
                    </h5>
                    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i> Close
                    </button>
                </div>
                <div class="card-body">
                    <!-- Student Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>LRN:</strong> <?= esc($student['lrn']) ?></p>
                            <p><strong>Grade Level:</strong> <?= esc($student['grade_level']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>School Year:</strong> <?= esc($schoolYear) ?></p>
                            <p><strong>GWA:</strong> 
                                <span class="badge bg-<?= $gwa >= 90 ? 'success' : ($gwa >= 85 ? 'info' : ($gwa >= 75 ? 'warning' : 'danger')) ?> text-white">
                                    <?= $gwa ? number_format($gwa, 2) : 'N/A' ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Quarter Tabs -->
                    <ul class="nav nav-tabs" id="quarterTabs" role="tablist">
                        <?php for ($q = 1; $q <= 4; $q++): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $q === 1 ? 'active' : '' ?>" 
                                        id="quarter<?= $q ?>-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#quarter<?= $q ?>" 
                                        type="button" role="tab">
                                    Quarter <?= $q ?>
                                    <?php if (isset($allQuarterGrades[$q]) && $allQuarterGrades[$q] !== null): ?>
                                        <span class="badge bg-secondary ms-1"><?= number_format($allQuarterGrades[$q], 1) ?></span>
                                    <?php endif; ?>
                                </button>
                            </li>
                        <?php endfor; ?>
                    </ul>

                    <!-- Quarter Content -->
                    <div class="tab-content" id="quarterTabsContent">
                        <?php for ($q = 1; $q <= 4; $q++): ?>
                            <div class="tab-pane fade <?= $q === 1 ? 'show active' : '' ?>" 
                                 id="quarter<?= $q ?>" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Code</th>
                                                <th>Grade</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($grades[$q])): ?>
                                                <?php foreach ($grades[$q] as $gradeData): ?>
                                                    <tr>
                                                        <td><?= esc($gradeData['subject']['subject_name']) ?></td>
                                                        <td><span class="badge bg-light text-dark"><?= esc($gradeData['subject']['subject_code']) ?></span></td>
                                                        <td>
                                                            <?php if ($gradeData['grade']): ?>
                                                                <span class="badge bg-<?= $gradeData['grade']['grade'] >= 90 ? 'success' : ($gradeData['grade']['grade'] >= 85 ? 'info' : ($gradeData['grade']['grade'] >= 75 ? 'warning' : 'danger')) ?> text-white">
                                                                    <?= number_format($gradeData['grade']['grade'], 2) ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">Not graded</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($gradeData['grade'] && isset($gradeData['grade']['remarks'])): ?>
                                                                <?= esc($gradeData['grade']['remarks']) ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">--</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="2"><strong class="fs-5">Quarter Average:</strong></td>
                                                <td>
                                                    <?php if (isset($allQuarterGrades[$q]) && $allQuarterGrades[$q] !== null): ?>
                                                        <span class="badge bg-<?= $allQuarterGrades[$q] >= 90 ? 'success' : ($allQuarterGrades[$q] >= 85 ? 'info' : ($allQuarterGrades[$q] >= 75 ? 'warning' : 'danger')) ?> text-white fs-5 px-3 py-2">
                                                            <?= number_format($allQuarterGrades[$q], 2) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted fs-5">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($allQuarterGrades[$q]) && $allQuarterGrades[$q] !== null): ?>
                                                        <span class="badge bg-<?= $allQuarterGrades[$q] >= 75 ? 'success' : 'danger' ?> text-white fs-5 px-3 py-2">
                                                            <?= $allQuarterGrades[$q] >= 75 ? 'PASSED' : 'FAILED' ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted fs-5">--</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>