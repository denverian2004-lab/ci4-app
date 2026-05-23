<?php

namespace App\Controllers;

use App\Models\EvaluationModel;
use App\Models\EmployeeModel;

class EvaluationController extends BaseController
{
    protected $evaluationModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->evaluationModel = new EvaluationModel();
        $this->employeeModel   = new EmployeeModel();
    }

    public function index()
    {
        $evaluations = $this->evaluationModel->getEvaluationsWithDetails();

        return view('admin/evaluations/index', [
            'title'       => 'Performance Evaluations',
            'evaluations' => $evaluations,
        ]);
    }

    public function create()
    {
        return view('admin/evaluations/create', [
            'title'     => 'Add Evaluation',
            'employees' => $this->employeeModel->getActiveEmployees(),
        ]);
    }

    public function store()
    {
        $rules = [
            'employee_id' => 'required|integer',
            'period'      => 'required|min_length[3]',
            'score'       => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $score = (float)$this->request->getPost('score');
        if ($score < 0 || $score > 100) {
            return redirect()->back()
                             ->with('error', 'Score must be between 0 and 100.')
                             ->withInput();
        }

        $this->evaluationModel->insert([
            'employee_id'  => $this->request->getPost('employee_id'),
            'evaluated_by' => session()->get('user_id'),
            'period'       => $this->request->getPost('period'),
            'score'        => $score,
            'comments'     => $this->request->getPost('comments'),
        ]);

        return redirect()->to('/admin/evaluations')
                         ->with('success', 'Evaluation submitted successfully!');
    }

    public function view(int $id)
    {
        $evaluation = $this->evaluationModel->getEvaluationsWithDetails();
        $evaluation = array_filter($evaluation, fn($e) => $e['id'] == $id);
        $evaluation = array_values($evaluation)[0] ?? null;

        if (!$evaluation) {
            return redirect()->to('/admin/evaluations')->with('error', 'Evaluation not found.');
        }

        return view('admin/evaluations/view', [
            'title'      => 'Evaluation Details',
            'evaluation' => $evaluation,
        ]);
    }
}