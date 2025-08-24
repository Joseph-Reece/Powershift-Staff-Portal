<?php

namespace App\Http\Requests;

use App\Services\LeaveService;
use Illuminate\Foundation\Http\FormRequest;

class ShowLeaveRequisitionRequest extends FormRequest
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'no' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    try {
                        $this->leaveService->getLeaveRequisition($value);
                    } catch (\Exception $e) {
                        $fail("The leave requisition '{$value}' is invalid or not accessible.");
                    }
                },
            ],
        ];
    }
}