<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class FileManagerRequestModel extends Model
{
    protected $table = 'file_manager_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'fmr_file_id', 'fmr_file_name', 'fmr_action', 'fmr_reason', 'fmr_new_value', 'fmr_status',
        'fmr_request_by', 'fmr_request_date', 'fmr_approve_by',
        'fmr_approve_date', 'fmr_notes',
    ];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    
    public function createDeleteRequest(int $fileId, string $reason, int $userId, string $fileName = ''): bool
    {
        return (bool) $this->insert([
            'fmr_file_id'     => $fileId,
            'fmr_file_name'   => $fileName,
            'fmr_action'      => 'delete',
            'fmr_reason'      => $reason,
            'fmr_status'      => 'pending',
            'fmr_request_by'  => $userId,
            'fmr_request_date' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getUserRequests(int $userId): array
    {
        $db = db_connect();
        return $db->table('file_manager_requests fmr')
            ->select("fmr.*, COALESCE(fmr.fmr_file_name, fm2.file_name) as file_name, fm2.is_folder")
            ->join('siimut_file_manager fm2', 'fm2.id = fmr.fmr_file_id', 'left')
            ->where('fmr.fmr_request_by', $userId)
            ->orderBy('fmr.fmr_request_date', 'DESC')
            ->get()
            ->getResult();
    }

    public function getPendingRequests(?string $departmentId = null, ?int $requestedBy = null, ?int $limit = null): array
    {
        $db = db_connect();
        $builder = $db->table('file_manager_requests fmr')
            ->select("fmr.*, up.profile_fullname as request_by_name, COALESCE(fmr.fmr_file_name, fm2.file_name) as file_name, fm2.is_folder, fm2.file_path")
            ->join('user_profile up', 'up.profile_id = fmr.fmr_request_by', 'left')
            ->join('siimut_file_manager fm2', 'fm2.id = fmr.fmr_file_id', 'left')
            ->where('fmr.fmr_status', 'pending');

        if ($departmentId) {
            $builder->where('up.profile_department_id', $departmentId);
        }
        if ($requestedBy) {
            $builder->where('fmr.fmr_request_by', $requestedBy);
        }
        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->orderBy('fmr.fmr_request_date', 'DESC')
            ->get()
            ->getResult();
    }

    public function getPendingCount(): int
    {
        $db = db_connect();
        return $db->table('file_manager_requests')
            ->where('fmr_status', 'pending')
            ->countAllResults();
    }

    public function getMyRecentRequests(int $userId, int $limit = 5): array
    {
        $db = db_connect();
        return $db->table('file_manager_requests fmr')
            ->select("fmr.*, COALESCE(fmr.fmr_file_name, fm2.file_name) as file_name, fm2.is_folder, fm2.file_path")
            ->join('siimut_file_manager fm2', 'fm2.id = fmr.fmr_file_id', 'left')
            ->where('fmr.fmr_request_by', $userId)
            ->orderBy('fmr.fmr_request_date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getHistory(?string $departmentId = null, ?int $requestedBy = null): array
    {
        $db = db_connect();
        $builder = $db->table('file_manager_requests fmr')
            ->select("fmr.*, up.profile_fullname as request_by_name, up2.profile_fullname as approve_by_name, COALESCE(fmr.fmr_file_name, fm2.file_name) as file_name, fm2.is_folder, fm2.file_path")
            ->join('user_profile up', 'up.profile_id = fmr.fmr_request_by', 'left')
            ->join('user_profile up2', 'up2.profile_id = fmr.fmr_approve_by', 'left')
            ->join('siimut_file_manager fm2', 'fm2.id = fmr.fmr_file_id', 'left')
            ->whereIn('fmr.fmr_status', ['approved', 'rejected']);

        if ($departmentId) {
            $builder->where('up.profile_department_id', $departmentId);
        }
        if ($requestedBy) {
            $builder->where('fmr.fmr_request_by', $requestedBy);
        }

        return $builder->orderBy('fmr.fmr_request_date', 'DESC')
            ->get()
            ->getResult();
    }

    public function approveRequest(int $id, int $adminId): bool
    {
        return $this->update($id, [
            'fmr_status'       => 'approved',
            'fmr_approve_by'   => $adminId,
            'fmr_approve_date' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rejectRequest(int $id, int $adminId, string $notes): bool
    {
        return $this->update($id, [
            'fmr_status'       => 'rejected',
            'fmr_approve_by'   => $adminId,
            'fmr_approve_date' => date('Y-m-d H:i:s'),
            'fmr_notes'        => $notes,
        ]);
    }
}
