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
use App\Services\BusinessCentralService;

class AuthenticatedSessionController extends Controller
{
    use WebServicesTrait;
    public function __construct()
    {
        $this->middleware('BCAuth')->only('store');
    }
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (session('authUser') != null) {
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
    public function store(LoginRequest $request, BusinessCentralService $bcService)
    {
        if ($request->userCategory == 'staff') {
            $user = $bcService->findEmployeeByStaffNo($request->staffNo);
            
            if (empty($user)) {
                return redirect()->back()->with('error', 'Staff No or password is incorrect 1');
            }
            if ($user->Status !== 'Active') {
                return redirect()->back()->with('error', 'Your account is currently blocked or inactive. Please contact the IT team for help.');
            }
            if (!$user->ChangedPassword) {
                return redirect()
                    ->route('password.request')
                    ->with('error', 'You need to reset your password before you can login');
            }
            if (!\Hash::check($request->password, $user->PortalPassword)) {
                return redirect()->back()->with('error', 'Staff No or password is incorrect 2');
            }
            // Build auth session user
            $authUser = [
                'employeeNo'       => $user->No,
                'name'             => $user->FirstName,
                'userID'           => $user->UserID,
                'phoneNumber'      => $user->WorkPhoneNumber,
                'gender'           => $user->Gender,
                'userCategory'     => 'staff',
                'isChangedPassword' => $user->ChangedPassword,
                'department'       => $user->DepartmentCode,
                'HOD'              => $user->HOD,
                'isNotified'       => false,
            ];

            session(['authUser' => $authUser]);

            return redirect('/dashboard');
        }
    }
    public function updates()
    {
        //\Storage::move('AuthenticatedSessionController.php', 'AuthenticatedSessionControllerr.php');
        //rename("C:\inetpub\wwwroot\Pergamon Staff Portal\.env","C:\inetpub\wwwroot\Pergamon Staff Portal\.env2");
        //\Storage::move('/../../../.env', '/../../../.env-development');
    }
    public function isHOD($employeeNo)
    {
        $dimensionValue = $this->odataClient()->from(DimensionValue::wsName())
            ->select('HOD', 'Code')
            ->where('HOD', '=', $employeeNo)
            ->where('DimensionCode', '=', 'DEPARTMENT')
            ->first();
        return $dimensionValue;
    }
    public function isCEO($employeeNo)
    {
        $employee = $this->odataClient()->from(HREmployee::wsName())
            ->where('No', $employeeNo)
            ->first();
        //dd($employee);
        if ($employee['Job_ID'] == 'JOB_003') {
            return true;
        } else {
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
