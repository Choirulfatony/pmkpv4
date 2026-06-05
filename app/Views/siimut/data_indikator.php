<div class="container-fluid">
    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools d-flex flex-wrap justify-content-between w-100 align-items-center gap-2">
                <form onsubmit="event.preventDefault(); get_pencariandata();" class="mb-0">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <input type="text" id="cari_input" class="form-control" placeholder="Cari...">
                        <button class="btn btn-outline-secondary" type="submit" title="Cari">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearForm(); bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-indikator')).show();" title="Tambah Indikator">
                        <i class="fas fa-plus-circle"></i> Tambah
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reload_table()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

                    <div class="card-body" style="overflow-x:auto; position: relative;">
            <div class="overlay-wrapper" id="loading_overlay" style="display: none;">
                <div class="overlay">
                    <i class="loader"></i>
                </div>
            </div>
            <div class="table-responsive">
                <table id="table-indikator" class="table table-striped" style="width: 100%;">
                    <thead>
                        <tr class="header-row">
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Judul Indikator</th>
                            <th class="text-center" style="width: 70px;">Target</th>
                            <th class="text-center" style="width: 80px;">Satuan</th>
                            <th class="text-center" style="width: 90px;">Frekuensi</th>
                            <th class="text-center" style="width: 80px;">Status</th>
                            <th class="text-center" style="width: 130px;"><i class="fa fa-eye"></i></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-indikator" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #6f42c1; color: white;">
                <h6 class="modal-title" id="modal-indikator-label" style="color: #ffffff; font-size:0.9rem;">Form Indikator</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size:0.7rem;"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-indikator">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1" hidden>
                                <input type="text" class="form-control form-control-sm" name="indicator_id" id="indicator_id">
                            </div>
                            <div class="mb-1" hidden>
                                <input type="text" class="form-control form-control-sm" name="module" id="module" value="<?= esc($module) ?>">
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Judul Indikator <span class="text-danger">*</span></label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Judul Indikator ..." name="indicator_element" id="indicator_element"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Dasar Pemikiran</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Dasar Pemikiran ..." name="dasar_pemikiran" id="dasar_pemikiran"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Dimensi Mutu</label>
                                <div class="row small">
                                    <div class="col-sm-6">
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu1" value="1">
                                            <label class="form-check-label" for="dimensi_mutu1">Keselamatan (Safety)</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu2" value="2">
                                            <label class="form-check-label" for="dimensi_mutu2">Efektivitas (Efectiveness)</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu3" value="3">
                                            <label class="form-check-label" for="dimensi_mutu3">Fokus pada Pasien (Patient Centerdness)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu4" value="4">
                                            <label class="form-check-label" for="dimensi_mutu4">Tepat waktu (Timely)</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu5" value="5">
                                            <label class="form-check-label" for="dimensi_mutu5">Efisiensi (Efficiency)</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu6" value="6">
                                            <label class="form-check-label" for="dimensi_mutu6">Keadilan (Equity)</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="dimensi_mutu[]" class="form-check-input" type="checkbox" id="dimensi_mutu7" value="7">
                                            <label class="form-check-label" for="dimensi_mutu7">Terintegrasi</label>
                </div>
            </div>
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Tujuan</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Tujuan ..." name="tujuan" id="tujuan"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Definisi Operasional</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Definisi Operasional..." name="definisi_operasional" id="definisi_operasional"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Jenis Indikator</label>
                                <select class="form-select form-select-sm" name="jenis_indikator" id="jenis_indikator">
                                    <option value=""></option>
                                    <option value="Input">Input</option>
                                    <option value="Proses">Proses</option>
                                    <option value="Output">Output</option>
                                    <option value="Outcome">Outcome</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="small mb-0">Simbol Operator Standar Capaian</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Contoh: >= atau <=" name="indicator_target_calculation" id="indicator_target_calculation">
                                    </div>
                                    <div class="mb-1">
                                        <label class="small mb-0">Standar Capaian</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Standar Capaian..." name="indicator_target" id="indicator_target" onkeypress="return hanyaAngka(event)">
                                    </div>
                                    <div class="mb-1">
                                        <label class="small mb-0">Satuan Standar</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Satuan Standar..." name="indicator_target_unit" id="indicator_target_unit">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="small mb-0">Faktor Pengali</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Faktor Pengali..." name="indicator_factors" id="indicator_factors" onkeypress="return hanyaAngka(event)">
                                    </div>
                                    <div class="mb-1">
                                        <label class="small mb-0">Satuan Pengali</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Satuan Pengali..." name="indicator_units" id="indicator_units">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Kriteria Inklusi</label>
                                <textarea class="form-control form-control-sm" placeholder="Kriteria Inklusi ..." name="indicator_inclusive" id="indicator_inclusive"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="small mb-0">Kriteria Eksklusi</label>
                                <textarea class="form-control form-control-sm" placeholder="Kriteria Eksklusi ..." name="indicator_exclusive" id="indicator_exclusive"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Periode Pengumpulan Data</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Periode Pengumpulan Data ..." name="periode_pengumpulan_data" id="periode_pengumpulan_data"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Sumber Data</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Sumber Data ..." name="sumber_data" id="sumber_data"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Instrumen Pengambilan Data</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Instrumen Pengambilan Data ..." name="instrumen_pengambilan_data" id="instrumen_pengambilan_data"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Besar Sampel</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Besar Sampel ..." name="besar_sampel" id="besar_sampel"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Cara Pengambilan Sampel</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Cara Pengambilan Sampel ..." name="cara_pengambilan_sampel" id="cara_pengambilan_sampel"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Periode Pengumpulan Data</label>
                                <select class="form-select form-select-sm" name="metode_pengumpulan_data" id="metode_pengumpulan_data">
                                    <option value=""></option>
                                    <option value="Y">Tahunan</option>
                                    <option value="M">Bulanan</option>
                                    <option value="W">Mingguan</option>
                                    <option value="D">Harian</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Periode Analisis dan Pelaporan Data</label>
                                <textarea class="form-control form-control-sm" rows="1" placeholder="Periode Analisis dan Pelaporan Data ..." name="periode_analisis_dan_pelaporan_data" id="periode_analisis_dan_pelaporan_data"></textarea>
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Penyajian Data</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Penyajian Data ..." name="penyajian_data" id="penyajian_data">
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Penanggung Jawab</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Penanggung Jawab ..." name="penanggung_jawab" id="penanggung_jawab">
                            </div>
                            <div class="mb-1">
                                <label class="small mb-0">Status Aktif Indikator</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="indicator_record_status" name="indicator_record_status" value="A">
                                    <label class="form-check-label small" for="indicator_record_status">Aktif</label>
                                </div>
                            </div>
                            <div class="mt-2 d-flex gap-1">
                                <button type="button" id="btn-save-indikator" class="btn btn-sm btn-primary px-3 py-0" style="font-size:0.75rem;">Simpan</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-0" data-bs-dismiss="modal" style="font-size:0.75rem;">Batal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-1 px-2 justify-content-between">
                <small class="text-muted"><?= date('l, d-m-Y') ?></small>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-numdenum" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #6f42c1; color: white;">
                <h6 class="modal-title" id="judunumdenum" style="color: #ffffff; font-size: 0.9rem;">Numerator / Denominator</h6>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-xs btn-outline-light border-0" onclick="showNumDenumAdd()" id="toggleNumDenum" title="Tambah">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-light border-0" onclick="reloadNumDenum()" id="reloadViewNumDenum" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.7rem;"></button>
                </div>
            </div>
            <div class="modal-body p-3" id="numdenum-list">
                <div class="table-responsive">
                    <table id="table-numdenum" class="table table-sm table-striped mb-0 align-middle" style="width: 100%; font-size: 0.8rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:32px;">#</th>
                                <th class="text-center" style="width:100px;">Tipe</th>
                                <th>Variabel</th>
                                <th style="width:70px;">Satuan</th>
                                <th class="text-center" style="width:75px;"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-body p-2" id="numdenum-form" style="display: none;">
                <form id="form-numdenum">
                    <input type="hidden" name="variable_id" id="variable_id" value="0">
                    <input type="hidden" name="variable_indicator_id" id="variable_indicator_id">
                    <div class="mb-1 row g-1">
                        <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">Tipe</label>
                        <div class="col-sm-9">
                            <select class="form-select form-select-sm" name="variable_type" id="variable_type">
                                <option value="">-----</option>
                                <option value="N">Numerator</option>
                                <option value="D">Denominator</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-1 row g-1">
                        <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">Variabel</label>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-sm" name="variable_name" id="variable_name" rows="1" placeholder="Nama variabel..."></textarea>
                        </div>
                    </div>
                    <div class="mb-1 row g-1">
                        <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">Satuan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" name="variable_unit_name" id="variable_unit_name" placeholder="Satuan...">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-1 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="closeNumDenumForm()" id="btnBackNumDenum" style="display:none; font-size:0.75rem;"><i class="fas fa-arrow-left"></i> Kembali</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-0" id="btn-save-numdenum" style="display:none; font-size:0.75rem;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-del-numdenum" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-3">
                <input type="hidden" id="del_variable_id">
                <i class="fas fa-trash-alt text-danger mb-2" style="font-size:1.5rem;"></i>
                <p class="mb-0 small">Hapus data ini?</p>
            </div>
            <div class="modal-footer py-1 justify-content-center border-0 gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-0" data-bs-dismiss="modal" style="font-size:0.75rem;">Batal</button>
                <button type="button" class="btn btn-sm btn-danger px-3 py-0" onclick="confirmDeleteNumDenum()" style="font-size:0.75rem;">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-view" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #6f42c1; color: white;">
                <h6 class="modal-title" id="judulelementview" style="color: #ffffff; font-size:0.9rem;"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size:0.7rem;"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card mb-0">
                    <div class="card-body p-2">
                        <div class="text-center mb-2 small">
                    <div style="font-weight: bold;">PEMERINTAH PROVINSI JAWA TIMUR</div>
                    <div style="font-weight: bold;">RSUD dr. SOEDONO</div>
                    <div>Jalan dr. Sutomo No.59 Madiun, Telp.(0351) 464325, 464326, Fax (0351) 458058</div>
                    <div>Website: www.rssoedono.jatimprov.go.id. Email: rsu_soedonomdn@jatimprov.go.id</div>
                    <hr style="border: 1px solid #000; margin: 6px 0;">
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" style="font-size:0.8rem;">
                            <tbody>
                                <tr><td style="width: 30%;"><strong>Judul Indikator</strong></td><td id="viewindicator_element"></td></tr>
                                <tr><td><strong>Dasar Pemikiran</strong></td><td id="viewdasar_pemikiran"></td></tr>
                                <tr><td><strong>Dimensi Mutu</strong></td><td id="viewdimensi_mutu"></td></tr>
                                <tr><td><strong>Tujuan</strong></td><td id="viewtujuan"></td></tr>
                                <tr><td><strong>Definisi Operasional</strong></td><td id="viewdefinisi_operasional"></td></tr>
                                <tr><td><strong>Jenis Indikator</strong></td><td id="viewjenis_indikator"></td></tr>
                                <tr><td><strong>Simbol Operator Standar Capaian</strong></td><td id="viewindicator_target_calculation"></td></tr>
                                <tr><td><strong>Standar Capaian</strong></td><td id="viewindicator_target"></td></tr>
                                <tr><td><strong>Satuan Standar</strong></td><td id="viewindicator_target_unit"></td></tr>
                                <tr><td><strong>Faktor Pengali</strong></td><td id="viewindicator_factors"></td></tr>
                                <tr><td><strong>Satuan Pengali</strong></td><td id="viewindicator_units"></td></tr>
                                <tr><td><strong>Kriteria Inklusi</strong></td><td id="viewindicator_inclusive"></td></tr>
                                <tr><td><strong>Kriteria Eksklusi</strong></td><td id="viewindicator_exclusive"></td></tr>
                                <tr><td><strong>Metode Pengumpulan Data</strong></td><td id="viewmetode_pengumpulan_data"></td></tr>
                                <tr><td><strong>Sumber Data</strong></td><td id="viewsumber_data"></td></tr>
                                <tr><td><strong>Instrumen Pengambilan Data</strong></td><td id="viewinstrumen_pengambilan_data"></td></tr>
                                <tr><td><strong>Besar Sampel</strong></td><td id="viewbesar_sampel"></td></tr>
                                <tr><td><strong>Cara Pengambilan Sampel</strong></td><td id="viewcara_pengambilan_sampel"></td></tr>
                                <tr><td><strong>Periode Pengumpulan Data</strong></td><td id="viewperiode_pengumpulan_data"></td></tr>
                                <tr><td><strong>Periode Analisis dan Pelaporan Data</strong></td><td id="viewperiode_analisis_dan_pelaporan_data"></td></tr>
                                <tr><td><strong>Penyajian Data</strong></td><td id="viewpenyajian_data"></td></tr>
                                <tr><td><strong>Penanggung Jawab</strong></td><td id="viewpenanggung_jawab"></td></tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-1 px-2 justify-content-end border-0">
                <small class="text-muted"><?= date('l, d-m-Y') ?></small>
            </div>
        </div>
    </div>
</div>

<style>
    .header-row {
        background: linear-gradient(50deg, red, orange, yellow, green, blue, indigo, violet);
        background-size: 400% 400%;
        animation: rgbAnimation 500s ease infinite;
        color: white;
        padding: 10px 20px;
        border-radius: 1px;
        text-decoration: none;
    }

    @keyframes rgbAnimation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .dataTables_wrapper .dataTables_processing {
        display: none !important;
    }

    .overlay-wrapper {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: transparent;
        z-index: 9999;
    }

    .overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: transparent;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loader {
        width: 3em;
        height: 3em;
        transform: rotate(165deg);
    }

    .loader:before, .loader:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        display: block;
        width: 1em;
        height: 1em;
        border-radius: 0.5em;
        transform: translate(-50%, -50%);
    }

    .loader:before { animation: before8 2s infinite; }
    .loader:after  { animation: after6  2s infinite; }

    @keyframes before8 {
        0%   { width: 1em; box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75); }
        35%  { width: 4em; box-shadow: 0 -1em rgba(225, 20, 98, 0.75), 0 1em rgba(111, 202, 220, 0.75); }
        70%  { width: 1em; box-shadow: -2em -1em rgba(225, 20, 98, 0.75), 2em 1em rgba(111, 202, 220, 0.75); }
        100% { box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75); }
    }

    @keyframes after6 {
        0%   { height: 1em; box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75); }
        35%  { height: 4em; box-shadow: 1em 0 rgba(61, 184, 143, 0.75), -1em 0 rgba(233, 169, 32, 0.75); }
        70%  { height: 1em; box-shadow: 1em -2em rgba(61, 184, 143, 0.75), -1em 2em rgba(233, 169, 32, 0.75); }
        100% { box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75); }
    }

    #table-indikator_wrapper .btn-group {
        gap: 2px;
    }

    #table-indikator_wrapper .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    #table-indikator_wrapper td {
        vertical-align: middle;
    }

    #table-indikator_wrapper th {
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        #table-indikator_wrapper .btn-group .btn {
            padding: 0.15rem 0.3rem;
            font-size: 0.7rem;
        }
        #table-indikator_wrapper td,
        #table-indikator_wrapper th {
            font-size: 0.8rem;
            padding: 0.3rem 0.4rem;
        }
    }
        .btn-xs {
            padding: 0.1rem 0.3rem;
            font-size: 0.7rem;
            line-height: 1.2;
        }
        .btn-group-xs > .btn {
            padding: 0.1rem 0.3rem;
            font-size: 0.7rem;
            line-height: 1.2;
        }
        .table-sm > :not(caption) > * > * {
            padding: 0.2rem 0.3rem;
        }
    </style>

<script>
    let table;
    let currentModule = '<?= esc($module) ?>';

    $(document).ready(function () {
        table = $('#table-indikator').DataTable({
            processing: false,
            serverSide: true,
            searching: false,
            autoWidth: true,
            ajax: {
                url: "<?= base_url('siimut/data-indikator/ajax-get-data') ?>",
                type: 'POST',
                data: function (d) {
                    d.module = currentModule;
                    d.cari_input = $('#cari_input').val();
                    return d;
                },
                beforeSend: function () {
                    $('#loading_overlay').show();
                },
                complete: function () {
                    $('#loading_overlay').hide();
                }
            },
            columns: [
                { data: null, className: 'dt-center', orderable: false, searchable: false },
                { data: 'indicator_element' },
                { data: 'indicator_target', className: 'dt-center' },
                { data: 'indicator_units', className: 'dt-center' },
                { data: 'indicator_frequency', className: 'dt-center' },
                { data: 'indicator_record_status', className: 'dt-center' },
                { data: null, className: 'dt-center', orderable: false, searchable: false }
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
                        const statusMap = {'A': '<span class="badge bg-success">Aktif</span>', 'D': '<span class="badge bg-info">Draft</span>', 'X': '<span class="badge bg-secondary">Nonaktif</span>'};
                        return statusMap[data] || data;
                    }
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        let buttons = '<div class="btn-group btn-group-sm" style="white-space:nowrap">';
                        buttons += '<button type="button" class="btn btn-success" onclick="viewIndikator(' + row.indicator_id + ')" title="Lihat"><i class="fas fa-eye"></i></button>';
                        buttons += '<button type="button" class="btn btn-info" onclick="editIndikator(' + row.indicator_id + ')" title="Edit"><i class="fas fa-edit"></i></button>';
                        buttons += '<button type="button" class="btn btn-primary" onclick="openNumDenum(' + row.indicator_id + ')" title="Num/Denum"><i class="fas fa-calculator"></i></button>';
                        buttons += '<button type="button" class="btn btn-danger" onclick="deleteIndikator(' + row.indicator_id + ')" title="Hapus"><i class="fas fa-trash"></i></button>';
                        if (row.indicator_record_status === 'X') {
                            buttons += '<button type="button" class="btn btn-warning" onclick="restoreIndikator(' + row.indicator_id + ')" title="Pulihkan"><i class="fas fa-undo"></i></button>';
                        }
                        buttons += '</div>';
                        return buttons;
                    }
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            }
        });

        $('#btn-save-indikator').on('click', function () {
            saveIndikator();
        });

        $('#form-indikator').on('submit', function (e) {
            e.preventDefault();
            saveIndikator();
        });
    });

    function get_pencariandata() {
        table.ajax.reload(null, false);
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function hanyaAngka(event) {
        var angka = (event.which) ? event.which : event.keyCode;
        if (angka != 46 && angka > 31 && (angka < 48 || angka > 57))
            return false;
        return true;
    }

    function clearForm() {
        $('#form-indikator')[0].reset();
        $('#indicator_id').val('');
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
                    const d = response.data;
                    $('#indicator_id').val(d.indicator_id || '');
                    $('#indicator_element').val(d.indicator_element || '');
                    $('#dasar_pemikiran').val(d.dasar_pemikiran || '');
                    $('#tujuan').val(d.tujuan || '');
                    $('#definisi_operasional').val(d.indicator_definition || '');
                    $('#jenis_indikator').val(d.indicator_type || '');
                    $('#indicator_target_calculation').val(d.indicator_target_calculation || '');
                    $('#indicator_target').val(d.indicator_target || '');
                    $('#indicator_target_unit').val(d.indicator_target_unit || '');
                    $('#indicator_factors').val(d.indicator_factors || '');
                    $('#indicator_units').val(d.indicator_units || '');
                    $('#indicator_inclusive').val(d.indicator_criteria_inclusive || '');
                    $('#indicator_exclusive').val(d.indicator_criteria_exclusive || '');
                    $('#periode_pengumpulan_data').val(d.periode_pengumpulan_data || '');
                    $('#sumber_data').val(d.indicator_source_of_data || '');
                    $('#instrumen_pengambilan_data').val(d.instrumen_pengambilan_data || '');
                    $('#besar_sampel').val(d.besar_sampel || '');
                    $('#cara_pengambilan_sampel').val(d.cara_pengambilan_sampel || '');
                    $('#metode_pengumpulan_data').val(d.indicator_frequency || '');
                    $('#periode_analisis_dan_pelaporan_data').val(d.periode_analisis_dan_pelaporan_data || '');
                    $('#penyajian_data').val(d.penyajian_data || '');
                    $('#penanggung_jawab').val(d.penanggung_jawab || '');
                    $('#indicator_record_status').prop('checked', d.indicator_record_status === 'A');

                    if (d.dimensi_mutu) {
                        var dims = d.dimensi_mutu.split(',');
                        for (var i = 1; i <= 7; i++) {
                            $('#dimensi_mutu' + i).prop('checked', dims.includes(String(i)));
                        }
                    }

                    $('#modal-indikator-label').text('Edit Indikator: ' + (d.indicator_element || ''));
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-indikator')).show();
                } else {
                    Swal.fire('Error', response.message || 'Gagal mengambil data indikator', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal terhubung ke server', 'error');
            }
        });
    }

    var currentIndicatorId = 0;
    var tableNumDenum;

    function openNumDenum(indicatorId) {
        currentIndicatorId = indicatorId;
        $('#variable_indicator_id').val(indicatorId);
        $('#numdenum-list').show();
        $('#numdenum-form').hide();
        $('#btnBackNumDenum').hide();
        $('#btn-save-numdenum').hide();
        $('#toggleNumDenum').show();

        if (tableNumDenum) {
            tableNumDenum.ajax.reload(null, false);
        } else {
            tableNumDenum = $('#table-numdenum').DataTable({
                processing: false,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "<?= base_url('siimut/data-indikator/ajax-get-numdenum') ?>",
                    type: 'POST',
                    data: function (d) {
                        d.indicator_id = currentIndicatorId;
                        d.module = currentModule;
                        return d;
                    },
                    beforeSend: function () { $('#loading_overlay').show(); },
                    complete: function () { $('#loading_overlay').hide(); }
                },
                columns: [
                    { data: null, className: 'dt-center', orderable: false, searchable: false },
                    { data: 'variable_type', className: 'dt-center' },
                    { data: 'variable_name' },
                    { data: 'variable_unit_name' },
                    { data: null, className: 'dt-center', orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        targets: 1,
                        render: function (data, type, row) {
                            var map = {'N': '<span class="badge bg-info">Numerator</span>', 'D': '<span class="badge bg-warning text-dark">Denominator</span>'};
                            return map[data] || data;
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row) {
                            return data || '<small class="text-muted">-</small>';
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, row) {
                            var btns = '<div class="btn-group btn-group-xs gap-1">';
                            btns += '<button type="button" class="btn btn-xs btn-outline-info border-0" onclick="editNumDenum(' + row.variable_id + ')" title="Edit"><i class="fas fa-edit"></i></button>';
                            btns += '<button type="button" class="btn btn-xs btn-outline-danger border-0" onclick="deleteNumDenum(' + row.variable_id + ')" title="Hapus"><i class="fas fa-trash"></i></button>';
                            btns += '</div>';
                            return btns;
                        }
                    }
                ],
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
            });
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-numdenum')).show();
    }

    function showNumDenumAdd() {
        $('#form-numdenum')[0].reset();
        $('#variable_id').val(0);
        $('#variable_indicator_id').val(currentIndicatorId);
        $('#numdenum-list').hide();
        $('#numdenum-form').show();
        $('#btnBackNumDenum').show();
        $('#btn-save-numdenum').show();
        $('#toggleNumDenum').hide();
    }

    function closeNumDenumForm() {
        $('#numdenum-list').show();
        $('#numdenum-form').hide();
        $('#btnBackNumDenum').hide();
        $('#btn-save-numdenum').hide();
        $('#toggleNumDenum').show();
    }

    function reloadNumDenum() {
        if (tableNumDenum) tableNumDenum.ajax.reload(null, false);
    }

    function editNumDenum(id) {
        $.ajax({
            url: "<?= base_url('siimut/data-indikator/get-numdenum-detail') ?>",
            type: 'POST',
            data: {id: id, module: currentModule},
            dataType: 'json',
            success: function (resp) {
                if (resp.status && resp.data) {
                    var d = resp.data;
                    $('#variable_id').val(d.variable_id || 0);
                    $('#variable_indicator_id').val(d.variable_indicator_id || currentIndicatorId);
                    $('#variable_type').val(d.variable_type || '');
                    $('#variable_name').val(d.variable_name || '');
                    $('#variable_unit_name').val(d.variable_unit_name || '');
                    $('#numdenum-list').hide();
                    $('#numdenum-form').show();
                    $('#btnBackNumDenum').show();
                    $('#btn-save-numdenum').show();
                    $('#toggleNumDenum').hide();
                } else {
                    Swal.fire('Error', resp.message || 'Gagal mengambil data', 'error');
                }
            },
            error: function () { Swal.fire('Error', 'Gagal terhubung ke server', 'error'); }
        });
    }

    function deleteNumDenum(id) {
        $('#del_variable_id').val(id);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-del-numdenum')).show();
    }

    function confirmDeleteNumDenum() {
        var id = $('#del_variable_id').val();
        $.ajax({
            url: "<?= base_url('siimut/data-indikator/delete-numdenum') ?>",
            type: 'POST',
            data: {id: id, module: currentModule},
            dataType: 'json',
            success: function (resp) {
                if (resp.status) {
                    Swal.fire('Berhasil', resp.message, 'success');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-del-numdenum')).hide();
                    reloadNumDenum();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            },
            error: function () { Swal.fire('Error', 'Gagal terhubung ke server', 'error'); }
        });
    }

    $(document).on('click', '#btn-save-numdenum', function () {
        var data = $('#form-numdenum').serializeArray();
        var obj = {};
        data.forEach(function (item) { obj[item.name] = item.value; });
        obj.module = currentModule;

        $.ajax({
            url: "<?= base_url('siimut/data-indikator/save-numdenum') ?>",
            type: 'POST',
            data: obj,
            dataType: 'json',
            success: function (resp) {
                if (resp.status) {
                    Swal.fire('Berhasil', resp.message, 'success');
                    closeNumDenumForm();
                    reloadNumDenum();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            },
            error: function () { Swal.fire('Error', 'Gagal terhubung ke server', 'error'); }
        });
    });

    function viewIndikator(id) {
        $.ajax({
            url: "<?= base_url('siimut/data-indikator/get-detail') ?>",
            type: 'POST',
            data: {id: id, module: currentModule},
            dataType: 'json',
            success: function (response) {
                if (response.status && response.data) {
                    const d = response.data;
                    $('#judulelementview').text(d.indicator_element || '');
                    $('#viewindicator_element').text(d.indicator_element || '-');
                    $('#viewdasar_pemikiran').text(d.dasar_pemikiran || '-');
                    $('#viewdimensi_mutu').text(d.dimensi_mutu || '-');
                    $('#viewtujuan').text(d.tujuan || '-');
                    $('#viewdefinisi_operasional').text(d.indicator_definition || '-');
                    $('#viewjenis_indikator').text(d.indicator_type || '-');
                    $('#viewindicator_target_calculation').text(d.indicator_target_calculation || '-');
                    $('#viewindicator_target').text(d.indicator_target || '-');
                    $('#viewindicator_target_unit').text(d.indicator_target_unit || '-');
                    $('#viewindicator_factors').text(d.indicator_factors || '-');
                    $('#viewindicator_units').text(d.indicator_units || '-');
                    $('#viewindicator_inclusive').text(d.indicator_criteria_inclusive || '-');
                    $('#viewindicator_exclusive').text(d.indicator_criteria_exclusive || '-');

                    var freqMap = {'Y': 'Tahunan', 'M': 'Bulanan', 'W': 'Mingguan', 'D': 'Harian'};
                    $('#viewmetode_pengumpulan_data').text(freqMap[d.indicator_frequency] || d.indicator_frequency || '-');

                    $('#viewsumber_data').text(d.indicator_source_of_data || '-');
                    $('#viewinstrumen_pengambilan_data').text(d.instrumen_pengambilan_data || '-');
                    $('#viewbesar_sampel').text(d.besar_sampel || '-');
                    $('#viewcara_pengambilan_sampel').text(d.cara_pengambilan_sampel || '-');
                    $('#viewperiode_pengumpulan_data').text(d.periode_pengumpulan_data || '-');
                    $('#viewperiode_analisis_dan_pelaporan_data').text(d.periode_analisis_dan_pelaporan_data || '-');
                    $('#viewpenyajian_data').text(d.penyajian_data || '-');
                    $('#viewpenanggung_jawab').text(d.penanggung_jawab || '-');

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-view')).show();
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
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-indikator')).hide();
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
