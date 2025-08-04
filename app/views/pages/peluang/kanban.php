<style>
  :root {
    --kanban-bg: #f4f7fc;
    --column-bg: #ffffff;
    --card-bg: #ffffff;
    --card-hover-bg: #f9fafb;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --font-family: 'Poppins', sans-serif;
    --transition-fast: all 0.2s ease-in-out;
  }

  body {
    background-color: var(--kanban-bg);
  }

  /* PERBAIKAN UTAMA DI SINI: Menghapus batasan tinggi */
  .kanban-container-wrapper {
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    font-family: var(--font-family);
    background-color: var(--kanban-bg);
    min-height: calc(100vh - 57px);
    /* Memastikan container setidaknya setinggi layar */
  }

  .kanban-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 0 0.5rem;
  }

  .kanban-title h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .kanban-actions .btn {
    background-color: var(--column-bg);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    transition: var(--transition-fast);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .kanban-actions .btn:hover {
    background-color: #f3f4f6;
  }

  /* PERBAIKAN UTAMA DI SINI: Menghapus batasan tinggi */
  .kanban-board {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.5rem;
  }

  .kanban-column {
    display: flex;
    flex-direction: column;
    background-color: var(--column-bg);
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.07);
    overflow: hidden;
  }

  .kanban-column-header {
    padding: 1rem 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
    position: relative;
  }

  .kanban-column-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
  }

  .kanban-column-header .stage-name {
    margin-right: 0.75rem;
  }

  .stage-count {
    background-color: #f3f4f6;
    color: var(--text-secondary);
    font-size: 0.8rem;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-weight: 500;
  }

  .stage-analisis-kebutuhan .kanban-column-header::before {
    background-color: #3b82f6;
  }

  .stage-proposal .kanban-column-header::before {
    background-color: #8b5cf6;
  }

  .stage-negosiasi .kanban-column-header::before {
    background-color: #f59e0b;
  }

  .stage-menang .kanban-column-header::before {
    background-color: #10b981;
  }

  .stage-kalah .kanban-column-header::before {
    background-color: #ef4444;
  }

  /* PERBAIKAN UTAMA DI SINI: Menghapus overflow agar tidak ada scroll di dalam kolom */
  .kanban-cards {
    flex-grow: 1;
    padding: 1rem;
    background-color: #f9fafb;
    min-height: 150px;
    /* Jaga agar kolom tidak collapse saat kosong */
  }

  .kanban-cards.drag-over {
    background-color: #f0f3f8;
  }

  .kanban-card {
    background-color: var(--card-bg);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: grab;
    transition: var(--transition-fast);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.06);
    border: 1px solid var(--border-color);
    position: relative;
  }

  .kanban-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }

  .kanban-card.dragging {
    opacity: 0.8;
    transform: rotate(3deg);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }

  .card-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-primary);
    margin-right: 25px;
  }

  .card-company {
    color: var(--text-secondary);
    font-size: 0.85rem;
  }

  .card-product-list {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border-color);
  }

  .card-product-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-secondary);
  }

  .card-product-list li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .card-product-list i {
    font-size: 0.9rem;
  }

  .card-footer-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-secondary);
  }

  .card-owner {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .card-value {
    font-weight: 600;
    color: #059669;
    background-color: #dcfce7;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
  }

  .view-detail-btn {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    color: #9ca3af;
    text-decoration: none;
    transition: var(--transition-fast);
    font-size: 1.1rem;
  }

  .view-detail-btn:hover {
    color: #3b82f6;
    transform: scale(1.2);
  }
</style>

<div class="kanban-container-wrapper">
  <div class="kanban-header">
    <div class="kanban-title">
      <h1>Papan Kanban Peluang</h1>
    </div>
    <div class="kanban-actions"><a href="<?= BASE_URL; ?>/peluang" class="btn"><i class="bi bi-table me-2"></i>Tampilan Tabel</a></div>
  </div>

  <div class="kanban-board">
    <?php
    $stageConfig = [
      'Analisis Kebutuhan' => ['class' => 'stage-analisis-kebutuhan'],
      'Proposal' => ['class' => 'stage-proposal'],
      'Negosiasi' => ['class' => 'stage-negosiasi'],
      'Menang' => ['class' => 'stage-menang'],
      'Kalah' => ['class' => 'stage-kalah']
    ];
    ?>

    <?php foreach ($data['dealsByStage'] as $stage => $deals): ?>
      <?php $stageSlug = str_replace(' ', '-', strtolower($stage)); ?>
      <div class="kanban-column <?= $stageConfig[$stage]['class'] ?? '' ?>">
        <div class="kanban-column-header">
          <span class="stage-name"><?= htmlspecialchars($stage) ?></span>
          <span class="stage-count" id="count-<?= $stageSlug ?>"><?= count($deals) ?></span>
        </div>
        <div class="kanban-cards" data-stage="<?= htmlspecialchars($stage) ?>">
          <?php foreach ($deals as $deal): ?>
            <div class="kanban-card" data-id="<?= $deal->deal_id ?>" draggable="<?= can('update', 'deals') ? 'true' : 'false' ?>">

              <a href="<?= BASE_URL; ?>/deals/detail/<?= $deal->deal_id; ?>" class="view-detail-btn" title="Lihat Detail">
                <i class="bi bi-eye-fill"></i>
              </a>

              <h6 class="card-title mb-1"><?= htmlspecialchars($deal->company_name) ?></h6>
              <p class="card-company mb-2"><?= htmlspecialchars($deal->name) ?></p>

              <?php if (!empty($deal->product_names)): ?>
                <div class="card-product-list">
                  <ul>
                    <li><i class="bi bi-box-seam"></i><span><?= htmlspecialchars($deal->product_names); ?></span></li>
                  </ul>
                </div>
              <?php endif; ?>

              <div class="card-footer-details">
                <div class="card-owner"><i class="bi bi-person-circle"></i><span><?= htmlspecialchars($deal->owner_name) ?></span></div>
                <div class="card-value">Rp <?= number_format($deal->value, 0, ',', '.') ?></div>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (!can('update', 'deals')): ?>
      return;
    <?php endif; ?>

    const cards = document.querySelectorAll('.kanban-card[draggable="true"]');
    const columns = document.querySelectorAll('.kanban-cards');
    let draggedCard = null;
    let isDragging = false;

    cards.forEach(card => {
      card.addEventListener('dragstart', (e) => {
        if (e.target.tagName.toLowerCase() === 'a' || e.target.tagName.toLowerCase() === 'i') {
          e.preventDefault();
          return;
        }
        isDragging = true;
        draggedCard = card;
        setTimeout(() => card.classList.add('dragging'), 0);
        e.dataTransfer.effectAllowed = 'move';
      });

      card.addEventListener('dragend', () => {
        if (draggedCard) {
          draggedCard.classList.remove('dragging');
        }
        draggedCard = null;
        setTimeout(() => {
          isDragging = false;
        }, 50);
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
          // **PERUBAHAN POSISI DROP DI SINI**
          targetColumn.prepend(draggedCard); // Menggunakan prepend() untuk menempatkan di paling atas

          updateDealStage(
            draggedCard.dataset.id,
            targetColumn.dataset.stage,
            originalColumn,
            draggedCard
          );
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
          if (!data.success) {
            originalColumn.appendChild(cardElement);
            updateCardCounts();
          }
        })
        .catch(() => {
          originalColumn.appendChild(cardElement);
          updateCardCounts();
        });
    }

    function updateCardCounts() {
      columns.forEach(column => {
        const stageSlug = column.dataset.stage.replace(/ /g, '-').toLowerCase();
        const countElement = document.getElementById(`count-${stageSlug}`);
        if (countElement) {
          countElement.innerText = column.querySelectorAll('.kanban-card').length;
        }
      });
    }
  });
</script>