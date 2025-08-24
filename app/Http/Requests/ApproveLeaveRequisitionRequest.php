<?php

namespace App\Http\Requests;

use App\Services\LeaveService;
use Illuminate\Foundation\Http\FormRequest;

class ApproveLeaveRequisitionRequest extends FormRequest
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
            'requisitionNo' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    try {
                        $this->leaveService->getEditableLeaveRequisition($value);
                    } catch (\Exception $e) {
                        $fail("The leave requisition '{$value}' is not eligible for approval or does not exist.");
                    }
                },
            ],
        ];
    }
}