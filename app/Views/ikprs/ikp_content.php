<!-- DEBUG: hris_user_id = <?= session('hris_user_id') ?> | user_role = <?= session('user_role') ?> | initial_tab = <?= $initial_tab ?? 'EMPTY' ?> -->

  <input type="hidden" id="initialTabInput" value="<?= $initial_tab ?? '' ?>">

  <style>
      /* =====================================================
        FIX CI3 LOOK (Bootstrap 5 + AdminLTE 4)
        ===================================================== */

      /* CARD SPACING */
      .card {
          margin-bottom: 1rem;
      }

      /* PROFILE CARD */
      .profile-card h5 {
          margin-bottom: .15rem;
      }

      .profile-card small {
          font-size: 13px;
      }

      /* NAV LIST (LEFT MENU) */
      .nav-pills .nav-link {
          color: var(--bs-body-color) !important;
          padding: .55rem .75rem;
          font-size: 14px;
      }

      .nav-pills .nav-link i {
          color: var(--bs-secondary-color);
      }

      .nav-pills .nav-link:hover {
          background-color: var(--bs-secondary-bg);
          color: var(--bs-body-color) !important;
      }

      /* DARK MODE FIX */
      .dark-mode .nav-pills .nav-link {
          color: #ddd !important;
      }

      .dark-mode .nav-pills .nav-link:hover {
          background-color: #2b2b2b;
          color: #fff !important;
      }

      .nav-pills .badge {
          margin-left: auto;
          font-size: 12px;
      }

      /* INPUT BUTTON */
      .btn-input {
          margin-bottom: .75rem;
      }

      /* =====================================================
        ADMINLTE 3 MAILBOX LOOK (BS5 + ALTE4)
        ===================================================== */

      .mailbox-table {
          font-size: 14px;
      }

      .mailbox-table tr {
          border-bottom: 1px solid #dee2e6;
      }

      .mailbox-table td {
          vertical-align: middle;
          padding: .6rem .5rem;
      }

      /* Checkbox */
      .mailbox-check {
          width: 40px;
          text-align: center;
      }

      /* Star */
      .mailbox-star {
          width: 40px;
          text-align: center;
          color: #f39c12;
      }

      /* Sender */
      .mailbox-name {
          width: 180px;
          font-weight: 600;
      }

      .mailbox-name a {
          color: var(--bs-primary);

          text-decoration: none;
      }

      .mailbox-name a:hover {
          text-decoration: underline;
      }

      /* Subject */
      .mailbox-subject strong {
          font-weight: 600;
      }

      /* Date */
      .mailbox-date {
          width: 120px;
          text-align: right;
          font-size: 13px;
          color: #6c757d;
      }

      /* Hover */
      .mailbox-table tbody tr:hover {
          background-color: #f4f6f9;
          cursor: pointer;
      }

      /* HEADER TOOLBAR */
      .mailbox-header {
          padding: .6rem .75rem;
          border-bottom: 1px solid #dee2e6;
      }

      /* SEARCH (ADMINLTE 3 STYLE) */
      .mailbox-search {
          width: 220px;
      }

      .mailbox-search input {
          font-size: 13px;
      }

      .mailbox-search .btn {
          padding: .25rem .5rem;
      }

      /* FOOTER */
      .mailbox-footer {
          padding: .55rem .75rem;
          border-top: 1px solid #dee2e6;
      }

      /* BUTTON DEFAULT STYLE */
      .btn-mailbox {
          background-color: #f8f9fa;
          border: 1px solid #ced4da;
          color: #495057;
      }

      .btn-mailbox:hover {
          background-color: #e9ecef;
      }

      /* REMOVE LINK BLUE */
      .mailbox-table a {
          color: inherit;
      }

      /* BUTTON btnInbox */
      #btnInbox:hover {
          background-color: #e2e4e7;
      }

      #btnInbox.active {
          background-color: #e2e4e7;
          font-weight: 600;
      }

      /* BUTTON btnDrafts */
      #btnDrafts:hover {
          background-color: #e2e4e7;
      }

      #btnDrafts.active {
          background-color: #e2e4e7;
          font-weight: 600;
      }

      /* BUTTON btnSent */
      #btnSent:hover {
          background-color: #e2e4e7;
      }

      #btnSent.active {
          background-color: #e2e4e7;
          font-weight: 600;
      }

      /* BUTTON btnInfo */
      #btnInfo:hover {
          background-color: #e2e4e7;
      }

      #btnInfo.active {
          background-color: #e2e4e7;
          font-weight: 600;
      }

      /* ===== TOP LOADING BAR (RGB JELAS) ===== */
      .top-loading {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 5px;
          /* agak tebal biar kelihatan */
          overflow: hidden;
          z-index: 2000;
          background: rgba(0, 0, 0, 0.05);
          /* garis tipis background */
      }

      .top-loading-bar {
          width: 30%;
          height: 100%;
          background: linear-gradient(90deg,
                  rgba(255, 0, 0, 0),
                  rgb(255, 0, 0),
                  rgb(0, 200, 255),
                  rgba(255, 0, 0, 0));
          animation: loading-move 2.5s linear infinite;
      }

      @keyframes loading-move {
          from {
              transform: translateX(-100%);
          }

          to {
              transform: translateX(400%);
          }
      }
  </style>

  <div id="contentIKP">
      <div class="container-fluid">
          <div class="row">
              <!-- SIDEBAR -->
              <!-- LEFT SIDEBAR -->
              <div class="col-md-3">
                  <!-- <a href="#" class="btn btn-primary w-100 mb-3">Compose</a> -->

                  <!-- Profile Image -->
                  <div class="card card-outline card-secondary ikp-card position-relative profile-card">
                      <div class="card-body text-center">
                          <img
                              src="<?= base_url('assets/img/orang.png') ?>"
                              class="rounded-circle mb-2"
                              width="100">

                          <?php if (session('user_role') === 'KARU'): ?>

                              <h5 class="fw-bold mb-1">
                                  <?= esc(session('hris_full_name')) ?>
                              </h5>

                              <small class="text-muted d-block">
                                  <?= esc(session('hris_nip')) ?>
                              </small>

                              <small class="text-muted fst-italic d-block">
                                  Kepala Ruangan - <?= esc(session('karu_room_name')) ?>
                              </small>

                          <?php elseif (session('user_role') === 'KOMITE'): ?>

                              <h5 class="fw-bold mb-1">
                                  <?= esc(session('hris_full_name')) ?>
                              </h5>

                              <small class="text-muted d-block">
                                  <?= esc(session('hris_nip')) ?>
                              </small>

                              <small class="text-muted fst-italic d-block">
                                  Komite PMKP
                              </small>

                          <?php else: ?>

                              <h5 class="fw-bold mb-0">
                                  <?= esc(session('hris_full_name')) ?>
                              </h5>

                              <small class="text-muted">
                                  <?= esc(session('hris_nip')) ?>
                              </small>

                              <small class="text-muted fst-italic d-block">
                                  Pelapor IKPRS
                              </small>

                          <?php endif; ?>
                      </div>
                  </div>

                  <!-- Folders -->
                  <div class="card">
                      <div class="card-body p-2">

                          <button id="btnInputIkp" class="btn btn-secondary btn-sm w-100 btn-input">
                              <i class="bi bi-plus"></i> Input Laporan
                          </button>


                          <ul class="nav nav-pills flex-column">
                              <li class="nav-item">
                                  <a id="btnInfo" href="#" class="nav-link d-flex align-items-center">
                                      <i class="bi bi-bell me-2"></i>
                                      Info
                                      <span id="badge-notif" class="badge bg-info ms-auto">0</span>
                                  </a>
                              </li>


                              <li class="nav-item">
                                  <a id="btnInbox" href="#" class="nav-link d-flex align-items-center">
                                      <i class="bi bi-inbox me-2"></i>
                                      Inbox
                                      <span id="badge-inbox" class="badge bg-primary ms-auto">
                                          0
                                      </span>
                                  </a>
                              </li>


                              <li class="nav-item">
                                  <a id="btnSend" href="#" class="nav-link d-flex align-items-center">
                                      <i class="bi bi-send me-2"></i>
                                      Sent
                                      <span id="badge-send" class="badge bg-success ms-auto">
                                          0
                                      </span>
                                  </a>
                              </li>

                              <li class="nav-item">
                                  <a id="btnDrafts" href="#" class="nav-link d-flex align-items-center">
                                      <i class="bi bi-file-earmark-text me-2"></i>
                                      Drafts
                                      <span id="badge-draft" class="badge bg-warning ms-auto">
                                          0
                                      </span>
                                  </a>
                              </li>
                          </ul>

                      </div>
                  </div>
              </div>

              <!-- CONTENT -->
              <div class="col-md-9">
                  <div class="card card-outline card-secondary ikp-card position-relative">

                      <!-- 🔥 LOADING SESUAI CARD -->
                      <div id="loading_bar" class="top-loading d-none">
                          <div class="top-loading-bar"></div>
                      </div>

                      <div class="card-body">
                          <div id="inbox-wrapper"></div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <script>
      let inboxLoading = false;
      let draftsLoading = false;
      let sendLoading = false;
      let infoLoading = false;
      let lastInboxCount = 0;
      const user_role = "<?= session('user_role') ?>";

      window.IKP = {
          isSearching: false
      };

      $(document).ready(function() {
          // Load tab dari hidden input
          const initialTab = $('#initialTabInput').val();
          
          // Load initial content
          if (initialTab === 'info') {
              loadInfo(1);
          } else {
              loadInbox();
          }

          // Update badge from counter-ajax - hanya untuk HRIS login (yang punya hris_user_id)
          const hrisUserId = "<?= session('hris_user_id') ?? '' ?>";
          if (hrisUserId !== '') {
              $.get("<?= site_url('ikprs/counter-ajax') ?>", function(res) {
                  if (res.error && res.error === 'User belum login') {
                      console.log('User not logged in, skipping badge update');
                      return;
                  }
                  if (res.total_notif !== undefined) {
                      $('#badge-notif').text(res.total_notif);
                  }
                  if (res.total_inbox !== undefined) {
                      $('#badge-inbox').text(res.total_inbox);
                  }
                  if (res.total_send !== undefined) {
                      $('#badge-send').text(res.total_send);
                  }
              }).fail(function(xhr) {
                  // Ignore network errors
              });
          }

          initValidasiKomite();

      });


      /* ===== EVENT Loding PROCESSING ===== */
      $('#inbox-wrapper').on('processing.inbox', function(e, processing) {
          if (processing) {
              $('#loading_bar').removeClass('d-none').addClass('show');
          } else {
              $('#loading_bar').removeClass('show');
              setTimeout(() => $('#loading_bar').addClass('d-none'), 300);
          }
      });

      window.showIKPLoading = function() {
          $('#inbox-wrapper').trigger('processing.inbox', [true]);
      };

      window.hideIKPLoading = function() {
          $('#inbox-wrapper').trigger('processing.inbox', [false]);
      };

      /* ===== KLIK INPUT IKP ===== */

      $(document).on('click', '#btnInputIkp', function() {

          $('.nav-pills .nav-link').removeClass('active');
          $('#inbox-wrapper').trigger('processing.inbox', [true]);

          $.ajax({
              url: "<?= site_url('ikprs/_form_add_ikp') ?>",
              type: "GET",

              success: function(response) {

                  $('#inbox-wrapper').trigger('processing.inbox', [false]);

                  // Jika normal → tampilkan form
                  $('#inbox-wrapper').html(response);

                  if (typeof initIkpStepper === 'function') initIkpStepper();
                  if (typeof bindMedrecFormatter === 'function') bindMedrecFormatter();
                  if (typeof toggleResetButton === 'function') toggleResetButton();
                  if (typeof bindAutoSearch === 'function') bindAutoSearch();
                  if (typeof bindErrorCleanerStep1 === 'function') bindErrorCleanerStep1();
                  if (typeof bindErrorCleanerStep2 === 'function') bindErrorCleanerStep2();
                  if (typeof bindErrorCleanerStep3 === 'function') bindErrorCleanerStep3();
                  if (typeof bindErrorCleanerStep4 === 'function') bindErrorCleanerStep4();
                  if (typeof bindErrorCleanerStep5 === 'function') bindErrorCleanerStep5();
                  if (typeof loadTempatInsiden === 'function') loadTempatInsiden();
              },

              error: function(xhr) {

                  $('#inbox-wrapper').trigger('processing.inbox', [false]);

                  if (xhr.status === 403) {

                      let timerInterval;

                      Swal.fire({
                          icon: 'warning',
                          title: 'Akses Ditolak',
                          html: `
                        Anda tidak memiliki izin untuk mengakses halaman ini.<br>
                        Silakan login ulang dengan akun yang memiliki akses.<br><br>
                        <b><span id="countdown">5</span></b>`,
                          timer: 5000,
                          timerProgressBar: true,
                          showConfirmButton: false,
                          showCloseButton: true,
                          allowOutsideClick: true,
                          didOpen: () => {

                              const countdownEl = document.getElementById('countdown');
                              let timeLeft = 5;

                              timerInterval = setInterval(() => {
                                  timeLeft--;
                                  if (countdownEl) {
                                      countdownEl.textContent = timeLeft;
                                  }
                              }, 1000);
                          },
                          willClose: () => {
                              clearInterval(timerInterval);
                          }
                      });

                  } else {

                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Terjadi kesalahan saat memuat halaman.',
                          showCloseButton: true
                      });

                  }
              }
          });

      });

      /*
       * ===============================
       * Inbox 
       * ===============================
       */

      /* ===== KLIK MENU INBOX ===== */
      $(document).on('click', '#btnInbox', function(e) {
          e.preventDefault(); // ⛔ WAJIB
          loadInbox();
      });

      /* ===== FUNGSI LOAD INBOX (SATU-SATUNYA) ===== */
      function loadInbox(page = 1, keywordParam = null) {

          if (inboxLoading) return;
          inboxLoading = true;

          let keyword = keywordParam ?? $('#searchInbox').val() ?? '';

          $('#inbox-wrapper').trigger('processing.inbox', [true]);

          $.get("<?= site_url('ikprs/form_inbox_karu') ?>", {
              page: page,
              keyword: keyword
          }, function(res) {

              $('#inbox-wrapper').html(res);

          }).always(function() {

              inboxLoading = false;
              $('#inbox-wrapper').trigger('processing.inbox', [false]);

          });
      }

      /* ===== reloadInbox ===== */
      $(document).off('click', '.btn-inbox-reload').on('click', '.btn-inbox-reload', function() {
          loadInbox(1);
      });

      /* ===== SEARCH ===== */
      $(document).on('submit', '#formSearchInbox', function(e) {
          e.preventDefault();
          loadInbox(1);
      });

      /* ===== SEARCH INBOX (ENTER KEY) ===== */
      $(document).on('keydown', '#searchInbox', function(e) {
          if (e.keyCode === 13) {
              e.preventDefault();
              loadInbox(1, this.value);
          }
      });

      /* ===== SEARCH INBOX (BUTTON) ===== */
      $(document).off('click', '.btn-search-inbox')
          .on('click', '.btn-search-inbox', function() {
              let keyword = $('#searchInbox').val();
              loadInbox(1, keyword);
          });

      /* ===== PAGINATION ===== */

      // NEXT PAGINATION
      $(document).on('click', '.btn-inbox-next:not(.disabled)', function() {
          loadInbox($(this).data('page'));
      });

      // PREV PAGINATION
      $(document).on('click', '.btn-inbox-prev:not(.disabled)', function() {
          loadInbox($(this).data('page'));
      });

      /* ===== KLIK Inbox ===== */

      function loadDetailInsiden(id, tipe = 'inbox') {
          console.log("load detail:", id, tipe);

          if (inboxLoading) return;
          inboxLoading = true;

          $('#inbox-wrapper').trigger('processing.inbox', [true]);

          $.ajax({
              url: "<?= site_url('ikprs/detail-insiden') ?>/" + id,
              type: "GET",
              data: {
                  tipe: tipe
              },

              success: function(res) {

                  $('#inbox-wrapper').html(res);

                  // hanya inbox yang update status baca
                  if (tipe === 'inbox') {
                      tandaiSudahDibaca(id);
                  }

              },

              complete: function() {
                  inboxLoading = false;
                  $('#inbox-wrapper').trigger('processing.inbox', [false]);
              }
          });
      }

      $(document).on('click', '.btn-insiden-next', function() {

          const id = $(this).data('id');
          const tipe = $(this).data('tipe') || 'inbox';

          console.log("NEXT:", id, tipe);

          if (id) {
              loadDetailInsiden(id, tipe);
          }

      });

      $(document).on('click', '.btn-insiden-prev', function() {

          const id = $(this).data('id');
          const tipe = $(this).data('tipe') || 'inbox';

          console.log("PREV:", id, tipe);

          if (id) {
              loadDetailInsiden(id, tipe);
          }

      });

      $(document).on('click', '.btn-back', function() {

          const tipe = $(this).data('tipe') || 'inbox';

          console.log("BACK:", tipe);

          if (tipe === 'send') {
              loadSend(1);
          } else {
              loadInbox(1);
          }

      });

      $(document).on('click', '.btn-detail-reload', function() {

          const id = $(this).data('id');
          const tipe = $(this).data('tipe') || 'inbox';

          console.log("RELOAD:", id, tipe);

          if (id) {
              loadDetailInsiden(id, tipe);
          }

      });

      $(document).on('click', '.inbox-row', function() {
          const row = $(this);
          const id = row.data('id');

          console.log("OPEN INBOX:", id);

          loadDetailInsiden(id, 'inbox');

          // hilangkan unread
          row.removeClass('fw-bold');
          row.find('.bi-circle-fill').remove();

      });

      $(document).on('click', '.send-row', function() {
          const id = $(this).data('id');
          console.log("OPEN SEND:", id);
          loadDetailInsiden(id, 'send');
      });

      /*
       * ===============================
       * Drafts 
       * ===============================
       */

      /* ===== KLIK MENU DRAFTS ===== */
      $(document).on('click', '#btnDrafts', function(e) {
          e.preventDefault(); // ⛔ WAJIB
          loadDrafts(1);
      });

      /* ===== LOAD DRAFTS ===== */
      function loadDrafts(page = 1, keywordParam = null) {

          if (draftsLoading) return;

          draftsLoading = true;

          let keyword = keywordParam ?? $('#searchDraft').val() ?? '';

          $('#inbox-wrapper').trigger('processing.inbox', [true]);

          $.get("<?= site_url('ikprs/form_drafts') ?>", {
              page: page,
              keyword: keyword
          }, function(res) {

              $('#inbox-wrapper').html(res);

          }).always(function() {

              draftsLoading = false;
              $('#inbox-wrapper').trigger('processing.inbox', [false]);

          });
      }

      /* ===== reloadDrafts ===== */
      $(document).off('click', '.btn-draft-reload').on('click', '.btn-draft-reload', function() {
          loadDrafts(1);
      });

      /* ===== SEARCH ===== */
      $(document).on('submit', '#formSearchDraft', function(e) {
          e.preventDefault();
          loadDrafts(1);
      });

      /* ===== SEARCH DRAFT (ENTER KEY) ===== */
      $(document).on('keydown', '#searchDraft', function(e) {
          if (e.keyCode === 13) {
              e.preventDefault();
              loadDrafts(1, this.value);
          }
      });

      /* ===== SEARCH DRAFT (BUTTON) ===== */
      $(document).off('click', '.btn-search-draft')
          .on('click', '.btn-search-draft', function() {
              let keyword = $('#searchDraft').val();
              loadDrafts(1, keyword);
          });

      /* ===== PAGINATION ===== */

      // NEXT PAGINATION
      $(document).on('click', '.btn-draft-next:not(.disabled)', function() {
          loadDrafts($(this).data('page'));
      });

      // PREV PAGINATION
      $(document).on('click', '.btn-draft-prev:not(.disabled)', function() {
          loadDrafts($(this).data('page'));
      });

      /*
       * ===============================
       * Sent 
       * ===============================
       */

      /* ===== KLIK MENU SEND ===== */
      $(document).on('click', '#btnSend', function(e) {
          e.preventDefault(); // ⛔ WAJIB
          loadSend(1);
      });


      /* ===== LOAD SEND ===== */
      function loadSend(page = 1, keywordParam = null) {

          if (sendLoading) return;

          sendLoading = true;

          let keyword = keywordParam ?? $('#searchSend').val() ?? '';

          $('#inbox-wrapper').trigger('processing.inbox', [true]);

          $.get("<?= site_url('ikprs/form_send') ?>", {
              page: page,
              keyword: keyword
          }, function(res) {

              $('#inbox-wrapper').html(res);

          }).always(function() {

              sendLoading = false;
              $('#inbox-wrapper').trigger('processing.inbox', [false]);

          });
      }

      /* ===== SEARCH SEND (ENTER KEY) ===== */
      $(document).on('keydown', '#searchSend', function(e) {
          if (e.keyCode === 13) {
              e.preventDefault();
              loadSend(1, this.value);
          }
      });

      /* ===== SEARCH SEND (BUTTON) ===== */
      $(document).off('click', '.btn-search-send')
          .on('click', '.btn-search-send', function() {
              let keyword = $('#searchSend').val();
              loadSend(1, keyword);
          });


      /* ===== PAGINATION SEND ===== */

      // NEXT PAGINATION
      $(document).on('click', '.btn-send-next:not(.disabled)', function() {
          loadSend($(this).data('page'));
      });

      // PREV PAGINATION
      $(document).on('click', '.btn-send-prev:not(.disabled)', function() {
          loadSend($(this).data('page'));
      });

      /* ===== reloadSend ===== */
      $(document).off('click', '.btn-send-reload').on('click', '.btn-send-reload', function() {
          loadSend(1);
      });

      /* ===== KLIK SEND ===== */
      //   $(document).on('click', '.send-row', function() {

      //       let id = $(this).data('id');

      //       console.log("OPEN SEND:", id);

      //       loadDetailInsiden(id, 'send');

      //   });

      /* ===============================
       * INFO / NOTIFIKASI
       * ===============================
       */

      /* ===== KLIK MENU INFO ===== */
      $(document).on('click', '#btnInfo', function(e) {
          e.preventDefault();
          loadInfo(1);
      });

      /* ===== LOAD INFO ===== */
      function loadInfo(page = 1, keywordParam = null) {

          if (infoLoading) return;
          infoLoading = true;

          let keyword = keywordParam ?? $('#searchInfo').val() ?? '';

          $('#inbox-wrapper').trigger('processing.inbox', [true]);

          $.get("<?= site_url('ikprs/form_info') ?>", {
              page: page,
              keyword: keyword
          }, function(res) {

              $('#inbox-wrapper').html(res);

          }).always(function() {

              infoLoading = false;
              $('#inbox-wrapper').trigger('processing.inbox', [false]);

          });
      }

      /* ===== SEARCH INFO (ENTER KEY) ===== */
      $(document).on('keydown', '#searchInfo', function(e) {
          if (e.keyCode === 13) {
              e.preventDefault();
              loadInfo(1, this.value);
          }
      });

      /* ===== SEARCH INFO (BUTTON) ===== */
      $(document).off('click', '.btn-search-info')
          .on('click', '.btn-search-info', function() {
              let keyword = $('#searchInfo').val();
              loadInfo(1, keyword);
          });

      /* ===== PAGINATION INFO ===== */
      $(document).on('click', '.btn-info-next:not(.disabled)', function() {
          loadInfo($(this).data('page'));
      });

      $(document).on('click', '.btn-info-prev:not(.disabled)', function() {
          loadInfo($(this).data('page'));
      });

      /* ===== reloadInfo ===== */
      $(document).off('click', '.btn-info-reload').on('click', '.btn-info-reload', function() {
          loadInfo(1);
      });

      /* ===== KLIK ROW INFO ===== */
      $(document).on('click', '.info-row', function() {

          let id = $(this).data('id');

          // tandai sudah dibaca
          tandaiSudahDibaca(id);

          // buka detail inbox
          loadDetailInsiden(id, 'inbox');

      });


      /* ===== tandaiBaca ===== */
      function tandaiSudahDibaca(id) {

          $.ajax({
              url: "<?= site_url('ikprs/tandaiDibaca') ?>",
              type: "POST",
              dataType: "json",
              data: {
                  insiden_id: id
              },
              success: function(res) {

                  refreshNotif(); // update badge + dropdown notif


              },
              error: function(xhr) {
                  console.log("error tandai dibaca", xhr);
              }

          });

      }

      $(document).on('click', '.btn-kirim-verifikasi', function() {
          kirimVerifikasi(this);
      });

      $(document).off('click', '.btn-kirim-verifikasi').on('click', '.btn-kirim-verifikasi', function() {
          kirimVerifikasi(this);
      });

      // hilangkan error saat pilih grading
      $(document).on('change', 'input[name="grading"]', function() {
          $('#verifikasi_error').html('');
      });

      // hilangkan error saat isi catatan
      $(document).on('keyup', '#catatan_karu', function() {
          $('#verifikasi_error').html('');
      });

      //Verifikasi karu 
      function kirimVerifikasi(btn) {
          let id = $('#insiden_id').val();
          let catatan = $('#catatan_karu').val();
          let grading = $('input[name="grading"]:checked').val();

          $('#verifikasi_error').html('');

          if (!grading) {
              $('#verifikasi_error').html('Grading risiko harus dipilih');
              return;
          }

          if (!catatan || catatan.trim() === '') {
              $('#verifikasi_error').html('Catatan KARU tidak boleh kosong');
              $('#catatan_karu').focus();
              return;
          }

          $(btn).prop('disabled', true);

          $.ajax({
              url: "<?= base_url('ikprs/simpan_verifikasi') ?>",
              type: "POST",
              dataType: "json",
              data: {
                  insiden_id: id,
                  catatan_karu: catatan,
                  grading: grading
              },
              success: function(res) {

                  if (res.status) {

                      $('#verifikasi_error').html(
                          '<div class="text-success">' + res.message + '</div>'
                      );

                      //   loadDetailInsiden(res.insiden_id);

                      // kembali ke inbox setelah verifikasi
                      loadInbox(1);

                      // delay kecil supaya DB selesai update
                      setTimeout(function() {
                          refreshNotif();
                      }, 100);

                  } else {

                      $('#verifikasi_error').html(res.message);
                      $(btn).prop('disabled', false);

                  }

              },
              error: function() {

                  $('#verifikasi_error').html('Terjadi kesalahan server');
                  $(btn).prop('disabled', false);

              }
          });

      }

      //  validasi-komite
      //   $(document).on('click', '.btn-validasi-komite', function() {

      //       let id = $(this).data('id');
      //       let aksi = $(this).data('aksi');

      //       let catatan = $('#catatan_komite').val();
      //       let grading = $('input[name="grading_komite"]:checked').val();

      //       if (!grading) {
      //           $('#komite_error').text('Grading wajib dipilih');
      //           return;
      //       }

      //       $.ajax({
      //           url: base_url + 'ikprs/validasi_komite',
      //           type: 'POST',
      //           data: {
      //               id: id,
      //               aksi: aksi,
      //               catatan: catatan,
      //               grading: grading
      //           },
      //           dataType: 'json',
      //           success: function(res) {
      //               if (res.status == 'success') {
      //                   location.reload();
      //               } else {
      //                   $('#komite_error').text(res.message);
      //               }
      //           }
      //       });

      //   });

      function initValidasiKomite() {

          // realtime hapus error catatan
          $(document).on('input', '#catatan_komite', function() {
              if ($(this).val().trim() !== '') {
                  $('#komite_error').text('');
                  $(this).removeClass('is-invalid');
              }
          });

          // realtime hapus error grading
          $(document).on('change', 'input[name="grading_komite"]', function() {
              $('#komite_error').text('');
          });
      }


      // FUNCTION UTAMA
      function validasiKomite(btn) {

          let id = $(btn).data('id');
          let aksi = $(btn).data('aksi');

          let catatan = $('#catatan_komite').val().trim();
          let grading = $('input[name="grading_komite"]:checked').val();

          let error = '';

          // VALIDASI
          if (!catatan) {
              error = 'Catatan komite wajib diisi';
              $('#catatan_komite').addClass('is-invalid');
          } else if (!grading) {
              error = 'Grading wajib dipilih';
          }

          if (error) {
              $('#komite_error').text(error);
              return;
          }

          // reset error
          $('#komite_error').text('');
          $('#catatan_komite').removeClass('is-invalid');

          $(btn).prop('disabled', true);

          $.ajax({
              url: "<?= base_url('ikprs/validasi_komite') ?>",
              type: 'POST',
              data: {
                  id: id,
                  aksi: aksi,
                  catatan: catatan,
                  grading: grading
              },
              dataType: 'json',
              success: function(res) {
                  if (res.status == 'success') {
                      location.reload();
                  } else {
                      $('#komite_error').text(res.message);
                      $(btn).prop('disabled', false);
                  }
              },
              error: function() {
                  $('#komite_error').text('Terjadi kesalahan server');
                  $(btn).prop('disabled', false);
              }
          });
      }
  </script>