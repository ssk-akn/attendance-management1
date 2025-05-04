<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class CorrectionRequest extends FormRequest
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
            'new_work_start' => 'required|date_format:H:i',
            'new_work_end' => 'required|date_format:H:i',
            'remarks' => 'required',
            'new_break_start' => 'nullable|array',
            'new_break_end' => 'nullable|array',
            'new_break_start.*' => 'date_format:H:i',
            'new_break_end.*' => 'date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'new_work_start.required' => '出勤時間を入力してください',
            'new_work_end.required' => '退勤時間を入力してください',
            'remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $workStart = Carbon::createFromFormat('H:i', $this->input('new_work_start'));
                $workEnd = Carbon::createFromFormat('H:i', $this->input('new_work_end'));
            }catch (\Exception $e) {
                return;
            }

            if ($workStart->gte($workEnd)) {
                $validator->errors()->add('new_work_start', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $breakStarts = $this->input('new_break_start', []);
            $breakEnds = $this->input('new_break_end', []);

            foreach ($breakStarts as $index => $startValue) {
                if (!isset($breakEnds[$index])) {
                    continue;
                }
                try {
                    $breakStart = Carbon::createFromFormat('H:i', $startValue);
                    $breakEnd = Carbon::createFromFormat('H:i', $breakEnds[$index]);
                } catch (\Exception $e) {
                    continue;
                }

                if ($breakStart->lt($workStart) || $breakStart->gt($workEnd) || $breakEnd->gt($workEnd) || $breakEnd->lt($workStart)) {
                    $validator->errors()->add("new_break_start.$index", '休憩時間が勤務時間外です');
                }
            }
        });
    }
}
