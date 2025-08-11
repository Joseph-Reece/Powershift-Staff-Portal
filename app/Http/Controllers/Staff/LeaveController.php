<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\Traits\AttachmentTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\HRLeaveRequisition;
use App\Models\LeaveType;
use App\Models\HRLeavePeriod;
use App\Models\HRLeaveLedger;
use App\Models\HREmployee;
use Carbon\Carbon;

class LeaveController extends Controller
{
    use WebServicesTrait;
    use AttachmentTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $dateToday = Carbon::now();
        $startDate = $dateToday->startOfYear()->format('Y-m-d');
        $endDate = $dateToday->endOfYear()->format('Y-m-d');
        $requsitions = $this->odataClient()->from(HRLeaveRequisition::wsName())
        ->where('User_ID',session('authUser')['userID'])
        ->where('#filter',"(Application_Date gt $startDate and Application_Date lt $endDate)"."filter#")
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.leave.index')->with($data);
    }
    public function create(){
        $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Status','Pending Approval')->where('Employee_No',session('authUser')['employeeNo'])->first();
        // if($requisition != null){
        //     return redirect()->back()->with('error','Oops! you cannot make a new leave application while there is another one that is pending approval.');
        // }
        if(session('authUser')['Gender'] == 'Male'){
            $notGender = 'Female';
        }else{
            $notGender = 'Male';
        }
        $leaveTypes = $this->odataClient()->from(LeaveType::wsName())
        ->where('Gender','!=',$notGender)
        ->get();
        $relievers = $this->odataClient()->from(HREmployee::wsName())
        ->select('No','First_Name','Middle_Name','Last_Name')
        ->where('No','!=',session('authUser')['employeeNo'])
        ->where('Status','=','Active')
        ->get();
        //$relievers = [];
        // $isOnLeave = false;
        // //get relievers not on leave
        // foreach($employees as $employee){
        //     $leaveEntries = $this->odataClient()->from(HRLeaveLedger::wsName())->where('Employee_No',$employee['No'])->where('Leave_Period',(int)date('Y'))->get();
        //     if($leaveEntries != null){
        //         foreach($leaveEntries as $leave){
        //             $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('No',$leave['Document_No'])->first();
        //             if($requisition != null){
        //                 if(Carbon::parse($requisition->End_Date)->gt(date('Y-m-d'))){
        //                     $isOnLeave = true;
        //                 }
        //             }
        //         }
        //         if(!$isOnLeave){
        //             $relievers[] = $employee;
        //         }
        //     }else{
        //         $relievers[] = $employee;
        //     }
        // }
        $data = [
            'leaveTypes' => $leaveTypes,
            'relievers' => $relievers,
            'action' => 'create',
        ];
        return view('staff.leave.application')->with($data);
    }
    public function store(REQUEST $request){
        $request->validate([
            'leaveType' => 'required',
            'appliedDays' => 'nullable',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'reason' => 'required',
            'requestLeaveAllowance' => 'required',
        ]);
        $dates = $this->getLeaveDates($request->leaveType,$request->appliedDays,$request->startDate);
        if($dates == null){
            return redirect()->back()->with('error','Oops! something went wrong. Please try again');
        }
        try{
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
            if($result->return_value != ''){
                if($request->hasFile('attachment')){
                    // dd('heere');
                    $this->validate($request,
                        [
                            'attachment' => 'required|max:2999',
                        ],
                        ['attachment.max' => 'File size cannot exceed 3MB']
                    );
                    try{
                        // $service = $this->MySoapClient(config('app.cuStaffPortal'));
                        $params = new \stdClass();
                        $params->accountNo = $result->return_value;
                        $params->action = 'insert';
                        $params->attachmentID = 0;
                        $params->description = 'Leave Attachment';
                        $file = $request->file('attachment');
                        $b64File = base64_encode(file_get_contents($file));
                        $params->b64File = $b64File;
                        $params->fileName = $params->description.'.'.$file->getClientOriginalExtension();
                        $params->fileName = str_replace(" ","-",$params->fileName);
                        $params->fileName = str_replace("/","_",$params->fileName);
                        $params->tableID = 52202673;

                        $result = $service->FnAttachment($params);
                        // dd($result);
                        // $returnValue = $result->return_value;
                    }
                    catch (\SoapFault $e) {
                        $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
                        $errorMsg = $e->faultstring;
                        return redirect()->back()->with('error',$errorMsg);
                    }

                    $data = [
                        'pKey' => $result->return_value,
                        'tableDesc' => HRLeaveRequisition::tableDesc(),
                        'file' => $request->file('attachment'),
                        'description' => 'leave_attachment_'.$result->return_value
                    ];
                    // dd($data);
                    // $this->uploadAttachment($data);
                }
                return redirect('/staff/leave')->with('success','Leave application created successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            // dd($errorMsg);
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function show($no){
        $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())
        ->where('Document_No',$no)
        ->where('Employee_No',session('authUser')['employeeNo'])
        ->first();
        if($requisition == null){
            return redirect('/staff/leave')->with('error','Leave application not found.');
        }
        $relieverDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($requisition->Duties_Taken_Over_By);
        $requisition['relieverDesc'] = $relieverDesc;
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->Document_No);
        $data = [
            'requisition' => $requisition,
            'approvers' => $approvers,
        ];
        return view('staff.leave.show')->with($data);
    }
    public function edit($no){
        $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())
        ->where('Document_No',$no)
        ->where('Employee_No',session('authUser')['employeeNo'])
        ->where('Status','Open')
        ->first();
        if($requisition == null){
            return redirect('/staff/leave')->with('error','Leave application is no longer editable or does not exist.');
        }
        // if($requisition->Hourly){
        //     $requisition->Start_Time = Carbon::parse($requisition->Start_Time);
        //     $requisition->Return_Time = Carbon::parse($requisition->Return_Time);
        //     $requisition->Start_Date = Carbon::parse($requisition->Start_Time)->format('m/d/Y h:i A');
        //     $appliedHours = $requisition->Start_Time->diffInHours($requisition->Return_Time);
        //     $requisition->appliedHours = $appliedHours;
        // }
        //
        $leaveTypes = $this->odataClient()->from(LeaveType::wsName())
        ->get();
        $relievers = $this->odataClient()->from(HREmployee::wsName())
        ->select('No','First_Name','Middle_Name','Last_Name')
        ->where('No','!=',session('authUser')['employeeNo'])
        ->where('Status','=','Active')
        ->get();
        // $relievers = [];
        // $isOnLeave = false;
        // foreach($employees as $employee){
        //     $leaveEntries = $this->odataClient()->from(HRLeaveLedger::wsName())->where('Employee_No',$employee['No'])->where('Leave_Period',(int)date('Y'))->get();
        //     if($leaveEntries != null){
        //         foreach($leaveEntries as $leave){
        //             $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('No',$leave['Document_No'])->first();
        //             if($requisition != null){
        //                 if(Carbon::parse($requisition->End_Date)->gt(date('Y-m-d'))){
        //                     $isOnLeave = true;
        //                 }
        //             }
        //         }
        //         if(!$isOnLeave){
        //             $relievers[] = $employee;
        //         }
        //     }else{
        //         $relievers[] = $employee;
        //     }
        // }
        $data = [
            'leaveTypes' => $leaveTypes,
            'relievers' => $relievers,
            'action' => 'edit',
            'requisition' => $requisition,
        ];
        return view('staff.leave.application')->with($data);
    }
    public function update(REQUEST $request){
        $request->validate([
            'leaveType' => 'required',
            'appliedDays' => 'nullable',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'reason' => 'required',
            'requestLeaveAllowance' => 'required',
        ]);
        $dates = $this->getLeaveDates($request->leaveType,$request->appliedDays,$request->startDate);
        if($dates == null){
            return redirect()->back()->with('error','Oops! something went wrong. Please try again');
        }

        try{
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
            $result = $service->LeaveApplication($params);
            if($result->return_value == true){
                return redirect('/staff/leave')->with('success','Leave application updated successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }

    //
    public function createLeaveAttachment($no){
        $data=[
            'leaveNo'=>$no
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
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->docNo = $request->leaveNo;
            $params->docNo2 = $request->leaveNo;
            $params->description = $request->description;
            $params->tableID = 52202673;
            if($request->hasFile('file')){
                $file = $request->file('file');
                $b64File = base64_encode(file_get_contents($file));
                $params->file = $b64File;
                $params->fileName = $params->description.'.'.$file->getClientOriginalExtension();
                $params->fileName = str_replace(" ","-",$params->fileName);
                $params->fileName = str_replace("/","_",$params->fileName);
                // dd($params->filename);
            }
            $params->tableID = 52202543;
            $result = $service->UploadDocumentAttachment($params);
            $returnValue = $result->return_value;
            if($returnValue){
                $message = "Saved successfully";
                return redirect('/staff/leave/show/'.$request->leaveNo)->with('success',$message);
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function showLeaveAttachment(REQUEST $request){
        $request->validate([
            'attachmentID' => 'required',
            'tableID' => 'required',
            'leaveNo' => 'required',
            'fileName' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if(!isset($params)){
                $params = new \stdClass();
            }
            $params->docNo = $request->leaveNo;
            $params->attachmentID = $request->attachmentID;
            $params->tableID = $request->tableID;
            $result = $service->GetDocumentAttachment($params);
			if($result->return_value != ""){
				$data = base64_decode($result->return_value);
                header('Content-Type: application/pdf');
                header("Content-Disposition:attachment;filename=\"$request->fileName\"");
                echo $data;
			}else{
                return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
            }
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function deleteLeaveAttachment(REQUEST $request){
        $request->validate([
            'docId' => 'required',
            'leaveNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if(!isset($params)){
                $params = new \stdClass();
            }
            $params->docNo = $request->leaveNo;
            $params->docID = $request->docId;
            $result = $service->DeleteDocumentAttachment($params);
			if($result->return_value != ""){
                return redirect()->back()->with('success',"Deleted successfully");
			}else{
                return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
            }
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function cancel(REQUEST $request){
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->requisitionNo = $request->requisitionNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $result = $service->CancelLeaveApplication($params);
            if($result->return_value == true){
                return redirect('/staff/leave')->with('success','Leave application cancelled successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function approval(REQUEST $request){
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->requisitionNo = $request->requisitionNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->tableID = HRLeaveRequisition::tableDesc()['tableID'];
            $result = $service->RequestLeaveApproval($params);
            if($result->return_value == true){
                return redirect('/staff/leave')->with('success','Leave application sent for approval successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function getLeaveDates($leaveType,$endDate,$startDate){
        $leaveTypeDesc = $this->odataClient()->from(LeaveType::wsName())->where('Code',$leaveType)->first();
        $leaveBalance = $this->getLeaveBalance($leaveType);
        // if($leaveBalance <= 0 || $appliedDays > $leaveBalance){
        //     return null;
        // }
        $start_date = str_replace('_',"/",$startDate);
        $end_date = str_replace('_',"/",$endDate);
        // if($leaveTypeDesc->Allow_Hourly){
        //     $endDate = Carbon::parse($start_date)->addHours($appliedDays);
        //     $returnDate = Carbon::parse($start_date)->addHours($appliedDays);
        //     $endDate = Carbon::parse($endDate)->format('d-M-Y h:i:s');
        //     $returnDate = Carbon::parse($returnDate)->format('d-M-Y h:i:s');
        // }else{
        //
        // $endDate = Carbon::parse($start_date)->addDays($appliedDays-1);
        // if(Carbon::parse( $endDate)->dayOfWeek == Carbon::SUNDAY){
        //     $endDate = Carbon::parse($endDate)->addDays(1);
        // }
        // $returnDate = Carbon::parse($start_date)->addDays(($appliedDays+1));
        // if(Carbon::parse($returnDate)->dayOfWeek == Carbon::SUNDAY){
        //     $returnDate = Carbon::parse($returnDate)->addDays(1);
        // }
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->empNo = session('authUser')['employeeNo'];
            $params->startDate = Carbon::parse(strtotime($start_date))->format('Y-m-d');
            $params->endDate = Carbon::parse(strtotime($end_date))->format('Y-m-d');
            $params->leaveType = $leaveType;
            $result = $service->FnGetLeaveDetails($params);
            if($result->return_value != ""){
                $vars = explode("##",$result->return_value);
                $appliedDays = $vars[1];
                $returnDate = Carbon::parse($vars[0])->format('d-M-Y');
                $data = [
                    'isWeekend' =>  Carbon::parse($start_date)->dayOfWeek == Carbon::SUNDAY,
                    'endDate' => $endDate,
                    'appliedDays' => $appliedDays,
                    'returnDate' =>$returnDate,
                ];
                return $data;
            }
            return null;
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            dd($e->faultstring);
            return null;
        }

    }
    public function getLeaveBalance($leaveType){
        $empNo = session('authUser')['employeeNo'];
        $leaveTypeDesc = $this->odataClient()->from(LeaveType::wsName())
        ->where('Code',$leaveType)
        ->first();
       $pendingApproval = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Status','Pending Approval')->where('Employee_No',session('authUser')['employeeNo'])->where('Leave_Type',$leaveType)->where('#filter','End_Date gt '.date('Y-m-d').'filter#')->count();
        $currentPeriod = $this->odataClient()->from(HRLeavePeriod::wsName())->where('Current_Leave','true')->first();
       //$takenDays = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Status','Released')->where('Employee_No',session('authUser')['employeeNo'])->where('Leave_Type',$leaveType)->count();
        $data = [];
        $data['balance'] = 0;
        $data['isHourly'] = false;
        $data['pendingCount'] = $pendingApproval;
        if($leaveTypeDesc != null){
            $leaveTypeDays = $leaveTypeDesc['Days'];
            $leaveEntries = $this->odataClient()->from(HRLeaveLedger::wsName())
            ->where('Employee_No',$empNo)
            ->where('Leave_Type',$leaveType)
            ->where('Leave_Period',$currentPeriod->Code)
            ->get();
            $availableDays = $leaveTypeDays;
            $deductions = 0;
            $additions = 0;
            $leaveBalance = 0;
            //$leaveBalance = $leaveTypeDays - ($takenDays+$pendingApproval);
            if($leaveEntries != null){
                foreach($leaveEntries as $entry){
                    if($entry->No_of_Days < 0){
                        $deductions =  $deductions + -$entry->No_of_Days;
                    }else{
                        $additions =  $additions + $entry->No_of_Days;
                    }
                    // $takenDays = $takenDays + $entry->No_of_Days;
                }
            }
            if($leaveTypeDesc['Code'] == 'ANNUAL'){
                $leaveBalance = $additions - $deductions;
            }else{
                $deductions = $deductions - $additions;
                $leaveBalance = $leaveTypeDays - $deductions;
            }
            if($leaveBalance >= 0){
                $data['balance'] = (int)$leaveBalance;
            }
            return $data;
        }
    }
    //
    public function leaveStatement(){
        if(session('authUser')['Gender'] == 'Male'){
            $notGender = 'Female';
        }else{
            $notGender = 'Male';
        }
        $leaveTypes = $this->odataClient()->from(LeaveType::wsName())->where('Gender','!=',$notGender)->get();
        $data = [
            'leaveTypes' => $leaveTypes,
        ];
        return view('staff.leave.statement')->with($data);
    }
    public function generateLeaveStatement(REQUEST $request){
        $request->validate([
            'leaveType' => 'required',
        ]);
        // $period = Carbon::parse("$request->month/01/$request->year")->format('Y-m-d');
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if(!isset($params)){
                $params = new \stdClass();
            }
            $empNo = session('authUser')['employeeNo'];
            $params->employeeNo = $empNo;
            $params->leaveType = $request->leaveType;
            $fname = str_replace('/','_',$empNo)."_leave.pdf";
            $params->filenameFromApp = $fname;
            $result = $service->GenerateLeaveStatement($params);
			if($result->return_value != ""){
				$data = base64_decode($result->return_value);
                header('Content-Type: application/pdf');
                echo $data;
			}
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function isOnLeave($empNo){
        $leaveEntries = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Employee_No',$empNo)->where('Status','Released')->get();
        if($leaveEntries != null){
            foreach($leaveEntries as $leave){
                $requisition = $this->odataClient()->from(HRLeaveRequisition::wsName())->where('Document_No',$leave['Document_No'])->first();
                if($requisition != null){
                    if(Carbon::parse($requisition->End_Date)->gt(date('Y-m-d'))){
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
