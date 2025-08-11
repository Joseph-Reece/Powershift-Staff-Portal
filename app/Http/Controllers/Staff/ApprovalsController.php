<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\ApprovalEntry;
use App\Models\ApprovalCommentLine;
use App\Models\HRLeaveRequisition;
use App\Models\ImprestHeader;
use App\Models\ClaimHeader;
use App\Models\ImprestSurrenderHeader;
use App\Models\ImprestLine;
use App\Models\ClaimLine;
use App\Models\ImprestSurrenderLine;
use App\Models\TransportRequisition;
use App\Models\PurchaseRequisitionHeader;
use App\Models\PurchaseRequisitionLine;
use App\Models\StoreRequisitionHeader;
use App\Models\StoreRequisitionLine;
use App\Models\PaymentVoucherHeader;
use App\Models\PettyCashVoucherHeader;
use App\Models\PettyCashVoucherLine;
use App\Models\PurchaseHeader;
use App\Models\PaymentVoucherLine;
use Carbon\Carbon;

class ApprovalsController extends Controller
{
    use WebServicesTrait;
    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function openDocuments(){
        isset($_GET['skip'])? $skip = $_GET['skip']:$skip = 0;
        isset($_GET['docType'])? $docType = $_GET['docType']:$docType = '';
        $approvals = $this->odataClient()->from(ApprovalEntry::wsName())
        ->where('Status','Open')
        ->where('Approver_ID',session('authUser')['userID'])
        ->where('Document_Type',$docType != ''? '=':'!=',$docType != ''? $docType:null)
        ->take(30)
        ->get();
        $data = [
            'approvals' => $approvals,
            'status' => 'Open',
            'title' => 'Documents Pending Your Approval',
        ];
        return view('staff.approval.index')->with($data);
    }
    public function approvedDocuments(){
        isset($_GET['skip'])? $skip = $_GET['skip']:$skip = 0;
        isset($_GET['docType'])? $docType = $_GET['docType']:$docType = '';
        $approvals = $this->odataClient()->from(ApprovalEntry::wsName())
        ->where('Status','Approved')
        ->where('Approver_ID',session('authUser')['userID'])
        ->where('Document_Type',$docType != ''? '=':'!=',$docType != ''? $docType:null)
        ->take(30)
        ->skip($skip)
        ->get();
        $data = [
            'approvals' => $approvals,
            'status' => 'Approved',
            'title' => 'Documents You have Approved',
        ];
        return view('staff.approval.index')->with($data);
    }
    public function rejectedDocuments(){
        isset($_GET['skip'])? $skip = $_GET['skip']:$skip = 0;
        isset($_GET['docType'])? $docType = $_GET['docType']:$docType = '';
        $approvals = $this->odataClient()->from(ApprovalEntry::wsName())
        ->where('Status','Rejected')
        ->where('Approver_ID',session('authUser')['userID'])
        ->where('Document_Type',$docType != ''? '=':'!=',$docType != ''? $docType:null)
        ->take(30)
        ->get();
        $data = [
            'approvals' => $approvals,
            'status' => 'Rejected',
            'title' => 'Documents You have Rejected',
        ];
        return view('staff.approval.index')->with($data);
    }
    public function viewDocument($docNo){
        $document = $this->odataClient()->from(ApprovalEntry::wsName())
        ->where('Document_No',$docNo)
        ->where('Approver_ID',session('authUser')['userID'])
        ->where('Status','Open')
        ->first();
        if($document != null){
            $comment = $this->odataClient()->from(ApprovalCommentLine::wsName())
            ->where('Document_No',$docNo)
            ->where('Table_ID',$document->Table_ID)
            // ->where('Document_Type',$document->Document_Type)
            ->where('User_ID',$document->User_ID)
            ->where('Record_ID_to_Approve',$document->Record_ID_to_Approve)
            ->first();
            $document['approvalComment'] = $comment;
            //details
            $data = [];
            $employeeDesc = null;
            $documentName = null;
            if($document->Table_ID == 52202673){
                $documentName = 'Leave Request Approval';
                $data = $this->odataClient()->from(HRLeaveRequisition::wsName())
                ->where('Document_No',$document->Document_No)
                ->first();
                if($data == null){
                    return redirect()->back()->with('error','Leave header not found');
                }
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Employee_No);
            }
            elseif($document->Document_Type == 'TransportRequest'){
                $documentName = 'Transport Request Approval';
                $data = $this->odataClient()->from(TransportRequisition::wsName())->where('Transport_Requisition_No',$document->Document_No)->first();
                if($data == null){
                    return redirect()->back()->with('error','Voucher header not found');
                }
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc2($data->Requested_By);
            }
            elseif($document->Table_ID == 52202786){
                $documentName = 'Imprest Request Approval';
                $requisition = $this->odataClient()->from(ImprestHeader::wsName())->where('No',$document->Document_No)->first();
                if($requisition == null){
                    return redirect()->back()->with('error','Imprest header not found');
                }
                $lines = $this->odataClient()->from(ImprestLine::wsName())->where('No',$document->Document_No)->get();
                $requisition['lines'] = $lines;
                $data = $requisition;
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Employee_No);
            }
            elseif($document->Table_ID == 52202707){
                $documentName = 'Imprest Surrender Approval';
                $requisition = $this->odataClient()->from(ImprestSurrenderHeader::wsName())->where('No',$document->Document_No)->first();
                if($requisition == null){
                    return redirect()->back()->with('error','Voucher header not found');
                }
                $lines = $this->odataClient()->from(ImprestSurrenderLine::wsName())->where('Surrender_Doc_No',$document->Document_No)->get();
                $requisition['lines'] = $lines;
                $data = $requisition;
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($requisition['Account_No']);
            }
            elseif($document->Table_ID == 38){//purchase order
                $requisition = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())->where('No',$document->Document_No)->first();
                if($requisition == null){
                    $requisition = $this->odataClient()->from(StoreRequisitionHeader::wsName())->where('No',$document->Document_No)->first();
                }
                if($requisition == null){
                    return redirect()->back()->with('error','Requisition header not found');
                }
                if($requisition->DocApprovalType == 'Requisition' and $requisition->Document_Type == 'Quote')
                {
                    $documentName = 'Purchase Requisition Approval';
                }else{
                    $documentName = 'Store Requisition Approval';
                }
                $lines = $this->odataClient()->from(PurchaseRequisitionLine::wsName())->where('Document_No',$document->Document_No)->get();
                foreach($lines as $key => $line){
                    $lines[$key]['itemDesc'] = app('App\Http\Controllers\Staff\GeneralController')->itemDesc($line->No);
                }
                $requisition['lines'] = $lines;
                $data = $requisition;
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Employee_No);
            }
            elseif($document->Table_ID == 52202966){//Store Req
                $requisition = $this->odataClient()->from(StoreRequisitionHeader::wsName())->where('No',$document->Document_No)->first();
                if($requisition == null){
                    return redirect()->back()->with('error','Requisition header not found');
                }
                $documentName = 'Store Requisition Approval';
                $lines = $this->odataClient()->from(StoreRequisitionLine::wsName())->where('Requistion_No',$document->Document_No)->get();
                foreach($lines as $key => $line){
                    $lines[$key]['itemDesc'] = app('App\Http\Controllers\Staff\GeneralController')->itemDesc($line->No);
                }
                $requisition['lines'] = $lines;
                $data = $requisition;
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Employee_No);
            }
            elseif($document->Table_ID == 52202717){
                $documentName = 'Staff Claim Approval';
                $requisition = $this->odataClient()->from(ClaimHeader::wsName())->where('No',$document->Document_No)->first();
                if($requisition == null){
                    return redirect()->back()->with('error','Document header not found');
                }
                $lines = $this->odataClient()->from(ClaimLine::wsName())->where('No',$document->Document_No)->get();
                $requisition['lines'] = $lines;
                $data = $requisition;
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Staff_No);
            }
            elseif($document->Document_Type == 'Payment Voucher'){
                $documentName = 'Payment Voucher Approval';
                $voucherHeader = $this->odataClient()->from(PaymentVoucherHeader::wsName())->where('No',$document->Document_No)->first();
                if($voucherHeader == null){
                    return redirect()->back()->with('error','Voucher header not found');
                }
                $lines = $this->odataClient()->from(PaymentVoucherLine::wsName())->where('No',$document->Document_No)->get();
                $voucherHeader['lines'] = $lines;
                $data = $voucherHeader;
                // $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Staff_No);
            }
            elseif($document->Document_Type == 'Petty Cash'){
                $documentName = 'Petty Cash Approval';
                $voucherHeader = $this->odataClient()->from(PettyCashVoucherHeader::wsName())->where('No',$document->Document_No)->first();
                if($voucherHeader == null){
                    return redirect()->back()->with('error','Voucher header not found');
                }
                $lines = $this->odataClient()->from(PettyCashVoucherLine::wsName())->where('Document_No',$document->Document_No)->get();
                $voucherHeader['lines'] = $lines;
                $data = $voucherHeader;
                // $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Staff_No);
            }
            elseif($document->Document_Type == 'Order'){
                $documentName = 'Order Approval';
                $requisition = $this->odataClient()->from(PurchaseRequisitionHeader::wsName())->where('No',$document->Document_No)->first();
                if($requisition == null){
                    return redirect()->back()->with('error','Requisition header not found');
                }
                $lines = $this->odataClient()->from(PurchaseRequisitionLine::wsName())->where('Document_No',$document->Document_No)->get();
                foreach($lines as $key => $line){
                    $lines[$key]['itemDesc'] = app('App\Http\Controllers\Staff\GeneralController')->itemDesc($line->No);
                }
                $requisition['lines'] = $lines;
                $data = $requisition;
                $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc($data->Employee_No);
            }
        }
        if($documentName == null)
        {
            return redirect()->back()->with('error','Oops! it seems the approval of this document is not allowed on the portal.');

        }
        $data = [
            'document' => $document,
            'data' => $data,
            'employeeDesc' => $employeeDesc,
            'documentName' => $documentName,
        ];
        return view('staff.approval.show')->with($data);
    }
    public function documentApproval(REQUEST $request){
        $request->validate([
            'docNo' => 'required',
            'isApprove' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->entryNo = $request->entryNo;
            $params->docNo = $request->docNo;
            $params->userID = session('authUser')['userID'];
            $params->isApprove = $request->isApprove;
            $params->comments = $request->comments;
            $result = $service->DocumentApproval($params);
            if($result->return_value == true){
                return redirect('/staff/approval/open')->with('success','Document Approved Successfully');
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
    public function approvalsCount($type,$status){
        $dateToday = Carbon::now();
        $startDate = $dateToday->startOfYear()->format('Y-m-d');
        $endDate = $dateToday->endOfYear()->format('Y-m-d');
        //
        $data = [];
        if($type == 'all' || $type == 'Pending'){
            $totalAll = $this->odataClient()->from(ApprovalEntry::wsName())->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalAll'] = $totalAll;
        }
        if($type == 'all' || $type == 'leave'){
            $totalLeave = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',HRLeaveRequisition::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalLeave'] = $totalLeave;
        }
        if($type == 'all' || $type == 'imprest'){
            $totalImprest = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',ImprestHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalImprest'] = $totalImprest;
        }
        if($type == 'all' || $type == 'imprestSurr'){
            $totalImprestSurr = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',ImprestHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalImprestSurr'] = $totalImprestSurr;
        }
        if($type == 'all' || $type == 'store'){
            $totalStore = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',StoreRequisitionHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalStore'] = $totalStore;
        }
        if($type == 'all' || $type == 'purchase'){
            $totalPurchase = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',PurchaseRequisitionHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalPurchase'] = $totalPurchase;
        }
        if($type == 'all' || $type == 'transport'){
            //$totalTransport = $this->odataClient()->from(ApprovalEntry::wsName())->where('Document_Type','TransportRequest')->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $totalTransport = 0;
            $data['totalTransport'] = $totalTransport;
        }
        if($type == 'all' || $type == 'claim'){
            $totalClaim = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',ClaimHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalClaim'] = $totalClaim;
        }
        if($type == 'all' || $type == 'paymentVoucher'){
            $totalPv = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',PaymentVoucherHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalPv'] = $totalPv;
        }
        if($type == 'all' || $type == 'pettyCash'){
            $totalPc = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',PettyCashVoucherHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalPc'] = $totalPc;
        }
        if($type == 'all' || $type == 'order'){
            $totalOrder = $this->odataClient()->from(ApprovalEntry::wsName())->where('Table_ID',PurchaseHeader::tableDesc()['tableID'])->where('Status',$status)->where('Approver_ID',session('authUser')['userID'])->where('#filter',"(Due_Date gt $startDate and Due_Date lt $endDate)"."filter#")->count();
            $data['totalOrder'] = $totalOrder;
        }
        if($status == 'Open')
        {
            $data['isNotified'] = session('authUser')['isNotified'];
            session()->put('authUser.isNotified',true);
            session()->save();
        }
        return $data;
    }
    public function getApprovers($docNo){
        $approvers = $this->odataClient()->from(ApprovalEntry::wsName())->where('Document_No',$docNo)->get();
        foreach($approvers as $key => $approver){
            $employeeDesc = app('App\Http\Controllers\Staff\GeneralController')->employeeDesc2($approver->Approver_ID);
            $approvers[$key]['employeeDesc'] = $employeeDesc;
        }
        return $approvers;
    }
}
