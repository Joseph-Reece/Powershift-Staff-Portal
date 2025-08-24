<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\Traits\AttachmentTrait;
use App\Http\Enums\LeaveType as EnumsLeaveType;
use App\Http\Requests\ApproveLeaveRequisitionRequest;
use App\Http\Requests\GenerateLeaveStatementRequest;
use App\Http\Requests\LeaveDetailsRequest;
use App\Http\Requests\ShowLeaveRequisitionRequest;
use App\Http\Requests\UpdateLeaveRequisitionRequest;
use App\Models\HRLeaveRequisition;
use App\Models\LeaveType;
use App\Models\HRLeavePeriod;
use App\Models\HRLeaveLedger;
use App\Models\HREmployee;
use App\Services\ApprovalService;
use App\Services\BusinessCentralService;
use App\Services\EmployeeService;
use App\Services\LeaveBalanceService;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class LeaveController extends Controller
{
    use WebServicesTrait;
    use AttachmentTrait;
    protected $leaveService;
    protected $employeeService;
    protected $approvalService;
    protected $bcService;

    public function __construct(
        LeaveService $leaveService,
        EmployeeService $employeeService,
        ApprovalService $approvalService,
        BusinessCentralService $bcService
    ) {
        $this->middleware('isAuth');
        $this->middleware('staff');
        $this->middleware('BCAuth');
        $this->leaveService = $leaveService;
        $this->employeeService = $employeeService;
        $this->approvalService = $approvalService;
        $this->bcService = $bcService;
    }

    // public function __construct(BusinessCentralService $bcService, LeaveService $leaveService)
    // {
    //     $this->bcService = $bcService;
    //     $this->leaveService = $leaveService;
    // }

    public function index()
    {
        $dateToday = Carbon::now();
        $startDate = $dateToday->startOfYear()->format('Y-m-d');
        $endDate = $dateToday->endOfYear()->format('Y-m-d');
        $employeeNo = session('authUser')['employeeNo'];

        $leaveRequests = $this->bcService->callPage(
            HRLeaveRequisition::wsName(),
            [
                '$filter' => "StartDate gt $startDate and StartDate lt $endDate and EmployeeNo eq '$employeeNo'"
            ]
        );
        // dd($leaveRequests->value);
        $data = [
            'requsitions' => $leaveRequests->value
        ];
        // dd($data);
        return view('staff.leave.index')->with($data);
    }
    public function create()
    {
        // $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Status', 'Pending Approval')->where('EmployeeNo', session('authUser')['employeeNo'])->first();
        $employeeNo = session('authUser')['employeeNo'];

        $leaveRequisition = $this->bcService->callPage(
            HRLeaveRequisition::wsName(),
            [
                '$filter' => "Status eq 'Pending Approval' and EmployeeNo eq '{$employeeNo}'",
                '$top' => 1
            ]
        );
        if (session('authUser')['gender'] == 'Male') {
            $notGender = 'Female';
        } else {
            $notGender = 'Male';
        }
        // dd($notGender);
        $leaveTypes = $this->bcService->callPage(
            LeaveType::wsName(),
            [
                '$filter' => "Gender ne '{$notGender}'"
            ]
        );
        // dd($leaveTypes);
        $relievers = $this->bcService->callPage(
            HREmployee::wsName(),
            [
                '$select' => 'No,FirstName,MiddleName,LastName',
                '$filter' => "No ne '{$employeeNo}' and Status eq 'Active'"
            ]
        );
        // dd($leaveTypes);
        //TODO: Filter Out Employees on leave
        $data = [
            'leaveTypes' => $leaveTypes->value,
            'relievers' => $relievers->value,
            'action' => 'create',
        ];

        // dd($data);

        return view('staff.leave.application')->with($data);
    }
    public function store(REQUEST $request)
    {
        $request->validate([
            'leaveType' => 'required',
            'appliedDays' => 'nullable',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'reason' => 'required',
            'requestLeaveAllowance' => 'required',
        ]);
        $dates = $this->getLeaveDates($request->leaveType, $request->appliedDays, $request->startDate);
        if ($dates == null) {
            return redirect()->back()->with('error', 'Oops! something went wrong. Please try again');
        }
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = 'create';
            $params->leaveNo = '';
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->daysApplied = 0;
            $params->startDate = Carbon::parse(strtotime($request->startDate))->toIso8601String();
            $params->endDate = Carbon::parse(strtotime($request->endDate))->toIso8601String();
            $params->reason = $request->reason;
            $params->reliever = $request->reliever;
            $params->myUserID = session('authUser')['userID'];
            $params->leaveType = $request->leaveType;
            $params->isRequestLeaveAllowance = $request->requestLeaveAllowance;
            // dd($request->all());
            $result = $service->LeaveApplication($params);
            // dd($result);
            if ($result->return_value != '') {
                if ($request->hasFile('attachment')) {
                    // dd('heere');
                    $this->validate(
                        $request,
                        [
                            'attachment' => 'required|max:2999',
                        ],
                        ['attachment.max' => 'File size cannot exceed 3MB']
                    );
                    try {
                        // $service = $this->MySoapClient(config('app.cuStaffPortal'));
                        $params = new \stdClass();
                        $params->accountNo = $result->return_value;
                        $params->action = 'insert';
                        $params->attachmentID = 0;
                        $params->description = 'Leave Attachment';
                        $file = $request->file('attachment');
                        $b64File = base64_encode(file_get_contents($file));
                        $params->b64File = $b64File;
                        $params->fileName = $params->description . '.' . $file->getClientOriginalExtension();
                        $params->fileName = str_replace(" ", "-", $params->fileName);
                        $params->fileName = str_replace("/", "_", $params->fileName);
                        $params->tableID = 52202673;

                        $result = $service->FnAttachment($params);
                        // dd($result);
                        // $returnValue = $result->return_value;
                    } catch (\SoapFault $e) {
                        $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
                        $errorMsg = $e->faultstring;
                        return redirect()->back()->with('error', $errorMsg);
                    }

                    $data = [
                        'pKey' => $result->return_value,
                        'tableDesc' => HRLeaveRequisition::tableDesc(),
                        'file' => $request->file('attachment'),
                        'description' => 'leave_attachment_' . $result->return_value
                    ];
                    // dd($data);
                    // $this->uploadAttachment($data);
                }
                return redirect('/staff/leave')->with('success', 'Leave application created successfully');
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            // dd($errorMsg);
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    public function show($no)
    {
        $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())
            ->where('No', $no)
            ->where('EmployeeNo', session('authUser')['employeeNo'])
            ->first();
        if ($requisition == null) {
            return redirect('/staff/leave')->with('error', 'Leave application not found.');
        }
        $relieverDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($requisition->Duties_Taken_Over_By);
        $requisition['relieverDesc'] = $relieverDesc;
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'approvers' => $approvers,
        ];
        return view('staff.leave.show')->with($data);
    }
    /**
     * Display a specific leave requisition.
     */
    public function showLeave(string $no): View|RedirectResponse
    {
        try {
            $requisition = $this->leaveService->getLeaveRequisition($no);
            $relieverDesc = $this->employeeService->getEmployeeDescription($requisition->Duties_Taken_Over_By ?? null);
            $approvers = $this->approvalService->getApprovers($no);

            $data = [
                'requisition' => (object) array_merge((array) $requisition, ['relieverDesc' => $relieverDesc]),
                'approvers' => $approvers,
            ];

            return view('staff.leave.show', $data);
        } catch (\Exception $e) {
            Log::error('Failed to fetch leave requisition details', [
                'no' => $no,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('leave.index')->with('error', 'Unable to retrieve leave requisition. Please try again or contact support.');
        }
    }
    public function edit($no)
    {
        $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())
            ->where('No', $no)
            ->where('EmployeeNo', session('authUser')['employeeNo'])
            ->where('Status', 'Open')
            ->first();
        if ($requisition == null) {
            return redirect('/staff/leave')->with('error', 'Leave application is no longer editable or does not exist.');
        }

        $leaveTypes = $this->odataClient()->from(LeaveType::wsName())
            ->get();
        $relievers = $this->odataClient()->from(HREmployee::wsName())
            ->select('No', 'FirstName', 'MiddleName', 'LastName')
            ->where('No', '!=', session('authUser')['employeeNo'])
            ->where('Status_1', '=', 'Active')
            ->get();

        $data = [
            'leaveTypes' => $leaveTypes,
            'relievers' => $relievers,
            'action' => 'edit',
            'requisition' => $requisition,
        ];
        return view('staff.leave.application')->with($data);
    }
    /**
     * Display the form to edit a leave requisition.
     */
    public function editThisLeave(string $no): View|RedirectResponse
    {
        try {
            $gender = Session::get('authUser.gender');
            $requisition = $this->leaveService->getEditableLeaveRequisition($no);
            $leaveTypes = $this->leaveService->getLeaveTypes($gender);
            $relievers = $this->employeeService->getActiveRelievers();

            $data = [
                'leaveTypes' => $leaveTypes,
                'relievers' => $relievers,
                'action' => 'edit',
                'requisition' => $requisition,
            ];

            return view('staff.leave.application', $data);
        } catch (\Exception $e) {
            Log::error('Failed to load leave requisition edit form', [
                'no' => $no,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('leave.index')->with('error', 'Unable to load leave requisition edit form. Please try again or contact support.');
        }
    }
    public function update(REQUEST $request)
    {
        $request->validate([
            'leaveType' => 'required',
            'appliedDays' => 'nullable',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'reason' => 'required',
            'requestLeaveAllowance' => 'required',
            'requisitionNo' => 'required'
        ]);
        // $dates = $this->getLeaveDates($request->leaveType,$request->appliedDays,$request->startDate);
        // if($dates == null){
        //     return redirect()->back()->with('error','Oops! something went wrong. Please try again');
        // }

        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = 'edit';
            $params->leaveNo = $request->requisitionNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->daysApplied = 0;
            $params->startDate = Carbon::parse(strtotime($request->startDate))->toIso8601String();
            $params->endDate = Carbon::parse(strtotime($request->endDate))->toIso8601String();
            $params->reason = $request->reason;
            $params->reliever = $request->reliever;
            $params->myUserID = session('authUser')['userID'];
            $params->leaveType = $request->leaveType;
            $params->isRequestLeaveAllowance = $request->requestLeaveAllowance;
            // dd($request);
            $result = $service->LeaveApplication($params);
            if ($result->return_value == true) {
                return redirect('/staff/leave')->with('success', 'Leave application updated successfully');
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    /**
     * Update a leave requisition.
     */
    public function updateThisLeave(UpdateLeaveRequisitionRequest $request): RedirectResponse
    {
        try {
            $this->leaveService->updateLeaveRequisition(
                $request->requisitionNo,
                $request->leaveType,
                $request->startDate,
                $request->endDate,
                $request->reason,
                $request->reliever,
                $request->boolean('requestLeaveAllowance')
            );

            return redirect()->route('leave.index')->with('success', 'Leave application updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update leave requisition', [
                'requisitionNo' => $request->requisitionNo,
                'leaveType' => $request->leaveType,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['requisitionNo' => 'Unable to update leave requisition. Please try again or contact support.']);
        }
    }

    //
    public function createLeaveAttachment($no)
    {
        $data = [
            'leaveNo' => $no
        ];
        return view('staff.leave.attachment-create')->with($data);
    }
    public function storeLeaveAttachment(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'file' => 'required|max:10000|mimes:pdf,doc,docx,jpeg,jpg,png',
        ]);

        // compress Picture before uploading.
        // Error: Failed to upload error for files above 2mbs
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->docNo = $request->leaveNo;
            $params->docNo2 = $request->leaveNo;
            $params->description = $request->description;
            $params->tableID = 52202673;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $b64File = base64_encode(file_get_contents($file));
                $params->file = $b64File;
                $params->fileName = $params->description . '.' . $file->getClientOriginalExtension();
                $params->fileName = str_replace(" ", "-", $params->fileName);
                $params->fileName = str_replace("/", "_", $params->fileName);
                // dd($params->filename);
            }
            $params->tableID = 52202543;
            $result = $service->UploadDocumentAttachment($params);
            $returnValue = $result->return_value;
            if ($returnValue) {
                $message = "Saved successfully";
                return redirect('/staff/leave/show/' . $request->leaveNo)->with('success', $message);
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    public function showLeaveAttachment(REQUEST $request)
    {
        $request->validate([
            'attachmentID' => 'required',
            'tableID' => 'required',
            'leaveNo' => 'required',
            'fileName' => 'required',
        ]);
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if (!isset($params)) {
                $params = new \stdClass();
            }
            $params->docNo = $request->leaveNo;
            $params->attachmentID = $request->attachmentID;
            $params->tableID = $request->tableID;
            $result = $service->GetDocumentAttachment($params);
            if ($result->return_value != "") {
                $data = base64_decode($result->return_value);
                header('Content-Type: application/pdf');
                header("Content-Disposition:attachment;filename=\"$request->fileName\"");
                echo $data;
            } else {
                return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
            }
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    public function deleteLeaveAttachment(REQUEST $request)
    {
        $request->validate([
            'docId' => 'required',
            'leaveNo' => 'required',
        ]);
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if (!isset($params)) {
                $params = new \stdClass();
            }
            $params->docNo = $request->leaveNo;
            $params->docID = $request->docId;
            $result = $service->DeleteDocumentAttachment($params);
            if ($result->return_value != "") {
                return redirect()->back()->with('success', "Deleted successfully");
            } else {
                return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
            }
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    public function cancel(REQUEST $request)
    {
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->requisitionNo = $request->requisitionNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $result = $service->CancelLeaveApplication($params);
            if ($result->return_value == true) {
                return redirect('/staff/leave')->with('success', 'Leave application cancelled successfully');
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    public function approval(REQUEST $request)
    {
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->requisitionNo = $request->requisitionNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->tableID = HRLeaveRequisition::tableDesc()['tableID'];
            $result = $service->RequestLeaveApproval($params);
            if ($result->return_value == true) {
                return redirect('/staff/leave')->with('success', 'Leave application sent for approval successfully');
            }
            return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    /**
     * Send a leave requisition for approval.
     */
    public function SendForLeaveApproval(ApproveLeaveRequisitionRequest $request): RedirectResponse
    {
        try {
            $this->leaveService->requestLeaveApproval($request->requisitionNo);

            return redirect()->route('leave.index')->with('success', 'Leave application sent for approval successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send leave requisition for approval', [
                'requisitionNo' => $request->requisitionNo,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['requisitionNo' => 'Unable to send leave requisition for approval. Please try again or contact support.']);
        }
    }
    public function getLeaveDates($leaveType, $endDate, $startDate)
    {
        // $leaveTypeDesc = $this->odataClient()->from(LeaveType::wsName())->where('Code', $leaveType)->first();
        // $leaveBalance = $this->getLeaveBalance($leaveType);
        // $leaveTypeDesc = $this->bcService->callPage(
        //     LeaveType::wsName(),
        //     [
        //         '$filter' => "Code eq $leaveType",
        //         '$top' => 1
        //     ]
        // );
        // $leaveBalance = $this->leaveService->getLeaveBalance($leaveType);


        $start_date = str_replace('_', "/", $startDate);
        $end_date = str_replace('_', "/", $endDate);
        try {

            // $service = $this->MySoapClient(config('app.cuStaffPortal'));

            $result = $this->bcService->callCodeUnitAction(
                'cuStaffPortal',
                'FnGetLeaveDetails',
                [
                    'empNo' => session('authUser')['employeeNo'],
                    'startDate' => Carbon::parse(strtotime($start_date))->format('Y-m-d'),
                    'endDate' => Carbon::parse(strtotime($end_date))->format('Y-m-d'),
                    'leaveType' => $leaveType
                ]
            );
            // dd($result);
            // return $result;
            // $result = $service->FnGetLeaveDetails($params);
            dd($result);
            if ($result->value != "") {
                $vars = explode("##", $result->value);
                $appliedDays = $vars[1];
                $returnDate = Carbon::parse($vars[0])->format('d-M-Y');
                $data = [
                    'isWeekend' =>  Carbon::parse($start_date)->dayOfWeek == Carbon::SUNDAY,
                    'endDate' => $endDate,
                    'appliedDays' => $appliedDays,
                    'returnDate' => $returnDate,
                ];
                return $data;
            }
            return null;
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            dd($e->faultstring);
            return null;
        }
    }
    /**
     * Get leave details for a given leave type and date range.
     */
    public function getLeaveDatesDetails(LeaveDetailsRequest $request): JsonResponse
    {
        // dd($request);
        try {
            $leaveDetails = $this->leaveService->getLeaveDetails(
                $request->leaveType,
                $request->startDate,
                $request->endDate
            );

            return response()->json([
                'success' => true,
                'data' => $leaveDetails,
                'message' => 'Leave details retrieved successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch leave details', [
                'leaveType' => $request->leaveType,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve leave details. Please try again or contact support.',
            ], 400);
        }
    }
    public function getLeaveBalance($leaveType)
    {
        $empNo = session('authUser')['employeeNo'];
        // $leaveTypeDesc = $this->odataClient()->from(LeaveType::wsName())
        //     ->where('Code', $leaveType)
        //     ->first();

        $LeavetypeDescription = $this->bcService->callPage(
            LeaveType::wsName(),
            [
                '$filter' => "Code eq $leaveType",
                '$top' => 1
            ]
        );


        $pendingApproval = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Status', 'Pending Approval')->where('EmployeeNo', session('authUser')['employeeNo'])->where('Leave_Type', $leaveType)->where('#filter', 'EndDate gt ' . date('Y-m-d') . 'filter#')->count();
        $currentPeriod = $this->odataClient()->from(HRLeavePeriod::wsName())->where('Current', 'true')->first();
        // dd($currentPeriod);
        // if ($currentPeriod == null)
        // {
        //     return redirect()->back()->with('error','Oops! Leave Calendar not Activated.'.config('app.errors')['persists']);
        // }
        //$takenDays = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Status','Released')->where('EmployeeNo',session('authUser')['employeeNo'])->where('Leave_Type',$leaveType)->count();
        $data = [];
        $data['balance'] = 0;
        $data['isHourly'] = false;
        $data['pendingCount'] = $pendingApproval;
        if ($LeavetypeDescription != null) {
            $leaveTypeDays = $LeavetypeDescription['Days'];
            $leaveEntries = $this->odataClient()->from(HRLeaveLedger::wsName())
                ->where('EmployeeNo', $empNo)
                ->where('LeaveType', $leaveType)
                ->where('CalendarCode', $currentPeriod->Code)
                ->get();
            $availableDays = $leaveTypeDays;
            $deductions = 0;
            $additions = 0;
            $leaveBalance = 0;
            // dd($leaveEntries);
            //$leaveBalance = $leaveTypeDays - ($takenDays+$pendingApproval);
            if ($leaveEntries != null) {
                foreach ($leaveEntries as $entry) {
                    if ($entry->NoofDays < 0) {
                        $deductions =  $deductions + -$entry->NoofDays;
                    } else {
                        $additions =  $additions + $entry->NoofDays;
                    }
                    // $takenDays = $takenDays + $entry->No_of_Days;
                }
            }

            if ($LeavetypeDescription['Code'] == EnumsLeaveType::ANNUAL) {
                $leaveBalance = $additions - $deductions;
            } else {
                $deductions = $deductions - $additions;
                $leaveBalance = $leaveTypeDays - $deductions;
            }
            if ($leaveBalance >= 0) {
                $data['balance'] = (int)$leaveBalance;
            }
            // dd($data);
            return $data;
        }
    }
    public function showBalance(Request $request)
    {
        try {
            $leaveType = $request->input('leave_type', LeaveType::ANNUAL);
            $balanceData = $this->leaveService->getLeaveBalance($leaveType);

            return response()->json([
                'success' => true,
                'data' => $balanceData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    //
    public function leaveStatement()
    {
        // dd(session('authUser'));
        if (session('authUser')['gender'] == 'Male') {
            $notGender = 'Female';
        } else {
            $notGender = 'Male';
        }
        $leaveTypes = $this->odataClient()->from(LeaveType::wsName())->where('Gender', '!=', $notGender)->get();
        $data = [
            'leaveTypes' => $leaveTypes,
        ];
        return view('staff.leave.statement')->with($data);
    }
    /**
     * Display the leave statement for the authenticated user.
     */
    public function getLeaveStatement(): View|RedirectResponse
    {
        try {
            $statement = $this->leaveService->getLeaveStatement();

            if (empty($statement)) {
                Log::warning('No leave types available for user', [
                    'employeeNo' => Session::get('authUser.employeeNo'),
                    'gender' => Session::get('authUser.gender'),
                ]);
                return redirect()->route('leave.index')->with('warning', 'No leave types available for your account.');
            }

            return view('staff.leave.statement', ['leaveTypes' => $statement]);
        } catch (\Exception $e) {
            Log::error('Failed to load leave statement', [
                'employeeNo' => Session::get('authUser.employeeNo'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('leave.index')->with('error', 'Unable to load leave statement. Please try again or contact support.');
        }
    }
    public function generateLeaveStatement(REQUEST $request)
    {
        $request->validate([
            'leaveType' => 'required',
        ]);
        // $period = Carbon::parse("$request->month/01/$request->year")->format('Y-m-d');
        try {
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if (!isset($params)) {
                $params = new \stdClass();
            }
            $empNo = session('authUser')['employeeNo'];
            $params->employeeNo = $empNo;
            $params->leaveType = $request->leaveType;
            $fname = str_replace('/', '_', $empNo) . "_leave.pdf";
            $params->filenameFromApp = $fname;
            // dd($params);
            $result = $service->GenerateLeaveStatement($params);
            // dd($result);
            if ($result->return_value != "") {
                $data = base64_decode($result->return_value, true);
                return response($data)->header('Content-Type', 'application/pdf');
            } else {

                return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
            }
        } catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    /**
     * Generate a leave statement PDF for the specified leave type.
     */
    public function generateLeaveTypeStatement(GenerateLeaveStatementRequest $request): Response|RedirectResponse
    {
        try {
            $pdfData = $this->leaveService->generateLeaveStatement(
                $request->leaveType,
                $request->getPeriod()
            );

            $filename = str_replace('/', '_', Session::get('authUser.employeeNo')) . "_leave_{$request->leaveType}.pdf";

            return response($pdfData)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            Log::error('Failed to generate leave statement PDF', [
                'employeeNo' => Session::get('authUser.employeeNo'),
                'leaveType' => $request->leaveType,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['leaveType' => 'Unable to generate leave statement. Please try again or contact support.']);
        }
    }
    public function isOnLeave($empNo)
    {
        $leaveEntries = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('EmployeeNo', $empNo)->where('Status', 'Released')->get();
        if ($leaveEntries != null) {
            foreach ($leaveEntries as $leave) {
                $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Document_No', $leave['Document_No'])->first();
                if ($requisition != null) {
                    if (Carbon::parse($requisition->End_Date)->gt(date('Y-m-d'))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
