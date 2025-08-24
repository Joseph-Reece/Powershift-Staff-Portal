<?php

namespace App\Http\Requests;

use App\Services\LeaveService;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class GenerateLeaveStatementRequest extends FormRequest
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
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:' . now()->year],
        ];
    }

    /**
     * Get the formatted period (YYYY-MM-DD) from month and year.
     *
     * @return string|null
     */
    public function getPeriod(): ?string
    {
        if ($this->month && $this->year) {
            return Carbon::create($this->year, $this->month, 1)->format('Y-m-d');
        }
        return null;
    }
}