<div class="bs-stepper" id="ikpStepper">

    <!-- ================= HEADER STEPPER ================= -->
    <div class="bs-stepper-header" role="tablist">

        <div class="step" data-target="#step-1">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Pasien</span>
            </button>
        </div>
        <div class="line"></div>

        <div class="step" data-target="#step-2">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Kejadian</span>
            </button>
        </div>
        <div class="line"></div>

        <div class="step" data-target="#step-3">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">3</span>
                <span class="bs-stepper-label">Pelapor</span>
            </button>
        </div>
        <div class="line"></div>

        <div class="step" data-target="#step-4">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">4</span>
                <span class="bs-stepper-label">Dampak</span>
            </button>
        </div>
        <div class="line"></div>

        <div class="step" data-target="#step-5">
            <button type="button" class="step-trigger">
                <span class="bs-stepper-circle">5</span>
                <span class="bs-stepper-label">Tindakan</span>
            </button>
        </div>
    </div>
    <hr>
    <div class="bs-stepper-content">
        <form id="form_ikp">
            <!-- STEP 1 -->
            <div id="step-1" class="content">

                <!-- 🔐 DATA PETUGAS (DARI SESSION – HIDDEN) -->
                <input type="hidden" class="form-control" name="user_id" value="<?= esc(session('hris_user_id')) ?>">
                <input type="hidden" class="form-control" name="nip" value="<?= esc(session('hris_nip')) ?>">
                <input type="hidden" class="form-control" name="nama_petugas" value="<?= esc(session('hris_full_name')) ?>">


                <!-- =========================
                 DATA KUNJUNGAN PASIEN
                  ========================== -->
                <div class="row g-3">

                    <!-- Asal Pasien -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="asal_pasien" class="form-label fw-semibold">
                            Asal Pasien <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="asal_pasien" name="asal_pasien">
                            <option value="">-- Pilih --</option>
                            <option value="1">Rawat Inap</option>
                            <option value="2">Rawat Jalan</option>
                            <option value="3">IGD</option>
                        </select>
                        <div class="invalid-feedback d-block" id="error_asal_pasien"></div>
                    </div>

                    <!-- Tanggal Masuk -->
                    <!-- <div class="col-12 col-sm-6 col-md-3">
                        <label for="tgl_masuk" class="form-label fw-semibold">
                            Tanggal Masuk <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk">
                        <div class="invalid-feedback d-block" id="error_tgl_masuk"></div>
                    </div> -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tgl_masuk" class="form-label fw-semibold">
                            Tanggal Masuk <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">📅</span>

                            <input type="text"
                                class="form-control"
                                name="tgl_masuk_view"
                                id="tgl_masuk_view"
                                placeholder="dd/mm/yyyy">

                            <!-- hidden untuk format database -->
                            <input type="hidden" class="form-control" name="tgl_masuk" id="tgl_masuk">

                        </div>

                        <div class="invalid-feedback" id="error_tgl_masuk"></div>
                    </div>

                    <!-- Jam Masuk -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="jam_masuk" class="form-label fw-semibold">
                            Jam Masuk
                        </label>
                        <input type="time" class="form-control" name="jam_masuk" id="jam_masuk" readonly>
                    </div>

                    <!-- No Medrec -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="kd_pasien" class="form-label fw-semibold">
                            No. Medrec <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input type="text"
                                class="form-control"
                                placeholder="No. Medrec..."
                                name="kd_pasien"
                                id="kd_pasien"
                                maxlength="10">

                            <span class="input-group-text bg-white d-none" id="loading_pasien">
                                <i class="fas fa-spinner fa-spin text-primary"></i>
                            </span>
                        </div>

                        <div class="invalid-feedback d-block" id="error_kd_pasien"></div>

                        <a href="javascript:void(0)"
                            id="btnResetPasien"
                            onclick="resetFormPasien()"
                            class="text-danger fw-semibold mt-2 d-none text-decoration-none">
                            <i class="fas fa-undo"></i> Reset Data
                        </a>
                    </div>

                </div>

                <hr class="my-4">

                <!-- =========================
                  IDENTITAS PASIEN (READONLY)
                ========================== -->
                <div class="row g-3">

                    <div class="col-12">
                        <label for="nama" class="form-label fw-semibold">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="kelompok_umur" class="form-label fw-semibold">Kelompok Umur</label>
                        <input type="text" class="form-control" name="kelompok_umur" id="kelompok_umur" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="umur_tahun" class="form-label fw-semibold">Umur</label>
                        <input type="text" class="form-control" name="umur_tahun" id="umur_tahun" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_unit" class="form-label fw-semibold">Kelas / Poli</label>
                        <input type="text" class="form-control" name="nama_unit" id="nama_unit" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_kamar" class="form-label fw-semibold">Ruangan</label>
                        <input type="text" class="form-control" name="nama_kamar" id="nama_kamar" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="kelamin" class="form-label fw-semibold">Kelamin</label>
                        <input type="text" class="form-control" name="kelamin" id="kelamin" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="penjamin" class="form-label fw-semibold">Penjamin</label>
                        <input type="text" class="form-control" name="penjamin" id="penjamin" readonly>
                    </div>

                </div>
            </div>

            <!-- STEP 2 -->
            <div id="step-2" class="content">
                <!-- isi step 2 -->
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="tgl_insiden" class="form-label fw-semibold">
                            Tanggal Insiden
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">📅</span>

                            <input type="text"
                                class="form-control"
                                name="tgl_insiden_view"
                                id="tgl_insiden_view"
                                placeholder="dd/mm/yyyy">

                            <!-- hidden untuk format database -->
                            <input type="hidden" class="form-control" name="tgl_insiden" id="tgl_insiden">

                        </div>

                        <div class="invalid-feedback d-block" id="error_tgl_insiden"></div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="jam_insiden" class="form-label fw-semibold">
                            Waktu Insiden
                        </label>
                        <input type="time"
                            class="form-control"
                            name="jam_insiden"
                            id="jam_insiden">
                        <div class="invalid-feedback d-block" id="error_jam_insiden"></div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="tempat_insiden" class="form-label fw-semibold">
                            Tempat Insiden
                        </label>

                        <select class="form-select" name="tempat_insiden" id="tempat_insiden">
                            <option value="">-- Pilih Tempat Insiden --</option>
                        </select>

                        <div class="invalid-feedback d-block" id="error_tempat_insiden"></div>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="insiden" class="form-label fw-semibold">
                        Insiden
                    </label>
                    <textarea class="form-control"
                        rows="2"
                        name="insiden"
                        id="insiden"
                        placeholder="Insiden ..."></textarea>
                    <div class="invalid-feedback d-block" id="error_insiden"></div>
                </div>

                <div class="mt-3">
                    <label for="kronologis_insiden" class="form-label fw-semibold">
                        Kronologis Insiden
                    </label>
                    <textarea class="form-control"
                        rows="3"
                        name="kronologis_insiden"
                        id="kronologis_insiden"
                        placeholder="Kronologis Insiden ..."></textarea>
                    <div class="invalid-feedback d-block" id="error_kronologis_insiden"></div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold mb-2">
                        Jenis Insiden
                    </label>

                    <div class="card card-outline card-secondary">
                        <div class="card-body p-3">

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                    type="radio"
                                    name="jenis_insiden"
                                    id="knc"
                                    value="KNC">
                                <label class="form-check-label" for="knc">
                                    Kejadian Nyaris Cedera (KNC / Near Miss)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                    type="radio"
                                    name="jenis_insiden"
                                    id="ktd"
                                    value="KTD">
                                <label class="form-check-label" for="ktd">
                                    Kejadian Tidak Diharapkan (KTD / Adverse Event)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                    type="radio"
                                    name="jenis_insiden"
                                    id="sentinel"
                                    value="Sentinel">
                                <label class="form-check-label" for="sentinel">
                                    Kejadian Sentinel (Sentinel Event)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                    type="radio"
                                    name="jenis_insiden"
                                    id="ktc"
                                    value="KTC">
                                <label class="form-check-label" for="ktc">
                                    Kejadian Tidak Cedera (KTC)
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                    type="radio"
                                    name="jenis_insiden"
                                    id="kpc"
                                    value="KPC">
                                <label class="form-check-label" for="kpc">
                                    Kejadian Potensial Cedera (KPC)
                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="invalid-feedback d-block mt-1" id="error_jenis_insiden"></div>
                </div>
            </div>

            <!-- STEP 3 -->
            <div id="step-3" class="content">
                <!-- isi step 3 -->
                <div class="row g-4">

                    <!-- KIRI -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2">
                            Orang Pertama Yang Melaporkan Insiden
                            <span class="text-danger">*</span>
                        </label>

                        <div class="card card-outline card-secondary">
                            <div class="card-body p-3">

                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="pelapor_insiden"
                                        id="pelapor_karyawan"
                                        value="Karyawan">
                                    <label class="form-check-label" for="pelapor_karyawan">
                                        Karyawan (Dokter / Perawat / Petugas)
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="pelapor_insiden"
                                        id="pelapor_pasien"
                                        value="Pasien">
                                    <label class="form-check-label" for="pelapor_pasien">
                                        Pasien
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="pelapor_insiden"
                                        id="pelapor_keluarga"
                                        value="Keluarga">
                                    <label class="form-check-label" for="pelapor_keluarga">
                                        Keluarga / Pendamping
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="pelapor_insiden"
                                        id="pelapor_pengunjung"
                                        value="Pengunjung">
                                    <label class="form-check-label" for="pelapor_pengunjung">
                                        Pengunjung
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="pelapor_insiden"
                                        id="pelapor_lain"
                                        value="Lain-lain">
                                    <label class="form-check-label" for="pelapor_lain">
                                        Lain-lain
                                    </label>
                                </div>

                                <!-- Lain-lain -->
                                <div class="mt-2 d-none" id="wrap_pelapor_lain">
                                    <input type="text"
                                        class="form-control"
                                        name="pelapor_lain_text"
                                        id="pelapor_lain_text"
                                        placeholder="Jelaskan pelapor lainnya...">
                                </div>

                            </div>
                        </div>

                        <div class="invalid-feedback d-block" id="error_pelapor_insiden"></div>
                    </div>

                    <!-- KANAN -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2">
                            Insiden terjadi pada
                            <span class="text-danger">*</span>
                        </label>

                        <div class="card card-outline card-secondary">
                            <div class="card-body p-3">

                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="insiden_pada"
                                        id="insiden_pasien"
                                        value="Pasien">
                                    <label class="form-check-label" for="insiden_pasien">
                                        Pasien
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="insiden_pada"
                                        id="insiden_lain"
                                        value="Lain-lain">
                                    <label class="form-check-label" for="insiden_lain">
                                        Lain-lain (karyawan / pengunjung / pendamping)
                                    </label>
                                </div>

                                <!-- Lain-lain -->
                                <div class="mt-2 d-none" id="wrap_insiden_lain">
                                    <input type="text"
                                        class="form-control"
                                        name="insiden_pada_lain"
                                        id="insiden_pada_lain"
                                        placeholder="Jelaskan insiden terjadi pada siapa...">
                                </div>

                            </div>
                        </div>

                        <div class="invalid-feedback d-block" id="error_insiden_pada"></div>
                    </div>

                </div>

                <hr class="my-4">

                <!-- SPESIALISASI -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Insiden terjadi pada pasien
                        <small class="text-muted">(sesuai kasus penyakit / spesialisasi)</small>
                        <span class="text-danger">*</span>
                    </label>

                    <div class="card card-outline card-primary mt-2">
                        <div class="card-body">

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Penyakit Dalam"> <label class="form-check-label">Penyakit Dalam & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Anak"> <label class="form-check-label">Anak & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Bedah"> <label class="form-check-label">Bedah & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Obgyn"> <label class="form-check-label">Obgyn & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="THT"> <label class="form-check-label">THT & Sub Spesialis</label></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Mata"> <label class="form-check-label">Mata & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Saraf"> <label class="form-check-label">Saraf & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Jantung"> <label class="form-check-label">Jantung & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Paru"> <label class="form-check-label">Paru & Sub Spesialis</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="spesialisasi_pasien" value="Jiwa"> <label class="form-check-label">Jiwa & Sub Spesialis</label></div>
                                </div>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input"
                                    type="radio"
                                    name="spesialisasi_pasien"
                                    id="sp_lain"
                                    value="Lain-lain">

                                <label class="form-check-label" for="sp_lain">
                                    Lain-lain
                                </label>
                            </div>

                            <div class="mt-2 d-none" id="wrap_spesialisasi_lain">
                                <input type="text"
                                    class="form-control"
                                    name="spesialisasi_lain"
                                    id="spesialisasi_lain"
                                    placeholder="Sebutkan spesialisasi lainnya...">
                            </div>

                        </div>
                    </div>

                    <div class="invalid-feedback d-block" id="error_spesialisasi"></div>
                </div>
            </div>

            <!-- STEP 4 -->
            <div id="step-4" class="content">
                <!-- isi step 4 -->
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2">
                        Akibat Insiden Terhadap Pasien
                        <span class="text-danger">*</span>
                    </label>

                    <div class="card card-outline card-danger">
                        <div class="card-body p-3">

                            <div class="row g-2">
                                <div class="col-md-6">

                                    <div class="form-check mb-2">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="akibat_insiden"
                                            id="akibat1"
                                            value="Kematian">
                                        <label class="form-check-label" for="akibat1">
                                            Kematian
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="akibat_insiden"
                                            id="akibat2"
                                            value="Cedera Irreversibel / Cedera Berat">
                                        <label class="form-check-label" for="akibat2">
                                            Cedera Irreversibel / Cedera Berat
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="akibat_insiden"
                                            id="akibat3"
                                            value="Cedera Reversibel / Cedera Sedang">
                                        <label class="form-check-label" for="akibat3">
                                            Cedera Reversibel / Cedera Sedang
                                        </label>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-check mb-2">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="akibat_insiden"
                                            id="akibat4"
                                            value="Cedera Ringan">
                                        <label class="form-check-label" for="akibat4">
                                            Cedera Ringan
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="akibat_insiden"
                                            id="akibat5"
                                            value="Tidak ada cedera">
                                        <label class="form-check-label" for="akibat5">
                                            Tidak ada cedera
                                        </label>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="invalid-feedback d-block mt-1" id="error_akibat"></div>
                </div>
            </div>

            <!-- STEP 5 -->
            <div id="step-5" class="content">
                <!-- =========================
                TINDAKAN SEGERA
                ========================== -->
                <div class="mb-4">
                    <label for="tindakan_segera" class="form-label fw-semibold">
                        Tindakan segera setelah kejadian & hasilnya
                    </label>
                    <input type="text"
                        class="form-control"
                        name="tindakan_segera"
                        id="tindakan_segera"
                        placeholder="Contoh: pemberian obat, observasi, rujukan, dll">
                    <div class="invalid-feedback d-block" id="error_tindakan_segera"></div>
                </div>

                <!-- =========================
                TINDAKAN DILAKUKAN OLEH
                ========================== -->
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">
                        Tindakan dilakukan oleh
                        <span class="text-danger">*</span>
                    </label>

                    <div class="card card-outline card-secondary">
                        <div class="card-body py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="tindakan_oleh"
                                            id="tindakan_dokter"
                                            value="Dokter">
                                        <label class="form-check-label" for="tindakan_dokter">
                                            Dokter
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="tindakan_oleh"
                                            id="tindakan_perawat"
                                            value="Perawat">
                                        <label class="form-check-label" for="tindakan_perawat">
                                            Perawat
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="invalid-feedback d-block mt-1" id="error_tindakan_oleh"></div>
                </div>

                <!-- =========================
                TIM & PETUGAS
                ========================== -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="tindakan_tim" class="form-label fw-semibold">
                            Tindakan dilakukan oleh tim
                        </label>
                        <input type="text"
                            class="form-control"
                            name="tindakan_tim"
                            id="tindakan_tim"
                            placeholder="Contoh: Dokter, Perawat, Farmasi">
                        <div class="invalid-feedback d-block" id="error_tindakan_tim"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="tindakan_petugas_lain" class="form-label fw-semibold">
                            Petugas lainnya (jika ada)
                        </label>
                        <input type="text"
                            class="form-control"
                            name="tindakan_petugas_lain"
                            id="tindakan_petugas_lain"
                            placeholder="Petugas lain">
                        <div class="invalid-feedback d-block" id="error_tindakan_petugas_lain"></div>
                    </div>
                </div>

                <!-- =========================
                PERNAH TERJADI
                ========================== -->
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">
                        Apakah kejadian serupa pernah terjadi di unit kerja lain?
                        <span class="text-danger">*</span>
                    </label>

                    <div class="card card-outline card-warning">
                        <div class="card-body py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="pernah_terjadi"
                                            id="pernah_ya"
                                            value="Ya">
                                        <label class="form-check-label" for="pernah_ya">
                                            Ya
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            name="pernah_terjadi"
                                            id="pernah_tidak"
                                            value="Tidak">
                                        <label class="form-check-label" for="pernah_tidak">
                                            Tidak
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="invalid-feedback d-block mt-1" id="error_pernah"></div>
                </div>

                <!-- =========================
                TINDAKAN LANJUTAN
                ========================== -->
                <div class="mb-3 d-none" id="wrap_form_tindakan_lanjutan">
                    <label for="tindakan_lanjutan" class="form-label fw-semibold">
                        Tindakan lanjutan untuk mencegah kejadian serupa
                    </label>
                    <textarea class="form-control"
                        name="tindakan_lanjutan"
                        id="tindakan_lanjutan"
                        rows="4"
                        placeholder="Jelaskan kapan dilakukan dan langkah pencegahannya"></textarea>
                </div>

            </div>

        </form>

        <div class="modal-footer border-0 mt-3 d-flex justify-content-between">
            <button type="button"
                class="btn btn-secondary"
                id="btnPrev"
                onclick="prevStep()"
                disabled>
                Kembali
            </button>

            <button type="button"
                class="btn btn-primary"
                id="btnNext"
                onclick="nextStep()"
                disabled>
                Selanjutnya
            </button>

            <!-- TOMBOL KIRIM LANGSUNG (HANYA UNTUK INPUT BARU) -->
            <?php if (empty($insiden_id)): ?>
            <button type="button"
                class="btn btn-success"
                id="btnKirimLangsung"
                onclick="submitIkp('KARU')"
                style="display: none;">
                <i class="bi bi-send"></i> Kirim Langsung ke KARU
            </button>
            <?php endif; ?>
        </div>
    </div>

</div>


<script>
    window.ikpStepperInstance = null;
    window.currentStep = 1;

    //tanggal
    // flatpickr("#tgl_masuk_view", {
    //     dateFormat: "d/m/Y",
    //     altInput: false,

    //     onChange: function(selectedDates, dateStr, instance) {

    //         if (selectedDates.length > 0) {

    //             let tgl = selectedDates[0];

    //             let yyyy = tgl.getFullYear();
    //             let mm = String(tgl.getMonth() + 1).padStart(2, '0');
    //             let dd = String(tgl.getDate()).padStart(2, '0');

    //             let formatDB = yyyy + "-" + mm + "-" + dd;

    //             $("#tgl_masuk").val(formatDB);

    //         }

    //     }

    // });

    // flatpickr("#tgl_insiden_view", {
    //     dateFormat: "d/m/Y",
    //     altInput: false,

    //     onChange: function(selectedDates, dateStr, instance) {

    //         if (selectedDates.length > 0) {

    //             let tgl = selectedDates[0];

    //             let yyyy = tgl.getFullYear();
    //             let mm = String(tgl.getMonth() + 1).padStart(2, '0');
    //             let dd = String(tgl.getDate()).padStart(2, '0');

    //             let formatDB = yyyy + "-" + mm + "-" + dd;

    //             $("#tgl_insiden").val(formatDB);

    //         }

    //     }

    // });

    setTanggal("#tgl_masuk_view", "#tgl_masuk");
    setTanggal("#tgl_insiden_view", "#tgl_insiden");

    function setTanggal(view, hidden) {

        flatpickr(view, {
            dateFormat: "d/m/Y",

            onChange: function(selectedDates) {

                if (selectedDates.length > 0) {

                    let tgl = selectedDates[0];

                    let yyyy = tgl.getFullYear();
                    let mm = String(tgl.getMonth() + 1).padStart(2, '0');
                    let dd = String(tgl.getDate()).padStart(2, '0');

                    let formatDB = yyyy + "-" + mm + "-" + dd;

                    $(hidden).val(formatDB);

                }

            }
        });

    }

    // step 3 - Pelapor Lain-lain
    $(document).on('change', 'input[name="pelapor_insiden"]', function() {
        if (this.value === 'Lain-lain') {
            $('#wrap_pelapor_lain').removeClass('d-none');
            $('#pelapor_lain_text').focus();
        } else {
            $('#wrap_pelapor_lain').addClass('d-none');
            $('#pelapor_lain_text').val('');
        }
    });

    // step 3 - Insiden pada Lain-lain
    $(document).on('change', 'input[name="insiden_pada"]', function() {
        if (this.value === 'Lain-lain') {
            $('#wrap_insiden_lain').removeClass('d-none');
            $('#insiden_pada_lain').focus();
        } else {
            $('#wrap_insiden_lain').addClass('d-none');
            $('#insiden_pada_lain').val('');
        }
    });

    // step 3 - Spesialisasi Lain-lain
    $(document).on('change', 'input[name="spesialisasi_pasien"]', function() {
        if (this.value === 'Lain-lain') {
            $('#wrap_spesialisasi_lain').removeClass('d-none');
            $('#spesialisasi_lain').focus();
        } else {
            $('#wrap_spesialisasi_lain').addClass('d-none');
            $('#spesialisasi_lain').val('');
        }
    });

    //step 5 - Pernah Terjadi
    $(document).on('change', 'input[name="pernah_terjadi"]', function() {
        if (this.value === 'Ya') {
            $('#wrap_form_tindakan_lanjutan').removeClass('d-none');
            $('#tindakan_lanjutan').focus();
        } else {
            $('#wrap_form_tindakan_lanjutan').addClass('d-none');
            $('#tindakan_lanjutan').val(''); // 🔥 kosongkan isinya
        }
    });

    // inisialisasi
    function bindMedrecFormatter() {

        $(document).off('input', '#kd_pasien');

        $(document).on('input', '#kd_pasien', function() {

            const input = this;

            let raw = input.value.replace(/\D/g, '').substring(0, 7);

            let formatted = '';
            if (raw.length > 0) formatted += raw.substring(0, 1);
            if (raw.length > 1) formatted += '-' + raw.substring(1, 3);
            if (raw.length > 3) formatted += '-' + raw.substring(3, 5);
            if (raw.length > 5) formatted += '-' + raw.substring(5, 7);

            input.value = formatted;
            input.setSelectionRange(formatted.length, formatted.length);

            if (raw.length === 7 && typeof tryCariPasienAuto === 'function') {
                tryCariPasienAuto();
            }
        });
    }

    // cari pasien
    function cariPasien() {

        if (IKP.isSearching) return;
        IKP.isSearching = true;

        const kd_pasien = $('#kd_pasien').val();
        const tgl_masuk = $('#tgl_masuk').val();
        const asal_pasien = $('#asal_pasien').val();

        $('#loading_pasien').show();

        $.ajax({
            url: "<?= site_url('ikprs/cari_pasien') ?>",
            type: "POST",
            dataType: "json",
            data: {
                kd_pasien,
                tgl_masuk,
                asal_pasien
            },

            success: function(res) {
                $('#loading_pasien').hide();
                IKP.isSearching = false;

                if (res.status === 'success') {
                    const d = res.data;

                    $('#nama').val(d.nama);
                    $('#jam_masuk').val(d.jam_masuk);
                    $('#kelompok_umur').val(d.kelompok_umur);
                    $('#nama_kamar').val(d.nama_kamar);
                    $('#kelamin').val(d.kelamin);
                    $('#nama_unit').val(d.nama_unit);
                    $('#penjamin').val(d.penjamin);
                    $('#umur_tahun').val(d.umur_tahun);

                } else {
                    $('#nama, #jam_masuk, #kelompok_umur, #nama_kamar, #kelamin, #nama_unit, #penjamin, #umur_tahun').val('');
                    toastWarning(res.message);
                }
            },

            error: function() {
                $('#loading_pasien').hide();
                IKP.isSearching = false;
                toastError('Terjadi kesalahan koneksi ke server');
            }
        });
    }

    // toggle tombol reset pasien
    function toggleResetButton() {
        let kd_pasien = ($("#kd_pasien").val() || "").trim();
        let tgl_masuk = ($("#tgl_masuk").val() || "").trim();
        let asal_pasien = ($("#asal_pasien").val() || "").trim();
        let tgl_masuk_view = ($("#tgl_masuk_view").val() || "").trim();

        if (kd_pasien || tgl_masuk || tgl_masuk_view || asal_pasien) {
            $("#btnResetPasien").removeClass("d-none");
        } else {
            $("#btnResetPasien").addClass("d-none");
        }
    }

    // binding untuk auto search saat tgl_masuk atau asal_pasien berubah
    function bindAutoSearch() {

        // ⛔ bersihkan dulu (anti dobel)
        $(document).off('change', '#tgl_masuk, #asal_pasien');

        // ✅ delegation (AMAN untuk AJAX)
        $(document).on('change', '#tgl_masuk, #asal_pasien', function() {
            resetAutoSearchState();

            tryCariPasienAuto();
            toggleResetButton();
        });
    }

    // reset state auto search (untuk mencegah pencarian otomatis saat user baru mulai input)
    function resetAutoSearchState() {
        if (window.IKP) {
            IKP.isSearching = false;
            IKP.lastMedrec = null;
        }
    }

    // reset form pasien
    function resetFormPasien() {

        if (window.IKP) {
            IKP.isSearching = false;
            IKP.lastMedrec = null; // 🔥 WAJIB
        }

        $('#kd_pasien').val('');
        $('#tgl_masuk').val('');
        $('#asal_pasien').val('').trigger('change');

        // reset flatpickr
        if ($("#tgl_masuk_view")[0]._flatpickr) {
            $("#tgl_masuk_view")[0]._flatpickr.clear();
        } else {
            $("#tgl_masuk_view").val('');
        }

        $('#nama, #jam_masuk, #kelompok_umur, #nama_kamar, #kelamin, #nama_unit, #penjamin, #umur_tahun')
            .val('');

        $('#error_kd_pasien, #error_tgl_masuk, #error_asal_pasien').html('');
        $('#loading_pasien').hide();
        $('#btnResetPasien').addClass('d-none');
    }

    function tryCariPasienAuto() {

        const kd_pasien_raw = $("#kd_pasien").val().replace(/\D/g, '');
        const tgl_masuk = $("#tgl_masuk").val();
        const asal_pasien = $("#asal_pasien").val();

        if (
            kd_pasien_raw.length === 7 &&
            tgl_masuk &&
            asal_pasien &&
            IKP.lastMedrec !== kd_pasien_raw
        ) {
            IKP.lastMedrec = kd_pasien_raw;
            cariPasien();
        }
    }

    // inisialisasi stepper
    function initIkpStepper() {
        const el = document.querySelector('#ikpStepper');
        if (!el || window.ikpStepperInstance) return;

        window.ikpStepperInstance = new Stepper(el, {
            linear: false,
            animation: true
        });

        // 🔥 satu-satunya sumber step aktif
        el.addEventListener('shown.bs-stepper', function(e) {
            window.currentStep = e.detail.indexStep + 1;
            updateNavButtons();
            console.log('📍 Step aktif:', window.currentStep);
        });

        window.ikpStepperInstance.to(1);

        document.getElementById('btnNext').disabled = false;
        document.getElementById('btnPrev').disabled = true;

        updateNavButtons();
        console.log('✅ Stepper SIAP (STABLE)');
    }

    // validasi step sebelum lanjut
    function nextStep() {

        if (!window.ikpStepperInstance) return;

        // 🔥 VALIDASI BERDASARKAN STEP AKTIF
        if (window.currentStep === 1) {
            if (!validateStep1()) return;
        }

        if (window.currentStep === 2) {
            if (!validateStep2()) return;
        }

        if (window.currentStep === 3) {
            if (!validateStep3()) return;
        }

        if (window.currentStep === 4) {
            if (!validateStep4()) return;
        }

        // const totalSteps = document.querySelectorAll('.bs-stepper-header .step').length;

        if (window.currentStep === 5) {
            if (!validateStep5()) return;

            // ✅ semua valid → simpan ke database
            submitIkp();
            return;
        }

        window.ikpStepperInstance.next();
    }

    // validasi step 1 (pasien)
    function validateStep1() {

        let valid = true;

        // bersihkan error dulu
        $('#error_asal_pasien, #error_tgl_masuk, #error_kd_pasien').html('');

        const asal = $('#asal_pasien').val();
        const tgl = $('#tgl_masuk').val();
        const kd = $('#kd_pasien').val();

        if (!asal) {
            $('#error_asal_pasien').text('Asal pasien wajib dipilih');
            valid = false;
        }

        if (!tgl) {
            $('#error_tgl_masuk').text('Tanggal masuk wajib diisi');
            valid = false;
        }

        if (!kd) {
            $('#error_kd_pasien').text('No. Medrec wajib diisi');
            valid = false;
        }

        // 🔥 fokus ke field pertama yang error
        if (!valid) {
            $('#asal_pasien, #tgl_masuk, #kd_pasien').filter(function() {
                return !$(this).val();
            }).first().focus();
        }

        return valid;
    }

    // validasi step 2 (insiden)
    function validateStep2() {

        let valid = true;

        // bersihkan error dulu
        $('#error_tgl_insiden, #error_jam_insiden, #error_tempat_insiden, #error_insiden, #error_kronologis_insiden, #error_jenis_insiden').html('');

        const tglin = $('#tgl_insiden').val();
        const jm = $('#jam_insiden').val();
        const tptinsi = $('#tempat_insiden').val();
        const ins = $('#insiden').val();
        const kronoins = $('#kronologis_insiden').val();
        const jin = $('input[name="jenis_insiden"]:checked').length > 0;


        if (!tglin) {
            $('#error_tgl_insiden').text('Tanggal insiden wajib diisi');
            valid = false;
        }

        if (!jm) {
            $('#error_jam_insiden').text('Jam insiden wajib diisi');
            valid = false;
        }

        if (!tptinsi) {
            $('#error_tempat_insiden').text('Tempat insiden wajib dipilih');
            valid = false;
        }

        if (!ins) {
            $('#error_insiden').text('Jenis insiden wajib dipilih');
            valid = false;
        }

        if (!kronoins) {
            $('#error_kronologis_insiden').text('Kronologi insiden wajib diisi');
            valid = false;
        }

        if (!jin) {
            $('#error_jenis_insiden').text('Jenis insiden wajib dipilih');
            valid = false;
        }




        // 🔥 fokus ke field pertama yang error
        if (!valid) {
            $('#tgl_insiden, #jam_insiden, #tempat_insiden, #insiden, #kronologi_insiden, #jenis_insiden').filter(function() {
                return !$(this).val();
            }).first().focus();
        }

        return valid;
    }

    // validasi step 3 (pelapor & insiden pada)
    function validateStep3() {

        let valid = true;

        // reset error
        $('#error_pelapor_insiden, #error_insiden_pada, #error_spesialisasi').html('');

        /* =========================
         * 1. PELAPOR INSIDEN
         * ========================= */
        const pelapor = $('input[name="pelapor_insiden"]:checked').val();

        if (!pelapor) {
            $('#error_pelapor_insiden').text('Pelapor insiden wajib dipilih');
            valid = false;
        }

        // jika pilih "Lain-lain"
        if (pelapor === 'Lain-lain') {
            const pelaporLain = $('#pelapor_lain_text').val();
            if (!pelaporLain) {
                $('#error_pelapor_insiden').text('Pelapor lainnya wajib dijelaskan');
                valid = false;
            }
        }

        /* =========================
         * 2. INSIDEN TERJADI PADA
         * ========================= */
        const insidenPada = $('input[name="insiden_pada"]:checked').val();

        if (!insidenPada) {
            $('#error_insiden_pada').text('Insiden terjadi pada wajib dipilih');
            valid = false;
        }

        if (insidenPada === 'Lain-lain') {
            const insidenLain = $('#insiden_pada_lain').val();
            if (!insidenLain) {
                $('#error_insiden_pada').text('Insiden terjadi pada lainnya wajib dijelaskan');
                valid = false;
            }
        }

        /* =========================
         * 3. SPESIALISASI PASIEN
         * ========================= */
        const spesialisasi = $('input[name="spesialisasi_pasien"]:checked').val();

        if (!spesialisasi) {
            $('#error_spesialisasi').text('Spesialisasi pasien wajib dipilih');
            valid = false;
        }

        if (spesialisasi === 'Lain-lain') {
            const spLain = $('#spesialisasi_lain').val();
            if (!spLain) {
                $('#error_spesialisasi').text('Spesialisasi lainnya wajib diisi');
                valid = false;
            }
        }

        /* =========================
         * FOCUS KE ERROR PERTAMA
         * ========================= */
        if (!valid) {

            if (!pelapor) {
                $('input[name="pelapor_insiden"]').first().focus();
            } else if (pelapor === 'Lain-lain' && !$('#pelapor_lain_text').val()) {
                $('#pelapor_lain_text').focus();
            } else if (!insidenPada) {
                $('input[name="insiden_pada"]').first().focus();
            } else if (insidenPada === 'Lain-lain' && !$('#insiden_pada_lain').val()) {
                $('#insiden_pada_lain').focus();
            } else if (!spesialisasi) {
                $('input[name="spesialisasi_pasien"]').first().focus();
            } else if (spesialisasi === 'Lain-lain' && !$('#spesialisasi_lain').val()) {
                $('#spesialisasi_lain').focus();
            }
        }

        return valid;
    }

    // validasi step 4 (akibat insiden)
    function validateStep4() {

        let valid = true;

        // bersihkan error
        $('#error_akibat').html('');

        // cek radio akibat insiden
        const akibat = $('input[name="akibat_insiden"]:checked').val();

        if (!akibat) {
            $('#error_akibat').text('Akibat insiden terhadap pasien wajib dipilih');
            valid = false;
        }

        // fokus ke radio pertama kalau error
        if (!valid) {
            $('input[name="akibat_insiden"]').first().focus();
        }

        return valid;
    }

    // validasi step 5 (tindakan lanjutan)
    function validateStep5() {

        let valid = true;

        const tise = $('#tindakan_segera').val();
        const tindakanOleh = $('input[name="tindakan_oleh"]:checked').val();
        const titim = $('#tindakan_tim').val();
        const petugasLain = $('#tindakan_petugas_lain').val();
        const pernah = $('input[name="pernah_terjadi"]:checked').val();

        // reset error
        $('#error_tindakan_segera, #error_tindakan_oleh, #error_pernah').html('');

        /* =========================
         * 1. TINDAKAN SEGERA (OPTIONAL)
         * ========================= */
        // ❗ tidak wajib → tidak divalidasi
        if (!tise) {
            $('#error_tindakan_segera').text('Tindakan segera wajib dipilih');
            valid = false;
        }

        /* =========================
         * 2. TINDAKAN OLEH (WAJIB)
         * ========================= */

        if (!tindakanOleh) {
            $('#error_tindakan_oleh').text('Tindakan dilakukan oleh wajib dipilih');
            valid = false;
        }

        /* =========================
         * 3. TINDAKAN TIM (WAJIB)
         * ========================= */
        if (!titim) {
            $('#error_tindakan_tim').text('Tindakan oleh tim wajib diisi');
            valid = false;
        }


        /* =========================
         * 4. PETUGAS LAIN (WAJIB)
         * ========================= */
        if (!petugasLain) {
            $('#error_tindakan_petugas_lain').text('Petugas lain wajib diisi');
            valid = false;
        }

        /* =========================
         * 5. PERNAH TERJADI (WAJIB)
         * ========================= */

        if (!pernah) {
            $('#error_pernah').text('Silakan pilih Ya atau Tidak');
            valid = false;
        }

        /* =========================
         * 4. JIKA PERNAH = YA → TINDAKAN LANJUTAN WAJIB
         * ========================= */
        if (pernah === 'Ya') {
            const lanjutan = $('#tindakan_lanjutan').val();
            if (!lanjutan) {
                $('#error_pernah').text(
                    'Karena pernah terjadi, tindakan lanjutan wajib diisi'
                );
                valid = false;
            }
        }

        /* =========================
         * FOKUS KE ERROR PERTAMA
         * ========================= */
        if (!valid) {
            if (!tindakanOleh) {
                $('input[name="tindakan_oleh"]').first().focus();
            } else if (!pernah) {
                $('input[name="pernah_terjadi"]').first().focus();
            } else if (pernah === 'Ya' && !$('#tindakan_lanjutan').val()) {
                $('#tindakan_lanjutan').focus();
            }
        }

        return valid;
    }

    // kosongkan step 1 error saat user mulai input)
    function bindErrorCleanerStep1() {

        // Asal pasien
        $(document).on('change', '#asal_pasien', function() {
            if ($(this).val()) {
                $('#error_asal_pasien').html('');
            }
        });

        // Tanggal masuk
        $(document).on('change', '#tgl_masuk', function() {
            if ($(this).val()) {
                $('#error_tgl_masuk').html('');
            }
        });

        // No Medrec
        $(document).on('input', '#kd_pasien', function() {
            if ($(this).val()) {
                $('#error_kd_pasien').html('');
            }
        });
    }

    // kosongkan step 2 error saat user mulai input)
    function bindErrorCleanerStep2() {

        // Tanggal insiden
        $(document).on('change', '#tgl_insiden', function() {
            if ($(this).val()) {
                $('#error_tgl_insiden').html('');
            }
        });

        // Jam insiden
        $(document).on('change', '#jam_insiden', function() {
            if ($(this).val()) {
                $('#error_jam_insiden').html('');
            }
        });

        // Tempat insiden
        $(document).on('change', '#tempat_insiden', function() {
            if ($(this).val()) {
                $('#error_tempat_insiden').html('');
            }
        });

        // Insiden
        $(document).on('input', '#insiden', function() {
            if ($(this).val()) {
                $('#error_insiden').html('');
            }
        });

        // Kronologi insiden
        $(document).on('input', '#kronologis_insiden', function() {
            if ($(this).val()) {
                $('#error_kronologis_insiden').html('');
            }
        });

        // Jenis insiden (radio group)
        $(document).on('change', 'input[name="jenis_insiden"]', function() {
            $('#error_jenis_insiden').html('');
            $('.card-outline').removeClass('card-error');
        });
    }

    // kosongkan step 3 error saat user mulai input)
    function bindErrorCleanerStep3() {

        // radio pelapor
        $(document).on('change', 'input[name="pelapor_insiden"]', function() {
            $('#error_pelapor_insiden').html('');

            if ($(this).val() === 'Lain-lain') {
                $('#wrap_pelapor_lain').removeClass('d-none');
            } else {
                $('#wrap_pelapor_lain').addClass('d-none');
                $('#pelapor_lain_text').val('');
            }
        });

        // input pelapor lain
        $(document).on('input', '#pelapor_lain_text', function() {
            if ($(this).val()) {
                $('#error_pelapor_insiden').html('');
            }
        });

        // radio insiden pada
        $(document).on('change', 'input[name="insiden_pada"]', function() {
            $('#error_insiden_pada').html('');

            if ($(this).val() === 'Lain-lain') {
                $('#wrap_insiden_lain').removeClass('d-none');
            } else {
                $('#wrap_insiden_lain').addClass('d-none');
                $('#insiden_pada_lain').val('');
            }
        });

        // input insiden pada lain
        $(document).on('input', '#insiden_pada_lain', function() {
            if ($(this).val()) {
                $('#error_insiden_pada').html('');
            }
        });

        // radio spesialisasi
        $(document).on('change', 'input[name="spesialisasi_pasien"]', function() {
            $('#error_spesialisasi').html('');

            if ($(this).val() === 'Lain-lain') {
                $('#wrap_spesialisasi_lain').removeClass('d-none');
            } else {
                $('#wrap_spesialisasi_lain').addClass('d-none');
                $('#spesialisasi_lain').val('');
            }
        });

        // input spesialisasi lain
        $(document).on('input', '#spesialisasi_lain', function() {
            if ($(this).val()) {
                $('#error_spesialisasi').html('');
            }
        });
    }

    // kosongkan step 4 error saat user mulai input)
    function bindErrorCleanerStep4() {

        $(document).on('change', 'input[name="akibat_insiden"]', function() {
            if ($('input[name="akibat_insiden"]:checked').length > 0) {
                $('#error_akibat').html('');
            }
        });
    }

    // kosongkan step 5 error saat user mulai input)
    function bindErrorCleanerStep5() {

        /* Tindakan segera */
        $(document).on('input', '#tindakan_segera', function() {
            if ($(this).val()) {
                $('#error_tindakan_segera').html('');
            }
        });

        /* Tindakan oleh */
        $(document).on('change', 'input[name="tindakan_oleh"]', function() {
            $('#error_tindakan_oleh').html('');
        });

        /* Tindakan tim */
        $(document).on('input', '#tindakan_tim', function() {
            if ($(this).val()) {
                $('#error_tindakan_tim').html('');
            }
        });

        /* Petugas lain */
        $(document).on('input', '#tindakan_petugas_lain', function() {
            if ($(this).val()) {
                $('#error_tindakan_petugas_lain').html('');
            }
        });

        /* Pernah terjadi */
        $(document).on('change', 'input[name="pernah_terjadi"]', function() {

            $('#error_pernah').html('');

            if ($(this).val() === 'Ya') {
                $('#wrap_form_tindakan_lanjutan').removeClass('d-none');
            } else {
                $('#wrap_form_tindakan_lanjutan').addClass('d-none');
                $('#tindakan_lanjutan').val('');
            }
        });

        /* Tindakan lanjutan */
        $(document).on('input', '#tindakan_lanjutan', function() {
            if ($(this).val()) {
                $('#error_pernah').html('');
            }
        });
    }

    // navigasi stepper
    function prevStep() {
        if (!window.ikpStepperInstance) return;
        if (window.currentStep <= 1) return;

        window.ikpStepperInstance.previous();
    }

    // update state tombol navigasi
    function updateNavButtons() {
        const totalSteps = document.querySelectorAll('.bs-stepper-header .step').length;
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const insiden_id = <?= !empty($insiden_id) ? $insiden_id : 0 ?>;

        btnPrev.disabled = window.currentStep === 1;

        if (window.currentStep === totalSteps) {
            if (insiden_id > 0) {
                // Edit DRAFT → tombol jadi "Kirim ke KARU"
                btnNext.textContent = 'Kirim ke KARU';
                btnNext.className = 'btn btn-success';
                btnNext.onclick = function() { kirimDraft(); };
            } else {
                // Input baru → tombol tetap "Simpan"
                btnNext.textContent = 'Simpan';
                btnNext.className = 'btn btn-primary';
                btnNext.onclick = function() { nextStep(); };
            }
        } else {
            btnNext.textContent = 'Selanjutnya';
            btnNext.className = 'btn btn-primary';
            btnNext.onclick = function() { nextStep(); };
        }
    }

    // load tempat insiden (select2 dengan AJAX)
    function loadTempatInsiden() {

        $('#tempat_insiden').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Tempat Insiden --',
            allowClear: true,

            ajax: {
                url: "<?= site_url('ikprs/get_departments') ?>",
                dataType: 'json',
                delay: 250,

                data: function(params) {
                    return {
                        q: params.term // keyword pencarian
                    };
                },

                processResults: function(data) {
                    return {
                        results: $.map(data, function(row) {
                            return {
                                id: row.department_id,
                                text: row.department_name,
                                hris_user_id: row.hris_user_id
                            };
                        })
                    };
                },

                cache: true
            }
        });
    }

    // submit form IKP (placeholder)
    function submitIkp(status = 'DRAFT') {

        // 🔥 tampilkan loading BAR DI LAYOUT UTAMA
        if (typeof showIKPLoading === 'function') {
            showIKPLoading();
        }

        $.ajax({
            url: "<?= site_url('ikprs/simpanikp') ?>",
            type: "POST",
            data: $('#form_ikp').serialize() + '&status_laporan=' + status,
            dataType: "json",

            success: function(res) {

                $('.invalid-feedback').text('');
                $('.is-invalid').removeClass('is-invalid');

                if (!res.status) {

                    if (res.errors) {
                        $.each(res.errors, function(field, msg) {
                            $('[name="' + field + '"]').addClass('is-invalid');
                            $('#error_' + field).text(msg);
                        });
                    }

                    toastError(res.message);

                    // ❌ HENTIKAN loading kalau gagal
                    hideIKPLoading();
                    return;
                }

                toastSuccess(res.message);
                // showIKPLoading();

                // 🔄 load inbox setelah simpan
                $('#inbox-wrapper').load(
                    "<?= site_url('ikprs/form_inbox_karu') ?>",
                    function() {
                        // ✅ STOP loading setelah load selesai
                        hideIKPLoading();
                    }
                );
            },

            error: function() {
                toastr.error('Terjadi kesalahan server');
                hideIKPLoading();
            }
        });
    }

    /* =====================================================
         TOAST HELPER
       ===================================================== */
    function toastWarning(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            iconColor: '#f0ad4e',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function toastError(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            iconColor: '#d9534f',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function toastSuccess(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            iconColor: '#5cb85c',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    /* ===== KIRIM DRAFT KE KARU ===== */
    function kirimDraft() {
        const insiden_id = <?= !empty($insiden_id) ? $insiden_id : 0 ?>;
        
        if (!insiden_id) {
            alert('ID insiden tidak ditemukan!');
            return;
        }

        if (!confirm('Kirim laporan ini ke KARU?')) return;

        const btn = $('#btnKirim');
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Mengirim...');

        $.ajax({
            url: "<?= site_url('ikprs/kirimDraft') ?>",
            type: "POST",
            dataType: "json",
            data: {
                insiden_id: insiden_id,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    alert('Laporan berhasil dikirim ke KARU');
                    // Reload Info tab
                    if (typeof loadInfo === 'function') {
                        loadInfo(1);
                    }
                    // Refresh notif counter
                    if (typeof refreshNotif === 'function') {
                        refreshNotif();
                    }
                    // Close modal if any
                    $('#modal_ikp').modal('hide');
                } else {
                    alert(res.message || 'Gagal mengirim laporan');
                    btn.prop('disabled', false).html('<i class="bi bi-send"></i> Kirim ke KARU');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengirim');
                btn.prop('disabled', false).html('<i class="bi bi-send"></i> Kirim ke KARU');
            }
        });
    }
</script>

<style>
    #ikpStepper .bs-stepper-header .step {
        pointer-events: none;
        opacity: 0.6;
    }

    #ikpStepper .bs-stepper-header .step.active {
        opacity: 1;
    }
</style>