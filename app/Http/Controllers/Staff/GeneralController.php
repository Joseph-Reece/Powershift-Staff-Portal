<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Http\Requests\GeneratePayslipRequest;
use App\Models\PayrollPeriod;
use App\Models\PrPeriodTransaction;
use Carbon\Carbon;
use App\Models\HREmployee;
use App\Models\Item;
use App\Models\ItemLedgerEntry;
use App\Models\FixedAsset;
use App\Models\GLAccount;
use App\Models\ApprovalEntry;
use App\Models\HRLeaveRequisition;
use App\Models\ImprestHeader;
use App\Models\ImprestSurrenderHeader;
use App\Models\PurchaseRequisitionHeader;
use App\Models\StoreRequisitionHeader;
use App\Models\TransportRequisition;
use App\Models\ClaimHeader;
use App\Models\PaymentVoucherHeader;
use App\Models\PaymentVoucherLine;
use App\Services\PayrollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class GeneralController extends Controller
{
    use WebServicesTrait;
    protected $payrollService;
    protected $dashboardService;

    public function __construct(
        PayrollService $payrollService,
        DashboardService $dashboardService
    ) {
        $this->middleware('isAuth');
        $this->middleware('staff');
        $this->payrollService = $payrollService;
        $this->dashboardService = $dashboardService;
    }
    public function dashboardStatistics()
    {
        $dateToday = Carbon::now();
        $startDate = $dateToday->startOfYear()->format('Y-m-d');
        $endDate = $dateToday->endOfYear()->format('Y-m-d');
        //
        $totalPendingApproval = $this->odataClient()->from(ApprovalEntry::wsName())->where('Status', 'Open')->where('Approver_ID', session('authUser')['userID'])->where('#filter', "(Due_Date gt $startDate and Due_Date lt $endDate)" . "filter#")->count();
        $totalApproved = $this->odataClient()->from(ApprovalEntry::wsName())->where('Status', 'Approved')->where('Approver_ID', session('authUser')['userID'])->where('#filter', "(Due_Date gt $startDate and Due_Date lt $endDate)" . "filter#")->count();
        $totalRejected = $this->odataClient()->from(ApprovalEntry::wsName())->where('Status', 'Rejected')->where('Approver_ID', session('authUser')['userID'])->where('#filter', "(Due_Date gt $startDate and Due_Date lt $endDate)" . "filter#")->count();
        $totalLeaveReqs = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('User_ID', session('authUser')['userID'])->where('#filter', "(Start_Date gt $startDate and Start_Date lt $endDate)" . "filter#")->count();
        $totalImprestReqs = $this->odataClient()->from(ImprestHeader::wsName())->where('Employee_No', session('authUser')['employeeNo'])->where('#filter', "(Date gt $startDate and Date lt $endDate)" . "filter#")->count();
        $totalImprestSurrenderReqs = $this->odataClient()->from(ImprestSurrenderHeader::wsName())->where('User_ID', session('authUser')['userID'])->where('#filter', "(Surrender_Date gt $startDate and Surrender_Date lt $endDate)" . "filter#")->count();
        $totalPurchaseReqs = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())->where('Assigned_User_ID', session('authUser')['userID'])->where('#filter', "(Document_Date gt $startDate and Document_Date lt $endDate)" . "filter#")->count();
        $totalStoreReqs = $this->odataClient()->from(StoreRequisitionHeader::wsName())->where('User_ID', session('authUser')['userID'])->where('#filter', "(Request_date gt $startDate and Request_date lt $endDate)" . "filter#")->count();
        //$totalTransportReqs = $this->odataClient()->from(TransportRequisition::wsName())->where('Requested_By',session('authUser')['userID'])->where('#filter',"(Date_of_Request gt $startDate and Date_of_Request lt $endDate)"."filter#")->count();
        $totalTransportReqs = 0;
        $totalClaims = $this->odataClient()->from(ClaimHeader::wsName())->where('Employee_No', session('authUser')['employeeNo'])->where('#filter', "(Date gt $startDate and Date lt $endDate)" . "filter#")->count();
        $data = [
            //
            'totalPendingApproval' => $totalPendingApproval,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'totalLeaveReqs' => $totalLeaveReqs,
            'totalImprestReqs' => $totalImprestReqs,
            'totalImprestSurrenderReqs' => $totalImprestSurrenderReqs,
            'totalPurchaseReqs' => $totalPurchaseReqs,
            'totalStoreReqs' => $totalStoreReqs,
            'totalTransportReqs' => $totalTransportReqs,
            'totalClaims' => $totalClaims,
        ];
        return $data;
    }
   /**
     * Fetch dashboard statistics as JSON.
     */
    public function GetDashboardStatistics(): JsonResponse
    {
        try {
            $userID = Session::get('authUser.userID');
            if (!$userID) {
                Log::warning('User ID not found in session for dashboard statistics');
                return response()->json(['error' => 'Unauthorized. Please log in again.'], 401);
            }

            $statistics = $this->dashboardService->getDashboardStatistics();

            return response()->json(['statistics' => $statistics]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard statistics', [
                'userID' => Session::get('authUser.userID'),
                'employeeNo' => Session::get('authUser.employeeNo'),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Unable to fetch dashboard data. Please try again or contact support.'], 500);
        }
    }
    public function payslip()
    {
        $periods = $this->odataClient()->from(PayrollPeriod::wsName())
            ->where('Closed', 'true')
            ->get();
        $periods = $periods->unique('Period_Year');
        $data = [
            'periods' => $periods
        ];
        return view('staff.payslip')->with($data);
    }
    /**
     * Display the payslip view with closed payroll periods.
     */
    public function getPayrollPeriods(): View|RedirectResponse
    {
        try {
            $empNo = Session::get('authUser.employeeNo');
            if (!$empNo) {
                Log::warning('Employee number not found in session for payslip view');
                return redirect()->route('staff.dashboard')->with('error', 'Unable to load payslip data. Please log in again.');
            }

            $periods = $this->payrollService->getPayrollPeriods();

            if (empty($periods)) {
                Log::warning('No payroll periods available for user', ['employeeNo' => $empNo]);
                return redirect()->route('dashboard')->with('warning', 'No payroll periods available.');
            }

            return view('staff.payslip', ['periods' => $periods]);
        } catch (\Exception $e) {
            Log::error('Failed to load payslip view', [
                'employeeNo' => Session::get('authUser.employeeNo'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')->with('error', 'Unable to load payslip data. Please try again or contact support.');
        }
    }
    public function generatePaySlip(REQUEST $request)
    {
        $request->validate([
            'year' => 'required',
            'month' => 'required',
        ]);
        // $period = Carbon::parse("$request->month/01/$request->year")->format('Y-m-d');
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if (!isset($params)) {
                $params = new \stdClass();
            }
            $empNo = session('authUser')['employeeNo'];
            $params->employeeNo = $empNo;
            $params->year = $request->year;
            $params->month = $request->month;
            $fname = str_replace('/', '_', $empNo) . "_ps.pdf";
            $params->filenameFromApp = $fname;
            $result = $service->GeneratePayslip($params);
            if ($result->return_value != "") {
                $data = base64_decode($result->return_value);
                header('Content-Type: application/pdf');
                echo $data;
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    /**
     * Generate a payslip PDF for the specified year and month.
     */
    public function generateMonthPaySlip(GeneratePayslipRequest $request): Response|RedirectResponse
    {
        try {
            $empNo = Session::get('authUser.employeeNo');
            if (!$empNo) {
                Log::warning('Employee number not found in session for payslip generation');
                return redirect()->route('payslip')->with('error', 'Unauthorized. Please log in again.');
            }

            $pdfData = $this->payrollService->generatePayslip(
                $request->year,
                $request->month
            );

            $filename = str_replace('/', '_', $empNo) . "_ps_{$request->year}_{$request->month}.pdf";

            return response($pdfData)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            Log::error('Failed to generate payslip PDF', [
                'employeeNo' => Session::get('authUser.employeeNo'),
                'year' => $request->year,
                'month' => $request->month,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('payslip')->withErrors(['month' => 'Unable to generate payslip. Please try again or contact support.']);
        }
    }
    public function pNine()
    {
        // $periodYears = $this->prPeriodYears();
        // $data = [
        //     'years' => $periodYears
        // ];
        return view('staff.p-nine');
    }
    public function generatePNine(REQUEST $request)
    {
        $request->validate([
            'year' => 'required|numeric|digits:4',
        ]);
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if (!isset($params)) {
                $params = new \stdClass();
            }
            $empNo = session('authUser')['employeeNo'];
            $params->employeeNo = $empNo;
            $params->year = $request->year;
            //$params->endDate = Carbon::parse(strtotime($request->endDate))->format('Y-m-d');
            $fname = str_replace('/', '_', $empNo) . "_p9.pdf";
            $params->filenameFromApp = $fname;
            $result = $service->GeneratePNine($params);
            if ($result->return_value != "") {
                $data = base64_decode($result->return_value);
                header('Content-Type: application/pdf');
                echo $data;
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    public function prPeriodYears()
    {
        $periods = $this->odataClient()->from(PayrollPeriod::wsName())->select('Period_Year')->where('Closed', 'true')->get();
        $periodYears = $periods->unique('Period_Year');
        return $periodYears;
    }
    // public function pNinePeriodYears(){
    //     $periods = $this->odataClient()->from(PayrollPeriod::wsName())->select('Period_Year')->where('Closed','true')->get();
    //     $periodYears = $periods->unique('Period_Year');
    //     return $periodYears;
    // }
    public function prPeriodTransactionYears()
    {
        $periods = $this->odataClient()->from(PrPeriodTransaction::wsName())->select('Period_Year')->get();
        $periodYears = $periods->unique('Period_Year');
        return $periodYears;
    }
    public function prPeriodYearMonths($year)
    {
        $periods = $this->odataClient()->from(PayrollPeriod::wsName())
            ->select('Period_Month')
            ->where('Closed', 'true')
            ->where('Period_Year', (int) $year)
            ->get();
        $periodMonths = $periods->unique('Period_Month');
        return $periodMonths;
    }
    /**
     * Fetch unique closed payroll period months for a given year.
     */
    public function PeriodYearMonths($year): JsonResponse
    {
        try {
            $empNo = Session::get('authUser.employeeNo');
            if (!$empNo) {
                Log::warning('Employee number not found in session for payroll period months');
                return response()->json(['error' => 'Unauthorized. Please log in again.'], 401);
            }

            $months = $this->payrollService->getPayrollPeriodMonths($year);

            if (empty($months)) {
                Log::warning('No payroll period months available for user', [
                    'employeeNo' => $empNo,
                    'year' => $year,
                ]);
                return response()->json(['error' => 'No payroll periods available for the selected year.'], 404);
            }

            return response()->json(['months' => $months]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payroll period months', [
                'employeeNo' => Session::get('authUser.employeeNo'),
                'year' => $year,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Unable to fetch payroll periods. Please try again or contact support.'], 500);
        }
    }
    public function employeeDesc($employeeNo)
    {
        $employee = $this->odataClient()->from(HREmployee::wsName())->select('FirstName', 'MiddleName', 'LastName')->where('No', $employeeNo)->first();
        return $employee;
    }
    public function employeeDesc2($userID)
    {
        $employee = $this->odataClient()->from(HREmployee::wsName())->select('FirstName', 'MiddleName', 'LastName')->where('UserID', $userID)->first();
        return $employee;
    }
    public function getItems()
    {
        $data = $this->odataClient()->from(Item::wsName())->select('No', 'Description')->get();
        //$data2 = $data->unique('Description');
        return $data;
    }
    public function getStoreItems($store)
    {
        $data = $this->odataClient()->from(Item::wsName())->select('No', 'Description')->where('Ledger_Location', $store)->get();
        return $data;
    }
    public function getItemBalance($item, $store)
    {
        $data = $this->odataClient()->from(ItemLedgerEntry::wsName())->where('Item_No', $item)->where('Location_Code', $store)->get();
        $balance = 0;
        foreach ($data as $entry) {
            $balance = $balance + $entry['Quantity'];
        }
        return round($balance, 0);
    }
    public function itemDesc($no)
    {
        $data = $this->odataClient()->from(Item::wsName())->select('No', 'Description')->where('No', $no)->first();
        return $data;
    }
    public function getAssets()
    {
        $data = $this->odataClient()->from(FixedAsset::wsName())->select('No', 'Description')->get();
        return $data;
    }
    public function getStoreAssets($store)
    {
        $data = $this->odataClient()->from(FixedAsset::wsName())->select('No', 'Description')->where('Location_Code', $store)->get();
        return $data;
    }
    public function getServices()
    {
        $data = $this->odataClient()->from(GLAccount::wsName())->select('No', 'Name')->where('Direct_Posting', 'true')->get();
        return $data;
    }
}
