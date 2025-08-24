<?php

namespace App\Services;

use App\Models\HREmployee;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;

class EmployeeService
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }

    /**
     * Fetch an employee's description (e.g., full name) by staff number.
     *
     * @param string|null $staffNo
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function getEmployeeDescription(?string $staffNo): ?string
    {
        if (!$staffNo) {
            return null;
        }

        $employee = $this->bcService->findEmployeeByStaffNo($staffNo);
        if (!$employee) {
            Log::warning('Employee not found for description', ['staffNo' => $staffNo]);
            return null;
        }

        // Assuming Business Central returns FirstName and LastName
        return trim("{$employee->FirstName} {$employee->LastName}");
    }
    /**
     * Fetch active employees (excluding the current user) for use as relievers.
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getActiveRelievers(): array
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        $result = $this->bcService->callPage(HREmployee::wsName(), [
            '$select' => 'No,FirstName,MiddleName,LastName',
            '$filter' => "No ne '{$empNo}' and Status eq 'Active'",
        ]);

        if (empty($result->value)) {
            Log::warning('No active relievers found in Business Central', ['empNo' => $empNo]);
            return [];
        }

        return array_map(function ($employee) {
            return (object) [
                'No' => $employee->No,
                'FirstName' => $employee->FirstName,
                'MiddleName' => $employee->MiddleName,
                'LastName' => $employee->LastName,
            ];
        }, $result->value);
    }
}