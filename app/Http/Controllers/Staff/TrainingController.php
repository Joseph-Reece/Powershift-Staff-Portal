<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\TrainingHeader;
use App\Models\TrainingLine;
use App\Models\TrainingNeed;
use App\Models\GLAccount;

class TrainingController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    //myUserId : Code[100];action : Text;DocNo : Code[50];department : Code[100];accountNo : Code[30];payingBankAccount : Code[30];purpose : Text;paymentReleaseDate : Date;payMode : Integer;responsibilityCenter : Code[30]
    public function index(){
        $requsitions = $this->odataClient()->from(TrainingHeader::wsName())
        ->where('EmployeeNo',session('authUser')['employeeNo'])
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.training.index')->with($data);
    }
    public function createHeader(){
        $needs = $this->odataClient()->from(TrainingNeed::wsName())->get();
        $data = [
            'needs' => $needs,
            'action' => 'create',
        ];
        return view('staff.requisition.training.create-header')->with($data);
    }
    public function storeHeader(REQUEST $request){
        $request->validate([
            'trainingNeed' => 'required',
            'comments' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->myAction = $request->action;
            $params->docNo = $request->action == 'create'? '':$request->requisitionNo;
            $params->comments = $request->comments;
            $params->trainingNeedCode = $request->trainingNeed;
            $params->myUserID = session('authUser')['userID'];
            $params->employeeNo = session('authUser')['employeeNo'];
            $result = $service->FnTrainingRequest($params);
            $returnValue = $result->return_value;
            if($returnValue != ''){
                return redirect('/staff/requisition/training/show/header/'.$returnValue)->with('success','Training request saved successfully. Kindly remember to send it for approval.');
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
        $requisition = $this->odataClient()->from(TrainingHeader::wsName())->where('Application_No',$reqNo)->where('Employee_no',session('authUser')['employeeNo'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $lines = $this->odataClient()->from(TrainingLine::wsName())->where('Application_No',$reqNo)->get();
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->Application_No);
        $data = [
            'requisition' => $requisition,
            'lines' => $lines,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.training.show-header')->with($data);
    }
    public function editHeader($reqNo){
        $needs = $this->odataClient()->from(TrainingNeed::wsName())->get();
        $requisition = $this->odataClient()->from(TrainingHeader::wsName())->where('Application_No',$reqNo)->where('Employee_no',session('authUser')['employeeNo'])->first();
        $data = [
            'needs' => $needs,
            'requisition' => $requisition,
            'action' => 'edit',
        ];
        return view('staff.requisition.training.create-header')->with($data);
    }
    public function requestApproval(REQUEST $request){
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->docNo = $request->requisitionNo;
            $params->myAction = 'requestApproval';
            $result = $service->TrainingApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/training')->with('success','Training sent for approval successfully');
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
            $params->docNo = $request->requisitionNo;
            $params->myAction = 'cancelApproval';
            $result = $service->TrainingApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/training')->with('success','Training request cancelled successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function TrainingHeaderDesc($training){
        $data = $this->odataClient()->from(TrainingHeader::wsName())->where('Application_No',$training)->where('Employee_no',session('authUser')['employeeNo'])->first();
        return $data;
    }
}
