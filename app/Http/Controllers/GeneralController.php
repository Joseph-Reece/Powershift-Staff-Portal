<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ApprovalEntry;
use App\Models\HRLeaveRequisition;
use App\Models\ImprestHeader;
use App\Models\ImprestSurrenderHeader;
use App\Models\PurchaseRequisitionHeader;
use App\Models\StoreRequisitionHeader;
use App\Models\ClaimHeader;
use App\Models\Farmer;
use App\Models\TransportRequisition;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
class GeneralController extends Controller
{
    use WebServicesTrait;
    public function __construct()
    {
        $this->middleware('isAuth')->only('dashboard');
    }
    public function dashboard(){
        if(session('authUser') != null){
            if(session('authUser')['userCategory'] == 'staff'){
                $data = [];
                return view('staff.dashboard')->with($data);
            }
            elseif(session('authUser')['userCategory'] == 'farmer'){
                $farmer = $this->odataClient()->from(Farmer::wsName())->where('Company_No',session('authUser')['farmerNo'])->first();
                $data = [
                    'farmer' => $farmer
                ];
                return view('farmer.dashboard')->with($data);
            }
        }else{
            return redirect('/')->with('error', 'Login to continue');
        }
    }
    public function SendSMS(){
        $apiKey = $_POST['apiKey'];
        $from = 'WAKULIMALTD';
        $username = 'wakulimadairy';
        $message = $_POST['message'];
        $to = $_POST['to'];
        $data = [
            'to' => $to,
            'message' => $message,
            'from' => $from,
            'username' => $username,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.africastalking.com/version1/messaging');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Accept: application/json';
        $headers[] = 'apiKey: '.$apiKey;
        // $headers[] = 'authorization:Bearer '.$apiKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        //return true;
        if (curl_errno($ch)) {
            $this->InsertLog("SMS Error",curl_error($ch),'');
            return false;
        }
        curl_close($ch);
        return true;
    }
    public function logout(){
        \Auth::logout();
        session()->flush();
        return redirect('/');
    }

}
