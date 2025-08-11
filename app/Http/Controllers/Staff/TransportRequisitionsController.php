<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\TransportRequisition;
use App\Models\DimensionValue;
use App\Models\ResponsibilityCenter;
use App\Models\Location;
use App\Models\Item;

class TransportRequisitionsController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $requsitions = $this->odataClient()->from(TransportRequisition::wsName())
        ->where('Requested_By',session('authUser')['userID'])
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.transport.index')->with($data);
    }
    public function create(){
        $departments = $this->odataClient()->from(DimensionValue::wsName())->where('Global_Dimension_No',2)->get();
        $respCenters = $this->odataClient()->from(ResponsibilityCenter::wsName())->get();
        $data = [
            'departments' => $departments,
            'respCenters' => $respCenters,
            'action' => 'create',
        ];
        return view('staff.requisition.transport.create')->with($data);
    }
    public function store(REQUEST $request){
        $request->validate([
            'purpose' => 'required',
            'responsibilityCenter' => 'required',
            'destination' => 'required',
            'commencement' => 'required',
            'dateOfTrip' => 'required|date',
            'noOfDays' => 'required|numeric',
            'noOfPassengers' => 'required|numeric',
            // 'requestType' => 'required',
            // 'travelType' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->action = $request->action;
            $params->reqNo = $request->action == 'create'? '':$request->requisitionNo;
            $params->requestType = $request->action;
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->purpose = $request->purpose;
            $params->responsibilityCenter = $request->responsibilityCenter;
            $params->destination = $request->destination;
            $params->commenceFrom = $request->commencement;
            $params->dateOfTrip = Carbon::parse(strtotime($request->dateOfTrip))->format('Y-m-d');
            $params->noOfDays = $request->noOfDays;
            $params->noOfPassengers = $request->noOfPassengers;
            $params->requestType = 0;
            $params->travelType = 0;
            $params->noSeries = 'TR';
            $result = $service->TransportRequisition($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/transport')->with('success','Transport requisition Created successfully.');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function show($reqNo){
        $requisition = $this->odataClient()->from(TransportRequisition::wsName())->where('Transport_Requisition_No',$reqNo)->where('Requested_By',session('authUser')['userID'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.transport.show')->with($data);
    }
    public function edit($reqNo){
        $departments = $this->odataClient()->from(DimensionValue::wsName())->where('Global_Dimension_No',2)->get();
        $respCenters = $this->odataClient()->from(ResponsibilityCenter::wsName())->get();
        $requisition = $this->odataClient()->from(TransportRequisition::wsName())->where('Transport_Requisition_No',$reqNo)->where('Requested_By',session('authUser')['userID'])->first();
        $data = [
            'departments' => $departments,
            'respCenters' => $respCenters,
            'requisition' => $requisition,
            'action' => 'edit',
        ];
        return view('staff.requisition.transport.create')->with($data);
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
            $params->tableID = TransportRequisition::tableDesc()['tableID'];
            $result = $service->RequestTransportReqApproval($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/transport')->with('success','Transport requisition sent for approval successfully');
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
            $params->tableID = TransportRequisition::tableDesc()['tableID'];
            $result = $service->CancelTransportRequisition($params);
            if($result->return_value == true){
                return redirect('/staff/requisition/transport')->with('success','Transport requisition cancelled successfully');
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
