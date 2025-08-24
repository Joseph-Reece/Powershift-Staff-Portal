<?php

namespace App\Services;

use App\Http\Enums\LeaveType as EnumsLeaveType;
use App\Services\BusinessCentralService;
use App\Models\LeaveType;
use App\Models\HRLeaveRequisition;
use App\Models\HRLeavePeriod;
use App\Models\HRLeaveLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class LeaveService
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }

    public function getLeaveBalance(string $leaveType): array
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        // Fetch leave type description
        $leaveTypeData = $this->bcService->callPage(LeaveType::wsName(), [
            '$filter' => "Code eq '{$leaveType}'",
            '$top' => 1,
        ]);

        if (empty($leaveTypeData)) {
            throw new ModelNotFoundException("Leave type '{$leaveType}' not found.");
        }
        // dd();
        $leaveTypeData = $leaveTypeData->value[0];


        $leaveTypeDays = $leaveTypeData->Days;

        $currentPeriod = $this->bcService->callPage(HRLeavePeriod::wsName(), [
            '$filter' => 'Current eq true',
            '$top' => 1,
        ]);

        if (!$currentPeriod) {
            throw new ModelNotFoundException('No active leave calendar found.');
        }
        $currentPeriod = $currentPeriod->value[0];

        // Count pending leave requests
        // $pendingApproval = $this->odataClient()->from(HRLeaveRequisition::wsName())
        //     ->where('Status', 'Pending Approval')
        //     ->where('EmployeeNo', $empNo)
        //     ->where('Leave_Type', $leaveType)
        //     ->where('EndDate', '>', now()->toDateString())
        //     ->count();
        $today = now()->format('Y-m-d');
        $pendingApproval = $this->bcService->callPage(HRLeaveRequisition::wsName(), [
            '$filter' => "Status eq 'Pending Approval' and EmployeeNo eq '{$empNo}' and LeaveType eq '{$leaveType}' and EndDate gt '{$today}'",
            '$count' => 'true',
            '$top' => 0,
        ])['@odata.count'] ?? 0;

        // Fetch leave ledger entries
        // $leaveEntries = $this->odataClient()->from(HRLeaveLedger::wsName())
        //     ->where('EmployeeNo', $empNo)
        //     ->where('LeaveType', $leaveType)
        //     ->where('CalendarCode', $currentPeriod->Code)
        //     ->get();
        $leaveEntries = $this->bcService->callPage(HRLeaveLedger::wsName(), [
            // '$filter' => "EmployeeNo eq '{$empNo}' and LeaveType eq '{$leaveType}' and CalendarCode eq '{$currentPeriod->Code}'",
            '$filter' => "EmployeeNo eq '{$empNo}' and LeaveType eq '{$leaveType}'",
        ]);

        // Calculate leave balance
        $additions = 0;
        $deductions = 0;
        // dd($leaveEntries);

        if (!empty($leaveEntries)) {
            foreach ($leaveEntries->value as $entry) {
                // dd($entry);
                $days = $entry->NoofDays;
                if ($days < 0) {
                    $deductions += abs($days);
                } else {
                    $additions += $days;
                }
            }
        }

        // Determine balance based on leave type
        $isAnnualLeave = $leaveTypeData->Code === Leavetype::ANNUAL;
        $balance = $isAnnualLeave
            ? $additions - $deductions
            : $leaveTypeDays - ($deductions - $additions);

        return [
            'balance' => max(0, (int)$balance),
            'isHourly' => false,
            'pendingCount' => $pendingApproval,
        ];
    }

    /**
     * Fetch leave details for a given leave type and date range.
     *
     * @param string $leaveType
     * @param string $startDate
     * @param string $endDate
     * @return array{isWeekend: bool, endDate: string, appliedDays: int, returnDate: string}
     * @throws InvalidArgumentException
     */
    public function getLeaveDetails(string $leaveType, string $startDate, string $endDate): array
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        // Validate leave type
        $leaveTypeData = $this->bcService->callPage(LeaveType::wsName(), [
            '$filter' => "Code eq '{$leaveType}'",
            '$top' => 1,
        ]);

        $start_date = str_replace('_', "/", $startDate);
        $end_date = str_replace('_', "/", $endDate);


        if (empty($leaveTypeData->value)) {
            throw new ModelNotFoundException("Leave type '{$leaveType}' not found.");
        }

        // Parse and validate dates
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Invalid date format provided.');
        }

        // Call Business Central codeunit
        $result = $this->bcService->callCodeunitAction(
            'cuStaffPortal',
            'FnGetLeaveDetails',
            [
                'empNo' => $empNo,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d'),
                'leaveType' => $leaveType,
            ]
        );

        if (!$result || !isset($result->value) || $result->value === '') {
            Log::error('Failed to fetch leave details from Business Central', [
                'empNo' => $empNo,
                'leaveType' => $leaveType,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'result' => $result,
            ]);
            throw new InvalidArgumentException('Unable to retrieve leave details.');
        }

        // Parse response (assumes ##-delimited string: returnDate##appliedDays)
        $vars = explode('##', $result->value);
        if (count($vars) !== 2) {
            Log::error('Invalid leave details response format', [
                'empNo' => $empNo,
                'response' => $result->value,
            ]);
            throw new InvalidArgumentException('Invalid response format from Business Central.');
        }

        try {
            $returnDate = Carbon::parse($vars[0])->format('d-M-Y');
            $appliedDays = (int) $vars[1];
        } catch (\Exception $e) {
            Log::error('Failed to parse leave details response', [
                'empNo' => $empNo,
                'response' => $result->value,
                'error' => $e->getMessage(),
            ]);
            throw new InvalidArgumentException('Failed to process leave details.');
        }

        return [
            'isWeekend' => $start->isSunday(),
            'endDate' => $end->format('d-M-Y'),
            'appliedDays' => $appliedDays,
            'returnDate' => $returnDate,
        ];
    }
    /**
     * Fetch a leave requisition by number for the authenticated employee.
     *
     * @param string $no
     * @return object|null
     * @throws InvalidArgumentException
     */
    public function getLeaveRequisition(string $no): ?object
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        $result = $this->bcService->callPage(HRLeaveRequisition::wsName(), [
            '$filter' => "ApplicationCode eq '{$no}' and EmployeeNo eq '{$empNo}'",
            '$top' => 1,
        ]);

        if (empty($result->value)) {
            throw new ModelNotFoundException("Leave requisition '{$no}' not found for employee '{$empNo}'.");
        }

        return (object) $result->value[0];
    }
    /**
     * Fetch an editable leave requisition by number for the authenticated employee.
     *
     * @param string $no
     * @return object|null
     * @throws InvalidArgumentException
     */
    public function getEditableLeaveRequisition(string $no): ?object
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        $result = $this->bcService->callPage(HRLeaveRequisition::wsName(), [
            '$filter' => "ApplicationCode eq '{$no}' and EmployeeNo eq '{$empNo}' and Status eq 'Open'",
            '$top' => 1,
        ]);

        if (empty($result->value)) {
            throw new ModelNotFoundException("Leave requisition '{$no}' is not editable or does not exist for employee '{$empNo}'.");
        }

        return (object) $result->value[0];
    }
    /**
     * Update a leave requisition in Business Central.
     *
     * @param string $requisitionNo
     * @param string $leaveType
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string $reason
     * @param string|null $reliever
     * @param bool $requestLeaveAllowance
     * @return bool
     * @throws InvalidArgumentException
     */
    public function updateLeaveRequisition(
        string $requisitionNo,
        string $leaveType,
        ?string $startDate,
        ?string $endDate,
        string $reason,
        ?string $reliever,
        bool $requestLeaveAllowance
    ): bool {
        $empNo = Session::get('authUser.employeeNo');
        $userId = Session::get('authUser.userID');
        if (!$empNo || !$userId) {
            throw new InvalidArgumentException('Employee number or user ID not found in session.');
        }

        // Validate leave type
        $leaveTypeData = $this->bcService->callPage(LeaveType::wsName(), [
            '$filter' => "Code eq '{$leaveType}'",
            '$top' => 1,
        ]);
        if (empty($leaveTypeData->value)) {
            throw new InvalidArgumentException("Leave type '{$leaveType}' not found.");
        }

        // Validate requisition
        $requisition = $this->getEditableLeaveRequisition($requisitionNo);
        if (!$requisition) {
            throw new ModelNotFoundException("Leave requisition '{$requisitionNo}' is not editable or does not exist.");
        }

        // Parse and validate dates
        $start = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : null;
        $end = $endDate ? Carbon::parse($endDate)->format('Y-m-d') : null;

        // Call Business Central codeunit
        $result = $this->bcService->callCodeunitAction(
            'cuStaffPortal',
            'LeaveApplication',
            [
                'action' => 'edit',
                'leaveNo' => $requisitionNo,
                'employeeNo' => $empNo,
                'daysApplied' => 0, // Assuming this is calculated server-side
                'startDate' => $start,
                'endDate' => $end,
                'reason' => $reason,
                'reliever' => $reliever ?? '',
                'myUserID' => $userId,
                'leaveType' => $leaveType,
                'isRequestLeaveAllowance' => $requestLeaveAllowance,
            ]
        );

        if (!$result || !isset($result->value) || $result->value !== true) {
            Log::error('Failed to update leave requisition in Business Central', [
                'requisitionNo' => $requisitionNo,
                'employeeNo' => $empNo,
                'leaveType' => $leaveType,
                'result' => $result,
            ]);
            throw new InvalidArgumentException('Failed to update leave requisition.');
        }

        return true;
    }
    /**
     * Send a leave requisition for approval in Business Central.
     *
     * @param string $requisitionNo
     * @return bool
     * @throws InvalidArgumentException
     */
    public function requestLeaveApproval(string $requisitionNo): bool
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        // Validate requisition
        $requisition = $this->getEditableLeaveRequisition($requisitionNo);
        if (!$requisition) {
            throw new ModelNotFoundException("Leave requisition '{$requisitionNo}' is not eligible for approval or does not exist.");
        }

        // Call Business Central codeunit
        $result = $this->bcService->callCodeunitAction(
            'cuStaffPortal',
            'RequestLeaveApproval',
            [
                'requisitionNo' => $requisitionNo,
                'employeeNo' => $empNo,
                'tableID' => HRLeaveRequisition::tableDesc()['tableID'], // Fallback to 50000 if not set
            ]
        );

        if (!$result || !isset($result->value) || $result->value !== true) {
            Log::error('Failed to send leave requisition for approval in Business Central', [
                'requisitionNo' => $requisitionNo,
                'employeeNo' => $empNo,
                'result' => $result,
            ]);
            throw new InvalidArgumentException('Failed to send leave requisition for approval.');
        }

        return true;
    }
    /**
     * Fetch leave types from Business Central, optionally filtered by gender.
     *
     * @param string|null $gender
     * @return array
     * @throws InvalidArgumentException
     */
    public function getLeaveTypes(?string $gender = null): array
    {
        $filters = [];
        if ($gender && in_array($gender, ['Male', 'Female'], true)) {
            $notGender = $gender === 'Male' ? 'Female' : 'Male';
            $filters['$filter'] = "Gender ne '{$notGender}'";
        }

        $result = $this->bcService->callPage(LeaveType::wsName(), $filters);

        if (empty($result->value)) {
            Log::warning('No leave types found in Business Central', ['gender' => $gender]);
            return [];
        }

        return array_map(function ($leaveType) {
            return (object) [
                'Code' => $leaveType->Code,
                'Description' => $leaveType->Description,
                'Gender' => $leaveType->Gender ?? 'All',
                // Add other relevant fields
            ];
        }, $result->value);
    }

    /**
     * Fetch leave statement data (leave types with balances) for the authenticated user.
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getLeaveStatement(): array
    {
        $empNo = Session::get('authUser.employeeNo');
        $gender = Session::get('authUser.gender');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        $leaveTypes = $this->getLeaveTypes($gender);
        $statement = [];

        foreach ($leaveTypes as $leaveType) {
            try {
                $balance = $this->getLeaveBalance($leaveType->Code);
                $statement[] = (object) [
                    'Code' => $leaveType->Code,
                    'Description' => $leaveType->Description,
                    'Gender' => $leaveType->Gender,
                    'Balance' => $balance['balance'],
                    'IsHourly' => $balance['isHourly'],
                    'PendingCount' => $balance['pendingCount'],
                ];
            } catch (\Exception $e) {
                Log::warning('Failed to fetch balance for leave type', [
                    'leaveType' => $leaveType->Code,
                    'error' => $e->getMessage(),
                ]);
                // Skip leave types with errors to avoid blocking the statement
                continue;
            }
        }

        return $statement;
    }
    /**
     * Generate a leave statement PDF for a specific leave type.
     *
     * @param string $leaveType
     * @param string|null $period
     * @return string
     * @throws InvalidArgumentException
     */
    public function generateLeaveStatement(string $leaveType, ?string $period = null): string
    {
        $empNo = Session::get('authUser.employeeNo');
        if (!$empNo) {
            throw new InvalidArgumentException('Employee number not found in session.');
        }

        // Validate leave type
        $leaveTypeData = $this->bcService->callPage(LeaveType::wsName(), [
            '$filter' => "Code eq '{$leaveType}'",
            '$top' => 1,
        ]);
        if (empty($leaveTypeData->value)) {
            throw new InvalidArgumentException("Leave type '{$leaveType}' not found.");
        }

        // Format period if provided
        $formattedPeriod = $period ? Carbon::parse($period)->format('Y-m-d') : null;

        // Generate filename
        $filename = str_replace('/', '_', $empNo) . "_leave_{$leaveType}.pdf";

        // Call Business Central codeunit
        $params = [
            'employeeNo' => $empNo,
            'leaveType' => $leaveType,
            'filenameFromApp' => $filename,
        ];
        if ($formattedPeriod) {
            $params['period'] = $formattedPeriod;
        }

        $result = $this->bcService->callCodeunitAction('cuStaffPortal', 'GenerateLeaveStatement', $params);
        dd($result);

        if (!$result || !isset($result->value) || !is_string($result->value)) {
            Log::error('Failed to generate leave statement PDF', [
                'employeeNo' => $empNo,
                'leaveType' => $leaveType,
                'period' => $period,
                'result' => $result,
            ]);
            throw new InvalidArgumentException('Failed to generate leave statement PDF.');
        }

        return base64_decode($result->value, true);
    }
}
