<?php

namespace App\Http\Requests;

use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
            'leaveType' => [
                'required',
                'string',                
            ],
            'appliedDays' => ['nullable', 'integer', 'min:1'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'reason' => ['required', 'string', 'max:255'],
            'requestLeaveAllowance' => ['required', 'boolean'],
            'reliever' => ['nullable', 'string', 'max:50'],
            'attachment' => ['nullable', 'file', 'max:2999'], // 3MB max
        ];
    }

    /**
     * Prepare validated data for the service.
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        $data = $this->validated();
        $dates = $this->leaveService->getLeaveDetails($data['leaveType'], $data['startDate'] ?? null, $data['endDate'] ?? null);

        if (!$dates) {
            throw new \Illuminate\Validation\ValidationException(
                \Illuminate\Support\Facades\Validator::make([], [], ['leaveType' => 'Unable to calculate leave dates. Please check your inputs.'])
            );
        }

        return array_merge($data, $dates);
    }
}