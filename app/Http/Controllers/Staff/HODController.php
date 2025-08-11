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
use App\Models\DimensionValue;
use Carbon\Carbon;

class HODController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
        $this->middleware('isHOD');
    }
    public function staff(){
        $status = null;
        $title = null;
        if(isset($_GET['status'])){
            $status = $_GET['status'];
        }
        if($status == null){
            $staff = $this->odataClient()->from(HREmployee::wsName())
            ->select('No','First_Name','Middle_Name','Last_Name',)
            ->where('No','!=',session('authUser')['employeeNo'])
            ->where('Status','=','Active')
            //->where('Department_Code','=',session('authUser')['HOD']['Code'])
            ->where('Department_Code','=',session('authUser')['department'])
            ->get();
        }else{
            $staff = [];
            if($status == 'onLeave'){
                $title = "Staff on leave";
                $employees = $this->odataClient()->from(HREmployee::wsName())
                ->select('No','First_Name','Middle_Name','Last_Name',)
                ->where('No','!=',session('authUser')['employeeNo'])
                ->where('Status','=','Active')
                //->where('Department_Code','=',session('authUser')['HOD']['Code'])
                ->where('Department_Code','=',session('authUser')['department'])
                ->get();

                foreach($employees as $employee){
                    $isOnLeave = app('App\Http\Controllers\Staff\LeaveController')->isOnLeave($employee->No);
                    if($isOnLeave){
                        $staff[] = $employee;
                    }
                }
            }
        }
        $data = [
            'staff' => $staff,
            'title' => $title,
        ];
        return view('staff.hod.department-staff')->with($data);
    }
    public function employeeDetails($staffNo){
        $employee = $this->odataClient()->from(HREmployee::wsName())
        ->select('No','First_Name','Middle_Name','Last_Name',)
        ->where('No','=',$staffNo)
        ->where('Status','=','Active')
        // ->where('Department_Code','=',session('authUser')['HOD']['Code'])
        ->where('Department_Code','=',session('authUser')['department'])
        ->first();
        if($employee == null){
            return redirect()->back()->with('error','Employee details not found');
        }
        $data = [
            'employee' => $employee
        ];
        return view('staff.hod.employee-details')->with($data);
    }

}
