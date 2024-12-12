<div class="row justify-content-center">
    <div class="col-12">
        @if(!empty($student_age_28))
            @if(isset($student_age_28['له حق التأجيل']) and !empty($student_age_28['له حق التأجيل']))
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title float-left font-weight-bold">الطلاب الذين سيبلغوا سن ال٢٨ الإسبوع
                            القادم</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-borderless text-center bg-danger">
                            <thead>
                            <tr>
                                <th class="align-middle">اسم الطالب</th>
                                <th class="align-middle">كود الطالب</th>
                                <th class="align-middle">الرقم القومي</th>
                                <th class="align-middle">تاريخ الميلاد</th>
                                <th class="align-middle">حالة التجنيد</th>
                                <th class="align-middle">موقف الطالب من التجنيد</th>
                                <th class="align-middle">ملاحظات التجنيد</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($student_age_28['له حق التأجيل'] as $value)
                                <tr>
                                    <td class="align-middle">{{$value['name']}}</td>
                                    <td class="align-middle"><a href="#">{{$value['username']}}</a></td>
                                    <td class="align-middle">{{$value['national_id']}}</td>
                                    <td class="align-middle">{{$value['birth_date']}}</td>
                                    <td class="align-middle">{{$value['enlistment_status']}}</td>
                                    <td class="align-middle">{{$value['position_of_recruitment']}}</td>
                                    <td class="align-middle">{{$value['recruitment_notes']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if(isset($student_age_28['اعفاء مؤقت']) and !empty($student_age_28['اعفاء مؤقت']))
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title float-left font-weight-bold">الطلاب الذين سيتم انهاء اعفتهم</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-borderless text-center bg-danger">
                            <thead>
                            <tr>
                                <th class="align-middle">اسم الطالب</th>
                                <th class="align-middle">كود الطالب</th>
                                <th class="align-middle">الرقم القومي</th>
                                <th class="align-middle">تاريخ الميلاد</th>
                                <th class="align-middle">حالة التجنيد</th>
                                <th class="align-middle">موقف الطالب من التجنيد</th>
                                <th class="align-middle">تاريخ الانتهاء الاعفاء</th>
                                <th class="align-middle">ملاحظات التجنيد</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($student_age_28['اعفاء مؤقت'] as $value)
                                <tr>
                                    @foreach($value as $v)
                                        <td class="align-middle">{{$v}}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
