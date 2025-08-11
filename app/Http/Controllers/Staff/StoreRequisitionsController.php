<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\StoreRequisitionHeader;
use App\Models\StoreRequisitionLine;
use App\Models\DimensionValue;
use App\Models\ResponsibilityCenter;
use App\Models\ImprestType;
use App\Models\Location;
use App\Models\Item;

class StoreRequisitionsController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $requsitions = $this->odataClient()->from(StoreRequisitionHeader::wsName())
        ->where('User_ID',session('authUser')['userID'])
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.store.index')->with($data);
    }
    public function createHeader(){
        $data = [
            'action' => 'create',
        ];
        return view('staff.requisition.store.create-header')->with($data);
    }
    public function storeHeader(REQUEST $request){
        $request->validate([
            'dateRequired' => 'required',
            'description' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->myAction = $request->action;
            $params->docNo = $request->action == 'create'? '':$request->requisitionNo;
            $params->myUserID = session('authUser')['userID'];
            $params->requestDescription = $request->description;
            $params->requestDate = $request->dateRequired;
            $result = $service->StoreRequisitionHeader($params);
            $returnValue = $result->return_value;
            if($returnValue != ''){
                return redirect('/staff/requisition/store/show/header/'.$returnValue)->with('success','Store requisition saved successfully. You should now create requisition Lines');
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
        $requisition = $this->odataClient()->from(StoreRequisitionHeader::wsName())->where('No',$reqNo)->where('User_ID',session('authUser')['userID'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $lines = $this->odataClient()->from(StoreRequisitionLine::wsName())->where('Requistion_No',$reqNo)->get();
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'lines' => $lines,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.store.show-header')->with($data);
    }
    public function editHeader($reqNo){
        $locations = $this->odataClient()->from(Location::wsName())->get();
        $requisition = $this->odataClient()->from(StoreRequisitionHeader::wsName())->where('No',$reqNo)->where('User_ID',session('authUser')['userID'])->first();
        $data = [
            'locations' => $locations,
            'requisition' => $requisition,
            'action' => 'edit',
        ];
        return view('staff.requisition.store.create-header')->with($data);
    }
    public function createLine($headerNo){
        $requisition = $this->odataClient()->from(StoreRequisitionHeader::wsName())->where('No',$headerNo)->where('User_ID',session('authUser')['userID'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $locations = $this->odataClient()->from(Location::wsName())->where('Name','!=','')->get();
        $items = $this->odataClient()->from(Item::wsName())->where('Description','!=','')->get();
        $data = [
            'locations' => $locations,
            'items' => $items,
            'requisition' => $requisition,
            'action' => 'create',
        ];
        return view('staff.requisition.store.create-line')->with($data);
    }
    public function storeLine(REQUEST $request){
        $request->validate([
            'type' => 'required',
            'item' => 'required',
            'issuingStore' => 'required',
            'quantity' => 'nullable|required_if:type,=,2|numeric',
            // 'purpose' => 'required',
        ],
        [
            'quantity.required_if' => 'Quantity is required'
        ]
        );
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->reqNo = $request->requisitionNo;
            $params->lineNo = $request->action != 'edit'? 0:$request->lineNo;
            $params->type = $request->type;
            $params->itemNo = $request->item;
            $params->quantity = $request->type == 1? $request->quantity:0;
            //$params->amount = $request->amount;
            $params->location = $request->issuingStore;
            $result = $service->StoreRequisitionLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/store/show/header/'.$request->requisitionNo)->with('success','Saved successfully');
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
            $result = $service->DeleteStoreReqLine($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/store/show/header/'.$request->requisitionNo)->with('success','Store requisition line deleted successfully');
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
            $params->tableID = StoreRequisitionHeader::tableDesc()['tableID'];
            $result = $service->RequestStoreReqApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/store')->with('success','Store requisition sent for approval successfully');
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
            $result = $service->CancelStoreRequisition($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/store')->with('success','Store requisition cancelled successfully');
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
