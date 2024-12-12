@extends('layout.layout')
@section('title', 'قائمه الطلاب')
@section('styles')
<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <h4>إجمالي عدد الطلاب {{$students->total()}}</h4>
        </div>
    </div>

    <div class="card card-primary text-capitalize">
        <div class="card-header">
            <h2 class="card-title">الطلاب الذين لم يكتمل بياناتهم</h2>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group col-12">
                <label for="username">اكواد الطلاب</label>
                <select class="select2" multiple="multiple"
                        data-placeholder="ادخل اسم او كود الطالب"
                        style="width: 100%;" id="username">
                        @foreach($students  as $student)
                        @if ($student->gender == NULL)
                        <option  selected value="{{$student->username}}">{{$student->username}}</option>
                        @endif
                        @endforeach
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>


    <div class="card card-primary collapsed-card">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title text-capitalize">data filter</h2>
            <div class="card-tools">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center justify-content-around">
                <div class="form-group col-lg-3 col-md-6">
                    <label for="studying_status">الحالة الدراسية</label>
                    <select form="search" class="custom-select" name="studying_status" id="studying_status">
                        <option value=""></option>
                        @foreach($filter_data['studying_status'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['studying_status']) and $filter['studying_status'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="study_group">الفرقة الدراسية</label>
                    <select form="search" class="custom-select" name="study_group" id="study_group">
                        <option value=""></option>
                        @foreach($filter_data['study_group'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['study_group']) and $filter['study_group'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="student_classification">تصنيف الطلاب</label>
                    <select form="search" class="custom-select" name="student_classification"
                            id="student_classification">
                        <option value=""></option>
                        @foreach($filter_data['student_classification'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['student_classification']) and $filter['student_classification'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="specialization">التخصص</label>
                    <select form="search" class="custom-select" name="specialization"
                            id="specialization">
                        <option value=""></option>
                        @foreach($filter_data['specialization'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['specialization']) and $filter['specialization'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="departments">الشعبة</label>
                    <select form="search" class="custom-select" name="departments_id" id="departments">
                        <option value=""></option>
                        @foreach($departments as $data)
                            <option value="{{$data->id}}"
                                @if((isset($filter_data['departments_id']) and $filter_data['departments_id'] == $data->id)) selected @endif>
                                {{$data->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="apply_classification">تصنيف التقديم</label>
                    <select form="search" class="custom-select" name="apply_classification" id="apply_classification">
                        <option value=""></option>
                        @foreach($filter_data['apply_classification'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['apply_classification']) and $filter['apply_classification'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="academic_advisor">المرشد الاكاديمي</label>
                    <select form="search" class="custom-select" name="academic_advisor" id="academic_advisor">
                        <option value=""></option>
                        @foreach($filter_data['academic_advisor'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['academic_advisor']) and $filter['academic_advisor'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="grade">التقدير</label>
                    <select form="search" class="custom-select" name="grade" id="grade">
                        <option value=""></option>
                        @foreach($filter_data['grade'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['grade']) and $filter['grade'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="nationality">الجنسية</label>
                    <select form="search" class="custom-select" name="nationality" id="nationality">
                        <option value=""></option>
                        @foreach($filter_data['nationality'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['nationality']) and $filter['nationality'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="birth_country">دولة الميلاد</label>
                    <select form="search" class="custom-select" name="birth_country" id="birth_country">
                        <option value=""></option>
                        @foreach($filter_data['birth_country'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['birth_country']) and $filter['birth_country'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="birth_province">محافظة الميلاد</label>
                    <select form="search" class="custom-select" name="birth_province" id="birth_province">
                        <option value=""></option>
                        @foreach($filter_data['birth_province'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['birth_province']) and $filter['birth_province'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="issuer_national_number">جهة الإصدار الرقم القومي</label>
                    <select form="search" class="custom-select" name="issuer_national_number"
                            id="issuer_national_number">
                        <option value=""></option>
                        @foreach($filter_data['issuer_national_number'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['issuer_national_number']) and $filter['issuer_national_number'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="immigrant_student">طالب وافد</label>
                    <select form="search" class="custom-select" name="immigrant_student" id="immigrant_student">
                        <option value=""></option>
                        @foreach($filter_data['immigrant_student'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['immigrant_student']) and $filter['immigrant_student'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="gender">الجنس</label>
                    <select form="search" class="custom-select" name="gender" id="gender">
                        <option value=""></option>
                        @foreach($filter_data['gender'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['gender']) and $filter['gender'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="certificate_obtained">الشهادة الحاصل عليها</label>
                    <select form="search" class="custom-select" name="certificate_obtained"
                            id="certificate_obtained">
                        <option value=""></option>
                        @foreach($filter_data['certificate_obtained'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['certificate_obtained']) and $filter['certificate_obtained'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="certificate_obtained_date">تاريخ الحصول علي الشهادة</label>
                    <select form="search" class="custom-select" name="certificate_obtained_date"
                            id="certificate_obtained_date">
                        <option value=""></option>
                        @foreach($filter_data['certificate_obtained_date'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['certificate_obtained_date']) and $filter['certificate_obtained_date'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="registration_date">تاريخ قيد الطالب بالمعهد</label>
                    <select form="search" class="custom-select" name="registration_date" id="registration_date">
                        <option value=""></option>
                        @foreach($filter_data['registration_date'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['registration_date']) and $filter['registration_date'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="father_profession">مهنة ولي الامر</label>
                    <select form="search" class="custom-select" name="father_profession" id="father_profession">
                        <option value=""></option>
                        @foreach($filter_data['father_profession'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['father_profession']) and $filter['father_profession'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="religion">الديانة</label>
                    <select form="search" class="custom-select" name="religion" id="religion">
                        <option value=""></option>
                        @foreach($filter_data['religion'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['religion']) and $filter['religion'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="enlistment_status">حالة التجنيد</label>
                    <select form="search" class="custom-select" name="enlistment_status" id="enlistment_status">
                        <option value=""></option>
                        @foreach($filter_data['enlistment_status'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['enlistment_status']) and $filter['enlistment_status'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="recruitment_area">منطقة التجنيد</label>
                    <select form="search" class="custom-select" name="recruitment_area" id="recruitment_area">
                        <option value=""></option>
                        @foreach($filter_data['recruitment_area'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['recruitment_area']) and $filter['recruitment_area'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="military_education">التربيه العسكريه</label>
                    <select form="search" class="custom-select" name="military_education" id="military_education">
                        <option value=""></option>
                        @foreach($filter_data['military_education'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['military_education']) and $filter['military_education'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row align-content-center">
                <div class="col-lg-3 col-md-6">
                    <select form="search" class="custom-select" name="per_page" id="per_page">
                        @foreach($filter_data['per_page'] as $data)
                            <option
                                value="{{$data}}" {{($items_per_pages == $data) ? 'selected': ''}}>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" form="search" class="btn btn-primary mx-3">بحث</button>
                <button type="button" id="reset" form="search" class="btn btn-primary mx-3">مسح</button>
            </div>
        </div>
    </div>
    <x-data-table :keys="$keys" :primaryKey="$primaryKey" :hiddenKeys="$hidden_keys" :removedKeys="$removed_keys"
                  :pages="$students" :search="$search" edit="student.change.data" delete="student.delete"
                  deleteMessage='هل أنت متأكد من حذف الطالب primaryKey ؟'></x-data-table>
@endsection
@section('scripts')
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>

    <script>
        @if($errors->any())
            toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        toastr.options.timeOut = 0;
        toastr.options.extendedTimeOut = 0;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-right";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif

        $('form#search').on('submit', function () {
            $('input[form="search"],select[form="search"]').each(function () {
                if ($(this).val() === '')
                    $(this).prop("disabled", true);
            });
            return true;
        });

        $('#reset').on('click', function () {
            $('input[form="search"],select[form="search"]').not('#per_page').val('');
        });


        $(function () {
            $('#username').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '{{route('student.datalist')}}',
                    dataType: "json",
                    type: "GET",
                    delay: 250,
                    minimumInputLength: 4,
                    data: function (params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name + '-' + item.username,
                                    value: item.username,
                                    id: item.username
                                }
                            })
                        };
                    }
                }
            });
        });
        $(function () {
            $('#std_code').select2({
                theme: 'bootstrap4',
            });
        });



    </script>
    @yield('component-style')
    @yield('component-script')

    <style>
        .select2-selection__choice__remove{
            display: none !important;
        }
    </style>
@endsection
