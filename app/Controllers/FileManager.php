<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FileManagerModel;
use App\Models\FileManagerRequestModel;

class FileManager extends AppController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $this->disableCache();

        $folderId = $this->request->getGet('folder');
        $model = new FileManagerModel();

        $currentFolder = null;
        $breadcrumbs = [];
        if ($folderId) {
            $currentFolder = $model->find((int) $folderId);
            if ($currentFolder && $currentFolder->is_folder) {
                $breadcrumbs = $model->getBreadcrumbs((int) $folderId);
            } else {
                $folderId = null;
            }
        }

        $items = $model->getItems($folderId ? (int) $folderId : null);

        foreach ($items as $item) {
            if (!$item->is_folder) {
                $item->size_formatted = $this->formatSize((int) $item->file_size);
            }
        }

        return $this->render('siimut/file_manager', [
            'judul'         => 'Dokumen Mutu',
            'icon'          => '<i class="bi bi-folder"></i>',
            '_content'      => view('siimut/file_manager', [
                'items'         => $items,
                'currentFolder' => $currentFolder,
                'breadcrumbs'   => $breadcrumbs,
            ]),
        ]);
    }

    public function ajaxList()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([]);
        }

        $model = new FileManagerModel();
        $folderId = $this->request->getPost('folder_id');
        $items = $model->getItems($folderId ? (int) $folderId : null);

        $result = [];
        foreach ($items as $row) {
            if ($row->is_folder) {
                $icon = '<i class="bi bi-folder-fill text-warning me-2 fs-5"></i>';
                $nameHtml = '<a href="' . site_url('siimut/dokumen-mutu?folder=' . $row->id) . '" class="text-decoration-none fw-medium">' . esc($row->file_name) . '</a>';
                $ext = '-';
                $size = '-';
                $actions = '<button class="btn btn-sm btn-outline-warning btn-request-delete" data-id="' . $row->id . '" data-type="folder" title="Minta Hapus Folder"><i class="bi bi-send"></i></button>';
            } else {
                $icon = '<i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>';
                $nameHtml = esc($row->file_name);
                $ext = strtoupper(pathinfo($row->file_name, PATHINFO_EXTENSION));
                $size = $this->formatSize((int) $row->file_size);

                $actions = '<a href="' . site_url('siimut/dokumen-mutu/download/' . $row->id) . '" class="btn btn-sm btn-outline-info me-1" title="Download"><i class="bi bi-download"></i></a>';
                $fileUrl = base_url('uploads/file_manager/' . $row->file_path);
                $actions .= '<a href="' . $fileUrl . '" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="View"><i class="bi bi-eye"></i></a>';
                $actions .= '<button class="btn btn-sm btn-outline-warning btn-request-delete" data-id="' . $row->id . '" data-type="file" title="Minta Hapus File"><i class="bi bi-send"></i></button>';
            }

            $result[] = [
                $icon . ' ' . $nameHtml,
                $ext,
                $size,
                $row->uploader_name ?: '-',
                $row->created_at ? date('d/m/Y H:i', strtotime($row->created_at)) : '-',
                $row->description ?: '-',
                $actions,
            ];
        }

        return $this->response->setJSON([
            'data' => $result,
        ]);
    }

    public function createFolder()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya admin yang dapat membuat folder']);
        }

        $name = trim($this->request->getPost('folder_name'));
        if (!$name) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama folder wajib diisi']);
        }

        $parentId = $this->request->getPost('parent_id');
        $parentId = $parentId ? (int) $parentId : null;

        $model = new FileManagerModel();
        $model->save([
            'file_name'   => $name,
            'is_folder'   => 1,
            'parent_id'   => $parentId,
            'uploaded_by' => session('profile_id'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Folder berhasil dibuat']);
    }

    public function upload()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            $error = $file ? $file->getErrorString() : 'Tidak ada file yang dipilih';
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid: ' . $error]);
        }

        $ext = strtolower($file->getExtension());
        $maxSize = 12 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON(['status' => false, 'message' => 'Maksimal ukuran file 12MB']);
        }

        $newName = date('Ymd_His') . '_' . $file->getRandomName();
        $uploadPath = FCPATH . 'uploads/file_manager';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if (!is_writable($uploadPath)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Folder upload tidak dapat ditulis. Hubungi administrator']);
        }

        try {
            $file->move($uploadPath, $newName);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan file: ' . $e->getMessage()]);
        }

        $parentId = $this->request->getPost('parent_id');
        $parentId = $parentId ? (int) $parentId : null;

        $model = new FileManagerModel();
        $model->save([
            'file_name'   => $file->getClientName(),
            'file_path'   => $newName,
            'file_type'   => $ext,
            'file_size'   => $file->getSize(),
            'is_folder'   => 0,
            'uploaded_by' => session('profile_id'),
            'description' => $this->request->getPost('description') ?? '',
            'parent_id'   => $parentId,
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'File berhasil diupload']);
    }

    public function download(int $id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $model = new FileManagerModel();
        $file = $model->find($id);
        if (!$file || $file->is_folder) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = FCPATH . 'uploads/file_manager/' . $file->file_path;
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        return $this->response->download($path, null)->setFileName($file->file_name);
    }

    public function delete(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya admin yang dapat menghapus']);
        }

        $model = new FileManagerModel();
        $item = $model->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ($item->is_folder) {
            $children = $model->getItems($id);
            if (!empty($children)) {
                return $this->response->setJSON(['status' => false, 'message' => 'Folder tidak kosong. Pindahkan atau hapus isi folder terlebih dahulu']);
            }
        } else {
            $path = FCPATH . 'uploads/file_manager/' . $item->file_path;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $model->delete($id);
        $type = $item->is_folder ? 'Folder' : 'File';
        return $this->response->setJSON(['status' => true, 'message' => $type . ' berhasil dihapus']);
    }

    public function deleteRequestsList()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $this->disableCache();

        return $this->render('siimut/delete_requests', [
            'judul'    => 'Permintaan Hapus Dokumen',
            'icon'     => '<i class="bi bi-exclamation-triangle"></i>',
            '_content' => view('siimut/delete_requests'),
        ]);
    }

    public function requestDelete()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $fileId = (int) $this->request->getPost('file_id');
        $reason = trim($this->request->getPost('reason') ?? '');

        if (!$fileId) {
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid']);
        }

        if (!$reason) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alasan wajib diisi']);
        }

        $fileModel = new FileManagerModel();
        $item = $fileModel->find($fileId);
        if ($item && $item->is_folder) {
            $children = $fileModel->where('parent_id', $item->id)->countAllResults();
            if ($children > 0) {
                return $this->response->setJSON(['status' => false, 'message' => 'Folder masih berisi file, kosongkan folder terlebih dahulu']);
            }
        }

        $reqModel = new FileManagerRequestModel();
        $saved = $reqModel->createDeleteRequest($fileId, $reason, (int) session('profile_id'), $item->file_name ?? '');

        if ($saved) {
            return $this->response->setJSON(['status' => true, 'message' => 'Permintaan penghapusan telah dikirim ke admin']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengirim permintaan']);
    }

    public function ajaxGetMyRequests()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $reqModel = new FileManagerRequestModel();
        $requests = $reqModel->getUserRequests((int) session('profile_id'));

        return $this->response->setJSON(['status' => true, 'data' => $requests]);
    }

    public function ajaxGetDeleteRequests()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $reqModel = new FileManagerRequestModel();
        $requests = $reqModel->getPendingRequests();

        return $this->response->setJSON(['status' => true, 'data' => $requests]);
    }

    public function ajaxGetDeleteHistory()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $reqModel = new FileManagerRequestModel();
        $history = $reqModel->getHistory();

        return $this->response->setJSON(['status' => true, 'data' => $history]);
    }

    public function approveDeleteRequest()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya admin yang dapat menyetujui']);
        }

        $reqId = (int) $this->request->getPost('id');
        if (!$reqId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $reqModel = new FileManagerRequestModel();
        $request = $reqModel->find($reqId);
        if (!$request) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $fileModel = new FileManagerModel();
        $item = $fileModel->find((int) $request->fmr_file_id);
        if (!$item) {
            return $this->response->setJSON(['status' => false, 'message' => 'File sudah tidak ditemukan']);
        }

        if ($item->is_folder) {
            $children = $fileModel->where('parent_id', $item->id)->countAllResults();
            if ($children > 0) {
                return $this->response->setJSON(['status' => false, 'message' => 'Folder masih berisi file, hapus isinya terlebih dahulu']);
            }
        }

        if (!$item->is_folder) {
            $path = FCPATH . 'uploads/file_manager/' . $item->file_path;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $fileModel->delete($item->id);

        $adminId = (int) session('profile_id');
        $reqModel->approveRequest($reqId, $adminId);

        return $this->response->setJSON(['status' => true, 'message' => 'Permintaan disetujui dan file telah dihapus']);
    }

    public function rejectDeleteRequest()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya admin yang dapat menolak']);
        }

        $reqId = (int) $this->request->getPost('id');
        $notes = trim($this->request->getPost('notes') ?? '');

        if (!$reqId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $reqModel = new FileManagerRequestModel();
        $adminId = (int) session('profile_id');
        $saved = $reqModel->rejectRequest($reqId, $adminId, $notes);

        if ($saved) {
            return $this->response->setJSON(['status' => true, 'message' => 'Permintaan ditolak']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menolak permintaan']);
    }

    public function renameFolder()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $folderId = (int) $this->request->getPost('folder_id');
        $newName = trim($this->request->getPost('folder_name') ?? '');

        if (!$folderId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Folder tidak valid']);
        }

        if (!$newName) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama folder wajib diisi']);
        }

        $model = new \App\Models\FileManagerModel();
        $folder = $model->find($folderId);
        if (!$folder || !$folder->is_folder) {
            return $this->response->setJSON(['status' => false, 'message' => 'Folder tidak ditemukan']);
        }

        $model->update($folderId, ['file_name' => $newName]);
        return $this->response->setJSON(['status' => true, 'message' => 'Nama folder berhasil diubah']);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
