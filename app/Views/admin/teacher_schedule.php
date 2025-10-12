<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Manage Schedule</h1>
        <p class="text-muted mb-0"><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></p>
    </div>
    <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Teachers
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Weekly Schedule</h5>
    </div>
    <div class="card-body">
        <form id="scheduleForm">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="15%">Time</th>
                            <th width="17%">Monday</th>
                            <th width="17%">Tuesday</th>
                            <th width="17%">Wednesday</th>
                            <th width="17%">Thursday</th>
                            <th width="17%">Friday</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTable">
                        <?php 
                        $timeSlots = [
                            '07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00',
                            '11:00-12:00', '12:00-13:00', '13:00-14:00', '14:00-15:00',
                            '15:00-16:00', '16:00-17:00'
                        ];
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        
                        // Organize existing schedules by day and time
                        $scheduleGrid = [];
                        foreach ($schedules as $schedule) {
                            $timeKey = date('H:i', strtotime($schedule['start_time'])) . '-' . date('H:i', strtotime($schedule['end_time']));
                            $scheduleGrid[$schedule['day_of_week']][$timeKey] = $schedule;
                        }
                        ?>
                        
                        <?php foreach ($timeSlots as $timeSlot): ?>
                        <tr>
                            <td class="fw-bold text-center"><?= $timeSlot ?></td>
                            <?php foreach ($days as $day): ?>
                            <td>
                                <?php 
                                $existingSchedule = $scheduleGrid[$day][$timeSlot] ?? null;
                                $startTime = explode('-', $timeSlot)[0];
                                $endTime = explode('-', $timeSlot)[1];
                                ?>
                                <div class="schedule-cell" data-day="<?= $day ?>" data-start="<?= $startTime ?>" data-end="<?= $endTime ?>">
                                    <select class="form-select form-select-sm mb-1 subject-select" name="subject">
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?= $subject['id'] ?>" <?= $existingSchedule && $existingSchedule['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                                                <?= esc($subject['subject_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <select class="form-select form-select-sm mb-1 section-select" name="section">
                                        <option value="">Select Section</option>
                                        <?php 
                                        $displayedSections = [];
                                        foreach ($sections as $section): 
                                            // Remove "Grade X - " prefix if it exists
                                            $sectionName = $section['section_name'];
                                            $sectionName = preg_replace('/^Grade \d+ - /', '', $sectionName);
                                            $displayText = $sectionName . ' (Grade ' . $section['grade_level'] . ')';
                                            
                                            // Skip if already displayed
                                            if (in_array($displayText, $displayedSections)) continue;
                                            $displayedSections[] = $displayText;
                                        ?>
                                            <option value="<?= $section['id'] ?>" <?= $existingSchedule && $existingSchedule['section_id'] == $section['id'] ? 'selected' : '' ?>>
                                                <?= esc($displayText) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <input type="text" class="form-control form-control-sm room-input" name="room" 
                                           placeholder="Room" value="<?= esc($existingSchedule['room'] ?? '') ?>">
                                </div>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle me-2"></i>Save Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const schedules = [];
    const cells = document.querySelectorAll('.schedule-cell');
    
    cells.forEach(cell => {
        const subjectSelect = cell.querySelector('.subject-select');
        const sectionSelect = cell.querySelector('.section-select');
        const roomInput = cell.querySelector('.room-input');
        
        if (subjectSelect.value && sectionSelect.value) {
            schedules.push({
                subject_id: subjectSelect.value,
                section_id: sectionSelect.value,
                day_of_week: cell.dataset.day,
                start_time: cell.dataset.start,
                end_time: cell.dataset.end,
                room: roomInput.value
            });
        }
    });
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    submitBtn.disabled = true;
    
    fetch('<?= base_url('admin/teachers/schedule/save/' . $teacher['id']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ schedules: schedules })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Schedule saved successfully!');
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save schedule');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<?= $this->endSection() ?>