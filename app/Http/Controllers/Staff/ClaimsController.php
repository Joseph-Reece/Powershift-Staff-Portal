<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\Traits\AttachmentTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\ClaimHeader;
use App\Models\Attachment;
use App\Models\ClaimLine;
use App\Models\DimensionValue;
use App\Models\MedicalCover;
use App\Models\ClaimType;
use App\Models\Currency;
use App\Models\GLAccount;

class ClaimsController extends Controller
{
    use WebServicesTrait;
    use AttachmentTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $requsitions = $this->odataClient()->from(ClaimHeader::wsName())
        ->where('Employee_No',session('authUser')['employeeNo'])
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.claim.index')->with($data);
    }
    public function createHeader(){
        $data = [
            'action' => 'create',
        ];
        return view('staff.requisition.claim.create-header')->with($data);
    }
    public function storeHeader(REQUEST $request){
        $request->validate([
            'purpose' => 'required',
            'claimDate' => 'required|Date',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->reqNo = $request->action == 'create'? '':$request->requisitionNo;
            $params->staffNo = session('authUser')['employeeNo'];
            $params->claimDescription = $request->purpose;
            $params->claimDate = Carbon::parse(strtotime($request->claimDate))->format('Y-m-d');
            $params->myUserID = session('authUser')['userID'];
            $result = $service->ClaimRequisitionHeader($params);
            $returnValue = $result->return_value;
            if($returnValue != ''){
                return redirect('/staff/requisition/claim/show/header/'.$returnValue)->with('success','Claim saved successfully. You should now create Claim Lines');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function showHeader($reqNo){
        $requisition = $this->odataClient()->from(ClaimHeader::wsName())->where('No',$reqNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $lines = $this->odataClient()->from(ClaimLine::wsName())->where('No',$reqNo)->get();
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'lines' => $lines,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.claim.show-header')->with($data);
    }
    public function editHeader($reqNo){
        $departments = $this->odataClient()->from(DimensionValue::wsName())->where('Global_Dimension_No',2)->get();
        $medicalCovers = $this->odataClient()->from(MedicalCover::wsName())->where('Employee_No',session('authUser')['employeeNo'])->where('Cover_Status','Active')->get();
        $requisition = $this->odataClient()->from(ClaimHeader::wsName())->where('No',$reqNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        $data = [
            'medicalCovers' => $medicalCovers,
            'requisition' => $requisition,
            'action' => 'edit',
        ];
        return view('staff.requisition.claim.create-header')->with($data);
    }
    public function createLine($headerNo){
        $requisition = $this->odataClient()->from(ClaimHeader::wsName())->where('No',$headerNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        $GLs = $this->odataClient()->from(GLAccount::wsName())->select('No','Name')->where('Direct_Posting','true')->get();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $claimTypes = $this->odataClient()->from(ClaimType::wsName())->where('Description','!=','')->where('Type','Claim')->get();
        $data = [
            'requisition' => $requisition,
            'GLs' => $GLs,
            'ClaimTypes' => $claimTypes,
            'action' => 'create',
        ];
        return view('staff.requisition.claim.create-line')->with($data);
    }
    public function storeLine(REQUEST $request){
        $request->validate([
            'claimType' => 'required',
            'accountNo' => 'required',
            'amount' => 'required|numeric',
            'receiptNo' => 'nullable',
            'expenditureDate' => 'required|date',
            'expenditureDescription' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->reqNo = $request->requisitionNo;
            $params->lineNo = $request->action != 'create'? $request->lineNo:0;
            $params->claimType = $request->claimType;
            $params->accountNo = $request->accountNo;
            $params->amount = $request->amount;
            $params->claimReceiptNo = $request->claimReceiptNo;
            $params->expenditureDate = Carbon::parse(strtotime($request->expenditureDate))->format('Y-m-d');
            $params->expenditureDescription = $request->expenditureDescription;
            $result = $service->ClaimRequisitionLine($params);
            $returnValue = $result->return_value;
            if($returnValue != 0){
                if($request->hasFile('attachment')){
                    $this->validate($request,
                        [
                            'attachment' => 'required|max:2999',
                        ],
                        ['attachment.max' => 'File size cannot exceed 3MB']
                    );
                    $data = ['pKey' => $request->requisitionNo,'tableDesc' => ClaimHeader::tableDesc(),'file' => $request->file('attachment'),'description' => 'claim_line_attachment_'.$returnValue];
                    $this->uploadAttachment($data);
                }
                return redirect('/staff/requisition/claim/show/header/'.$request->requisitionNo)->with('success','Saved successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function showLine($reqNo){
        $requisition = $this->odataClient()->from(ClaimHeader::wsName())->where('No',$reqNo)->where('Staff_No',session('authUser')['employeeNo'])->first();
        $data = [
            'requisition' => $requisition,
        ];
        return view('staff.requisition.claim.show-header')->with($data);
    }
    public function lineReceipt($header,$line){
        $attachment = $this->odataClient()->from(Attachment::wsName())
        ->where('Table_ID',ClaimHeader::tableDesc()['tableID'])
        ->where('No',$header)
        ->where('Name','claim_line_receipt_'.$line.'_'.$header)
        ->first();
        if($attachment != null){
            $data = ['pKey' => $header, 'tableDesc' => ClaimHeader::tableDesc(),'attachmentID' => $attachment['ID']];
            $b64File = $this->getAttachment($data);
            $path = config('app.reportsPath').$attachment->Name.'.'.$attachment->File_Extension;
            $file = fopen($path,'w');
            fwrite($file,base64_decode($b64File));
            fclose ($file);
            return response()->file($path)->deleteFileAfterSend(true);
        }else{
            return redirect()->back()->with('error','Receipt attachment not found');
        }
    }
    public function deleteLine(REQUEST $request){
        // dd($request->all());
        $request->validate([
            'requisitionNo' => 'required',
            'lineNo' => 'required',
        ]);

        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->requisitionNo = $request->requisitionNo;
            $params->lineNo = $request->lineNo;
            $result = $service->DeleteClaimLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/claim/show/header/'.$request->requisitionNo)->with('success','Claim line deleted successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function requestApproval(REQUEST $request){
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->reqNo = $request->requisitionNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $result = $service->RequestClaimApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/claim')->with('success','Claim sent for approval successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
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
            $result = $service->CancelClaimRequisition($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/claim')->with('success','Claim request cancelled successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function ClaimHeaderDesc($claim){
        $data = $this->odataClient()->from(ClaimHeader::wsName())->where('No',$claim)->where('Staff_No',session('authUser')['employeeNo'])->first();
        return $data;
    }
}
