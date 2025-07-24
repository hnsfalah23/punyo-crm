<style>
  :root {
    --kanban-bg: #f4f7fc;
    /* Light Gray-Blue background */
    --column-bg: #ffffff;
    /* White column background */
    --card-bg: #ffffff;
    --card-hover-bg: #f9fafb;
    --text-primary: #1f2937;
    /* Dark text for readability */
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --font-family: 'Poppins', sans-serif;
    --transition-fast: all 0.2s ease-in-out;
  }

  /* Main Kanban Container */
  .kanban-container-wrapper {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 57px);
    /* Full viewport height minus topbar */
    padding: 1.5rem;
    font-family: var(--font-family);
    background-color: var(--kanban-bg);
  }

  /* Kanban Header */
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

  /* Kanban Board - Grid Layout (No Horizontal Scroll) */
  .kanban-board {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    /* 5 columns */
    gap: 1.5rem;
    height: 100%;
    overflow: hidden;
  }

  /* Column Styling */
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

  /* Column Accent Colors */
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

  /* Cards Container */
  .kanban-cards {
    flex-grow: 1;
    padding: 1rem;
    overflow-y: auto;
    background-color: #f9fafb;
  }

  .kanban-cards.drag-over {
    background-color: #f0f3f8;
  }

  .kanban-cards::-webkit-scrollbar {
    width: 6px;
  }

  .kanban-cards::-webkit-scrollbar-track {
    background: #e5e7eb;
  }

  .kanban-cards::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
  }


  /* Card Styling */
  .kanban-card {
    background-color: var(--card-bg);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: grab;
    transition: var(--transition-fast);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.06);
    border: 1px solid var(--border-color);
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

  .kanban-card .card-title a {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
  }

  .kanban-card .card-company {
    color: var(--text-secondary);
    font-size: 0.85rem;
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
</style>

<div class="kanban-container-wrapper" data-aos="fade">
  <div class="kanban-header">
    <div class="kanban-title">
      <h1>Papan Kanban</h1>
    </div>
    <div class="kanban-actions">
      <a href="<?= BASE_URL; ?>/deals" class="btn"><i class="bi bi-table me-2"></i>Tampilan Tabel</a>
    </div>
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
          <?php if (empty($deals)): ?>
          <?php else: ?>
            <?php foreach ($deals as $deal): ?>
              <div class="kanban-card" data-id="<?= $deal->deal_id ?>" draggable="<?= can('update', 'deals') ? 'true' : 'false' ?>">
                <h6 class="card-title mb-1">
                  <a href="<?= BASE_URL; ?>/deals/detail/<?= $deal->deal_id; ?>">
                    <?= htmlspecialchars($deal->name) ?>
                  </a>
                </h6>
                <p class="card-company mb-0"><?= htmlspecialchars($deal->company_name) ?></p>
                <div class="card-footer-details">
                  <div class="card-owner">
                    <i class="bi bi-person-circle"></i>
                    <span><?= htmlspecialchars($deal->owner_name) ?></span>
                  </div>
                  <div class="card-value">
                    Rp <?= number_format($deal->value, 0, ',', '.') ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  // JavaScript logic remains the same
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (!can('update', 'deals')): ?>
      return;
    <?php endif; ?>

    const cards = document.querySelectorAll('.kanban-card[draggable="true"]');
    const columns = document.querySelectorAll('.kanban-cards');
    let draggedCard = null;

    cards.forEach(card => {
      card.addEventListener('dragstart', (e) => {
        draggedCard = card;
        setTimeout(() => card.classList.add('dragging'), 0);
        e.dataTransfer.effectAllowed = 'move';
      });

      card.addEventListener('dragend', () => {
        if (draggedCard) {
          draggedCard.classList.remove('dragging');
          draggedCard = null;
        }
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
          targetColumn.appendChild(draggedCard);

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