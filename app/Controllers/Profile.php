<?php

namespace App\Controllers;

use App\Models\StaffModel;

class Profile extends AppController
{
    protected StaffModel $staffModel;

    public function __construct()
    {
        parent::__construct();
        $this->staffModel = new StaffModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $profileId = session()->get('profile_id');
        $user = $this->staffModel->getStaffById((int) $profileId);

        if (!$user) {
            return redirect()->to('/siimut/dashboard')->with('error', 'User tidak ditemukan');
        }

        helper('profile');

        return $this->render('siimut/profile_index', [
            'judul' => 'Profil Saya',
            'icon'  => '<i class="bi bi-person-circle"></i>',
            'user'  => $user,
        ]);
    }

    public function update()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $profileId = session()->get('profile_id');

        $fullname   = trim($this->request->getPost('profile_fullname'));
        $email      = trim($this->request->getPost('profile_email'));
        $handphone1 = $this->request->getPost('profile_handphone1');
        $birthPlace = $this->request->getPost('profile_birth_place');
        $dob        = $this->request->getPost('profile_dob');
        $gender     = $this->request->getPost('profile_gender');
        $address    = $this->request->getPost('profile_address');
        $employeeId = $this->request->getPost('profile_employee_id');

        if (!$fullname || !$email) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama dan email wajib diisi']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format email tidak valid']);
        }

        $updateData = [
            'profile_fullname'    => $fullname,
            'profile_email'       => $email,
            'profile_handphone1'  => $handphone1 ?: null,
            'profile_birth_place' => $birthPlace ?: null,
            'profile_dob'         => $dob ?: null,
            'profile_gender'      => $gender ?: null,
            'profile_address'     => $address ?: null,
            'profile_employee_id' => $employeeId ?: null,
            'profile_update_by'   => $profileId,
            'profile_update_date' => date('Y-m-d H:i:s'),
        ];

        $result = $this->staffModel->updateStaff($profileId, $updateData);

        if ($result) {
            session()->set('nama_lengkap', $fullname);
            session()->set('profile_email', $email);

            return $this->response->setJSON(['status' => true, 'message' => 'Profil berhasil diperbarui']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal memperbarui profil']);
    }

    public function changePassword()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $profileId = session()->get('profile_id');

        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!$currentPassword || !$newPassword || !$confirmPassword) {
            return $this->response->setJSON(['status' => false, 'message' => 'Semua field password wajib diisi']);
        }

        if (strlen($newPassword) < 6) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password baru minimal 6 karakter']);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['status' => false, 'message' => 'Konfirmasi password tidak cocok']);
        }

        $db = db_connect();
        $user = $db->table('user_profile')
            ->select('profile_password')
            ->where('profile_id', $profileId)
            ->get()
            ->getRow();

        if (!$user || $user->profile_password !== md5($currentPassword)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password saat ini salah']);
        }

        $result = $db->table('user_profile')
            ->where('profile_id', $profileId)
            ->update([
                'profile_password'          => md5($newPassword),
                'profile_confirm_password'  => $confirmPassword,
                'profile_update_by'         => $profileId,
                'profile_update_date'       => date('Y-m-d H:i:s'),
            ]);

        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'Password berhasil diubah']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah password']);
    }

    public function updatePhoto()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $profileId = session()->get('profile_id');

        $file = $this->request->getFile('profile_photo');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pilih file foto']);
        }

        $allowedTypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($file->getMimeType(), $allowedTypes) || !in_array(strtolower($file->getExtension()), $allowedExts)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file harus JPG, PNG, GIF, atau WebP']);
        }

        $maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON(['status' => false, 'message' => 'Ukuran file maksimal 2MB']);
        }

        $uploadPath = FCPATH . 'uploads/profile_pics/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $ext = $file->getExtension();
        $newName = 'user_' . $profileId . '_' . time() . '.' . $ext;

        $file->move($uploadPath, $newName, true);

        $photoUrl = 'uploads/profile_pics/' . $newName;

        $this->staffModel->updateStaff($profileId, [
            'profile_photo'      => $photoUrl,
            'profile_update_by'  => $profileId,
            'profile_update_date' => date('Y-m-d H:i:s'),
        ]);

        session()->set('profile_picture', $photoUrl);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Foto profil berhasil diperbarui',
            'photo'   => base_url($photoUrl),
        ]);
    }
}
