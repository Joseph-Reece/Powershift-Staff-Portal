<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\WebServices;

class AppraisalController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $records = [];
        $records1 = $this->odataClient()->from(WebServices::AppraisalHeader())
        ->where('Employee_No',session('authUser')['employeeNo'])
        ->get();
        if($records1 != null){
            foreach($records1 as $rec){
                $records[]=$rec;
            }
        }
        $records2 = $this->odataClient()->from(WebServices::AppraisalHeader())
        ->where('Supervisor',session('authUser')['employeeNo'])
        ->get();
        if($records2 != null){
            foreach($records2 as $rec){
                $records[]=$rec;
            }
        }
        $data = [
            'records' => $records
        ];
        return view('staff.appraisal.index')->with($data);
    }
    // public function showHeader(){
    //     $needs = $this->odataClient()->from(TrainingNeed::wsName())->get();
    //     $data = [
    //         'needs' => $needs,
    //         'action' => 'create',
    //     ];
    //     return view('staff.appraisal.create-header')->with($data);
    // }
    // public function storeHeader(REQUEST $request){
    //     $request->validate([
    //         'trainingNeed' => 'required',
    //         'comments' => 'required',
    //     ]);
    //     try{
    //         $service = $this->MySoapClient(config('app.cuStaffPortal'));
    //         $params = new \stdClass();
    //         $params->myAction = $request->action;
    //         $params->docNo = $request->action == 'create'? '':$request->requisitionNo;
    //         $params->comments = $request->comments;
    //         $params->trainingNeedCode = $request->trainingNeed;
    //         $params->myUserID = session('authUser')['userID'];
    //         $params->employeeNo = session('authUser')['employeeNo'];
    //         $result = $service->FnTrainingRequest($params);
    //         $returnValue = $result->return_value;
    //         if($returnValue != ''){
    //             return redirect('/staff/appraisal/show/header/'.$returnValue)->with('success','Training request saved successfully. Kindly remember to send it for approval.');
    //         }
    //         return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
    //     }
    //     catch (\SoapFault $e) {
    //         $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
    //         $errorMsg = $e->faultstring;
    //         return redirect()->back()->with('error',$errorMsg);
    //     }
    // }
    public function showHeader($no){
        $record = $this->odataClient()->from(WebServices::AppraisalHeader())->where('Appraisal_No',$no)->first();
        if($record == null){
            return redirect()->back()->with('error','Requisition not found');
        }
        if($record->Employee_No !=  session('authUser')['employeeNo'] && $record->Supervisor != session('authUser')['employeeNo']){
            return redirect()->back()->with('error','You do not have permision to view this appraisal.');
        }
        $lines = $this->odataClient()->from(WebServices::AppraisalScore())->where('Appraisal_No',$no)->get();
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($record->Appraisal_No);
        $data = [
            'record' => $record,
            'lines' => $lines,
            'approvers' => $approvers,
        ];
        return view('staff.appraisal.show-header')->with($data);
    }
    public function editScore($no,$area,$measure){
        $record = $this->odataClient()->from(WebServices::AppraisalHeader())->where('Appraisal_No',$no)->first();
        if($record == null){
            return redirect()->back()->with('error','Appraisal document not found');
        }
        if($record->Employee_No !=  session('authUser')['employeeNo'] && $record->Supervisor != session('authUser')['employeeNo']){
            return redirect()->back()->with('error','You do not have permision to view this appraisal.');
        }
        $action = "";
        if($record->Status != "Closed"){
            $action = "edit";
        }else{
            $action = "view";
        }
        $line = $this->odataClient()->from(WebServices::AppraisalScore())
        ->where('Appraisal_No',$no)
        ->where('Key_Result_Area',$area)
        ->where('Performance_Measure',$measure)
        ->first();
        $data = [
            'line' => $line,
            'record' => $record,
            'action' => $action,
        ];
        return view('staff.appraisal.edit-score')->with($data);
    }
    public function storeAppraisalScore(REQUEST $request){
        $request->validate([
            'docNo' => 'required',
            'area' => 'required',
            'measure' => 'required',
        ]);
        $record = $this->odataClient()->from(WebServices::AppraisalHeader())->where('Appraisal_No',$request->docNo)->first();
        if($record == null){
            return redirect()->back()->with('error','Appraisal document not found');
        }
        if($record->Employee_No == session('authUser')['employeeNo']){
            $request->validate([
                'employeeRating' => 'required',
                'employeeComments' => 'required',
            ]);
            try{
                $service = $this->MySoapClient(config('app.cuStaffPortal'));
                $params = new \stdClass();
                $params->docNo = $request->docNo;
                $params->area = $request->area;
                $params->measure = $request->measure;
                $params->employeeRating = $request->employeeRating;
                $params->employeeComments = $request->employeeComments;
                $params->trainingNeedCode = $request->trainingNeed;
                $result = $service->FnAppraisalEmployeeRating($params);
                $returnValue = $result->return_value;
                if($returnValue == true){
                    return redirect("/staff/appraisal/show/header/$request->docNo")->with('success','Saved successfully.');
                }
                return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
            }
            catch (\SoapFault $e) {
                $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
                $errorMsg = $e->faultstring;
                return redirect()->back()->with('error',$errorMsg);
            }
        }else{
            $request->validate([
                'supervisorRating' => 'required',
                'supervisorComments' => 'required',
            ]);
            try{
                $service = $this->MySoapClient(config('app.cuStaffPortal'));
                $params = new \stdClass();
                $params->docNo = $request->docNo;
                $params->area = $request->area;
                $params->measure = $request->measure;
                $params->employeeRating = $request->employeeRating;
                $params->employeeComments = $request->employeeComments;
                $params->trainingNeedCode = $request->trainingNeed;
                $result = $service->FnAppraisalSupervisorRating($params);
                $returnValue = $result->return_value;
                if($returnValue == true){
                    return redirect("/staff/appraisal/show/header/$request->docNo")->with('success','Saved successfully.');
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
    public function submitAppraisal(REQUEST $request){
        $request->validate([
            'docNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->docNo = $request->docNo;
            $params->employeeNo = session('authUser')['employeeNo'];
            $result = $service->FnAppraisalSubmit($params);
            if($result->return_value == true){
                return redirect('/staff/appraisal')->with('success','Appraisal submitted successfully.');
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
                return redirect('/staff/appraisal')->with('success','Training request cancelled successfully');
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
        $data = $this->odataClient()->from(TrainingHeader::wsName())->where('Appraisal_No',$training)->where('Employee_No',session('authUser')['employeeNo'])->first();
        return $data;
    }
}
