<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetLinkRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class PasswordResetLinkController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(PasswordResetLinkRequest $request)
    {
        try {
            $result = $this->authService->initiatePasswordReset($request->staffNo);

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Password reset token sent to your email.',
            //     'data' => [
            //         'staffNo' => $result['employee']->No,
            //     ],
            // ]);
            return redirect()
                ->route('password.reset', $result['employee']->No)
                ->with('success', 'We have sent a reset token to your email. Please check your inbox.');

        } catch (\Exception $e) {
            \Log::error('Password Reset Error', [
                'staffNo' => $request->staffNo,
                'error' => $e->getMessage(),
            ]);

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Failed to process password reset request. Please try again or contact IT.',
            // ], 400);
            // return redirect()
            //     ->back()
            //     ->with('error', 'Failed to process password reset request. Please try again or contact IT.');
            return back()->withErrors([
                'staffNo' => 'Failed to process password reset request. Please try again or contact IT.',
            ]);
        }
    }
}
