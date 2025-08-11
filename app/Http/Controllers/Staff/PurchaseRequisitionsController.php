<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\PurchaseRequisitionHeader;
use App\Models\PurchaseRequisitionLine;
use App\Models\DimensionValue;
use App\Models\ResponsibilityCenter;
use App\Models\Location;
use App\Models\Item;
use App\Models\WorkplanActivity;

class PurchaseRequisitionsController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $requsitions = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())
        ->where('Assigned_User_ID',session('authUser')['userID'])
        ->where('DocApprovalType','Requisition')
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.purchase.index')->with($data);
    }
    public function createHeader(){
        $data = [
            'action' => 'create',
        ];
        return view('staff.requisition.purchase.create-header')->with($data);
    }
    public function storeHeader(REQUEST $request){
        $request->validate([
            'description' => 'required',
            'includingVAT' => 'required',
            'dateNeeded' => 'required|Date',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->reqNo = $request->action == 'create'? '':$request->requisitionNo;
            $params->postingDescription = $request->description;
            $params->pricesIncludingVAT = $request->includingVAT;
            $params->myUserId = session('authUser')['userID'];
            $params->orderDate = Carbon::parse(strtotime($request->dateNeeded))->format('Y-m-d');
            $result = $service->PurchaseRequisitionHeader($params);
            $returnValue = $result->return_value;
            if($returnValue != ''){
                return redirect('/staff/requisition/purchase/show/header/'.$returnValue)->with('success','Purchase requisition Created successfully. You should now create requisition Lines');
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
        $requisition = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())->where('No',$reqNo)->where('Assigned_User_ID',session('authUser')['userID'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $lines = $this->odataClient()->from(PurchaseRequisitionLine::wsName())->where('Document_No',$reqNo)->get();
        foreach($lines as $key => $line){
            $lines[$key]['itemDesc'] = app('App\Http\Controllers\Staff\GeneralController')->itemDesc($line->No);
        }
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'lines' => $lines,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.purchase.show-header')->with($data);
    }
    public function editHeader($reqNo){
        $requisition = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())->where('No',$reqNo)->where('Assigned_User_ID',session('authUser')['userID'])->first();
        $data = [
            'requisition' => $requisition,
            'action' => 'edit',
        ];
        return view('staff.requisition.purchase.create-header')->with($data);
    }
    public function createLine($headerNo){
        $requisition = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())->where('No',$headerNo)->where('Assigned_User_ID',session('authUser')['userID'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $locations = $this->odataClient()->from(Location::wsName())->where('Name','!=','')->get();
        $items = $this->odataClient()->from(Item::wsName())->where('Description','!=','')->get();
        $plans = $this->odataClient()->from(WorkplanActivity::wsName())->get();
        $data = [
            'locations' => $locations,
            'items' => $items,
            'requisition' => $requisition,
            'plans' => $plans,
            'action' => 'create',
        ];
        return view('staff.requisition.purchase.create-line')->with($data);
    }
    public function storeLine(REQUEST $request){
        $request->validate([
            'type' => 'required',
            'itemNo' => 'required',
            'location' => 'nullable',
            'procurementPlan' => 'nullable',
            'reason' => 'required',
            'quantity' => 'required|numeric',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->reqNo = $request->requisitionNo;
            $params->lineNo = $request->action != 'edit'? 0:$request->lineNo;
            $params->itemNo = $request->itemNo;
            $params->quantity = $request->quantity;
            $params->location = $request->whereNeeded;
            $params->type = $request->type;
            $params->procurementPlan = $request->procurementPlan;
            $params->reasonForRequest = $request->reason;
            $result = $service->PurchaseRequisitionLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/purchase/show/header/'.$request->requisitionNo)->with('success','Saved successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
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
            $result = $service->DeletePurchaseReqLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/purchase/show/header/'.$request->requisitionNo)->with('success','Purchase requisition line deleted successfully');
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
            $params->tableID = PurchaseRequisitionHeader::tableDesc()['tableID'];
            $result = $service->RequestPurchaseReqApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/purchase')->with('success','Purchase requisition sent for approval successfully');
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
            $result = $service->CancelPurchaseRequisition($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/purchase')->with('success','Purchase requisition cancelled successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
}
