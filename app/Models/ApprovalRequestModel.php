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
        'ar_request_date', 'ar_approve_by', 'ar_approve_date', 'ar_notes',
        'ar_action_type'
    ];
    protected $useTimestamps = false;

    public function saveRequest(int $indicatorId, string $departmentId, string $period, string $reason, int $userId, string $actionType = 'edit'): bool
    {
        return (bool) $this->insert([
            'ar_indicator_id'   => $indicatorId,
            'ar_department_id'  => $departmentId,
            'ar_period'         => $period,
            'ar_reason'         => $reason,
            'ar_action_type'    => $actionType,
            'ar_status'         => 'pending',
            'ar_request_by'     => $userId,
            'ar_request_date'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function getPendingRequests(): array
    {
        $db = db_connect();
        return $db->table('approval_requests ar')
            ->select('ar.*, up.profile_fullname as request_by_name, qi.indicator_element as indicator_name, mid.department_name')
            ->join('user_profile up', 'up.profile_id = ar.ar_request_by', 'left')
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = ar.ar_department_id', 'left')
            ->where('ar.ar_status', 'pending')
            ->orderBy('ar.ar_request_date', 'DESC')
            ->get()
            ->getResult();
    }

    public function getAllRequests(): array
    {
        $db = db_connect();
        return $db->table('approval_requests ar')
            ->select('ar.*, up1.profile_fullname as request_by_name, up2.profile_fullname as approve_by_name, qi.indicator_element as indicator_name, mid.department_name')
            ->join('user_profile up1', 'up1.profile_id = ar.ar_request_by', 'left')
            ->join('user_profile up2', 'up2.profile_id = ar.ar_approve_by', 'left')
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = ar.ar_department_id', 'left')
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

    public function getApprovedAction(int $indicatorId, string $departmentId, string $period, int $userId): ?string
    {
        $db = db_connect();
        $row = $db->table('approval_requests')
            ->select('ar_action_type')
            ->where('ar_indicator_id', $indicatorId)
            ->where('ar_department_id', $departmentId)
            ->where('ar_period', $period)
            ->where('ar_request_by', $userId)
            ->where('ar_status', 'approved')
            ->get()
            ->getRow();
        return $row ? $row->ar_action_type : null;
    }

    public function isApprovedForAction(int $indicatorId, string $departmentId, string $period, int $userId, string $actionType): bool
    {
        $db = db_connect();
        $row = $db->table('approval_requests')
            ->where('ar_indicator_id', $indicatorId)
            ->where('ar_department_id', $departmentId)
            ->where('ar_period', $period)
            ->where('ar_request_by', $userId)
            ->where('ar_status', 'approved')
            ->where('ar_action_type', $actionType)
            ->get()
            ->getRow();
        return $row !== null;
    }

    public function markApprovalUsed(int $indicatorId, string $departmentId, string $period, int $userId): void
    {
        $db = db_connect();
        $db->table('approval_requests')
            ->where('ar_indicator_id', $indicatorId)
            ->where('ar_department_id', $departmentId)
            ->where('ar_period', $period)
            ->where('ar_request_by', $userId)
            ->where('ar_status', 'approved')
            ->update(['ar_status' => 'completed']);
    }

    public function getRequestStatus(int $indicatorId, string $departmentId, string $period, int $userId)
    {
        $db = db_connect();
        return $db->table('approval_requests ar')
            ->select('ar.*, up.profile_fullname as approve_by_name')
            ->join('user_profile up', 'up.profile_id = ar.ar_approve_by', 'left')
            ->where('ar.ar_indicator_id', $indicatorId)
            ->where('ar.ar_department_id', $departmentId)
            ->where('ar.ar_period', $period)
            ->where('ar.ar_request_by', $userId)
            ->orderBy('ar.ar_request_date', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
}
