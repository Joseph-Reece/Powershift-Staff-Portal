<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use Carbon\Carbon;
use App\Models\ImprestHeader;
use App\Models\ImprestSurrenderHeader;
use App\Models\ImprestLine;
use App\Models\ImprestSurrenderLine;
use App\Models\DimensionValue;
use App\Models\ResponsibilityCenter;
use App\Models\ImprestType;
use App\Models\PostedReceipt;

class ImprestsSurrenderController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $requsitions = $this->odataClient()->from(ImprestSurrenderHeader::wsName())
        ->where('User_ID',session('authUser')['userID'])
        ->get();
        $data = [
            'requsitions' => $requsitions
        ];
        return view('staff.requisition.imprest-surrender.index')->with($data);
    }
    public function createHeader(){
        $requisitions = $this->odataClient()->from(ImprestHeader::wsName())
        ->where('Employee_No',session('authUser')['employeeNo'])
        ->get();
        $data = [
            'requisitions' => $requisitions,
        ];
        return view('staff.requisition.imprest-surrender.create-header')->with($data);
    }
    public function storeHeader(REQUEST $request){
        $request->validate([
            'imprest' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->docNo = $request->action == 'edit'?$request->No:"";
            $params->imprestIssueDocNo = $request->imprest;
            $params->myUserID = session('authUser')['userID'];
            $params->myAction = $request->action == ''?"create":"update";
            $params->receivedFrom = session('authUser')['userID'];
            $params->pVNo = '';
            $result = $service->ImprestSurrenderHeader($params);
            $returnValue = $result->return_value;
            if($returnValue != ''){
                return redirect('/staff/requisition/imprest-surrender/show/header/'.$returnValue)->with('success','Imprest surrender Created successfully. You should now create Imprest Lines');
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
        $requisition = $this->odataClient()->from(ImprestSurrenderHeader::wsName())->where('No',$reqNo)->where('User_ID',session('authUser')['userID'])->first();
        if($requisition == null){
            return redirect()->back()->with('error','Imprest surrender not found');
        }
        $lines = $this->odataClient()->from(ImprestSurrenderLine::wsName())->where('Surrender_Doc_No',$reqNo)->get();
        $receipts = $this->odataClient()->from(PostedReceipt::wsName())->get();
        $approvers = app('App\Http\Controllers\Staff\ApprovalsController')->getApprovers($requisition->No);
        $data = [
            'requisition' => $requisition,
            'lines' => $lines,
            'receipts' => $receipts,
            'approvers' => $approvers,
        ];
        return view('staff.requisition.imprest-surrender.show-header')->with($data);
    }
    public function updateLines(REQUEST $request){
        $lines = $request->except('_token','_method');
        // dd($lines);
        if($lines != null){
            foreach($lines as $key => $line){
                $receiptAmount = 0;
                if(strpos($key,'lineNo__')  !== false){
                    $data1 = explode('__',$key);
                    $lineNo = $data1[1];
                }
                    $accountNoName = 'accountNo__'.$lineNo;
                    $accountNo = $request->$accountNoName;
                    //
                    $surrenderDocNoName = 'surrenderNo__'.$lineNo;
                    $surrenderDocNo = $request->$surrenderDocNoName;
                    //
                    $ipReceipt = 'receipt__'.$accountNo.'__'.$surrenderDocNo;
                    $receiptNo = $request->$ipReceipt;
                    //
                    $recAmountName = 'receiptAmount__'.$lineNo;
                    $receiptAmount = $request->$recAmountName;
                    //
                    $actualSpentName= 'spent__'.$lineNo;
                    $actualSpent= $request->$actualSpentName;
                    try{
                        $service = $this->MySoapClient(config('app.cuStaffPortal'));
                        $params = new \stdClass();
                        $params->lineNo = $lineNo;
                        $params->accountNo = $accountNo;
                        $params->docNo = $surrenderDocNo;
                        $params->actualSpent = $actualSpent;
                        $params->cashReceiptNo = $receiptNo;
                        $params->cashReceiptAmount = $receiptAmount;
                        $result = $service->ImprestSurrenderLine($params);
                        if($result->return_value != true){
                            return redirect()->back()->with('error','Something went wrong');
                        }
                    }
                    catch (\SoapFault $e) {
                        $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
                        $errorMsg = $e->faultstring;
                        return redirect()->back()->with('error',$errorMsg);
                    }
            }
            return redirect()->back()->with('success','Lines updated successfully');
        }

    }
    public function requestApproval(REQUEST $request){
        $request->validate([
            'requisitionNo' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->docNo = $request->requisitionNo;
            $result = $service->RequestImprestSurrenderApproval($params);
            if($result->return_value == true){
                return redirect()->back()->with('success','Imprest surrender sent for approval successfully');
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
    public function isImprestSurrendered($imprestNo,$amount){
        $surrenders = $this->odataClient()->from(ImprestSurrenderHeader::wsName())
        ->where('Imprest_Issue_Doc_No',$imprestNo)
        ->where('User_ID',session('authUser')['userID'])
        ->get();
        $amount = 0;
        foreach($surrenders as $surrender){
            $amt = $amt + $surrender['Amount'];
        }
        if($amt >= $amount){
            return true;
        }
        return false;
    }
}
