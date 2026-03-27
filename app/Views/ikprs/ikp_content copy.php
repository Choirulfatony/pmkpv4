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
          color: #212529 !important;
          padding: .55rem .75rem;
          font-size: 14px;
      }

      .nav-pills .nav-link i {
          color: #6c757d;
      }

      .nav-pills .nav-link:hover {
          background-color: #f4f6f9;
          color: #000 !important;
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
          color: #007bff;
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

      /* .row {
          margin-left: -12px;
          margin-right: -12px;
      }

      html,
      body {
          overflow-x: hidden;
      } */

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
                                  <a href="#" class="nav-link d-flex align-items-center">
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

                              <!-- <li class="nav-item">
                                  <a class="nav-link d-flex align-items-center">
                                      <i class="bi bi-bell me-2"></i>
                                      Notifikasi
                                      <span class="badge bg-danger ms-auto">0
                                          <?php /* <?= esc($total_notif) ?>*/ ?>
                                      </span>
                                  </a>
                              </li> -->

                              <!-- <li class="nav-item">
                                  <a class="nav-link d-flex align-items-center">
                                      <i class="bi bi-check-circle me-2"></i>
                                      Approved
                                      <span class="badge bg-danger ms-auto">0
                                          <?php /* <?= esc($total_notif) ?> */ ?>
                                      </span>
                                  </a>
                              </li> -->

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

      const user_id = "<?= session('hris_user_id') ?>";
      const user_role = "<?= session('user_role') ?>";

      window.IKP = {
          isSearching: false
      };


      //   $(document).ready(function() {
      //       loadNotif();
      //       refreshBadgeCounter();

      //       setInterval(function() {

      //           loadNotif();
      //           refreshBadgeCounter();

      //       }, 10000);

      //   });
      
      $(document).ready(function() {

          loadAllNotif();

          setInterval(function() {
              loadAllNotif();
          }, 10000);

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

      /* ===== AUTO LOAD SAAT HALAMAN DIBUKA ===== */
      $(document).ready(function() {
          loadInbox(); // 🎯 INI YANG BIKIN TIDAK KOSONG
      });


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

      /* ===== REFRESH BADGE COUNTER (AJAX) ===== */
      //   function refreshBadgeCounter() {
      //       $.ajax({
      //           url: "<?= base_url('ikprs/counter-ajax') ?>",
      //           type: "GET",
      //           dataType: "json",
      //           success: function(res) {
      //               //   console.log("COUNTER:", res); // 🔎 cek response

      //               let notif = res.total_notif ?? 0;
      //               let inbox = res.total_inbox ?? 0;
      //               let draft = res.total_draft ?? 0;
      //               let send = res.total_send ?? 0;

      //               $('#badge-notif_header').text(notif);

      //               $('#badge-notif')
      //                   .text(notif)
      //                   .removeClass('bg-info bg-info')
      //                   .addClass(notif > 0 ? 'bg-info' : 'bg-info');

      //               $('#badge-inbox')
      //                   .text(inbox)
      //                   .removeClass('bg-primary bg-primary')
      //                   .addClass(inbox > 0 ? 'bg-primary' : 'bg-primary');

      //               $('#badge-draft')
      //                   .text(draft)
      //                   .removeClass('bg-warning bg-warning')
      //                   .addClass(draft > 0 ? 'bg-warning' : 'bg-warning');

      //               $('#badge-send')
      //                   .text(send)
      //                   .removeClass('bg-success bg-success')
      //                   .addClass(send > 0 ? 'bg-success' : 'bg-success');

      //           }
      //       });
      //   }



      function loadAllNotif() {

          $.ajax({
              url: "<?= base_url('ikprs/counter-ajax') ?>",
              type: "GET",
              dataType: "json",
              success: function(res) {

                  let notif = res.total_notif ?? 0;
                  let inbox = res.total_inbox ?? 0;
                  let draft = res.total_draft ?? 0;
                  let send = res.total_send ?? 0;

                  /* =============================
                     UPDATE BADGE COUNTER
                  ============================= */

                  $('#badge-notif_header').text(notif);

                  $('#badge-notif')
                      .text(notif)
                      .removeClass('bg-info')
                      .addClass('bg-info');

                  $('#badge-inbox')
                      .text(inbox)
                      .removeClass('bg-primary')
                      .addClass('bg-primary');

                  $('#badge-draft')
                      .text(draft)
                      .removeClass('bg-warning')
                      .addClass('bg-warning');

                  $('#badge-send')
                      .text(send)
                      .removeClass('bg-success')
                      .addClass('bg-success');


                  /* =============================
                     UPDATE DROPDOWN NOTIF
                  ============================= */

                  $('#notif-header').text(notif + " Notifications");

                  let html = '';

                  if (!res.data || res.data.length === 0) {

                      html = `
                <a class="dropdown-item text-center text-muted">
                    Tidak ada notifikasi
                </a>`;

                  } else {

                      res.data.forEach(function(item) {

                          let icon = "bi bi-info-circle text-secondary";

                          switch (item.jenis) {

                              case "KTD":
                                  icon = "bi bi-exclamation-triangle-fill text-danger";
                                  break;

                              case "KNC":
                                  icon = "bi bi-exclamation-circle-fill text-warning";
                                  break;

                              case "KTC":
                                  icon = "bi bi-info-circle-fill text-primary";
                                  break;

                              case "KPC":
                                  icon = "bi bi-shield-exclamation text-purple";
                                  break;

                              case "SENTINEL":
                                  icon = "bi bi-exclamation-octagon-fill text-danger";
                                  break;
                          }

                          let bg = item.is_read == 0 ? "bg-light" : "";
                          let bold = item.is_read == 0 ? "fw-bold" : "";
                          let status_read = item.is_read == 0 ? "Baru" : "Sudah dibaca";
                          let warna_status = item.is_read == 0 ? "text-danger" : "text-success";

                          //   let disabled = '';

                          //   if (role !== 'KARU' || item.status_laporan === 'INSTALASI') {
                          //       disabled = 'style="pointer-events:none;opacity:.5"';
                          //   }
                          let disabledClass = '';
                          let receiver = item.current_receiver_id ?? 0;

                          if (parseInt(user_id) !== parseInt(receiver)) {
                              disabledClass = 'notif-disabled';
                          }
                          //   if (Number(user_id) !== Number(item.current_receiver_id)) {
                          //       disabledClass = 'notif-disabled';
                          //   }
                          html += `
                            <a href="#" 
                            class="dropdown-item notif-open ${bg} ${bold} ${disabledClass}"
                            data-id="${item.id}"
                            data-insiden="${item.insiden_id}"
                            data-notif="${item.notif_id}"
                            data-receiver="${item.current_receiver_id}"
                            data-status="${item.status_laporan}">


                            <div class="d-flex">

                                    <div class="me-2">
                                        <i class="${icon}"></i>
                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="d-flex justify-content-between">

                                            <span>
                                                <b>${item.jenis ?? '-'}</b> - ${item.unit ?? '-'}
                                            </span>

                                            <small class="text-muted">
                                                ${item.waktu_lalu ?? ''}
                                            </small>

                                        </div>

                                        <small class="text-muted">
                                            ${item.status_text ?? ''}
                                        </small>

                                        <br>

                                        <small class="${warna_status}">
                                            ${status_read}
                                        </small>

                                    </div>

                            </div>

                            </a>

                            <div class="dropdown-divider"></div>
                            `;
                      });

                  }

                  $('#notif-list').html(html);

              }
          });

      }

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

                  loadAllNotif(); // update badge + dropdown notif


              },
              error: function(xhr) {
                  console.log("error tandai dibaca", xhr);
              }

          });

      }

      //   $(document).on('click', '.btn-kirim-verifikasi', function() {
      //       kirimVerifikasi(this);
      //   });

      //   function kirimVerifikasi(btn) {

      //       let id = $(btn).data('id');
      //       let catatan = $('#catatan_karu').val();
      //       let grading = $('input[name=grading]:checked').val();

      //       if (!grading) {
      //           alert('Grading risiko harus dipilih');
      //           return;
      //       }

      //       $.ajax({
      //           url: "<?= base_url('ikprs/simpan_verifikasi') ?>",
      //           type: "POST",
      //           data: {
      //               insiden_id: id,
      //               catatan_karu: catatan,
      //               grading: grading
      //           },
      //           success: function(res) {
      //               //   console.log(res);
      //               alert('Verifikasi berhasil disimpan');
      //           }
      //       });

      //   }

      $(document).on('click', '.btn-kirim-verifikasi', function() {
          kirimVerifikasi(this);
      });

      //   function kirimVerifikasi(btn) {

      //       let id = $(btn).data('id');
      //       let catatan = $('#catatan_karu').val();
      //       let grading = $('input[name=grading]:checked').val();

      //       // validasi grading
      //       if (!grading) {
      //           alert('Grading risiko harus dipilih');
      //           return;
      //       }

      //       // validasi catatan
      //       if (!catatan || catatan.trim() === '') {
      //           alert('Catatan KARU tidak boleh kosong');
      //           $('#catatan_karu').focus();
      //           return;
      //       }

      //       // disable tombol agar tidak double klik
      //       $(btn).prop('disabled', true);

      //       $.ajax({
      //           url: "<?= base_url('ikprs/simpan_verifikasi') ?>",
      //           type: "POST",
      //           dataType: "json",
      //           data: {
      //               insiden_id: id,
      //               catatan_karu: catatan,
      //               grading: grading
      //           },
      //           success: function(res) {

      //               if (res.status) {

      //                   alert(res.message);

      //                   // reload detail laporan
      //                   loadInboxDetail(res.insiden_id);

      //               } else {

      //                   alert(res.message);
      //                   $(btn).prop('disabled', false);

      //               }

      //           },
      //           error: function() {
      //               alert('Terjadi kesalahan server');
      //               $(btn).prop('disabled', false);
      //           }
      //       });

      //   }
      //   $(document).on('click', '.btn-kirim-verifikasi', function() {
      //       kirimVerifikasi(this);
      //   });

      //   $(document).on('click', '.btn-kirim-verifikasi', function() {
      //       kirimVerifikasi(this);
      //   });

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
                          loadAllNotif();
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
  </script>