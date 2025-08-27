<?php

namespace App\Http\Requests;

use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GeneratePayslipRequest extends FormRequest
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12'               
            ],
        ];
    }

    /**
     * Get the formatted period (YYYY-MM-DD) from year and month.
     *
     * @return string
     */
    public function getPeriod(): string
    {
        return Carbon::create($this->year, $this->month, 1)->format('Y-m-d');
    }
}