<?php

namespace App\Http\Requests;

use App\Services\LeaveService;
use App\Services\EmployeeService;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateLeaveRequisitionRequest extends FormRequest
{
    protected $leaveService;
    protected $employeeService;

    public function __construct(LeaveService $leaveService, EmployeeService $employeeService)
    {
        $this->leaveService = $leaveService;
        $this->employeeService = $employeeService;
    }

    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'requisitionNo' => [
                'required',
                'string'
            ],
            'leaveType' => [
                'required',
                'string',

            ],
            'startDate' => [
                'nullable',
                'date'
            ],
            'endDate' => [
                'nullable',
                'date'
            ],
            'reason' => ['required', 'string', 'max:255'],
            'reliever' => [
                'nullable',
                'string',
            ],
            'requestLeaveAllowance' => ['required', 'boolean'],
        ];
    }
}