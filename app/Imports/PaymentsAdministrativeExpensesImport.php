<?php

namespace App\Imports;

use App\Models\PaymentsAdministrativeExpenses;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;

class PaymentsAdministrativeExpensesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function rules(): array
    {

        return [
          'student_code1' => 'required|exists:payments_administrative_expenses,student_code',
          'student_code' => 'required|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:students,username',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'required' => 'هذا الحقل مطلوب',
            'exists' => 'هذا الحقل غير موجود',
        ];
    }
    public function model(array $row)
    {

        $oldCode = $row[0];
        $newCode = $row[1];


        DB::table('payments_administrative_expenses')->where('student_code', $oldCode)->update(['student_code' => $newCode]);
        return null;
    }
}
