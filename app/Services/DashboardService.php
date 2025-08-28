<?php

namespace App\Services;

use App\Models\ApprovalEntry;
use App\Models\HRLeaveRequisition;
use App\Models\ImprestHeader;
use App\Models\ImprestSurrenderHeader;
use App\Models\PurchaseRequisitionHeader;
use App\Models\StoreRequisitionHeader;
use App\Models\TransportRequisition;
use App\Models\ClaimHeader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class DashboardService
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }

    /**
     * Fetch dashboard statistics for the current year.
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getDashboardStatistics(): array
    {
        $userID = Session::get('authUser.userID');
        $employeeNo = Session::get('authUser.employeeNo');
        if (!$userID || !$employeeNo) {
            throw new InvalidArgumentException('User ID or employee number not found in session.');
        }

        $startDate = Carbon::now()->startOfYear()->format('Y-m-d\T00:00:00Z');
        $endDate = Carbon::now()->endOfYear()->format('Y-m-d\T23:59:59Z');

        $statistics = [
            'totalPendingApproval' => 0,
            'totalApproved' => 0,
            'totalRejected' => 0,
            'totalLeaveReqs' => 0,
            'totalImprestReqs' => 0,
            'totalImprestSurrenderReqs' => 0,
            'totalPurchaseReqs' => 0,
            'totalStoreReqs' => 0,
            'totalTransportReqs' => 0,
            'totalClaims' => 0,
        ];

        try {
            // Approval Entries
            // $statistics['totalPendingApproval'] = $this->getCount(ApprovalEntry::wsName(), [
            //     '$filter' => "Status eq 'Open' and Approver_ID eq '{$userID}' and Due_Date gt {$startDate} and Due_Date lt {$endDate}",
            // ]);
            // $statistics['totalApproved'] = $this->getCount(ApprovalEntry::wsName(), [
            //     '$filter' => "Status eq 'Approved' and Approver_ID eq '{$userID}' and Due_Date gt {$startDate} and Due_Date lt {$endDate}",
            // ]);
            // $statistics['totalRejected'] = $this->getCount(ApprovalEntry::wsName(), [
            //     '$filter' => "Status eq 'Rejected' and Approver_ID eq '{$userID}' and Due_Date gt {$startDate} and Due_Date lt {$endDate}",
            // ]);

            // Leave Requisitions
            $statistics['totalLeaveReqs'] = $this->getCount(HRLeaveRequisition::wsName(), [
                '$filter' => "User_ID eq '{$userID}' and StartDate gt {$startDate} and StartDate lt {$endDate}",
            ]);
            // dd($statistics['totalLeaveReqs']);

            // Imprest Requests
            // $statistics['totalImprestReqs'] = $this->getCount(ImprestHeader::wsName(), [
            //     '$filter' => "Employee_No eq '{$employeeNo}' and Date gt {$startDate} and Date lt {$endDate}",
            // ]);

            // Imprest Surrender Requests
            // $statistics['totalImprestSurrenderReqs'] = $this->getCount(ImprestSurrenderHeader::wsName(), [
            //     '$filter' => "User_ID eq '{$userID}' and Surrender_Date gt {$startDate} and Surrender_Date lt {$endDate}",
            // ]);

            // Purchase Requisitions
            // $statistics['totalPurchaseReqs'] = $this->getCount(PurchaseRequisitionHeader::wsName(), [
            //     '$filter' => "Assigned_User_ID eq '{$userID}' and Document_Date gt {$startDate} and Document_Date lt {$endDate}",
            // ]);

            // Store Requisitions
            // $statistics['totalStoreReqs'] = $this->getCount(StoreRequisitionHeader::wsName(), [
            //     '$filter' => "User_ID eq '{$userID}' and Request_date gt {$startDate} and Request_date lt {$endDate}",
            // ]);

            // Transport Requisitions
            // $statistics['totalTransportReqs'] = $this->getCount(TransportRequisition::wsName(), [
            //     '$filter' => "Requested_By eq '{$userID}' and Date_of_Request gt {$startDate} and Date_of_Request lt {$endDate}",
            // ]);

            // Claims
            // $statistics['totalClaims'] = $this->getCount(ClaimHeader::wsName(), [
            //     '$filter' => "Employee_No eq '{$employeeNo}' and Date gt {$startDate} and Date lt {$endDate}",
            // ]);

            $statistics['totalClaims'] = 0;
            $statistics['totalTransportReqs'] = 0;
            $statistics['totalStoreReqs'] = 0;
            $statistics['totalPurchaseReqs'] = 0;
            $statistics['totalImprestSurrenderReqs'] = 0;
            $statistics['totalImprestReqs'] =0;
            $statistics['totalRejected'] =0;
            $statistics['totalApproved'] =0;
            $statistics['totalPendingApproval'] =0;

            return $statistics;
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard statistics', [
                'userID' => $userID,
                'employeeNo' => $employeeNo,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Rethrow for controller to handle
        }
    }

    /**
     * Helper method to get count from Business Central with OData.
     *
     * @param string $pageName
     * @param array $filters
     * @return int
     */
    private function getCount(string $pageName, array $filters): int
    {
        $result = $this->bcService->callPage($pageName, array_merge($filters, ['$count' => 'true']));
        return $result && isset($result->{'@odata.count'}) ? (int) $result->{'@odata.count'} : 0;
    }
}