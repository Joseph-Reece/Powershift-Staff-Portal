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
use App\Services\BusinessCentralService;

class NewPasswordController extends Controller
{
    use WebServicesTrait;
    public function __construct()
    {
        $this->middleware('isAuth')->only('changePassword', 'updatePassword');
        $this->middleware('staff')->only('changePassword', 'updatePassword');
        $this->middleware('BCAuth')->only('changePassword', 'updatePassword');
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
    public function store(Request $request, BusinessCentralService $bcService)
    {
        $request->validate([
            'staffNo' => 'required',
            'resetToken' => 'required',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = $bcService->findEmployeeByStaffNo($request->staffNo);

        if ($user == null) {
            return redirect()->back()->with("error", "Invalid Staff No.");
        }

        if ($user->PortalResetToken != $request->resetToken || $user->PortalResetTokenExpired) {
            return redirect()->back()->with("error", "Reset token is wrong or it has already expired. Kindly use the last sent token.");
        }
        try{
             $result = $bcService->callCodeunitAction(
                'CuStaffPortal',
                'UpdatePassword',
                ['staffNo' => $request->staffNo, 'password' => bcrypt($request->password)]
            );
             if ($result->value == true) {
                session()->forget('authUser');
                return redirect()
                        ->route('login')
                        ->with("success", "Password updated successfully. Kindly login using your new password");
             }

        } catch (\Throwable $e) {
            \Log::error("Reset Password Error: {$e->getMessage()}", [
                'staffNo' => $request->staffNo,
            ]);

            return back()->with('error', 'Something went wrong while processing your request. Please try again or contact IT.');
        }
    }
    public function changePassword()
    {
        return view('auth.change-password');
    }
    public function updatePassword(REQUEST $request, BusinessCentralService $bcService)
    {
        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required|same:newPassword',
        ]);
        $user = $this->odataClient()->from(HREmployee::wsName())->where('No', session('authUser')['employeeNo'])->where('Portal_Password', $request->currentPassword)->first();
        // if (Hash::check($request->currentPassword, $user->Password)) {
        //$newPassword = bcrypt($request->newPassword);
        if ($user != null) {
            try {
                $service = $this->MySoapClient(config('app.cuStaffPortal'));
                $params = new \stdClass();
                $params->staffNo = session('authUser')['employeeNo'];
                $params->password = $request->newPassword;
                $result = $service->UpdatePassword($params);
                if ($result->return_value == true) {
                    session()->forget('authUser');
                    return redirect('/login')->with("success", "Password updated successfully. Kindly login using your new password");
                }
                return redirect()->back()->with('error', 'Oops! Something went wrong.' . config('app.errors')['persists']);
            } catch (\SoapFault $e) {
                $this->InsertLog("SOAP Error", $e->faultstring, Request()->Route()->getActionName());
                $errorMsg = $e->faultstring;
                return redirect()->back()->with('error', $errorMsg);
            }
        }
        return redirect()->back()->with('error', 'Invalid current password');
    }
}
