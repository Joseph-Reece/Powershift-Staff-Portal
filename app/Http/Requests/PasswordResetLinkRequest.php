<?php

namespace App\Http\Requests;

use App\Services\BusinessCentralService;
use Illuminate\Foundation\Http\FormRequest;

class PasswordResetLinkRequest extends FormRequest
{
    protected $bcService;

    public function __construct(BusinessCentralService $bcService)
    {
        $this->bcService = $bcService;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'staffNo' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $employee = $this->bcService->findEmployeeByStaffNo($value);
                    if (!$employee) {
                        $fail('Invalid Staff Number.');
                    } elseif (empty($employee->CompanyEMail)) {
                        $fail('Your email is not set in your employee details. Please contact IT.');
                    }
                },
            ],
        ];
    }
}
