<?php

namespace App\Http\Requests;

use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class LeaveDetailsRequest extends FormRequest
{
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
    public function rules()
    {
        return [
            'leaveType' => [
                'required',
                'string',
                // function ($attribute, $value, $fail) {
                //     $leaveTypeData = $this->bcService->callPage(LeaveType::wsName(), [
                //         '$filter' => "Code eq '{$value}'",
                //         '$top' => 1,
                //     ]);
                //     if (empty($leaveTypeData->value)) {
                //         $fail("The selected leave type is invalid.");
                //     }
                // },
            ],
            'startDate' => [
                'required',
                'date',
                // function ($attribute, $value, $fail) {
                //     try {
                //         Carbon::parse($value);
                //     } catch (\Exception $e) {
                //         $fail("The start date is not a valid date.");
                //     }
                // },
            ],
            'endDate' => [
                'required',
                'date',
                // function ($attribute, $value, $fail) {
                //     try {
                //         Carbon::parse($value);
                //     } catch (\Exception $e) {
                //         $fail("The end date is not a valid date.");
                //     }
                // },
            ],
        ];
    }
}
