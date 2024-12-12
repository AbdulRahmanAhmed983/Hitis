@extends('layout.layout')
@section('title', 'مراجعة البيانات الشخصية')
@section('styles')
@endsection
@section('content')
    <div class="alert alert-warning col-12 text-center">
        <h4>برجاء مراجعة شئون الطلاب في حالة وجود خطأ في البيانات</h4>
    </div>
    @if(array_search(true,$data_errors))
        <div class="alert alert-danger col-12 text-center">
            <h4>يرجى التحقق من البيانات ذات اللون الأحمر</h4>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">اسم الطالب</label>
                        <h5 type="text" class="text-center" id="name">{{$student['name']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="photo" @class(['text-danger'=>$data_errors['photo']])>صورة الطالب</label>
                        @if(!$data_errors['photo'])
                            <img id="old_photo" src="{{$student['photo']}}" alt="student photo"
                                 class="mx-auto d-block" style="max-width: 70%">
                        @else
                            <div class="alert alert-danger col-12 text-center">
                                <h5>لا توجد صوره</h5>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="religion">الديانة</label>
                        <h5 type="text" class="text-center" id="religion">{{$student['religion']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="birth_date">تاريخ الميلاد</label>
                        <h5 type="text" class="text-center" id="birth_date">{{$student['birth_date']}}</h5>
                    </div>
                    <div @class(['form-group','text-danger'=>$data_errors['certificate_obtained']])>
                        <label for="certificate_obtained">الشهادة الحاصل عليها</label>
                        <h5 type="text" class="text-center"
                            id="certificate_obtained">{{$student['certificate_obtained']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="certificate_obtained_date">تاريخ الحصول علي الشهادة</label>
                        <h5 type="text" class="text-center"
                            id="certificate_obtained_date">{{$student['certificate_obtained_date']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="certificate_seating_number">رقم جلوس الشهادة</label>
                        <h5 type="text" class="text-center"
                            id="certificate_seating_number">{{$student['certificate_seating_number']}}</h5>
                    </div>
                    <div @class(['form-group','text-danger'=>$data_errors['certificate_degree']])>
                        <label for="certificate_degree">مجموع الطالب</label>
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 type="text" class="text-center"
                                    id="certificate_degree">{{$student['certificate_degree']}}</h5>
                            </div>
                            <span style="font-size: 30px;">&#8260;</span>
                            <div class="col">
                                <h5 type="text" class="text-center"
                                    id="certificate_degree_total">{{$student['certificate_degree_total']}}</h5>
                            </div>
                            <span class="font-weight-bold">النسبه المئويه</span>
                            <div class="col">
                                <h5 type="text" class="text-center" id="certificate_degree_percentage">
                                    {{$student['certificate_degree_percentage']}}%</h5>
                            </div>
                        </div>
                    </div>
                    @if($data_show['english_degree'])
                        <div @class(['form-group','text-danger'=>$data_errors['english_degree']])>
                            <label for="english_degree">درجة اللغه الإنجليزية</label>
                            <h5 type="text" class="text-center" id="english_degree">{{$student['english_degree']}}</h5>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-body">
                    <div @class(['form-group','text-danger'=>$data_errors['email']])>
                        <label for="email">البريد الإلكتروني</label>
                        <h5 type="text" class="text-center" id="email">{{$student['email']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="mobile">موبايل الطالب</label>
                        <h5 type="text" class="text-center" id="mobile">{{$student['mobile']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="landline_phone">التليفون الارضي</label>
                        <h5 type="text" class="text-center" id="landline_phone">{{$student['landline_phone']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="father_profession">مهنة ولي الامر</label>
                        <h5 type="text" class="text-center"
                            id="father_profession">{{$student['father_profession']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="parents_phone1">تليفون ولي الامر الاول</label>
                        <h5 type="text" class="text-center" id="parents_phone1">{{$student['parents_phone1']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="parents_phone2">تليفون ولي الامر الثاني</label>
                        <h5 type="text" class="text-center" id="parents_phone2">{{$student['parents_phone2']}}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="form-group">
                        <label for="birth_country">دولة الميلاد</label>
                        <h5 type="text" class="text-center" id="birth_country">{{$student['birth_country']}}</h5>
                    </div>
                    @if($data_show['birth_province'])
                        <div @class(['form-group','text-danger'=>$data_errors['birth_province']])>
                            <label for="birth_province">محافظة الميلاد</label>
                            <h5 type="text" class="text-center" id="birth_province">{{$student['birth_province']}}</h5>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="address">العنوان</label>
                        <h5 type="text" class="text-center" id="address">{{$student['address']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="nationality">الجنسية</label>
                        <h5 type="text" class="text-center" id="nationality">{{$student['nationality']}}</h5>
                    </div>
                    @if($data_show['national_id'])
                        <div class="form-group">
                            <label for="national_id">الرقم القومي</label>
                            <h5 type="text" class="text-center" id="national_id">{{$student['national_id']}}</h5>
                        </div>
                    @else
                        <div class="form-group">
                            <label for="national_id">رقم الباسبور</label>
                            <h5 type="text" class="text-center" id="national_id">{{$student['national_id']}}</h5>
                        </div>
                    @endif
                    @if($data_show['national_id'])
                        <div @class(['form-group','text-danger'=>$data_errors['issuer_national_number']])>
                            <label for="issuer_national_number">جهة الإصدار الرقم القومي</label>
                            <h5 type="text" class="text-center"
                                id="issuer_national_number">{{$student['issuer_national_number']}}</h5>

                        </div>
                    @endif
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-body">
                    <div class="form-group">
                        <label for="gender">الجنس</label>
                        <h5 type="text" class="text-center" id="gender">{{$student['gender']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="specialization">التخصص</label>
                        <h5 type="text" class="text-center" id="specialization">{{$student['specialization']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="study_group">الفرقة الدراسية</label>
                        <h5 type="text" class="text-center" id="study_group">{{$student['study_group']}}</h5>
                    </div>
                    <div class="form-group">
                        <label for="academic_advisor">المرشد الاكاديمي</label>
                        <h5 type="text" class="text-center" id="academic_advisor">{{$student['academic_advisor']}}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
