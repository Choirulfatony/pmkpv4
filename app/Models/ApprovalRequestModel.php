<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ApprovalRequestModel extends Model
{
    protected $table = 'approval_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ar_indicator_id', 'ar_department_id', 'ar_period', 'ar_period_end',
        'ar_reason', 'ar_status', 'ar_request_by',
        'ar_request_date', 'ar_approve_by', 'ar_approve_date', 'ar_notes',
        'ar_action_type', 'ar_group_type'
    ];
    protected $useTimestamps = false;

    public function saveRequest(int $indicatorId, string $departmentId, string $period, string $reason, int $userId, string $actionType = 'edit', ?string $groupType = null): bool
    {
        return (bool) $this->insert([
            'ar_indicator_id'   => $indicatorId,
            'ar_department_id'  => $departmentId,
            'ar_period'         => $period,
            'ar_reason'         => $reason,
            'ar_action_type'    => $actionType,
            'ar_group_type'     => $groupType,
            'ar_status'         => 'pending',
            'ar_request_by'     => $userId,
            'ar_request_date'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function saveOpenPeriodRequest(int $indicatorId, string $departmentId, string $periodStart, string $periodEnd, string $reason, int $userId, string $actionType = 'open_period', ?string $groupType = null): bool
    {
        return (bool) $this->insert([
            'ar_indicator_id'   => $indicatorId,
            'ar_department_id'  => $departmentId,
            'ar_period'         => $periodStart,
            'ar_period_end'     => $periodEnd,
            'ar_reason'         => $reason,
            'ar_action_type'    => $actionType,
            'ar_group_type'     => $groupType,
            'ar_status'         => 'pending',
            'ar_request_by'     => $userId,
            'ar_request_date'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function getPendingRequests(?string $groupType = null, ?int $limit = null): array
    {
        $db = db_connect();
        $q = $db->table('approval_requests ar')
            ->select("ar.*, up.profile_fullname as request_by_name, COALESCE(qi.indicator_element, lqi.indicator_element) as indicator_name, mid.department_name, COALESCE(qi.indicator_category_id, lqi.indicator_category_id) as indicator_category_id")
            ->join('user_profile up', 'up.profile_id = ar.ar_request_by', 'left')
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('local_quality_indicator lqi', 'lqi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = ar.ar_department_id', 'left')
            ->where('ar.ar_status', 'pending')
            ->orderBy('ar.ar_request_date', 'DESC');
        if ($groupType !== null) {
            $q->where('ar.ar_group_type', $groupType);
        }
        if ($limit !== null) {
            $q->limit($limit);
        }
        return $q->get()->getResult();
    }

    public function getMyRecentRequests(int $profileId, int $limit = 5): array
    {
        $db = db_connect();
        return $db->table('approval_requests ar')
            ->select("ar.*, COALESCE(qi.indicator_element, lqi.indicator_element) as indicator_name, mid.department_name, COALESCE(qi.indicator_category_id, lqi.indicator_category_id) as indicator_category_id")
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('local_quality_indicator lqi', 'lqi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = ar.ar_department_id', 'left')
            ->where('ar.ar_request_by', $profileId)
            ->orderBy('ar.ar_request_date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getPendingCount(?string $groupType = null): int
    {
        $db = db_connect();
        $q = $db->table('approval_requests ar')
            ->where('ar_status', 'pending');
        if ($groupType !== null) {
            $q->where('ar_group_type', $groupType);
        }
        return $q->countAllResults();
    }

    public function getAllRequests(?string $groupType = null): array
    {
        $db = db_connect();
        $q = $db->table('approval_requests ar')
            ->select("ar.*, up1.profile_fullname as request_by_name, up2.profile_fullname as approve_by_name, COALESCE(qi.indicator_element, lqi.indicator_element) as indicator_name, mid.department_name, COALESCE(qi.indicator_category_id, lqi.indicator_category_id) as indicator_category_id")
            ->join('user_profile up1', 'up1.profile_id = ar.ar_request_by', 'left')
            ->join('user_profile up2', 'up2.profile_id = ar.ar_approve_by', 'left')
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('local_quality_indicator lqi', 'lqi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = ar.ar_department_id', 'left')
            ->orderBy('ar.ar_request_date', 'DESC');
        if ($groupType !== null) {
            $q->where('ar.ar_group_type', $groupType);
        }
        return $q->get()->getResult();
    }

    public function getPendingByType(string $actionType): array
    {
        $db = db_connect();
        return $db->table('approval_requests ar')
            ->select("ar.*, up.profile_fullname as request_by_name, COALESCE(qi.indicator_element, lqi.indicator_element) as indicator_name, mid.department_name")
            ->join('user_profile up', 'up.profile_id = ar.ar_request_by', 'left')
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('local_quality_indicator lqi', 'lqi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = ar.ar_department_id', 'left')
            ->where('ar.ar_status', 'pending')
            ->where('ar.ar_action_type', $actionType)
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

    public function rejectExpiredApprovals(): int
    {
        $db = db_connect();
        $cutoff = date('Y-m-d H:i:s', strtotime('-48 hours'));
        $db->table('approval_requests')
            ->where('ar_status', 'approved')
            ->where('ar_approve_date <', $cutoff)
            ->update([
                'ar_status' => 'rejected',
                'ar_notes'  => 'Ditolak otomatis: respon lama (lebih dari 2x24 jam)',
            ]);
        return $db->affectedRows();
    }

    public function isApproved(int $indicatorId, string $departmentId, string $period, int $userId): bool
    {
        $this->rejectExpiredApprovals();
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
        $this->rejectExpiredApprovals();
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
        $this->rejectExpiredApprovals();
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

    public function getUserRequests(int $userId, string $departmentId, ?string $actionType = null, ?string $groupType = null): array
    {
        $db = db_connect();
        $q = $db->table('approval_requests ar')
            ->select("ar.*, up.profile_fullname as approve_by_name, COALESCE(qi.indicator_element, lqi.indicator_element) as indicator_name")
            ->join('user_profile up', 'up.profile_id = ar.ar_approve_by', 'left')
            ->join('quality_indicator qi', 'qi.indicator_id = ar.ar_indicator_id', 'left')
            ->join('local_quality_indicator lqi', 'lqi.indicator_id = ar.ar_indicator_id', 'left')
            ->where('ar.ar_request_by', $userId)
            ->where('ar.ar_department_id', $departmentId)
            ->orderBy('ar.ar_request_date', 'DESC')
            ->limit(5);

        if ($actionType !== null) {
            $q->where('ar.ar_action_type', $actionType);
        }

        if ($groupType !== null && $groupType !== '') {
            $q->where('ar.ar_group_type', $groupType);
        }

        return $q->get()->getResult();
    }

    public function getById(int $id)
    {
        return $this->asObject()->find($id);
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
