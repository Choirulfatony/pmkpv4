<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ApprovalRequestModel extends Model
{
    protected $table = 'approval_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ar_indicator_id', 'ar_department_id', 'ar_period',
        'ar_reason', 'ar_status', 'ar_request_by',
        'ar_request_date', 'ar_approve_by', 'ar_approve_date', 'ar_notes'
    ];
    protected $useTimestamps = false;

    public function saveRequest(int $indicatorId, string $departmentId, string $period, string $reason, int $userId): bool
    {
        return $this->insert([
            'ar_indicator_id'   => $indicatorId,
            'ar_department_id'  => $departmentId,
            'ar_period'         => $period,
            'ar_reason'         => $reason,
            'ar_status'         => 'pending',
            'ar_request_by'     => $userId,
            'ar_request_date'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function getPendingRequests(): array
    {
        $db = db_connect();
        return $db->table('approval_requests ar')
            ->select('ar.*, p.profile_fullname as request_by_name')
            ->join('profile p', 'p.profile_id = ar.ar_request_by', 'left')
            ->where('ar.ar_status', 'pending')
            ->orderBy('ar.ar_request_date', 'DESC')
            ->get()
            ->getResult();
    }

    public function approveRequest(int $id, int $adminId): bool
    {
        return $this->update($id, [
            'ar_status'       => 'approved',
            'ar_approve_by'   => $adminId,
            'ar_approve_date' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rejectRequest(int $id, int $adminId, string $notes): bool
    {
        return $this->update($id, [
            'ar_status'       => 'rejected',
            'ar_approve_by'   => $adminId,
            'ar_approve_date' => date('Y-m-d H:i:s'),
            'ar_notes'        => $notes,
        ]);
    }

    public function isApproved(int $indicatorId, string $departmentId, string $period, int $userId): bool
    {
        $db = db_connect();
        $row = $db->table('approval_requests')
            ->where('ar_indicator_id', $indicatorId)
            ->where('ar_department_id', $departmentId)
            ->where('ar_period', $period)
            ->where('ar_request_by', $userId)
            ->where('ar_status', 'approved')
            ->get()
            ->getRow();
        return $row !== null;
    }
}
