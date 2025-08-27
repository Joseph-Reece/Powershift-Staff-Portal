<?php

namespace App\Services;

use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class PayrollService
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }

    /**
     * Fetch unique closed payroll periods by year from Business Central.
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPayrollPeriods(): array
    {
        $result = $this->bcService->callPage(PayrollPeriod::wsName(), [
            // '$filter' => 'Closed eq true',
            '$select' => 'PeriodYear',
            '$apply' => 'groupby((PeriodYear))',
        ]);

        // dd($result);

        if (empty($result->value)) {
            Log::warning('No closed payroll periods found in Business Central');
            return [];
        }

        return array_map(function ($period) {
            return (object) [
                'Period_Year' => $period->PeriodYear,
            ];
        }, $result->value);
    }
    /**
     * Fetch unique closed payroll period months for a given year from Business Central.
     *
     * @param int $year
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPayrollPeriodMonths(int $year): array
    {
        if ($year < 2000 || $year > now()->year) {
            throw new InvalidArgumentException('Invalid year provided.');
        }

        $result = $this->bcService->callPage(PayrollPeriod::wsName(), [
            // '$filter' => "Closed eq true and Period_Year eq {$year}",
            '$filter' => "PeriodYear eq {$year}",
            '$select' => 'PeriodMonth',
            '$apply' => 'groupby((PeriodMonth))',
        ]);

        if (empty($result->value)) {
            Log::warning('No closed payroll period months found for year', ['year' => $year]);
            return [];
        }

        return array_map(function ($period) {
            return (object) [
                'PeriodMonth' => $period->PeriodMonth,
                'MonthName' => Carbon::create()->month($period->PeriodMonth)->format('F'),
            ];
        }, $result->value);
    }


    // Existing methods: getPayrollPeriods, getPayrollPeriodMonths, generatePayslip

    /**
     * Validate if a month is available for a given year.
     *
     * @param int $year
     * @param int $month
     * @return bool
     */
    public function isValidPayrollMonth(int $year, int $month): bool
    {
        $months = $this->getPayrollPeriodMonths($year);
        return collect($months)->pluck('PeriodMonth')->contains($month);
    }

    /**
     * Generate a payslip PDF for a specific year and month.
     *
     * @param string $year
     * @param string $month
     * @return string
     * @throws InvalidArgumentException
     */
    public function generatePayslip(string $year, string $month): string
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        // Validate year and month
        if (!is_numeric($year) || $year < 2000 || $year > now()->year) {
            throw new InvalidArgumentException('Invalid year provided.');
        }
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            throw new InvalidArgumentException('Invalid month provided.');
        }

        // Validate against available periods
        if (!$this->isValidPayrollMonth((int)$year, (int)$month)) {
            throw new InvalidArgumentException('Selected month is not available for the specified year.');
        }

        // Format period
        $period = Carbon::create($year, $month, 1)->format('Y-m-d');

        // Generate filename
        $filename = str_replace('/', '_', $empNo) . "_ps_{$year}_{$month}.pdf";

        // Call Business Central codeunit
        $result = $this->bcService->callCodeunitAction('cuStaffPortal', 'GeneratePayslip', [
            'employeeNo' => $empNo,
            'year' => (int)$year,
            'month' => (int)$month,
            // 'period' => $period,
            'filenameFromApp' => $filename,
        ]);

        if (!$result || !isset($result->value) || !is_string($result->value)) {
            Log::error('Failed to generate payslip PDF', [
                'employeeNo' => $empNo,
                'year' => $year,
                'month' => $month,
                'result' => $result,
            ]);
            throw new InvalidArgumentException('Failed to generate payslip PDF.');
        }

        return base64_decode($result->value, true);
    }
}
