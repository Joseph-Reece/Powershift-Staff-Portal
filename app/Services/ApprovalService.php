<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use App\Models\ApprovalEntry;
use App\Models\ApprovalCommentLine;

class ApprovalService
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }

    /**
     * Fetch approvers for a leave requisition.
     *
     * @param string $requisitionNo
     * @return array
     * @throws InvalidArgumentException
     */
    public function getApprovers(string $requisitionNo): array
    {
        // Example: Assumes a Business Central page or codeunit for approvers
        $result = $this->bcService->callPage(ApprovalEntry::wsName(), [
            '$filter' => "DocumentNo eq '{$requisitionNo}'",
        ]);

        if (empty($result->value)) {
            Log::warning('No approvers found for requisition', ['requisitionNo' => $requisitionNo]);
            return [];
        }

        // Transform to array of approver details (adjust based on actual data)
        return array_map(function ($entry) {
            return [
                'approverId' => $entry->ApproverID ?? null,
                'name' => $entry->ApproverName ?? 'Unknown',
                'status' => $entry->Status ?? 'Pending',
            ];
        }, $result->value);
    }
}