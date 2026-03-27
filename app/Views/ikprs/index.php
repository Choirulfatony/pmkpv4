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
  </style>

  <!-- begin::Container-->
  <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">

          <!-- LEFT SIDEBAR -->
          <div class="col-md-3">
              <!-- <a href="#" class="btn btn-primary w-100 mb-3">Compose</a> -->

              <!-- Profile Image -->
              <div class="card profile-card">
                  <div class="card-body text-center">
                      <img
                          src="<?= base_url('assets/login/img/rssm_icon.ico') ?>"
                          class="rounded-circle mb-2"
                          width="100">

                      <h5 class="fw-bold mb-0">
                          <?= esc(session('nama_lengkap')) ?>
                      </h5>

                      <small class="text-muted">
                          <?= esc(session('department_name')) ?>
                      </small>

                      <a href="<?= site_url('auth/logout') ?>" class="btn btn-primary w-100 mt-3">
                          <i class="bi bi-box-arrow-right me-1"></i>
                          Logout
                      </a>
                  </div>
              </div>



              <!-- Folders -->
              <div class="card">
                  <div class="card-body p-2">

                      <button class="btn btn-primary btn-sm w-100 btn-input">
                          <i class="bi bi-plus"></i> Input Laporan
                      </button>

                      <ul class="nav nav-pills flex-column">
                          <li class="nav-item">
                              <a class="nav-link d-flex align-items-center">
                                  <i class="bi bi-bell me-2"></i>
                                  Notifikasi
                                  <span class="badge bg-danger ms-auto">0
                                      <?php /* <?= esc($total_notif) ?>*/ ?>
                                  </span>
                              </a>
                          </li>

                          <li class="nav-item">
                              <a class="nav-link d-flex align-items-center">
                                  <i class="bi bi-check-circle me-2"></i>
                                  Approved
                                  <span class="badge bg-danger ms-auto">0
                                      <?php /* <?= esc($total_notif) ?> */ ?>

                                  </span>
                              </a>
                          </li>

                          <li class="nav-item">
                              <a class="nav-link d-flex align-items-center">
                                  <i class="bi bi-inbox me-2"></i>
                                  Inbox
                                  <span class="badge bg-primary ms-auto">0
                                      <?php /* <?= esc($total_sent + $total_draft) ?> */ ?>

                                  </span>
                              </a>
                          </li>

                          <li class="nav-item">
                              <a class="nav-link d-flex align-items-center">
                                  <i class="bi bi-send me-2"></i>
                                  Sent
                                  <span class="badge bg-success ms-auto">0
                                      <?php /* <?= esc($total_sent) ?>*/ ?>
                                  </span>
                              </a>
                          </li>

                          <li class="nav-item">
                              <a class="nav-link d-flex align-items-center">
                                  <i class="bi bi-file-earmark-text me-2"></i>
                                  Drafts
                                  <span class="badge bg-warning text-dark ms-auto">0
                                      <?php /* <?= esc($total_draft) ?>*/ ?>

                                  </span>
                              </a>
                          </li>
                      </ul>

                  </div>
              </div>

          </div>

          <!-- RIGHT CONTENT -->
          <div class="col-md-9">
              <div class="card card-outline card-primary">
                  <!-- HEADER -->
                  <div class="card-header mailbox-header d-flex align-items-center gap-2">
                      <!-- LEFT TOOLBAR -->
                      <button class="btn btn-mailbox btn-sm checkbox-toggle" type="button">
                          <i class="bi bi-square"></i>
                      </button>
                      <button class="btn btn-mailbox btn-sm">
                          <i class="bi bi-trash"></i>
                      </button>
                      <button class="btn btn-mailbox btn-sm">
                          <i class="bi bi-arrow-repeat"></i>
                      </button>

                      <!-- PUSH RIGHT -->
                      <div class="ms-auto"></div>

                      <!-- SEARCH -->
                      <div class="input-group input-group-sm mailbox-search">
                          <input type="text" class="form-control" placeholder="Search Mail">
                          <button class="btn btn-primary">
                              <i class="bi bi-search"></i>
                          </button>
                      </div>
                  </div>

                  <!-- BODY -->
                  <div class="card-body p-0">
                      <div class="table-responsive">
                          <table class="table table-hover mailbox-table mb-0">
                              <tbody>
                                  <tr>
                                      <td class="mailbox-check">
                                          <input class="form-check-input mailbox-checkbox" type="checkbox">
                                      </td>
                                      <td class="mailbox-star">
                                          <i class="bi bi-star-fill"></i>
                                      </td>
                                      <td class="mailbox-name">
                                          <a href="#">Alexander Pierce</a>
                                      </td>
                                      <td class="mailbox-subject">
                                          <strong>AdminLTE 3.0 Issue</strong> – Trying to find a solution…
                                      </td>
                                      <td class="mailbox-date">
                                          5 mins ago
                                      </td>
                                  </tr>

                                  <tr>
                                      <td class="mailbox-check">
                                          <input class="form-check-input mailbox-checkbox" type="checkbox">
                                      </td>
                                      <td class="mailbox-star">
                                          <i class="bi bi-star"></i>
                                      </td>
                                      <td class="mailbox-name">
                                          <a href="#">Alexander Pierce</a>
                                      </td>
                                      <td class="mailbox-subject">
                                          <strong>AdminLTE 4 Issue</strong> – Bootstrap 5 update…
                                      </td>
                                      <td class="mailbox-date">
                                          1 hour ago
                                      </td>
                                  </tr>

                              </tbody>
                          </table>
                      </div>
                  </div>

                  <!-- FOOTER -->
                  <div class="card-header mailbox-header d-flex align-items-center gap-2">
                      <!-- LEFT TOOLBAR -->
                      <button class="btn btn-mailbox btn-sm checkbox-toggle" type="button">
                          <i class="bi bi-square"></i>
                      </button>
                      <button class="btn btn-mailbox btn-sm">
                          <i class="bi bi-trash"></i>
                      </button>
                      <button class="btn btn-mailbox btn-sm">
                          <i class="bi bi-arrow-repeat"></i>
                      </button>

                      <!-- PUSH RIGHT -->
                      <div class="ms-auto"></div>

                      <div class="d-flex align-items-center gap-2">
                          <span class="text-muted">1–50 / 200</span>
                          <div class="btn-group btn-group-sm">
                              <button class="btn btn-mailbox">
                                  <i class="bi bi-chevron-left"></i>
                              </button>
                              <button class="btn btn-mailbox">
                                  <i class="bi bi-chevron-right"></i>
                              </button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

      </div>
  </div>

  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const btnLogout = document.getElementById('btnLogout');
          if (btnLogout) {
              btnLogout.addEventListener('click', function() {
                  window.location.href = "<?= site_url('auth/logout') ?>";
              });
          }
      });
  </script>