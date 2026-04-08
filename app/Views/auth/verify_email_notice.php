<style>
    .verification-success {
        max-width: 500px;
        margin: 0 auto;
    }
    
    .success-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        animation: scaleIn 0.5s ease-out;
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .success-icon i {
        font-size: 50px;
        color: white;
    }
    
    .email-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .email-icon i {
        font-size: 40px;
        color: white;
    }
</style>

<div class="verification-success">
    <?php if (session()->getFlashdata('success')): ?>
        <!-- Success State -->
        <div class="text-center">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <h4 class="text-success mb-3">Email Berhasil Diverifikasi!</h4>
            <p class="text-muted">Selamat! Akun Anda telah aktif dan terverifikasi.</p>
            
            <div class="d-grid gap-2 mt-4">
                <a href="<?= site_url('/siimut/dashboard') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Dashboard
                </a>
                <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Pending Verification State -->
        <div class="text-center">
            <div class="email-icon">
                <i class="bi bi-envelope-check"></i>
            </div>
            <h4 class="text-primary mb-3">Cek Email Anda!</h4>
            <p class="text-muted mb-4">
                Kami telah mengirimkan link verifikasi ke alamat email:
                <strong class="text-dark d-block mt-2"><?= esc($email ?? '') ?></strong>
            </p>
            
            <div class="alert alert-light border mb-4">
                <i class="bi bi-info-circle text-info me-2"></i>
                <small class="text-muted">
                    Link verifikasi akan kadaluarsa dalam <strong>24 jam</strong>. 
                    Silakan cek folder <strong>Spam</strong> jika email tidak ditemukan di Kotak Masuk.
                </small>
            </div>
            
            <div class="d-grid gap-2">
                <a href="https://mail.google.com" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-google me-2"></i>Buka Gmail
                </a>
                <a href="<?= site_url('auth') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Login
                </a>
            </div>
            
            <hr class="my-4">
            
            <small class="text-muted">
                Tidak menerima email? 
                <a href="<?= site_url('auth/logout') ?>" class="text-decoration-none">Coba daftar lagi</a>
            </small>
        </div>
    <?php endif; ?>
</div>
