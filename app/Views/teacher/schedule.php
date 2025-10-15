<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Schedule</h1>
    <?php if ($teacher): ?>
        <span class="text-muted"><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></span>
    <?php endif; ?>
</div>

<?php if (!empty($schedules)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Weekly Teaching Schedule</h5>
        </div>
        <div class="card-body">
            <!-- Schedule List -->
            <div id="scheduleList">
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
                        
                        // Organize schedules by day and time
                        $scheduleGrid = [];
                        foreach ($schedules as $schedule) {
                            $timeKey = date('H:i', strtotime($schedule['start_time'])) . '-' . date('H:i', strtotime($schedule['end_time']));
                            $scheduleGrid[$schedule['day_of_week']][$timeKey] = $schedule;
                        }
                        ?>
                        
                <?php foreach ($timeSlots as $timeSlot): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold"><?= $timeSlot ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($days as $day): ?>
                            <div class="col-md-2 mb-3">
                                <h6 class="text-center mb-2"><?= $day ?></h6>
                                <?php 
                                $schedule = $scheduleGrid[$day][$timeSlot] ?? null;
                                if ($schedule): 
                                ?>
                                    <div class="schedule-item p-2 bg-primary text-white rounded text-center">
                                        <div class="fw-bold small"><?= esc($schedule['subject_name']) ?></div>
                                        <small class="text-white"><?= esc($schedule['section_name']) ?></small>
                                        <?php if ($schedule['room']): ?>
                                            <div><small class="text-white"><i class="bi bi-geo-alt"></i> <?= esc($schedule['room']) ?></small></div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3 border rounded">
                                        <small>Free Period</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar3 fs-1 text-muted mb-3"></i>
            <h5 class="text-muted">No Schedule Available</h5>
            <p class="text-muted mb-0">Your teaching schedule has not been set up yet. Please contact the administrator.</p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?> 