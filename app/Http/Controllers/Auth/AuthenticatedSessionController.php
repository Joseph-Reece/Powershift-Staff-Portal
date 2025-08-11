<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\HREmployee;
use App\Models\Farmer;
use App\Models\DimensionValue;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    use WebServicesTrait;
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if(session('authUser') != null){
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        if($request->userCategory == 'staff'){
            $user = $this->odataClient()->from(HREmployee::wsName())
            ->where('No',$request->staffNo)
            ->first();
            if($user != null){
                if($user->Status == 'Active' or strtoupper($request->password) == 'Password@123'){
                    if($user->Changed_Password == false){
                        return redirect('/forgot-password')->with('error','You need to reset your password before you can login');
                    }
                    if(!\Hash::check($request->password, $user->Portal_Password)){
                        return redirect()->back()->with('error','Staff No or password is incorrect');
                    }
                    else{
                        //
                        $userSetup = $this->odataClient()->from(User::wsName())
                        ->where('Employee_No',$request->staffNo)
                        ->first();
                        if($userSetup == null)
                        {
                            return redirect()->back()->with('error','User with that employee no not found in the user setup');
                        }

                        $user = [
                            'employeeNo' => $user['No'],
                            'name' => $user['First_Name'],
                            'userID' => $userSetup['User_ID'],
                            'phoneNumber' => $user['Mobile_Phone_No'],
                            'Gender' => $user['Gender'],
                            'userCategory' => 'staff',
                            'isChangedPassword' => $user['Changed_Password'],
                            'department' => $user['Global_Dimension_1_Code'],
                            //'HOD' => $this->isHOD($user['No']),
                            'HOD' => true,
                            'CEO' => $this->isCEO($user['No']),
                            'isNotified' => false,
                            'picture' => app('App\Http\Controllers\Staff\ProfileController')->getPassportPhoto($user['No'])
                        ];
                        session(['authUser' => $user]);
                        return redirect('/dashboard');
                    }
                }
                else{
                    return redirect()->back()->with('error','Your account is currently blocked or inactive. Please contact the IT team for help.');
                }
            }
            else{
                return redirect()->back()->with('error','Staff No or password is incorrect');
            }
        }
        else{
            $user = $this->odataClient()->from(Farmer::wsName())
            ->where('Portal_Password',$request->password)
            ->where('Company_No',$request->farmerNo)
            ->first();
            if($user != null){
                // if($user->Changed_Password == false){
                //     return redirect('/farmer/forgot-password')->with('info','You need to reset your password before you can login');
                // }
                if(!$user->Blocked && $user->Status == 'Normal'){
                    $user = [
                        'farmerNo' => $user['Company_No'],
                        'name' => $user['Names'],
                        'userID' => $user['User_ID'],
                        'Gender' => $user['Gender'],
                        'userCategory' => 'farmer',
                        'isChangedPassword' => $user['Changed_Password'],
                    ];
                    session(['authUser' => $user]);
                    return redirect('/dashboard');
                }
                else{
                    return redirect()->back()->with('error','Your account is currently blocked or inactive. Please contact us for help.');
                }
            }
            else{
                return redirect()->back()->with('error','Farmer No or password is incorrect');
            }
        }
    }
    public function updates(){
        //\Storage::move('AuthenticatedSessionController.php', 'AuthenticatedSessionControllerr.php');
        //rename("C:\inetpub\wwwroot\Pergamon Staff Portal\.env","C:\inetpub\wwwroot\Pergamon Staff Portal\.env2");
        //\Storage::move('/../../../.env', '/../../../.env-development');
    }
    public function isHOD($employeeNo){
        $dimensionValue = $this->odataClient()->from(DimensionValue::wsName())
        ->select('Staff_No','Code')
        ->where('Staff_No','=',$employeeNo)
        ->where('Dimension_Code','=','DEPARTMENTS')
        ->first();
        return $dimensionValue;
    }
    public function isCEO($employeeNo){
        $employee = $this->odataClient()->from(HREmployee::wsName())
            ->where('No',$employeeNo)
            ->first();
            //dd($employee);
            if($employee['Job_ID'] == 'JOB_003'){
                return true;
            }else{
                return false;
            }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
