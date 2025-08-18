<main class="container-fluid px-4">
  <h1 class="mt-4"><?= $data['title'] ?></h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active"><?= $data['title'] ?></li>
  </ol>

  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-clock-history me-1"></i> Data Aktivitas</span>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActivityModal">
        <i class="bi bi-plus-circle me-1"></i>
        Tambah Aktivitas
      </button>
    </div>
    <div class="card-body">
      <?php flash('activity_message'); ?>

      <ul class="nav nav-tabs" id="activityTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">Mendatang</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">Berlalu</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-view" type="button" role="tab" aria-controls="calendar" aria-selected="false">Kalender</button>
        </li>
      </ul>

      <div class="tab-content pt-3" id="activityTabContent">
        <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nama Aktivitas</th>
                  <th>Tipe</th>
                  <th>Waktu Mulai</th>
                  <th>Pemilik</th>
                  <th>Terkait Dengan</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($data['upcoming_activities'])): ?>
                  <tr>
                    <td colspan="5" class="text-center">Tidak ada aktivitas mendatang.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($data['upcoming_activities'] as $activity): ?>
                    <tr>
                      <td><?= htmlspecialchars($activity->name) ?></td>
                      <td><?= htmlspecialchars($activity->type) ?></td>
                      <td><?= date('d M Y, H:i', strtotime($activity->start_time)) ?></td>
                      <td><?= htmlspecialchars($activity->owner_name) ?></td>
                      <td><?= ucfirst(htmlspecialchars($activity->related_item_type)) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nama Aktivitas</th>
                  <th>Tipe</th>
                  <th>Waktu Mulai</th>
                  <th>Pemilik</th>
                  <th>Terkait Dengan</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($data['past_activities'])): ?>
                  <tr>
                    <td colspan="5" class="text-center">Tidak ada aktivitas yang sudah berlalu.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($data['past_activities'] as $activity): ?>
                    <tr>
                      <td><?= htmlspecialchars($activity->name) ?></td>
                      <td><?= htmlspecialchars($activity->type) ?></td>
                      <td><?= date('d M Y, H:i', strtotime($activity->start_time)) ?></td>
                      <td><?= htmlspecialchars($activity->owner_name) ?></td>
                      <td><?= ucfirst(htmlspecialchars($activity->related_item_type)) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane fade" id="calendar-view" role="tabpanel" aria-labelledby="calendar-tab">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</main>

<div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addActivityModalLabel">Tambah Aktivitas Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= BASE_URL ?>/activities/add" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Nama Aktivitas</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="type" class="form-label">Tipe</label>
            <select class="form-select" id="type" name="type" required>
              <option value="Panggilan">Panggilan</option>
              <option value="Email">Email</option>
              <option value="Rapat">Rapat</option>
              <option value="Tugas">Tugas</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="start_time" class="form-label">Waktu Mulai</label>
              <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="end_time" class="form-label">Waktu Selesai</label>
              <input type="datetime-local" class="form-control" id="end_time" name="end_time">
            </div>
          </div>
          <div class="mb-3">
            <label for="related_to" class="form-label">Kaitkan dengan</label>
            <select class="form-select" id="related_to">
              <option value="">Tidak ada</option>
              <option value="prospek">Prospek</option>
              <option value="peluang">Peluang</option>
            </select>
          </div>
          <div class="mb-3 d-none" id="prospek-container">
            <label for="prospek_id" class="form-label">Pilih Prospek</label>
            <select class="form-select" id="prospek_id" name="prospek_id">
              <option value="">Pilih...</option>
              <?php foreach ($data['prospek'] as $prospek): ?>
                <option value="<?= $prospek->lead_id ?>"><?= htmlspecialchars($prospek->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3 d-none" id="peluang-container">
            <label for="peluang_id" class="form-label">Pilih Peluang</label>
            <select class="form-select" id="peluang_id" name="peluang_id">
              <option value="">Pilih...</option>
              <?php foreach ($data['peluang'] as $peluang): ?>
                <option value="<?= $peluang->deal_id ?>"><?= htmlspecialchars($peluang->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="documentation_photo" class="form-label">Dokumentasi Foto</label>
            <input class="form-control" type="file" id="documentation_photo" name="documentation_photo">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Logika dropdown Prospek/Peluang (tidak berubah)
    const relatedToSelect = document.getElementById('related_to');
    const prospekContainer = document.getElementById('prospek-container');
    const peluangContainer = document.getElementById('peluang-container');
    const prospekSelect = document.getElementById('prospek_id');
    const peluangSelect = document.getElementById('peluang_id');

    relatedToSelect.addEventListener('change', function() {
      prospekContainer.classList.add('d-none');
      peluangContainer.classList.add('d-none');
      prospekSelect.name = '';
      peluangSelect.name = '';

      if (this.value === 'prospek') {
        prospekContainer.classList.remove('d-none');
        prospekSelect.name = 'prospek_id';
      } else if (this.value === 'peluang') {
        peluangContainer.classList.remove('d-none');
        peluangSelect.name = 'peluang_id';
      }
    });

    // Inisialisasi FullCalendar - dengan pengecekan
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && typeof FullCalendar !== 'undefined') {

      console.log('Initializing FullCalendar...'); // Debug log

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: {
          url: '<?= BASE_URL ?>/activities/calendar_json',
          failure: function(error) {
            console.error('Error loading calendar events:', error);
            alert('Gagal memuat data kalender. Periksa koneksi atau endpoint.');
          }
        },
        height: 650,
        eventClick: function(info) {
          // Handle event click
          alert('Event: ' + info.event.title);
        },
        loading: function(bool) {
          if (bool) {
            console.log('Loading calendar events...');
          } else {
            console.log('Calendar events loaded.');
          }
        }
      });

      // Render kalender
      calendar.render();

      // Event listener untuk tab kalender
      const calendarTab = document.getElementById('calendar-tab');
      if (calendarTab) {
        calendarTab.addEventListener('shown.bs.tab', function() {
          console.log('Calendar tab shown, updating size...');
          setTimeout(function() {
            calendar.updateSize();
          }, 100);
        });
      }

    } else {
      console.error('FullCalendar not loaded or calendar element not found');
      if (!calendarEl) console.error('Calendar element #calendar not found');
      if (typeof FullCalendar === 'undefined') console.error('FullCalendar library not loaded');
    }
  });
</script>