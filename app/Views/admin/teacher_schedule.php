<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Manage Schedule</h1>
        <h2 class="teacher-name"><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></h2>
    </div>
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm text-muted border-0 active" id="gridViewBtn" onclick="switchView('grid')" style="background: none; color: #495057 !important; opacity: 1;">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" class="btn btn-sm text-muted border-0" id="listViewBtn" onclick="switchView('list')" style="background: none; color: #495057 !important; opacity: 0.5;">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Teachers
        </a>
    </div>
</div>

<style>
.teacher-name {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2563eb;
    margin: 8px 0;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.time-slot-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.time-edit-btn {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 12px;
    padding: 2px;
    cursor: pointer;
    border-radius: 3px;
}

.time-edit-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.time-input {
    width: 60px;
    font-size: 12px;
    text-align: center;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px 4px;
}

.schedule-cell {
    min-height: 80px;
    padding: 8px;
}

.form-select-sm, .form-control-sm {
    font-size: 11px;
    padding: 4px 6px;
}
</style>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Weekly Schedule</h5>
    </div>
    <div class="card-body">
        <?php 
        // Get all unique time slots from existing schedules
        $existingTimeSlots = [];
        foreach ($schedules as $schedule) {
            $timeKey = date('H:i', strtotime($schedule['start_time'])) . '-' . date('H:i', strtotime($schedule['end_time']));
            $existingTimeSlots[$timeKey] = true;
        }
        
        // Default time slots
        $defaultTimeSlots = [
            '07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00',
            '11:00-12:00', '12:00-13:00', '13:00-14:00', '14:00-15:00',
            '15:00-16:00', '16:00-17:00'
        ];
        
        // Merge existing and default time slots, sort by start time
        $allTimeSlots = array_merge($defaultTimeSlots, array_keys($existingTimeSlots));
        $allTimeSlots = array_unique($allTimeSlots);
        
        // Sort time slots by start time
        usort($allTimeSlots, function($a, $b) {
            $startA = explode('-', $a)[0];
            $startB = explode('-', $b)[0];
            return strcmp($startA, $startB);
        });
        
        $timeSlots = $allTimeSlots;
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // Organize existing schedules by day and time
        $scheduleGrid = [];
        foreach ($schedules as $schedule) {
            $timeKey = date('H:i', strtotime($schedule['start_time'])) . '-' . date('H:i', strtotime($schedule['end_time']));
            $scheduleGrid[$schedule['day_of_week']][$timeKey] = $schedule;
        }
        ?>
        
        <!-- List View -->
        <div id="listView" style="display: none;">
            <form id="scheduleFormList">
                <?php foreach ($timeSlots as $index => $timeSlot): ?>
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><?= $timeSlot ?></h6>
                            <div class="time-slot-controls">
                                <button type="button" class="time-edit-btn" onclick="adjustTimeList(<?= $index ?>, 15)" title="+15 minutes">
                                    <i class="bi bi-chevron-up"></i>
                                </button>
                                <button type="button" class="time-edit-btn" onclick="adjustTimeList(<?= $index ?>, -15)" title="-15 minutes">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day): ?>
                            <div class="col-md">
                                <h6 class="text-muted mb-2"><?= $day ?></h6>
                                <?php 
                                $existingSchedule = $scheduleGrid[$day][$timeSlot] ?? null;
                                $startTime = explode('-', $timeSlot)[0];
                                $endTime = explode('-', $timeSlot)[1];
                                ?>
                                <div class="schedule-cell-list" data-day="<?= $day ?>" data-start="<?= $startTime ?>" data-end="<?= $endTime ?>">
                                    <select class="form-select form-select-sm mb-2 subject-select" name="subject" onchange="updateSubjectOptions('<?= $day ?>')">
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?= $subject['id'] ?>" <?= $existingSchedule && $existingSchedule['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                                                <?= esc($subject['subject_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <select class="form-select form-select-sm mb-2 section-select" name="section" onchange="updateSubjectOptions('<?= $day ?>')">
                                        <option value="">Select Section</option>
                                        <?php 
                                        $displayedSections = [];
                                        foreach ($sections as $section): 
                                            $sectionName = $section['section_name'];
                                            $sectionName = preg_replace('/^Grade \d+ - /', '', $sectionName);
                                            $displayText = $sectionName . ' (Grade ' . $section['grade_level'] . ')';
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
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-2"></i>Save Schedule
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Grid View -->
        <div id="gridView">
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
                        
                        <?php foreach ($timeSlots as $index => $timeSlot): ?>
                        <tr>
                            <td class="fw-bold text-center">
                                <div class="time-slot-controls">
                                    <button type="button" class="time-edit-btn" onclick="adjustTime(<?= $index ?>, 'both', 15)" title="+15 minutes">
                                        <i class="bi bi-chevron-up"></i>
                                    </button>
                                    <div class="time-display" onclick="editTimeSlot(<?= $index ?>)" style="cursor: pointer;">
                                        <span class="time-text"><?= $timeSlot ?></span>
                                        <div class="time-inputs" style="display: none;">
                                            <input type="time" class="time-input start-time" value="<?= explode('-', $timeSlot)[0] ?>">
                                            <span>-</span>
                                            <input type="time" class="time-input end-time" value="<?= explode('-', $timeSlot)[1] ?>">
                                        </div>
                                    </div>
                                    <button type="button" class="time-edit-btn" onclick="adjustTime(<?= $index ?>, 'both', -15)" title="-15 minutes">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </td>
                            <?php foreach ($days as $day): ?>
                            <td>
                                <?php 
                                $existingSchedule = $scheduleGrid[$day][$timeSlot] ?? null;
                                $startTime = explode('-', $timeSlot)[0];
                                $endTime = explode('-', $timeSlot)[1];
                                ?>
                                <div class="schedule-cell" data-day="<?= $day ?>" data-start="<?= $startTime ?>" data-end="<?= $endTime ?>">
                                    <select class="form-select form-select-sm mb-1 subject-select" name="subject" onchange="updateSubjectOptions('<?= $day ?>')">
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?= $subject['id'] ?>" <?= $existingSchedule && $existingSchedule['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                                                <?= esc($subject['subject_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <select class="form-select form-select-sm mb-1 section-select" name="section" onchange="updateSubjectOptions('<?= $day ?>')">
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
</div>

<script>
let timeSlots = <?= json_encode($timeSlots) ?>;
let hasExistingSchedules = <?= json_encode(!empty($schedules)) ?>;

function switchView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (viewType === 'grid') {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        gridBtn.style.opacity = '1';
        listBtn.style.opacity = '0.5';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        gridBtn.style.opacity = '0.5';
        listBtn.style.opacity = '1';
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
    }
}

function adjustTimeList(index, amount) {
    const timeHeaders = document.querySelectorAll('#listView .card-header h6');
    const timeHeader = timeHeaders[index];
    const currentTime = timeHeader.textContent;
    const [startTime, endTime] = currentTime.split('-');
    
    const newStart = addMinutes(startTime, amount);
    const newEnd = addMinutes(endTime, amount);
    
    const newTimeSlot = newStart + '-' + newEnd;
    timeSlots[index] = newTimeSlot;
    timeHeader.textContent = newTimeSlot;
    
    // Update all schedule cells for this time slot
    const card = timeHeader.closest('.card');
    const cells = card.querySelectorAll('.schedule-cell-list');
    cells.forEach(cell => {
        cell.dataset.start = newStart;
        cell.dataset.end = newEnd;
    });
}

function updateSubjectOptions(day) {
    const daySelects = document.querySelectorAll(`[data-day="${day}"] .subject-select`);
    const daySectionSelects = document.querySelectorAll(`[data-day="${day}"] .section-select`);
    const selectedSubjects = [];
    const selectedSections = [];
    
    // Get all selected subjects and sections for this day
    daySelects.forEach(select => {
        if (select.value) {
            selectedSubjects.push(select.value);
        }
    });
    
    daySectionSelects.forEach(select => {
        if (select.value) {
            selectedSections.push(select.value);
        }
    });
    
    // Update all subject selects for this day
    daySelects.forEach(select => {
        const currentValue = select.value;
        const options = select.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Keep "Select Subject" option
            
            if (selectedSubjects.includes(option.value) && option.value !== currentValue) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });
    });
    
    // Update all section selects for this day
    daySectionSelects.forEach(select => {
        const currentValue = select.value;
        const options = select.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Keep "Select Section" option
            
            if (selectedSections.includes(option.value) && option.value !== currentValue) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });
    });
}

function adjustTime(index, type, amount) {
    const timeDisplay = document.querySelectorAll('.time-display')[index];
    const timeText = timeDisplay.querySelector('.time-text');
    const currentTime = timeText.textContent;
    const [startTime, endTime] = currentTime.split('-');
    
    const newStart = addMinutes(startTime, amount);
    const newEnd = addMinutes(endTime, amount);
    
    const newTimeSlot = newStart + '-' + newEnd;
    timeSlots[index] = newTimeSlot;
    timeText.textContent = newTimeSlot;
    
    // Update all schedule cells for this time slot
    const row = timeDisplay.closest('tr');
    const cells = row.querySelectorAll('.schedule-cell');
    cells.forEach(cell => {
        cell.dataset.start = newStart;
        cell.dataset.end = newEnd;
    });
}

function addMinutes(time, minutes) {
    const [hours, mins] = time.split(':').map(Number);
    let totalMinutes = hours * 60 + mins + minutes;
    
    // Keep within 24-hour format
    if (totalMinutes < 0) totalMinutes = 0;
    if (totalMinutes >= 24 * 60) totalMinutes = 23 * 60 + 59;
    
    const newHours = Math.floor(totalMinutes / 60);
    const newMins = totalMinutes % 60;
    
    return String(newHours).padStart(2, '0') + ':' + String(newMins).padStart(2, '0');
}

function editTimeSlot(index) {
    const timeDisplay = document.querySelectorAll('.time-display')[index];
    const timeText = timeDisplay.querySelector('.time-text');
    const timeInputs = timeDisplay.querySelector('.time-inputs');
    
    if (timeInputs.style.display === 'none') {
        timeText.style.display = 'none';
        timeInputs.style.display = 'flex';
        timeInputs.style.alignItems = 'center';
        timeInputs.style.gap = '4px';
        
        const startInput = timeInputs.querySelector('.start-time');
        const endInput = timeInputs.querySelector('.end-time');
        
        const saveTime = () => {
            const newTimeSlot = startInput.value + '-' + endInput.value;
            timeSlots[index] = newTimeSlot;
            timeText.textContent = newTimeSlot;
            timeText.style.display = 'block';
            timeInputs.style.display = 'none';
            
            // Update all schedule cells for this time slot
            const row = timeDisplay.closest('tr');
            const cells = row.querySelectorAll('.schedule-cell');
            cells.forEach(cell => {
                cell.dataset.start = startInput.value;
                cell.dataset.end = endInput.value;
            });
        };
        
        startInput.addEventListener('blur', saveTime);
        endInput.addEventListener('blur', saveTime);
        startInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') saveTime();
        });
        endInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') saveTime();
        });
        
        startInput.focus();
    }
}

function rebuildScheduleTable() {
    location.reload(); // Simple approach - reload page with new time slots
}

// Initialize subject filtering for all days
document.addEventListener('DOMContentLoaded', function() {
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    days.forEach(day => {
        updateSubjectOptions(day);
    });
});

document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    saveSchedule(e, '.schedule-cell');
});

document.getElementById('scheduleFormList').addEventListener('submit', function(e) {
    saveSchedule(e, '.schedule-cell-list');
});

function saveSchedule(e, cellSelector) {
    e.preventDefault();
    
    const schedules = [];
    const cells = document.querySelectorAll(cellSelector);
    
    cells.forEach(cell => {
        const subjectSelect = cell.querySelector('.subject-select');
        const sectionSelect = cell.querySelector('.section-select');
        const roomInput = cell.querySelector('.room-input');
        
        if (subjectSelect.value && sectionSelect.value) {
            // Convert time format for database
            const startTime = cell.dataset.start + ':00';
            const endTime = cell.dataset.end + ':00';
            
            schedules.push({
                subject_id: subjectSelect.value,
                section_id: sectionSelect.value,
                day_of_week: cell.dataset.day,
                start_time: startTime,
                end_time: endTime,
                room: roomInput.value || ''
            });
        }
    });
    
    console.log('Saving schedules:', schedules); // Debug log
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
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
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data); // Debug log
        if (data.success) {
            showNotification('Schedule saved successfully!', 'success');
            // Don't reload automatically to preserve custom time slots
        } else {
            showNotification('Error: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to save schedule: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
function showNotification(message, type) {
    // Remove existing notifications
    const existing = document.querySelector('.schedule-notification');
    if (existing) existing.remove();
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `schedule-notification alert alert-${type === 'success' ? 'success' : 'danger'}`;
    notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

</script>

<?= $this->endSection() ?>