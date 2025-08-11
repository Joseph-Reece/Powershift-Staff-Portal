<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\HREmployee;

class NewPasswordController extends Controller
{
    use WebServicesTrait;
    public function __construct()
    {
        $this->middleware('isAuth')->only('changePassword','updatePassword');
        $this->middleware('staff')->only('changePassword','updatePassword');
    }
    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'staffNo' => 'required',
            'resetToken' => 'required',
            'password' => 'required|string|confirmed|min:8',
        ]);
        $request->staffNo = str_replace("__","/",$request->staffNo);
        $user = $this->odataClient()->from(HREmployee::wsName())->where('No',$request->staffNo)->where('Status','Active')->first();
        if($user == null){
            return redirect()->back()->with("error","Invalid Staff No.");
        }
        if($user['Reset_Token'] != $request->resetToken || $user['Token_Expired']){
            return redirect()->back()->with("error","Reset token is wrong or it has already expired. Kindly use the last sent token.");
        }
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $params->staffNo = $request->staffNo;
            $params->password = bcrypt($request->password);
            $result = $service->UpdatePassword($params);
            if($result->return_value == true){
                session()->forget('authUser');
                return redirect('/login')->with("success","Password updated successfully. Kindly login using your new password");
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        /*$status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );*/

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        /*return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);*/
    }
    public function changePassword(){
        return view('auth.change-password');
    }
    public function updatePassword(REQUEST $request){
        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required|same:newPassword',
        ]);
        $user = $this->odataClient()->from(HREmployee::wsName())->where('No',session('authUser')['employeeNo'])->where('Portal_Password',$request->currentPassword)->first();
        // if (Hash::check($request->currentPassword, $user->Password)) {
            //$newPassword = bcrypt($request->newPassword);
        if ($user != null) {
            try{
                $service = $this->MySoapClient(config('app.cuStaffPortal'));
                $params = new \stdClass();
                $params->staffNo = session('authUser')['employeeNo'];
                $params->password = $request->newPassword;
                $result = $service->UpdatePassword($params);
                if($result->return_value == true){
                    session()->forget('authUser');
                    return redirect('/login')->with("success","Password updated successfully. Kindly login using your new password");
                }
                return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
            }
            catch (\SoapFault $e) {
                $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
                $errorMsg = $e->faultstring;
                return redirect()->back()->with('error',$errorMsg);
            }
        }
        return redirect()->back()->with('error','Invalid current password');
    }
}
