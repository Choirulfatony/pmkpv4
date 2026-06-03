<?php $this->extend('_layout/_template') ?>

<?php $this->section('content') ?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><?= esc($judul) ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url('/siimut/dashboard') ?>">Beranda</a></li>
          <li class="breadcrumb-item active"><?= esc($judul) ?></li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Indikator <?= esc($modTitle) ?></h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-indikator" onclick="clearForm()">
                <i class="fas fa-plus"></i> Tambah Indikator
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="table-indikator" class="table table-bordered table-striped dataTable dtr-inline" style="width:100%">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Indikator</th>
                  <th>Target</th>
                  <th>Satuan</th>
                  <th>Frekuensi</th>
                  <th>Kategori</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama Indikator</th>
                  <th>Target</th>
                  <th>Satuan</th>
                  <th>Frekuensi</th>
                  <th>Kategori</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>

<!-- Modal -->
<div class="modal fade" id="modal-indikator" tabindex="-1" role="aria-labelledby="modal-indikator-label" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-indikator-label">Form Indikator</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-indikator">
          <input type="hidden" id="indicator_id" name="indicator_id" value="0">
          <input type="hidden" id="module" name="module" value="<?= esc($module) ?>">
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Indikator <span class="text-red">*</span></label>
                <input type="text" class="form-control" id="indicator_element" name="indicator_element" placeholder="Nama indikator">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>ID Nama Indikator</label>
                <input type="text" class="form-control" id="indicator_name_id" name="indicator_name_id" placeholder="ID nama indikator (untuk pencarian)">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Target</label>
                <input type="text" class="form-control" id="indicator_target" name="indicator_target" placeholder="Target nilai">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Satuan</label>
                <input type="text" class="form-control" id="indicator_units" name="indicator_units" placeholder="Satuan (misal: %)">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Target Unit</label>
                <input type="text" class="form-control" id="indicator_target_unit" name="indicator_target_unit" placeholder="Unit target (opsional)">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Perhitungan Target</label>
                <input type="text" class="form-control" id="indicator_target_calculation" name="indicator_target_calculation" placeholder="Contoh: >, <, >=, <=, =">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Faktor Pengali</label>
                <input type="text" class="form-control" id="indicator_factors" name="indicator_factors" placeholder="Faktor pengali (misal: 100)">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Frekuensi</label>
                <select class="form-control" id="indicator_frequency" name="indicator_frequency">
                  <option value="D">Harian</option>
                  <option value="M">Bulanan</option>
                  <option value="Y">Tahunan</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Standar Nilai</label>
                <input type="number" class="form-control" id="indicator_value_standard" name="indicator_value_standard" placeholder="Standar nilai (opsional)">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Area Pemantauan</label>
                <input type="text" class="form-control" id="indicator_monitoring_area" name="indicator_monitoring_area" placeholder="Area pemantauan">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tipe Indikator</label>
                <input type="text" class="form-control" id="indicator_type" name="indicator_type" placeholder="Tipe indikator">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Sumber Data</label>
                <input type="text" class="form-control" id="indicator_source_of_data" name="indicator_source_of_data" placeholder="Sumber data indikator">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Definisi Indikator</label>
                <textarea class="form-control" id="indicator_definition" name="indicator_definition" rows="3" placeholder="Definisi lengkap indikator"></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Kriteria Inklusif</label>
                <textarea class="form-control" id="indicator_criteria_inclusive" name="indicator_criteria_inclusive" rows="3" placeholder="Kriteria yang termasuk dalam perhitungan"></textarea>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kriteria Eksklusif</label>
                <textarea class="form-control" id="indicator_criteria_exclusive" name="indicator_criteria_exclusive" rows="3" placeholder="Kriteria yang dikecualikan dari perhitungan"></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>LCL (Batas Bawah Kontrol)</label>
                <input type="text" class="form-control" id="indicator_lcl" name="indicator_lcl" placeholder="Batas bawah kontrol">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>UCL (Batas Atas Kontrol)</label>
                <input type="text" class="form-control" id="indicator_ucl" name="indicator_ucl" placeholder="Batas atas kontrol">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Berlaku</label>
                <input type="date" class="form-control" id="indicator_valid_date" name="indicator_valid_date">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>No. Urut</label>
                <input type="number" class="form-control" id="indicator_order_number" name="indicator_order_number" placeholder="Nomor urut untuk sorting">
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="indicator_iscomplete" name="indicator_iscomplete" value="1">
              <label class="form-check-label" for="indicator_iscomplete">Indikator Selesai</label>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Catatan Terakhir Update</label>
                <input type="text" class="form-control-plaintext" id="indicator_last_updated" readonly>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btn-save-indikator">Simpan</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php $this->endSection() ?>

<?php $this->section('pagejs') ?>
<script>
  let table;
  let currentModule = '<?= esc($module) ?>';
  
  $(document).ready(function () {
    // Initialize DataTable
    table = $('#table-indikator').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= base_url('siimut/data-indikator/ajax-get-data') ?>",
        type: 'POST',
        data: function (d) {
          d.module = currentModule;
          return d;
        }
      },
      columns: [
        { data: null, className: 'dt-center', orderable: false, searchable: false }, // No
        { data: 'indicator_element' },
        { data: 'indicator_target' },
        { data: 'indicator_units' },
        { data: 'indicator_frequency' },
        { data: 'indicator_category_id' },
        { data: 'indicator_record_status' },
        { data: null, className: 'dt-center', orderable: false, searchable: false } // Actions
      ],
      columnDefs: [
        {
          targets: 0,
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          targets: 4,
          render: function (data, type, row) {
            const freqMap = {'D': 'Harian', 'M': 'Bulanan', 'Y': 'Tahunan'};
            return freqMap[data] || data;
          }
        },
        {
          targets: 5,
          render: function (data, type, row) {
            const catMap = {'4': 'INM', '5': 'IMPRS', '6': 'IMPUnit'};
            return catMap[data] || data;
          }
        },
        {
          targets: 6,
          render: function (data, type, row) {
            const statusMap = {'A': '<span class="badge badge-success">Aktif</span>', 'D': '<span class="badge badge-info">Draft</span>', 'X': '<span class="badge badge-secondary">Nonaktif</span>'};
            return statusMap[data] || data;
          }
        },
        {
          targets: 7,
          render: function (data, type, row) {
            let buttons = '';
            buttons += '<button type="button" class="btn btn-sm btn-info mr-1" onclick=\"editIndikator(' + row.indicator_id + ')\" title="Edit"><i class="fas fa-edit"></i></button>';
            buttons += '<button type="button" class="btn btn-sm btn-danger mr-1" onclick=\"deleteIndikator(' + row.indicator_id + ')\" title="Hapus"><i class="fas fa-trash"></i></button>';
            if (row.indicator_record_status === 'X') {
              buttons += '<button type="button" class="btn btn-sm btn-warning mr-1" onclick=\"restoreIndikator(' + row.indicator_id + ')\" title="Pulihkan"><i class="fas fa-undo"></i></button>';
            }
            return buttons;
          }
        }
      ],
      language: {
        url: "<?= base_url('vendor/datatables.net/i18n/id.json') ?>"
      }
    });
    
    // Form submission
    $('#btn-save-indikator').on('click', function () {
      saveIndikator();
    });
    
    $('#form-indikator').on('submit', function (e) {
      e.preventDefault();
      saveIndikator();
    });
  });
  
  function clearForm() {
    $('#form-indikator')[0].reset();
    $('#indicator_id').val('0');
    $('#indicator_last_updated').val('');
    $('#modal-indikator-label').text('Tambah Indikator Baru');
  }
  
  function editIndikator(id) {
    $.ajax({
      url: "<?= base_url('siimut/data-indikator/get-detail') ?>",
      type: 'POST',
      data: {id: id, module: currentModule},
      dataType: 'json',
      success: function (response) {
        if (response.status && response.data) {
          const data = response.data;
          $('#indicator_id').val(data.indicator_id || 0);
          $('#indicator_element').val(data.indicator_element || '');
          $('#indicator_name_id').val(data.indicator_name_id || '');
          $('#indicator_target').val(data.indicator_target || '');
          $('#indicator_target_calculation').val(data.indicator_target_calculation || '');
          $('#indicator_factors').val(data.indicator_factors || '');
          $('#indicator_units').val(data.indicator_units || '');
          $('#indicator_target_unit').val(data.indicator_target_unit || '');
          $('#indicator_frequency').val(data.indicator_frequency || 'D');
          $('#indicator_value_standard').val(data.indicator_value_standard || 0);
          $('#indicator_order_number').val(data.indicator_order_number || 0);
          $('#indicator_type').val(data.indicator_type || '');
          $('#indicator_monitoring_area').val(data.indicator_monitoring_area || '');
          $('#indicator_source_of_data').val(data.indicator_source_of_data || '');
          $('#indicator_definition').val(data.indicator_definition || '');
          $('#indicator_criteria_inclusive').val(data.indicator_criteria_inclusive || '');
          $('#indicator_criteria_exclusive').val(data.indicator_criteria_exclusive || '');
          $('#indicator_lcl').val(data.indicator_lcl || '');
          $('#indicator_ucl').val(data.indicator_ucl || '');
          $('#indicator_valid_date').val(data.indicator_valid_date ? data.indicator_valid_date.split('T')[0] : '');
          $('#indicator_iscomplete').prop('checked', data.indicator_iscomplete == 1);
          $('#indicator_last_updated').val(data.indicator_last_updated || '');
          
          $('#modal-indikator-label').text('Edit Indikator: ' + (data.indicator_element || ''));
          $('#modal-indikator').modal('show');
        } else {
          Swal.fire('Error', response.message || 'Gagal mengambil data indikator', 'error');
        }
      },
      error: function () {
        Swal.fire('Error', 'Gagal terhubung ke server', 'error');
      }
    });
  }
  
  function saveIndikator() {
    const formData = $('#form-indikator').serializeArray();
    const data = {};
    formData.forEach(function (item) {
      data[item.name] = item.value;
    });
    
    $.ajax({
      url: "<?= base_url('siimut/data-indikator/save') ?>",
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function (response) {
        if (response.status) {
          Swal.fire('Berhasil', response.message, 'success');
          $('#modal-indikator').modal('hide');
          table.ajax.reload(null, false);
        } else {
          Swal.fire('Error', response.message, 'error');
        }
      },
      error: function () {
        Swal.fire('Error', 'Gagal terhubung ke server', 'error');
      }
    });
  }
  
  function deleteIndikator(id) {
    Swal.fire({
      title: 'Anda yakin?',
      text: "Data indikator ini akan dihapus (soft delete)!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "<?= base_url('siimut/data-indikator/delete') ?>",
          type: 'POST',
          data: {id: id, module: currentModule},
          dataType: 'json',
          success: function (response) {
            if (response.status) {
              Swal.fire('Berhasil', response.message, 'success');
              table.ajax.reload(null, false);
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          },
          error: function () {
            Swal.fire('Error', 'Gagal terhubung ke server', 'error');
          }
        });
      }
    });
  }
  
  function restoreIndikator(id) {
    Swal.fire({
      title: 'Anda yakin?',
      text: "Data indikator ini akan dipulihkan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, pulihkan!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "<?= base_url('siimut/data-indikator/restore') ?>",
          type: 'POST',
          data: {id: id, module: currentModule},
          dataType: 'json',
          success: function (response) {
            if (response.status) {
              Swal.fire('Berhasil', response.message, 'success');
              table.ajax.reload(null, false);
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          },
          error: function () {
            Swal.fire('Error', 'Gagal terhubung ke server', 'error');
          }
        });
      }
    });
  }
</script>
<?php $this->endSection() ?>