<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\HREmployee;

class ProfileController extends Controller
{
    use WebServicesTrait;

    public function __construct()
    {
        $this->middleware('isAuth');
        $this->middleware('staff');
    }
    public function show(){
        $employee = $this->odataClient()->from(HREmployee::wsName())
        ->where('No',session('authUser')['employeeNo'])
        ->first();
        if($employee == null){
            return redirect('/dashboard')->with('error','Employee not found.');
        }
        $data = [
            'employee' => $employee
        ];
        return view('staff.profile.show')->with($data);
    }
    public function getPassportPhoto($staffNo){
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->no = $staffNo;
            $params->userType = 'staff';
            $result = $service->GetPassportPhoto($params);
            if($result->return_value != ''){
                return $result->return_value;
            }
            return null;
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
    }
}
