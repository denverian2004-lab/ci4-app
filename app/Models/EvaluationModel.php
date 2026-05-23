<?php

namespace App\Models;

use CodeIgniter\Model;

class EvaluationModel extends Model
{
    protected $table         = 'evaluations';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['employee_id', 'evaluated_by', 'period', 'score', 'comments'];
    protected $useTimestamps = false;

    public function getEvaluationsWithDetails()
    {
        return $this->select('evaluations.*, employees.first_name, employees.last_name, employees.employee_code, users.username as evaluator')
                    ->join('employees', 'employees.id = evaluations.employee_id')
                    ->join('users', 'users.id = evaluations.evaluated_by')
                    ->orderBy('evaluations.created_at', 'DESC')
                    ->findAll();
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}