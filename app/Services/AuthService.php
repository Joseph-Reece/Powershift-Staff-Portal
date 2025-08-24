<?php

namespace App\Services;

use App\Services\BusinessCentralService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AuthService
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }

    /**
     * Initiate a password reset by generating a token and sending it via email.
     *
     * @param string $staffNo
     * @return array{employee: object, token: string}
     * @throws InvalidArgumentException
     */
    public function initiatePasswordReset(string $staffNo): array
    {
        // Fetch employee
        $employee = $this->bcService->findEmployeeByStaffNo($staffNo);
        if (!$employee) {
            throw new InvalidArgumentException('Invalid Staff Number.');
        }
        if (empty($employee->CompanyEMail)) {
            throw new InvalidArgumentException('Employee email not set.');
        }

        // Generate secure token
        $resetToken = rand(10000, 99999);

        // Update token in Business Central
        $result = $this->bcService->callCodeunitAction(
            'CuStaffPortal',
            'UpdatePasswordToken',
            ['staffNo' => $staffNo, 'password_token' => (string)$resetToken]
        );

        if (!$result || !isset($result->value) || $result->value !== true) {
            Log::error('Failed to update password token in Business Central', [
                'staffNo' => $staffNo,
                'result' => $result,
            ]);
            throw new InvalidArgumentException('Failed to generate password reset token.');
        }

        // Send email
        $emailResult = $this->bcService->callCodeunitAction(
            'CuStaffPortal',
            'SendEmail',
            [
                'recipients' => $employee->CompanyEMail,
                'subject' => config('auth.password_reset.email_subject', 'Password Reset Token'),
                'message' => $this->buildEmailMessage($resetToken),
                'ccRecipients' => '',
            ]
        );

        if (!$emailResult || !isset($emailResult->value) || $emailResult->value !== true) {
            Log::error('Failed to send password reset email', [
                'staffNo' => $staffNo,
                'email' => $employee->CompanyEMail,
                'result' => $emailResult,
            ]);
            throw new InvalidArgumentException('Failed to send password reset email.');
        }

        return [
            'employee' => $employee,
            'token' => $resetToken,
        ];
    }

    /**
     * Build the email message for password reset.
     */
    private function buildEmailMessage(string $token): string
    {
        $template = config('auth.password_reset.email_template', 'Your password reset token is {token}.');
        return str_replace('{token}', $token, $template);
    }
}