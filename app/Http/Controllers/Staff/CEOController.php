<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\HRLeaveRequisition;
use App\Models\LeaveType;
use App\Models\HRLeaveLedger;
use App\Models\HREmployee;
use App\Models\PR_EmployeePostingGroup;
use App\Models\DimensionValue;
use Carbon\Carbon;
use App\Models\PayrollPeriod;

class CEOController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
        $this->middleware('isCEO');
    }
    public function masterRollReport(){
        $periods = $this->odataClient()->from(PayrollPeriod::wsName())
        ->where('Closed','true')
        ->get();
        $periods = $periods->unique('Period_Year');
        $PostGroups= $this->odataClient()->from(PR_EmployeePostingGroup::wsName())->get();
        $data = [
            'periods' => $periods,
            'PostGroups' => $PostGroups,
        ];
        return view('staff.ceo.master-roll')->with($data);
    }
    public function generateMasterRollReport(REQUEST $request){
        $request->validate([
            'year' => 'required',
            'month' => 'required',
        ]);
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if(!isset($params)){
                $params = new \stdClass();
            }
            $params->year = $request->year;
            $params->month = $request->month;
            $params->postingGroup = $request->postingGroup;
            $fname = $request->year.'-'.$request->month."_masterroll.pdf";
            $params->filenameFromApp = $fname;
            $result = $service->FnPayrollMasterRollReport($params);
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
}
