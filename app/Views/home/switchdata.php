         <!--begin::Container-->
         <div class="container-fluid">
             <!--begin::Row-->
             <div class="row">
                 <!-- Start col -->
                 <div class="col-lg-7 connectedSortable">
                     <div class="card mb-4">
                         <div class="card-header">
                             <h3 class="card-title">SW Data Center RSSM</h3>
                             <!-- <button id="scanBtnDataCenter" class="btn btn-primary btn-sm float-end">Scan</button> -->
                         </div>
                         <div class="card-body position-relative">
                             <div class="table-responsive position-relative">
                                 <!-- Overlay Loading -->
                                 <!-- <div id="loading_overlaydc" class="overlay">
                                     <div class="loader"></div>
                                 </div> -->
                                 <!-- ipTableDataCenter -->
                                 <!-- <table id="ipTableDataCenter" class="table table-sm table-bordered table-hover"> -->
                                 <table id="ipTableDataCenter" class="table table-striped table-hover table-bordered align-middle w-100">

                                     <thead>
                                         <tr>
                                             <th class="text-center text-nowrap" style="width: 50px;">#</th>
                                             <th class="text-nowrap text-center">IP Address</th>
                                             <th class="text-nowrap text-center">Status</th>
                                             <th class="text-nowrap text-center">MAC Address</th>
                                             <th class="text-nowrap text-center">Device Name</th>
                                             <th class="text-nowrap text-center">Location</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                     <!-- /.card -->
                 </div>
                 <!-- /.Start col -->

                 <!-- Start col -->
                 <div class="col-lg-5 connectedSortable">
                     <!-- Info Boxes Style 2 -->
                     <div class="small-box mb-4 text-bg-warning">
                         <div class="inner">
                             <h3>Aruba Instant On</h3>
                             <p>Instant On Portal</p>
                         </div>
                         <svg class="small-box-icon" fill="currentColor" viewBox="0 0 50 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <!-- Ikon WiFi (Kiri) -->
                             <g transform="translate(0,0)">
                                 <path fill="currentColor" d="M12 20c.828 0 1.5-.672 1.5-1.5S12.828 17 12 17s-1.5.672-1.5 1.5S11.172 20 12 20z" />
                                 <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M3.515 8.929a11.955 11.955 0 0116.97 0 1 1 0 001.415-1.415 13.955 13.955 0 00-19.8 0 1 1 0 001.415 1.415zM7.05 12.364a7.968 7.968 0 0111.313 0 1 1 0 001.414-1.414 9.968 9.968 0 00-14.142 0 1 1 0 001.414 1.414zM10.586 15.8a4 4 0 012.828 0 1 1 0 001.415-1.414 6 6 0 00-8.486 0 1 1 0 001.415 1.414 4 4 0 012.828 0z" />
                             </g>

                             <!-- Ikon Ethernet (Kanan) -->
                             <g transform="translate(24,0)">
                                 <path fill="currentColor" d="M4 10h16v4h2v-4a2 2 0 00-2-2h-4V4h-4v4H6a2 2 0 00-2 2v4h2v-4z" />
                                 <path fill="currentColor" d="M8 16v4h2v-4h4v4h2v-4h4v4h2v-4h-4v-2H8v2H4v4h2v-4h2z" />
                             </g>
                         </svg>
                         <div class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                             onclick="window.open('https://portal.instant-on.hpe.com/login', '_blank')"
                             style="cursor: pointer;">
                             More info <i class="bi bi-link-45deg"></i>
                         </div>
                     </div>
                     <!-- /.info-box -->
                     <div class="small-box mb-4 text-bg-success">
                         <div class="inner">
                             <h3>Unifi</h3>
                             <p>Unifi</p>
                         </div>
                         <svg class="small-box-icon" fill="currentColor" viewBox="0 0 50 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <!-- Ikon WiFi (Kiri) -->
                             <g transform="translate(0,0)">
                                 <path fill="currentColor" d="M12 20c.828 0 1.5-.672 1.5-1.5S12.828 17 12 17s-1.5.672-1.5 1.5S11.172 20 12 20z" />
                                 <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M3.515 8.929a11.955 11.955 0 0116.97 0 1 1 0 001.415-1.415 13.955 13.955 0 00-19.8 0 1 1 0 001.415 1.415zM7.05 12.364a7.968 7.968 0 0111.313 0 1 1 0 001.414-1.414 9.968 9.968 0 00-14.142 0 1 1 0 001.414 1.414zM10.586 15.8a4 4 0 012.828 0 1 1 0 001.415-1.414 6 6 0 00-8.486 0 1 1 0 001.415 1.414 4 4 0 012.828 0z" />
                             </g>

                             <!-- Ikon Ethernet (Kanan) -->
                             <g transform="translate(24,0)">
                                 <path fill="currentColor" d="M4 10h16v4h2v-4a2 2 0 00-2-2h-4V4h-4v4H6a2 2 0 00-2 2v4h2v-4z" />
                                 <path fill="currentColor" d="M8 16v4h2v-4h4v4h2v-4h4v4h2v-4h-4v-2H8v2H4v4h2v-4h2z" />
                             </g>
                         </svg>
                         <div class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                             onclick="window.open('https://192.168.0.5/network/default/dashboard', '_blank')"
                             style="cursor: pointer;">
                             More info <i class="bi bi-link-45deg"></i>
                         </div>
                     </div>
                     <!-- /.info-box -->
                     <div class="small-box mb-4 text-bg-danger">
                         <div class="inner">
                             <h3>Ruiji | Reyee</h3>
                             <p>Ruijie Networks</p>
                         </div>
                         <svg class="small-box-icon" fill="currentColor" viewBox="0 0 50 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <!-- Ikon WiFi (Kiri) -->
                             <g transform="translate(0,0)">
                                 <path fill="currentColor" d="M12 20c.828 0 1.5-.672 1.5-1.5S12.828 17 12 17s-1.5.672-1.5 1.5S11.172 20 12 20z" />
                                 <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M3.515 8.929a11.955 11.955 0 0116.97 0 1 1 0 001.415-1.415 13.955 13.955 0 00-19.8 0 1 1 0 001.415 1.415zM7.05 12.364a7.968 7.968 0 0111.313 0 1 1 0 001.414-1.414 9.968 9.968 0 00-14.142 0 1 1 0 001.414 1.414zM10.586 15.8a4 4 0 012.828 0 1 1 0 001.415-1.414 6 6 0 00-8.486 0 1 1 0 001.415 1.414 4 4 0 012.828 0z" />
                             </g>

                             <!-- Ikon Ethernet (Kanan) -->
                             <g transform="translate(24,0)">
                                 <path fill="currentColor" d="M4 10h16v4h2v-4a2 2 0 00-2-2h-4V4h-4v4H6a2 2 0 00-2 2v4h2v-4z" />
                                 <path fill="currentColor" d="M8 16v4h2v-4h4v4h2v-4h4v4h2v-4h-4v-2H8v2H4v4h2v-4h2z" />
                             </g>
                         </svg>
                         <div class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                             onclick="window.open('https://cloud-as.ruijienetworks.com/sso/login?service=https%3A%2F%2Fcloud-as.ruijienetworks.com%2Fadmin3%2F', '_blank')"
                             style="cursor: pointer;">
                             More info <i class="bi bi-link-45deg"></i>
                         </div>
                         <!-- /.info-box-content -->
                     </div>
                     <!-- /.card -->
                 </div>
                 <!-- /.Start col -->
             </div>
             <!-- /.row (main row) -->

             <!--begin::Row-->
             <div class="row">
                 <!-- Start col -->
                 <div class="col-lg-6 connectedSortable">
                     <div class="card mb-4">
                         <div class="card-header">
                             <h3 class="card-title">SW Distribusi FO RSSM</h3>
                             <!-- <button id="scanBtnDistribusiFo" class="btn btn-primary btn-sm float-end">Scan</button> -->
                         </div>
                         <div class="card-body position-relative">
                             <div class="table-responsive position-relative">
                                 <!-- Overlay Loading -->
                                 <!-- <div id="loading_overlayfo" class="overlay">
                                     <div class="loader"></div>
                                 </div> -->
                                 <!-- ipTableDistribusiFo -->
                                 <table id="" class="table table-sm table-bordered table-hover">
                                     <thead>
                                         <tr>
                                             <th class="text-center text-nowrap" style="width: 50px;">#</th>
                                             <th class="text-nowrap text-center">IP Address</th>
                                             <th class="text-nowrap text-center">Status</th>
                                             <th class="text-nowrap text-center">MAC Address</th>
                                             <th class="text-nowrap text-center">Device Name</th>
                                             <th class="text-nowrap text-center">Location</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                     <!-- /.card -->
                 </div>
                 <!-- /.Start col -->

                 <!-- Start col -->
                 <div class="col-lg-6 connectedSortable">
                     <div class="card mb-4">
                         <div class="card-header">
                             <h3 class="card-title">SW Ruangan RSSM</h3>
                             <!-- <button id="scanBtnDataSwRuagan" class="btn btn-primary btn-sm float-end">Scan</button> -->
                         </div>
                         <div class="card-body position-relative">
                             <div class="table-responsive position-relative">
                                 <!-- Overlay Loading -->
                                 <!-- <div id="loading_overlayru" class="overlay">
                                     <div class="loader"></div>
                                 </div> -->
                                 <!-- ipTableDataSwRuagan -->
                                 <table id="" class="table table-sm table-bordered table-hover">
                                     <thead>
                                         <tr>
                                             <th class="text-center text-nowrap" style="width: 50px;">#</th>
                                             <th class="text-nowrap text-center">IP Address</th>
                                             <th class="text-nowrap text-center">Status</th>
                                             <th class="text-nowrap text-center">MAC Address</th>
                                             <th class="text-nowrap text-center">Device Name</th>
                                             <th class="text-nowrap text-center">Location</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                     <!-- /.card -->
                 </div>
                 <!-- /.Start col -->
             </div>
             <!-- /.row (main row) -->
         </div>
         <!--end::Container-->

         <style>
             /* kecilkan info biar sama dengan pagination */
             div.dataTables_info {
                 font-size: 0.75rem;
                 /* kecilkan font */
                 color: #6c757d;
                 /* warna abu-abu soft */
                 padding-top: 0.4rem;
                 /* biar sejajar dengan pagination */
             }

             .table-responsive {
                 padding-bottom: 1rem;
                 /* biar scrollbar gak nutup pagination */
             }
         </style>

         <script>
             $(document).ready(function() {
                 //  console.log("jQuery jalan ✅");
                 ipTableDataCenter = TbDataCenterTable();
                 //  ipTableDistribusiFo = TbDataDistribusiFo();
                 //  ipTableDataSwRuagan = TbDataDistribusiRu();



                 // ✅ Event untuk menampilkan & menyembunyikan loading overlay DataCenter
                 //  $('#ipTableDataCenter').on('processing.dt', function(e, settings, processing) {
                 //      if (processing) {
                 //          $('#loading_overlaydc').fadeIn(); // Tampilkan overlay loading
                 //      } else {
                 //          $('#loading_overlaydc').fadeOut(); // Sembunyikan overlay loading
                 //      }
                 //  });

             });

             // Fungsi Nampilkan Tabel Data Center
             function TbDataCenterTable() {
                 return $('#ipTableDataCenter').DataTable({
                     processing: true,
                     serverSide: true,
                     autoWidth: false,
                     responsive: true,
                     ajax: {
                         url: "<?= site_url('Switchdata/get_ip_datacenter'); ?>",
                         type: "POST"
                     },
                     columns: [{
                             data: 0,
                             className: "text-center align-middle"
                         },
                         {
                             data: 1,
                             className: "text-center align-middle"
                         },
                         {
                             data: 2,
                             className: "text-center align-middle"
                         },
                         {
                             data: 3,
                             className: "text-center align-middle"
                         },
                         {
                             data: 4,
                             className: "text-center align-middle"
                         },
                         {
                             data: 5,
                             className: "text-center align-middle"
                         }
                     ],
                     columnDefs: [{
                         orderable: false,
                         targets: [0, 1, 2, 3, 4, 5]
                     }],
                     pageLength: 10,
                     lengthMenu: [10, 15, 25, 50],
                     language: {
                         emptyTable: "📭 Data tidak ditemukan atau belum tersedia.",
                         zeroRecords: "Tidak ada data yang sesuai dengan pencarian Anda.",
                         info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                         infoEmpty: "Menampilkan 0 - 0 dari 0 data",
                         infoFiltered: "(disaring dari _MAX_ total data)",
                         lengthMenu: "Tampilkan _MENU_ data",
                         search: "🔍 Cari:",
                         paginate: {
                             first: "Pertama",
                             last: "Terakhir",
                             next: "›",
                             previous: "‹"
                         }
                     },
                     // Integrasi penuh dengan Bootstrap 5
                     dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
                         "<'table-responsive'tr>" +
                         "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
                     rowCallback: function(row, data) {
                         let status = data[2];
                         if (status && status.includes("Online")) {
                             $(row).addClass("table-success");
                         } else {
                             $(row).addClass("table-danger");
                         }
                     },
                     drawCallback: function() {
                         // Tambah ikon bootstrap biar seragam
                         $('ul.pagination').addClass('pagination-sm mb-0');
                     }
                 });
             }

             // Fungsi untuk scan Data Center
             //  function scanDataCenter() {
             //      $.ajax({
             //          url: '<?= site_url('Switchdata/scan_datacenter') ?>',
             //          method: 'GET',
             //          dataType: 'json',
             //          beforeSend: function() {
             //              $('#scanBtnDataCenter').html('<span class="spinner-border spinner-border-sm"></span> Scanning....').prop('disabled', true);
             //          },
             //          success: function(response) {
             //              ipTableDataCenter.clear().draw(); // Kosongkan tabel sebelum mengisi data baru

             //              response.forEach(function(row, index) {
             //                  let statusClass = (row.status === 'Online') ? 'table-success' : 'table-danger';
             //                  let statusIcon = (row.status === 'Online') ?
             //                      '<i class="bi bi-check-circle text-success"></i> Online' :
             //                      '<i class="bi bi-check-circle text-danger"></i> Offline';
             //                  let ipLink = `<a href="//${row.ip_address}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none text-nowrap">${row.ip_address}</a>`;

             //                  let newRow = ipTableDataCenter.row.add([
             //                      `<span class="text-nowrap d-block text-center">${index + 1}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${ipLink}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${statusIcon}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.mac_address}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.device_name}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.nama_location}</span>`
             //                  ]).draw(false).node();

             //                  $(newRow).addClass(statusClass);
             //              });

             //              $('#scanBtnDataCenter').text('Scan').prop('disabled', false);

             //              // Hitung jumlah perangkat yang Online dan Offline
             //              // let onlineCount = response.filter(row => row.status === 'Online').length;
             //              // let offlineCount = response.filter(row => row.status === 'Offline').length;
             //              let onlineCount = response.filter(row => row.status === true).length;
             //              let offlineCount = response.filter(row => row.status === false).length;



             //              // ✅ Menampilkan SweetAlert setelah scan selesai
             //              const Toast = Swal.mixin({
             //                  toast: true,
             //                  position: 'top-right', // Posisi toast
             //                  showConfirmButton: false, // Tidak ada tombol OK
             //                  timer: 4000, // Toast menghilang setelah 4 detik
             //                  timerProgressBar: true, // Menampilkan progress bar
             //              });

             //              // Panggil Toast saat scan selesai
             //              Toast.fire({
             //                  icon: 'success',
             //                  title: `Scanning SW Data Center Selesai! 
             //             Berhasil memuat ${response.length} perangkat. 
             //             ✅ Online: ${onlineCount} | ❌ Offline: ${offlineCount}`
             //              });
             //              $('#scanBtnDataCenter').text('Scan').prop('disabled', false);
             //          },
             //          error: function(xhr, status, error) {
             //              console.error("Error saat scanning:", error);
             //              $('#scanBtnDataCenter').text('Scan').prop('disabled', false);
             //              Swal.fire({
             //                  icon: 'error',
             //                  title: 'Oops...',
             //                  text: 'Terjadi kesalahan saat scanning data. Silakan coba lagi!'
             //              });
             //          }
             //      });
             //  }

             // Fungsi Nampilkan Tabel SW Distribusi FO RSSM
             //  function TbDataDistribusiFo() {
             //      return $('#ipTableDistribusiFo').DataTable({
             //          "processing": false,
             //          "serverSide": true,
             //          "autoWidth": false,
             //          "responsive": true,
             //          "ajax": {
             //              "url": "<?= site_url('Switchdata/get_ip_datadistribusifo'); ?>",
             //              "type": "POST"
             //          },
             //          "columns": [{
             //                  "data": 0,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 1,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 2,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 3,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 4,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 5,
             //                  "className": "text-center"
             //              }
             //          ],
             //          "columnDefs": [{
             //              "orderable": false, // Matikan sorting
             //              "targets": [0, 1, 2, 3, 4, 5] // Target kolom No, IP, Status, dll.
             //          }],
             //          "pageLength": 10, // Jumlah data per halaman
             //          "lengthMenu": [10, 15, 25, 50], // Opsi jumlah data per halaman
             //          "language": {
             //              "emptyTable": "Data tidak ditemukan atau belum tersedia.",
             //              "zeroRecords": "Tidak ada data yang sesuai dengan pencarian Anda.",
             //              "info": "Showing  _START_ to _END_ of _TOTAL_ entries",
             //              "infoEmpty": "Showing 0 to 0 entries ",
             //              "infoFiltered": "(filtered  from  _MAX_ total entries)",
             //              "lengthMenu": "Show _MENU_ entries",
             //              "search": "Cari:",
             //              "paginate": {
             //                  "first": "First",
             //                  "last": "Last",
             //                  "next": "Next",
             //                  "previous": "Previous"
             //              }
             //          },
             //          "rowCallback": function(row, data) {
             //              let status = data[2]; // Status ada di kolom ke-2 (index 2)
             //              if (status.includes("Online")) {
             //                  $(row).addClass('table-success');
             //              } else {
             //                  $(row).addClass('table-danger');
             //              }
             //          }
             //      });
             //  }

             // Fungsi untuk scan Data SW Distribusi FO RSSM
             //  function scanDistribusiFo() {
             //      $.ajax({
             //          url: '<?= site_url('Switchdata/scan_distribusifo') ?>',
             //          method: 'GET',
             //          dataType: 'json',
             //          beforeSend: function() {
             //              $('#scanBtnDistribusiFo').html('<span class="spinner-border spinner-border-sm"></span> Scanning....').prop('disabled', true);
             //          },
             //          success: function(response) {
             //              ipTableDistribusiFo.clear().draw(); // Kosongkan tabel sebelum mengisi data baru

             //              response.forEach(function(row, index) {
             //                  let statusClass = (row.status === 'Online') ? 'table-success' : 'table-danger';
             //                  let statusIcon = (row.status === 'Online') ?
             //                      '<i class="bi bi-check-circle text-success"></i> Online' :
             //                      '<i class="bi bi-check-circle text-danger"></i> Offline';
             //                  let ipLink = `<a href="//${row.ip_address}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none text-nowrap">${row.ip_address}</a>`;

             //                  let newRow = ipTableDistribusiFo.row.add([
             //                      `<span class="text-nowrap d-block text-center">${index + 1}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${ipLink}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${statusIcon}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.mac_address}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.device_name}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.nama_location}</span>`
             //                  ]).draw(false).node();
             //                  $(newRow).addClass(statusClass);
             //              });

             //              $('#scanBtnDistribusiFo').text('Scan').prop('disabled', false);

             //              // Hitung jumlah perangkat yang Online dan Offline
             //              // let onlineCount = response.filter(row => row.status === 'Online').length;
             //              // let offlineCount = response.filter(row => row.status === 'Offline').length;
             //              let onlineCount = response.filter(row => row.status === true).length;
             //              let offlineCount = response.filter(row => row.status === false).length;


             //              // ✅ Menampilkan SweetAlert setelah scan selesai
             //              const Toast = Swal.mixin({
             //                  toast: true,
             //                  position: 'top-right', // Posisi toast
             //                  showConfirmButton: false, // Tidak ada tombol OK
             //                  timer: 4000, // Toast menghilang setelah 4 detik
             //                  timerProgressBar: true, // Menampilkan progress bar
             //              });

             //              // Panggil Toast saat scan selesai
             //              Toast.fire({
             //                  icon: 'success',
             //                  title: `Scanning SW Distribusi FO Selesai! 
             //              Berhasil memuat ${response.length} perangkat. 
             //              ✅ Online: ${onlineCount} | ❌ Offline: ${offlineCount}`
             //              });
             //              $('#scanBtnDistribusiFo').text('Scan').prop('disabled', false);
             //          },
             //          error: function(xhr, status, error) {
             //              console.error("Error saat scanning:", error);
             //              $('#scanBtnDistribusiFo').text('Scan').prop('disabled', false);
             //              Swal.fire({
             //                  icon: 'error',
             //                  title: 'Oops...',
             //                  text: 'Terjadi kesalahan saat scanning data. Silakan coba lagi!'
             //              });
             //          }
             //      });
             //  }

             // Fungsi Nampilkan Tabel SW Distribusi RU RSSM
             //  function TbDataDistribusiRu() {
             //      return $('#ipTableDataSwRuagan').DataTable({
             //          "processing": false,
             //          "serverSide": true,
             //          "autoWidth": false,
             //          "responsive": true,
             //          "ajax": {
             //              "url": "<?= site_url('Switchdata/get_ip_datadistribusiruangan'); ?>",
             //              "type": "POST"
             //          },
             //          "columns": [{
             //                  "data": 0,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 1,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 2,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 3,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 4,
             //                  "className": "text-center"
             //              },
             //              {
             //                  "data": 5,
             //                  "className": "text-center"
             //              }
             //          ],
             //          "columnDefs": [{
             //              "orderable": false, // Matikan sorting
             //              "targets": [0, 1, 2, 3, 4, 5] // Target kolom No, IP, Status, dll.
             //          }],
             //          "pageLength": 10, // Jumlah data per halaman
             //          "lengthMenu": [10, 15, 25, 50], // Opsi jumlah data per halaman
             //          "language": {
             //              "emptyTable": "Data tidak ditemukan atau belum tersedia.",
             //              "zeroRecords": "Tidak ada data yang sesuai dengan pencarian Anda.",
             //              "info": "Showing  _START_ to _END_ of _TOTAL_ entries",
             //              "infoEmpty": "Showing 0 to 0 entries ",
             //              "infoFiltered": "(filtered  from  _MAX_ total entries)",
             //              "lengthMenu": "Show _MENU_ entries",
             //              "search": "Cari:",
             //              "paginate": {
             //                  "first": "First",
             //                  "last": "Last",
             //                  "next": "Next",
             //                  "previous": "Previous"
             //              }
             //          },
             //          "rowCallback": function(row, data) {
             //              let status = data[2]; // Status ada di kolom ke-2 (index 2)
             //              if (status.includes("Online")) {
             //                  $(row).addClass('table-success');
             //              } else {
             //                  $(row).addClass('table-danger');
             //              }
             //          }
             //      });
             //  }

             // Fungsi untuk scan Data SW Distribusi RU RSSM
             //  function scanDistribusiRu() {
             //      $.ajax({
             //          url: '<?= site_url('Switchdata/scan_dataruangan') ?>',
             //          method: 'GET',
             //          dataType: 'json',
             //          beforeSend: function() {
             //              $('#scanBtnDataSwRuagan').html('<span class="spinner-border spinner-border-sm"></span> Scanning....').prop('disabled', true);
             //          },
             //          success: function(response) {
             //              ipTableDataSwRuagan.clear().draw(); // Kosongkan tabel sebelum mengisi data baru

             //              response.forEach(function(row, index) {
             //                  let statusClass = (row.status === 'Online') ? 'table-success' : 'table-danger';
             //                  let statusIcon = (row.status === 'Online') ?
             //                      '<i class="bi bi-check-circle text-success"></i> Online' :
             //                      '<i class="fa fa-times-circle text-danger"></i> Offline';
             //                  let ipLink = `<a href="//${row.ip_address}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none text-nowrap">${row.ip_address}</a>`;

             //                  let newRow = ipTableDataSwRuagan.row.add([
             //                      `<span class="text-nowrap d-block text-center">${index + 1}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${ipLink}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${statusIcon}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.mac_address}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.device_name}</span>`,
             //                      `<span class="text-nowrap d-block text-center">${row.nama_location}</span>`
             //                  ]).draw(false).node();
             //                  $(newRow).addClass(statusClass);
             //              });

             //              $('#scanBtnDataSwRuagan').text('Scan').prop('disabled', false);

             //              // Hitung jumlah perangkat yang Online dan Offline
             //              // let onlineCount = response.filter(row => row.status === 'Online').length;
             //              // let offlineCount = response.filter(row => row.status === 'Offline').length;
             //              let onlineCount = response.filter(row => row.status === true).length;
             //              let offlineCount = response.filter(row => row.status === false).length;


             //              // ✅ Menampilkan SweetAlert setelah scan selesai
             //              const Toast = Swal.mixin({
             //                  toast: true,
             //                  position: 'top-right', // Posisi toast
             //                  showConfirmButton: false, // Tidak ada tombol OK
             //                  timer: 4000, // Toast menghilang setelah 4 detik
             //                  timerProgressBar: true, // Menampilkan progress bar
             //              });

             //              // Panggil Toast saat scan selesai
             //              Toast.fire({
             //                  icon: 'success',
             //                  title: `Scanning SW Distribusi Ruangan Selesai! 
             //              Berhasil memuat ${response.length} perangkat. 
             //              ✅ Online: ${onlineCount} | ❌ Offline: ${offlineCount}`
             //              });
             //              $('#scanBtnDataSwRuagan').text('Scan').prop('disabled', false);
             //          },
             //          error: function(xhr, status, error) {
             //              console.error("Error saat scanning:", error);
             //              $('#scanBtnDataSwRuagan').text('Scan').prop('disabled', false);
             //              Swal.fire({
             //                  icon: 'error',
             //                  title: 'Oops...',
             //                  text: 'Terjadi kesalahan saat scanning data. Silakan coba lagi!'
             //              });
             //          }
             //      });
             //  }
         </script>