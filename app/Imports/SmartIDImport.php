<?php

namespace App\Imports;

use App\Models\SmartID;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SmartIDImport implements ToModel, WithHeadingRow, WithValidation

{
    public function rules(): array
    {
        return [
            'student_code' => 'required|string|exists:students,username',
            'card_code' => 'required|integer',
            ];
    }

    public function customValidationMessages(): array
    {
        return [
            'required' => 'هذا الحقل مطلوب',
            'student_code.exists' => 'هذا الحقل غير موجود',
            'integer.card_code' => 'يجب ان يكون رقم الكارد رقم ',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return SmartID::updateOrCreate(
            ['student_code' => $row['student_code']],
            ['card_code' => $row['card_code']]
        );
        
    }
}
