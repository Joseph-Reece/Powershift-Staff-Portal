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
use App\Models\Attendance;

class AttendanceController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function index(){
        $records = $this->odataClient()->from(Attendance::wsName())
        ->where('Staff_No',session('authUser')['employeeNo'])
        ->get();
        $data = [
            'records' => $records
        ];
        return view('staff.attendance')->with($data);
    }
    public function checkInCheckoutToday(REQUEST $request){
        $location = "";
        if($request->type == "checkin"){
            // $request->validate([
            //     'checkinLocation' => 'required',
            // ]);
            $location = $request->checkinLocation;
        }
        if($request->type == "checkout"){
            // $request->validate([
            //     'checkoutLocation' => 'required',
            // ]);
            $location = $request->checkoutLocation;
            $current_time = \Carbon\Carbon::parse(Time())->addHours(3)->format('H:i:s');
            $another_time = \Carbon\Carbon::parse(strtotime("17:00:00"))->format('H:i:s');
            if ($current_time < $another_time) {
                return redirect()->back()->with('error','You can only signout after 5:00 pm');
            }
        }
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            if(!isset($params)){
                $params = new \stdClass();
            }
            $params->employeeNo = session('authUser')['employeeNo'];
            $params->myUserID = session('authUser')['userID'];
            $params->type = $request->type;
            $params->location = $location;
            $result = $service->FnCheckinCheckout($params);
			if($result->return_value != ""){
                $user = session('authUser');
                $user['isSignedIn'] = true;
                session(['authUser' => $user]);
                return redirect()->back()->with('success',$result->return_value);
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
