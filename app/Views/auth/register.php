<h4 class="text-center mb-4 fw-bold">Registrasi Akun Baru</h4>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger text-center">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="alert alert-info text-center">
    <small>Lengkapi data berikut untuk menyelesaikan pendaftaran.</small>
</div>

<form action="<?= site_url('auth/register/process') ?>" method="post">

    <div class="form-outline mb-3">
        <input type="text" name="profile_fullname" class="form-control form-control-lg"
            value="<?= esc($name) ?>" required>
        <label class="form-label">Nama Lengkap</label>
    </div>

    <div class="form-outline mb-3">
        <input type="email" name="profile_email" class="form-control form-control-lg"
            value="<?= esc($email) ?>" readonly>
        <label class="form-label">Email Google</label>
    </div>

    <div class="form-outline mb-3">
        <select name="profile_gender" class="form-select form-select-lg">
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="1">Laki-laki</option>
            <option value="2">Perempuan</option>
        </select>
        <label class="form-label">Jenis Kelamin</label>
    </div>

    <div class="form-outline mb-3">
        <input type="text" name="profile_birth_place" class="form-control form-control-lg"
            placeholder="Contoh: Surabaya">
        <label class="form-label">Tempat Lahir</label>
    </div>

    <div class="form-outline mb-3">
        <input type="date" name="profile_dob" class="form-control form-control-lg">
        <label class="form-label">Tanggal Lahir</label>
    </div>

    <div class="form-outline mb-3">
        <input type="text" name="profile_handphone1" class="form-control form-control-lg"
            placeholder="08xxxxxxxxxx">
        <label class="form-label">Nomor HP 1</label>
    </div>

    <div class="form-outline mb-3">
        <input type="text" name="profile_handphone2" class="form-control form-control-lg"
            placeholder="08xxxxxxxxxx">
        <label class="form-label">Nomor HP 2 (opsional)</label>
    </div>

    <div class="text-center mt-4 pt-2">
        <button type="submit" class="btn btn-primary btn-lg w-100">
            Daftar & Login
        </button>
    </div>

</form>

<div class="text-center mt-3">
    <a href="<?= site_url('auth') ?>" class="text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali ke Login
    </a>
</div>
