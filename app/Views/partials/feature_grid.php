<?php if (!empty($features) && is_array($features)): ?>
  <div class="row g-3">
    <?php foreach ($features as $feature): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <a href="<?= esc($feature['href'] ?? '#') ?>" class="text-decoration-none">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex">
              <div class="me-3 text-primary" style="font-size:1.5rem;">
                <i class="bi <?= esc($feature['icon'] ?? 'bi-dot') ?>"></i>
              </div>
              <div>
                <h5 class="card-title mb-1"><?= esc($feature['title'] ?? '') ?></h5>
                <p class="card-text text-muted small mb-0">
                  <?= esc($feature['description'] ?? '') ?>
                </p>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>



