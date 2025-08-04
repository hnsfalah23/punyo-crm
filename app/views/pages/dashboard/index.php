<style>
  /* Style baru untuk kartu statistik yang lebih modern */
  .stat-card {
    border: 1px solid #e9ecef;
    border-left: 5px solid;
    transition: all 0.3s ease-in-out;
    background-color: #fff;
    height: 100%;
    /* Pastikan kartu mengisi tinggi kolom */
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }

  .stat-card.border-primary {
    border-color: #0d6efd;
  }

  .stat-card.border-warning {
    border-color: #ffc107;
  }

  .stat-card.border-success {
    border-color: #198754;
  }

  .stat-card.border-info {
    border-color: #0dcaf0;
  }

  .stat-card .card-body {
    padding: 1.5rem;
    display: flex;
    /* Gunakan flexbox */
    flex-direction: column;
    /* Susun konten secara vertikal */
    justify-content: center;
    /* Pusatkan konten secara vertikal */
  }

  .stat-card .stat-icon {
    font-size: 3rem;
    opacity: 0.15;
    transition: all 0.3s ease;
  }

  .stat-card:hover .stat-icon {
    opacity: 0.3;
    transform: scale(1.1) rotate(-5deg);
  }
</style>

<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Ringkasan Kinerja</li>
    </ol>
  </div>

  <?php flash('dashboard_message'); ?>

  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
      <div class="card stat-card border-primary">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="fs-2 fw-bold"><?= $data['stats']['total_leads'] ?? 0 ?></div>
              <div class="text-muted">Total Prospek</div>
            </div>
            <i class="bi bi-person-lines-fill stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
      <div class="card stat-card border-warning">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="fs-2 fw-bold"><?= $data['stats']['monthly_new_deals'] ?? 0 ?></div>
              <div class="text-muted">Deal Baru Bulan Ini</div>
            </div>
            <i class="bi bi-briefcase-fill stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
      <div class="card stat-card border-success">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="fs-3 fw-bold">Rp <?= number_format($data['stats']['monthly_revenue'] ?? 0, 0, ',', '.') ?></div>
              <div class="text-muted">Pendapatan Bulan Ini</div>
            </div>
            <i class="bi bi-cash-coin stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
      <div class="card stat-card border-info">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="fs-2 fw-bold"><?= $data['stats']['total_ongoing_deals'] ?? 0 ?></div>
              <div class="text-muted">Kesepakatan Berjalan</div>
            </div>
            <i class="bi bi-arrow-repeat stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6" data-aos="fade-up" data-aos-delay="500">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-funnel-fill me-1"></i>Filter Penjualan</div>
        <div class="card-body"><canvas id="salesFunnelChart"></canvas></div>
      </div>
    </div>
    <div class="col-xl-6" data-aos="fade-up" data-aos-delay="600">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-pie-chart-fill me-1"></i>Komposisi Tahapan Kesepakatan</div>
        <div class="card-body d-flex justify-content-center align-items-center">
          <canvas id="dealsByStageChart" style="max-height: 250px;"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6" data-aos="fade-up" data-aos-delay="700">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-graph-up me-1"></i>Pencapaian Target Penjualan</div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <canvas id="targetRevenueChart" style="max-height: 200px;"></canvas>
          <div class="mt-2 text-center">
            <strong>Rp <?= number_format($data['targetRevenueData']['achieved'], 0, ',', '.') ?></strong> /
            <small>Rp <?= number_format($data['targetRevenueData']['target'], 0, ',', '.') ?></small>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6" data-aos="fade-up" data-aos-delay="800">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-graph-up-arrow me-1"></i>Pencapaian Target Jumlah Deal</div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <canvas id="targetDealCountChart" style="max-height: 200px;"></canvas>
          <div class="mt-2 text-center">
            <strong><?= number_format($data['targetDealCountData']['achieved'], 0, ',', '.') ?> Deal</strong> /
            <small><?= number_format($data['targetDealCountData']['target'], 0, ',', '.') ?> Deal</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Grafik Dashboard
    const salesFunnelChart = document.getElementById('salesFunnelChart');
    if (salesFunnelChart) {
      const ctx = salesFunnelChart.getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Total Prospek', 'Total Kesepakatan', 'Kesepakatan Menang'],
          datasets: [{
            label: 'Jumlah',
            data: [
              <?= $data['funnelData']->total_leads ?? 0 ?>,
              <?= $data['funnelData']->total_deals ?? 0 ?>,
              <?= $data['funnelData']->deals_won ?? 0 ?>
            ],
            backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)'],
            borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
            borderWidth: 1
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
          }
        }
      });
    }

    const targetRevenueChart = document.getElementById('targetRevenueChart');
    if (targetRevenueChart) {
      const achieved = <?= $data['targetRevenueData']['achieved'] ?? 0 ?>;
      const target = <?= $data['targetRevenueData']['target'] ?? 0 ?>;
      const remaining = Math.max(0, target - achieved);

      new Chart(targetRevenueChart.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: ['Tercapai', 'Sisa Target'],
          datasets: [{
            data: [achieved, remaining],
            backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(220, 220, 220, 0.8)'],
            borderColor: ['#fff'],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          cutout: '70%',
          plugins: {
            legend: {
              position: 'top'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.label || '';
                  if (label) {
                    label += ': ';
                  }
                  if (context.parsed !== null) {
                    label += new Intl.NumberFormat('id-ID', {
                      style: 'currency',
                      currency: 'IDR'
                    }).format(context.parsed);
                  }
                  return label;
                }
              }
            }
          },
          animation: {
            animateScale: true,
            animateRotate: true
          }
        }
      });
    }

    const dealsByStageChart = document.getElementById('dealsByStageChart');
    if (dealsByStageChart) {
      const labels = <?= json_encode(array_column($data['dealsByStage'], 'stage')) ?>;
      const counts = <?= json_encode(array_column($data['dealsByStage'], 'count')) ?>;

      new Chart(dealsByStageChart.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            label: 'Jumlah Kesepakatan',
            data: counts,
            backgroundColor: [
              'rgba(147, 51, 234, 0.8)', 'rgba(13, 110, 253, 0.8)',
              'rgba(255, 206, 86, 0.8)', 'rgba(255, 159, 64, 0.8)',
              'rgba(40, 167, 69, 0.8)', 'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          cutout: '70%',
          plugins: {
            legend: {
              position: 'top'
            }
          },
          animation: {
            animateScale: true,
            animateRotate: true
          }
        }
      });
    }

    const targetDealCountChart = document.getElementById('targetDealCountChart');
    if (targetDealCountChart) {
      const achieved = <?= $data['targetDealCountData']['achieved'] ?? 0 ?>;
      const target = <?= $data['targetDealCountData']['target'] ?? 0 ?>;
      const remaining = Math.max(0, target - achieved);

      new Chart(targetDealCountChart.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: ['Tercapai', 'Sisa Target'],
          datasets: [{
            data: [achieved, remaining],
            backgroundColor: ['rgba(255, 193, 7, 0.8)', 'rgba(220, 220, 220, 0.8)'],
            borderColor: ['#fff'],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          cutout: '70%',
          plugins: {
            legend: {
              position: 'top'
            }
          }
        }
      });
    }
  });
</script>