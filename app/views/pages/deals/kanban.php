<style>
  /* Modern CSS Variables */
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --warning-gradient: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
    --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --card-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
    --card-shadow-hover: 0 12px 40px rgba(31, 38, 135, 0.25);
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: 1px solid rgba(255, 255, 255, 0.18);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Main Container */
  .modern-kanban-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
    position: relative;
  }

  .modern-kanban-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="7" cy="7" r="7"/></g></svg>') repeat;
    pointer-events: none;
  }

  .glass-header {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    border: var(--glass-border);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--card-shadow);
    position: relative;
    overflow: hidden;
  }

  .glass-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
  }

  .kanban-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  }

  .breadcrumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin: 1rem 0 0 0;
  }

  .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: var(--transition);
  }

  .breadcrumb-item a:hover {
    color: white;
  }

  .breadcrumb-item.active {
    color: rgba(255, 255, 255, 0.9);
  }

  /* Kanban Board Grid */
  .kanban-board-wrapper {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.5rem;
    padding: 0 1rem;
  }

  /* Responsive Grid */
  @media (max-width: 1400px) {
    .kanban-board-wrapper {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  @media (max-width: 1100px) {
    .kanban-board-wrapper {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .kanban-board-wrapper {
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
      padding: 0 0.5rem;
    }
  }

  @media (max-width: 576px) {
    .kanban-board-wrapper {
      grid-template-columns: 1fr;
    }
  }

  /* Column Styling */
  .kanban-column {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    border: var(--glass-border);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    transition: var(--transition);
    min-height: 600px;
  }

  .kanban-column:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
  }

  .kanban-column-header {
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  .kanban-column-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.9;
    z-index: 1;
  }

  .kanban-column-header h5 {
    position: relative;
    z-index: 2;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  }

  .stage-icon {
    margin-right: 0.75rem;
    font-size: 1.3rem;
  }

  .stage-count {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  /* Stage Colors */
  .stage-analisis-kebutuhan .kanban-column-header::before {
    background: var(--info-gradient);
  }

  .stage-proposal .kanban-column-header::before {
    background: var(--primary-gradient);
  }

  .stage-negosiasi .kanban-column-header::before {
    background: var(--warning-gradient);
  }

  .stage-menang .kanban-column-header::before {
    background: var(--success-gradient);
  }

  .stage-kalah .kanban-column-header::before {
    background: var(--danger-gradient);
  }

  /* Cards Container */
  .kanban-cards {
    padding: 1rem;
    min-height: 200px;
    max-height: 65vh;
    overflow-y: auto;
    transition: var(--transition);
  }

  .kanban-cards::-webkit-scrollbar {
    width: 8px;
  }

  .kanban-cards::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
  }

  .kanban-cards::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
  }

  .kanban-cards::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
  }

  /* Drag & Drop States */
  .kanban-cards.drag-over {
    background: rgba(255, 255, 255, 0.15);
    border: 2px dashed rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    transform: scale(1.02);
  }

  /* Card Styling */
  .kanban-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    cursor: <?= can('update', 'deals') ? 'grab' : 'default' ?>;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
  }

  .kanban-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition);
  }

  .kanban-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    border-color: rgba(102, 126, 234, 0.3);
  }

  .kanban-card:hover::before {
    opacity: 1;
  }

  .kanban-card.dragging {
    opacity: 0.7;
    cursor: grabbing;
    transform: rotate(3deg) scale(1.05);
    z-index: 1000;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  }

  /* Card Content */
  .card-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: #2d3748;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-title a {
    color: inherit;
    text-decoration: none;
    transition: var(--transition);
  }

  .card-title a:hover {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .card-company {
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 1rem;
    font-weight: 500;
  }

  .card-text {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .card-text i {
    width: 16px;
    text-align: center;
    color: #94a3b8;
  }

  .card-value {
    font-size: 1.2rem;
    font-weight: 800;
    color: #059669;
    text-align: center;
    padding: 0.75rem;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 8px;
    margin: 1rem 0;
    box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
  }

  .product-list {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px dashed rgba(148, 163, 184, 0.3);
  }

  .product-list .card-text {
    font-size: 0.8rem;
    color: #475569;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }

  .product-names {
    font-size: 0.75rem;
    color: #64748b;
    background: rgba(148, 163, 184, 0.1);
    padding: 0.5rem;
    border-radius: 6px;
    line-height: 1.4;
  }

  /* Empty State */
  .empty-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 1rem;
    text-align: center;
  }

  .empty-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
  }

  /* View Toggle Button */
  .view-toggle {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    border: var(--glass-border);
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    color: white;
    text-decoration: none;
    transition: var(--transition);
    font-weight: 600;
    box-shadow: var(--card-shadow);
  }

  .view-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
  }

  /* Animation */
  @keyframes slideInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .kanban-column {
    animation: slideInUp 0.6s ease forwards;
  }

  .kanban-column:nth-child(1) {
    animation-delay: 0.1s;
  }

  .kanban-column:nth-child(2) {
    animation-delay: 0.2s;
  }

  .kanban-column:nth-child(3) {
    animation-delay: 0.3s;
  }

  .kanban-column:nth-child(4) {
    animation-delay: 0.4s;
  }

  .kanban-column:nth-child(5) {
    animation-delay: 0.5s;
  }
</style>

<div class="modern-kanban-wrapper">
  <div class="container-fluid px-4">
    <div class="glass-header" data-aos="fade-up">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h1 class="kanban-title">Papan Kanban Kesepakatan</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Papan Kanban</li>
            </ol>
          </nav>
        </div>
        <a href="<?= BASE_URL; ?>/deals" class="view-toggle" data-aos="fade-up" data-aos-delay="100">
          <i class="bi bi-table me-2"></i>Tampilan Tabel
        </a>
      </div>
    </div>

    <div class="kanban-board-wrapper">
      <?php
      $stageConfig = [
        'Analisis Kebutuhan' => ['class' => 'stage-analisis-kebutuhan', 'icon' => 'bi-search'],
        'Proposal' => ['class' => 'stage-proposal', 'icon' => 'bi-file-earmark-text'],
        'Negosiasi' => ['class' => 'stage-negosiasi', 'icon' => 'bi-chat-dots'],
        'Menang' => ['class' => 'stage-menang', 'icon' => 'bi-trophy'],
        'Kalah' => ['class' => 'stage-kalah', 'icon' => 'bi-x-circle']
      ];
      ?>

      <?php foreach ($data['dealsByStage'] as $stage => $deals): ?>
        <?php $stageSlug = str_replace(' ', '-', strtolower($stage)); ?>
        <div class="kanban-column <?= $stageConfig[$stage]['class'] ?? '' ?>">
          <div class="kanban-column-header">
            <h5>
              <span>
                <i class="bi <?= $stageConfig[$stage]['icon'] ?? 'bi-circle' ?> stage-icon"></i>
                <?= htmlspecialchars($stage) ?>
              </span>
              <span class="stage-count" id="count-<?= $stageSlug ?>">
                <?= count($deals) ?>
              </span>
            </h5>
          </div>
          <div class="kanban-cards" data-stage="<?= htmlspecialchars($stage) ?>">
            <?php if (empty($deals)): ?>
              <div class="empty-placeholder">
                <i class="bi bi-inbox"></i>
                <div>Kosong</div>
              </div>
            <?php else: ?>
              <?php foreach ($deals as $deal): ?>
                <div class="kanban-card" data-id="<?= $deal->deal_id ?>" <?= can('update', 'deals') ? 'draggable="true"' : '' ?>>
                  <h6 class="card-title">
                    <a href="<?= BASE_URL; ?>/deals/detail/<?= $deal->deal_id; ?>">
                      <?= htmlspecialchars($deal->name) ?>
                    </a>
                  </h6>

                  <p class="card-company">
                    <?= htmlspecialchars($deal->company_name) ?>
                  </p>

                  <div class="card-text">
                    <i class="bi bi-person-fill"></i>
                    <span><?= htmlspecialchars($deal->owner_name) ?></span>
                  </div>

                  <div class="card-value">
                    Rp <?= number_format($deal->value, 0, ',', '.') ?>
                  </div>

                  <?php if (!empty($deal->product_names)): ?>
                    <div class="product-list">
                      <div class="card-text">
                        <i class="bi bi-box-seam-fill"></i>
                        <strong>Produk:</strong>
                      </div>
                      <div class="product-names">
                        <?= htmlspecialchars($deal->product_names) ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Cek jika user tidak punya izin, jangan jalankan script
    <?php if (!can('update', 'deals')): ?>
      return;
    <?php endif; ?>

    const cards = document.querySelectorAll('.kanban-card[draggable="true"]');
    const columns = document.querySelectorAll('.kanban-cards');
    let draggedCard = null;

    cards.forEach(card => {
      card.addEventListener('dragstart', (e) => {
        draggedCard = card;
        setTimeout(() => {
          card.classList.add('dragging');
        }, 0);
        e.dataTransfer.effectAllowed = 'move';
      });

      card.addEventListener('dragend', () => {
        if (draggedCard) {
          draggedCard.classList.remove('dragging');
        }
        draggedCard = null;
      });
    });

    columns.forEach(column => {
      column.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        e.currentTarget.classList.add('drag-over');
      });

      column.addEventListener('dragleave', (e) => {
        e.currentTarget.classList.remove('drag-over');
      });

      column.addEventListener('drop', (e) => {
        e.preventDefault();
        const targetColumn = e.currentTarget;
        targetColumn.classList.remove('drag-over');

        if (draggedCard && targetColumn !== draggedCard.parentElement) {
          const originalColumn = draggedCard.parentElement;
          const dealId = draggedCard.dataset.id;
          const newStage = targetColumn.dataset.stage;

          // Hapus placeholder jika ada
          const placeholder = targetColumn.querySelector('.empty-placeholder');
          if (placeholder) {
            placeholder.remove();
          }

          targetColumn.appendChild(draggedCard);
          updateDealStage(dealId, newStage, originalColumn, draggedCard);
          updateCardCounts();
        }
      });
    });

    function updateDealStage(dealId, newStage, originalColumn, cardElement) {
      const url = `<?= BASE_URL; ?>/deals/updateStage`;

      fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            deal_id: dealId,
            stage: newStage
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success || (data.status && data.status === 'success')) {
            showToast('success', 'Deal berhasil dipindahkan!');
          } else {
            showToast('error', data.message || 'Gagal memindahkan deal.');
            // Kembalikan kartu jika gagal
            originalColumn.appendChild(cardElement);
            updateCardCounts();
          }
        })
        .catch(error => {
          showToast('error', 'Terjadi kesalahan jaringan.');
          // Kembalikan kartu jika error
          originalColumn.appendChild(cardElement);
          updateCardCounts();
          console.error('Error:', error);
        });
    }

    function updateCardCounts() {
      columns.forEach(column => {
        const stageSlug = column.dataset.stage.replace(/ /g, '-').toLowerCase();
        const countElement = document.getElementById(`count-${stageSlug}`);
        const cardCount = column.querySelectorAll('.kanban-card').length;

        if (countElement) {
          countElement.innerText = cardCount;
        }

        // Tambah atau hapus placeholder
        const placeholder = column.querySelector('.empty-placeholder');
        if (cardCount === 0 && !placeholder) {
          const newPlaceholder = document.createElement('div');
          newPlaceholder.className = 'empty-placeholder';
          newPlaceholder.innerHTML = `<i class="bi bi-inbox"></i><div>Kosong</div>`;
          column.appendChild(newPlaceholder);
        } else if (cardCount > 0 && placeholder) {
          placeholder.remove();
        }
      });
    }

    function showToast(type, message) {
      const existingToast = document.querySelector('.toast-notification');
      if (existingToast) existingToast.remove();

      const toast = document.createElement('div');
      toast.className = `toast-notification toast-${type}`;
      toast.innerHTML = message;

      const styles = {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '12px',
        color: 'white',
        zIndex: '9999',
        boxShadow: '0 8px 32px rgba(0,0,0,0.2)',
        transform: 'translateX(400px)',
        transition: 'transform 0.5s ease',
        fontWeight: '600',
        background: type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'
      };

      Object.assign(toast.style, styles);
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.transform = 'translateX(0)';
      }, 100);

      setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
          if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
          }
        }, 500);
      }, 3500);
    }
  });
</script>