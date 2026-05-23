<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceThresholdModel extends Model
{
    protected $table         = 'attendance_thresholds';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['threshold_type', 'max_allowed', 'period'];
    protected $useTimestamps = false;

    public function getThreshold(string $type)
    {
        return $this->where('threshold_type', $type)->first();
    }

    public function getAll()
    {
        return $this->findAll();
    }

    // Get employees who exceeded threshold this month
    public function getViolators(string $type, string $monthStart, string $monthEnd)
    {
        $threshold = $this->getThreshold($type);
        if (!$threshold) return [];

        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT
                e.id,
                e.first_name,
                e.last_name,
                e.employee_code,
                e.position,
                d.name as department_name,
                COUNT(a.id) as count
            FROM attendance a
            JOIN employees e ON e.id = a.employee_id
            LEFT JOIN departments d ON d.id = e.department_id
            WHERE a.status = ?
            AND a.date >= ?
            AND a.date <= ?
            AND e.status = 'Active'
            GROUP BY e.id
            HAVING count >= ?
            ORDER BY count DESC
        ", [$type === 'absent' ? 'Absent' : 'Late', $monthStart, $monthEnd, $threshold['max_allowed']]);

        return $query->getResultArray();
    }
}