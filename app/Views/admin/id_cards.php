<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
#gridViewContainer {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 1.5rem !important;
}
#gridViewContainer .col {
  width: 100% !important;
  max-width: 100% !important;
  flex: none !important;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0"><i class="bi bi-person-badge me-2"></i>Student ID Cards</h1>
  <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Grade Level</label>
        <select class="form-select" name="grade">
          <option value="">All Grades</option>
          <?php for ($grade = 7; $grade <= 12; $grade++): ?>
            <option value="<?= $grade ?>" <?= $gradeFilter == $grade ? 'selected' : '' ?>>Grade <?= $grade ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Section</label>
        <select class="form-select" name="section">
          <option value="">All Sections</option>
          <?php foreach ($allSections as $section): ?>
            <option value="<?= $section['id'] ?>" <?= $sectionFilter == $section['id'] ? 'selected' : '' ?>>
              Grade <?= $section['grade_level'] ?> - <?= esc($section['section_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Search Student</label>
        <input type="text" class="form-control" name="search" value="<?= esc($searchTerm) ?>" placeholder="Name or Student ID">
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Student Cards -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Student ID Cards (<?= $totalStudents ?> students)</h5>
      <div class="d-flex gap-2">
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-outline-secondary btn-sm" id="gridView" onclick="toggleView('grid')">
            <i class="bi bi-grid-3x3-gap"></i>
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" id="listView" onclick="toggleView('list')">
            <i class="bi bi-list"></i>
          </button>
        </div>
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print All</button>
      </div>
    </div>
  </div>
  <div class="card-body">
    <?php if (empty($students)): ?>
      <div class="text-center py-5">
        <i class="bi bi-person-badge fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No Students Found</h5>
        <p class="text-muted">Try adjusting your filters or search criteria.</p>
      </div>
    <?php else: ?>
      <!-- Grid View -->
      <div id="gridViewContainer">
        <?php foreach ($students as $student): ?>
        <div class="col">
          <div class="card border shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                  <?php if ($student['photo_path']): ?>
                    <img src="<?= base_url($student['photo_path']) ?>" alt="Photo" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                  <?php else: ?>
                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="bi bi-person text-white"></i>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1"><?= esc($student['last_name'] . ', ' . $student['first_name']) ?></h6>
                  <small class="text-muted">ID: <?= esc($student['student_id'] ?? 'Pending') ?></small>
                </div>
              </div>
              
              <div class="mb-3">
                <span class="badge bg-primary me-2">Grade <?= esc($student['grade_level']) ?></span>
                <?php if ($student['section_name']): ?>
                  <span class="badge bg-secondary"><?= esc($student['section_name']) ?></span>
                <?php endif; ?>
              </div>
              
              <div class="d-grid gap-2">
                <a href="<?= base_url('admin/id-cards/view/' . $student['id']) ?>" class="btn btn-primary btn-sm">
                  <i class="bi bi-eye me-1"></i>View ID Card
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <!-- List View -->
      <div id="listViewContainer" class="d-none">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Student ID</th>
                <th>Grade & Section</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $student): ?>
              <tr>
                <td>
                  <?php if ($student['photo_path']): ?>
                    <img src="<?= base_url($student['photo_path']) ?>" alt="Photo" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                  <?php else: ?>
                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                      <i class="bi bi-person text-white"></i>
                    </div>
                  <?php endif; ?>
                </td>
                <td><?= esc($student['last_name'] . ', ' . $student['first_name']) ?></td>
                <td><?= esc($student['student_id'] ?? 'Pending') ?></td>
                <td>
                  <span class="badge bg-primary me-1">Grade <?= esc($student['grade_level']) ?></span>
                  <?php if ($student['section_name']): ?>
                    <span class="badge bg-secondary"><?= esc($student['section_name']) ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?= base_url('admin/id-cards/view/' . $student['id']) ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>View ID Card
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>
  
  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="card-footer">
    <nav>
      <ul class="pagination justify-content-center mb-0">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">
            <i class="bi bi-chevron-left"></i> Previous
          </a>
        </li>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">
            Next <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleView(view) {
  const gridView = document.getElementById('gridViewContainer');
  const listView = document.getElementById('listViewContainer');
  const gridBtn = document.getElementById('gridView');
  const listBtn = document.getElementById('listView');
  
  if (view === 'grid') {
    gridView.classList.remove('d-none');
    listView.classList.add('d-none');
    gridBtn.classList.add('active');
    listBtn.classList.remove('active');
  } else {
    gridView.classList.add('d-none');
    listView.classList.remove('d-none');
    gridBtn.classList.remove('active');
    listBtn.classList.add('active');
  }
}

// Set default view
document.addEventListener('DOMContentLoaded', function() {
  toggleView('grid');
});
</script>

<?= $this->endSection() ?>



