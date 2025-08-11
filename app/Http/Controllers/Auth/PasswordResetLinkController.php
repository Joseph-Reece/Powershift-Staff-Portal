<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Models\HREmployee;

class PasswordResetLinkController extends Controller
{
    use WebServicesTrait;
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
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
        ]);
        $user = $this->odataClient()->from(HREmployee::wsName())->where('No',$request->staffNo)->first();
        if($user == null){
            return redirect()->back()->with("error","Invalid Staff No.");
        }
        if($user['E_Mail'] == null){
            return redirect()->back()->with("error","Your email is not set in your employee details. Kindly contact the IT office for help.");
        }
        try{
            $service = $this->MySoapClient(config('app.cuStaffPortal'));
            $params = new \stdClass();
            $resetToken = rand(10000,99999);
            $params->password_token = $resetToken;
            $params->staffNo = $request->staffNo;
            $result = $service->UpdatePasswordToken($params);
            if($result->return_value == true){
                $service2 = $this->MySoapClient(config('app.cuStaffPortal'));
                $params1 = new \stdClass();
                $params1->receiver = $user['E_Mail'];
                $params1->subject = "Password Reset Token";
                $message = "Your new password reset token is ".$resetToken;
                $params1->message = $message;
                $result2 = $service2->SendEmail($params1);
                // $service2 = $this->MySoapClient(config('app.cuStaffPortal'));
                // $params1 = new \stdClass();
                // $params1->userType = 'staff';
                // $params1->phoneNumber = $user['Cellular_Phone_Number'];
                // $message = "Your new password reset token is ".$resetToken;
                // $params1->smsMessage = $message;
                // $params1->no = $request->staffNo;
                // $result2 = $service2->SendSMS($params1);
                if($result2->return_value == 'sent'){
                    $request->staffNo = str_replace("/","__",$request->staffNo);
                    return redirect('/reset-password/'.$request->staffNo)->with("success","We have sent a reset token to your phone via SMS. Kindly check your phone to get the reset token");
                }
            }
            return redirect()->back()->with('error','Oops! Something went wrong.'.config('app.errors')['persists']);
        }
        catch (\SoapFault $e) {
            $this->InsertLog("SOAP Error",$e->faultstring, Request()->Route()->getActionName());
            $errorMsg = $e->faultstring;
            return redirect()->back()->with('error',$errorMsg);
        }
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        /*$status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);*/
    }
}
