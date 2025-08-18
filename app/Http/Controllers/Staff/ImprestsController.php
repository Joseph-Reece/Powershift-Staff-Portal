<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\ImprestHeader;
use App\Models\ImprestLine;
use App\Models\DimensionValue;
use App\Models\ResponsibilityCenter;
use App\Models\ImprestType;
use App\Models\Location;
use App\Models\GLAccount;

class ImprestsController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    //myUserId : Code[100];action : Text;DocNo : Code[50];department : Code[100];accountNo : Code[30];payingBankAccount : Code[30];purpose : Text;paymentReleaseDate : Date;payMode : Integer;responsibilityCenter : Code[30]
    public function index(){
        $requsitions = $this->odataClient()->from(ImprestHeader::wsName())
        // ->where('EmployeeNo',session('authUser')['employeeNo'])
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.imprest.index')->with($data);
    }
    public function createHeader(){
        $locations = $this->odataClient()->from(Location::wsName())->get();
        $data = [
            'locations' => $locations,
            'action' => 'create',
        ];
        return view('staff.requisition.imprest.create-header')->with($data);
    }
    public function storeHeader(REQUEST $request){
        $request->validate([
            'dateRequired' => 'required|date',
            'purpose' => 'required',
            // 'department' => 'required',
            // 'responsibilityCenter' => 'required',
            'isStandingImprest' => 'required',
            'travelDestination' => 'required',
            'travelDate' => 'required|date',
            'returnDate' => 'required|date',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->docNo = $request->action == 'create'? '':$request->requisitionNo;
            //$params->employeeNo = session('authUser')['employeeNo'];
            $params->dateRequired = Carbon::parse(strtotime($request->dateRequired))->format('Y-m-d');
            $params->purpose = $request->purpose;
            // $params->responsibilityCenter = $request->responsibilityCenter;
            $params->myUserId = session('authUser')['userID'];
            // $params->department = $request->department;
            $params->isStandingImprest = $request->isStandingImprest;
            $params->travelDestination = $request->travelDestination;
            $params->travelDate = Carbon::parse(strtotime($request->travelDate))->format('Y-m-d');
            $params->returnDate = Carbon::parse(strtotime($request->returnDate))->format('Y-m-d');
            $result = $service->ImprestRequisitionHeader($params);
            $returnValue = $result->return_value;
            if($returnValue != ''){
                return redirect('/staff/requisition/imprest/show/header/'.$returnValue)->with('success','Imprest Created successfully. You should now create Imprest Lines');
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
        $requisition = $this->odataClient()->from(ImprestHeader::wsName())->where('No',$reqNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $lines = $this->odataClient()->from(ImprestLine::wsName())->where('No',$reqNo)->get();
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'lines' => $lines,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.imprest.show-header')->with($data);
    }
    public function editHeader($reqNo){
        $departments = $this->odataClient()->from(DimensionValue::wsName())->where('Global_Dimension_No',2)->get();
        $respCenters = $this->odataClient()->from(ResponsibilityCenter::wsName())->get();
        $requisition = $this->odataClient()->from(ImprestHeader::wsName())->where('No',$reqNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        $data = [
            'departments' => $departments,
            'respCenters' => $respCenters,
            'requisition' => $requisition,
            'action' => 'edit',
        ];
        return view('staff.requisition.imprest.create-header')->with($data);
    }
    public function createLine($headerNo){
        $requisition = $this->odataClient()->from(ImprestHeader::wsName())->where('No',$headerNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        $GLs = $this->odataClient()->from(GLAccount::wsName())->select('No','Name')->where('Direct_Posting','true')->get();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $imprestTypes = $this->odataClient()->from(ImprestType::wsName())->where('Description','!=','')->where('Type','Imprest')->get();
        $data = [
            'imprestTypes' => $imprestTypes,
            'requisition' => $requisition,
            'GLs' => $GLs,
            'action' => 'create',
        ];
        return view('staff.requisition.imprest.create-line')->with($data);
    }
    public function storeLine(REQUEST $request){
        $request->validate([
            'advanceType' => 'required',
            'accountNo' => 'required',
            'amount' => 'required|numeric',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->docNo = $request->requisitionNo;
            $params->lineNo = 0;
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->advanceType = $request->advanceType;
            $params->accountNo = $request->accountNo;
            $params->amount = $request->amount;
            $result = $service->ImprestRequisitionLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/imprest/show/header/'.$request->requisitionNo)->with('success','Saved successfully');
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
        $requisition = $this->odataClient()->from(ImprestHeader::wsName())->where('No',$reqNo)->where('Employee_No',session('authUser')['employeeNo'])->first();
        $data = [
            'requisition' => $requisition,
        ];
        return view('staff.requisition.imprest.show-header')->with($data);
    }
    public function deleteLine(REQUEST $request){
        $request->validate([
            'requisitionNo' => 'required',
            'lineNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->requisitionNo = $request->requisitionNo;
            $params->lineNo = $request->lineNo;
            $result = $service->DeleteImprestLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/imprest/show/header/'.$request->requisitionNo)->with('success','Imprest line deleted successfully');
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
            $params->tableID = ImprestHeader::tableDesc()['tableID'];
            $result = $service->RequestImprestApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/imprest')->with('success','Imprest sent for approval successfully');
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
            $result = $service->CancelImprestRequisition($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/imprest')->with('success','Imprest request cancelled successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function imprestHeaderDesc($imprest){
        $data = $this->odataClient()->from(ImprestHeader::wsName())->where('No',$imprest)->where('Employee_No',session('authUser')['employeeNo'])->first();
        return $data;
    }
}
