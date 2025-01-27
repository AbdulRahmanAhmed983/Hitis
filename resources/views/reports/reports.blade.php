@extends('layout.layout')
@section('title', 'تحميل التقارير')
@section('styles')
@endsection
@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success mt-3 text-center col-12">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mt-3 text-center">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
        </div>
    @endif
    <div class="row justify-content-around">
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">سجل التجنيد</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="enlistment" method="post" action="{{route('enlistment.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="enlistment_status">حالة التجنيد</label>
                                <select class="custom-select" name="enlistment_status" id="enlistment_status" required>
                                    <option value="" hidden></option>
                                    <option value="له حق التأجيل">له حق التأجيل</option>
                                    <option value="اعفاء مؤقت">اعفاء مؤقت</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="enlistment" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">سجل الدراسي</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="study" method="post" action="{{route('study.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                        @php
                                            $deptName = '';
                                            switch($value) {
                                                case 1:
                                                    $deptName = "عام";
                                                    break;
                                                case 2:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                                    break;
                                                case 3:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                                    break;
                                                case 4:
                                                    $deptName = "سياحة عام";
                                                    break;
                                                case 5:
                                                    $deptName = "الدراسات السياحية و ادارة الضيافة";
                                                    break;
                                                case 6:
                                                    $deptName = "ارشاد سياحي";
                                                    break;
                                                case 7:
                                                    $deptName = "ادارة الاعمال السياحية";
                                                    break;
                                                case 8:
                                                    $deptName = "ادارة الضيافة";
                                                    break;
                                                case 9:
                                                    $deptName = "الدراسات السياحية";
                                                    break;
                                                case 10:
                                                    $deptName = "ادارة الفنادق";
                                                    break;
                                                case 11:
                                                    $deptName = "ادارة شركات الطيران";
                                                    break;
                                                case 12:
                                                    $deptName = "ادارة المطاعم";
                                                    break;
                                                case 13:
                                                    $deptName = "ادارة شركات الملاحة";
                                                    break;
                                                case 14:
                                                    $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                                    break;
                                                case 15:
                                                    $deptName = "ادارة الاحداث الخاصة";
                                                    break;
                                                case 16:
                                                    $deptName = "ادارة فنون الطهي";
                                                    break;
                                                case 17:
                                                    $deptName = "ادارة الاحداث الرياضية";
                                                    break;
                                                case 18:
                                                    $deptName = "ادارة خدمة العملاء";
                                                    break;
                                            }
                                        @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="study" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">كشف الطلاب المسددين</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="paying-students-subject" method="post"
                          action="{{route('paying.students.subject.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                    @php
                                    $deptName = '';
                                    switch($value) {
                                        case 1:
                                            $deptName = "عام";
                                            break;
                                        case 2:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                            break;
                                        case 3:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                            break;
                                        case 4:
                                            $deptName = "سياحة عام";
                                            break;
                                        case 5:
                                            $deptName = "الدراسات السياحية و ادارة الضيافة";
                                            break;
                                        case 6:
                                            $deptName = "ارشاد سياحي";
                                            break;
                                        case 7:
                                            $deptName = "ادارة الاعمال السياحية";
                                            break;
                                        case 8:
                                            $deptName = "ادارة الضيافة";
                                            break;
                                        case 9:
                                            $deptName = "الدراسات السياحية";
                                            break;
                                        case 10:
                                            $deptName = "ادارة الفنادق";
                                            break;
                                        case 11:
                                            $deptName = "ادارة شركات الطيران";
                                            break;
                                        case 12:
                                            $deptName = "ادارة المطاعم";
                                            break;
                                        case 13:
                                            $deptName = "ادارة شركات الملاحة";
                                            break;
                                        case 14:
                                            $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                            break;
                                        case 15:
                                            $deptName = "ادارة الاحداث الخاصة";
                                            break;
                                        case 16:
                                            $deptName = "ادارة فنون الطهي";
                                            break;
                                        case 17:
                                            $deptName = "ادارة الاحداث الرياضية";
                                            break;
                                        case 18:
                                            $deptName = "ادارة خدمة العملاء";
                                            break;
                                    }
                                @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="paying-students-subject" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">كشف الطلاب المسجلين</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="registered-students-subject" method="post"
                          action="{{route('registered.students.subject.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                    @php
                                    $deptName = '';
                                    switch($value) {
                                        case 1:
                                            $deptName = "عام";
                                            break;
                                        case 2:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                            break;
                                        case 3:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                            break;
                                        case 4:
                                            $deptName = "سياحة عام";
                                            break;
                                        case 5:
                                            $deptName = "الدراسات السياحية و ادارة الضيافة";
                                            break;
                                        case 6:
                                            $deptName = "ارشاد سياحي";
                                            break;
                                        case 7:
                                            $deptName = "ادارة الاعمال السياحية";
                                            break;
                                        case 8:
                                            $deptName = "ادارة الضيافة";
                                            break;
                                        case 9:
                                            $deptName = "الدراسات السياحية";
                                            break;
                                        case 10:
                                            $deptName = "ادارة الفنادق";
                                            break;
                                        case 11:
                                            $deptName = "ادارة شركات الطيران";
                                            break;
                                        case 12:
                                            $deptName = "ادارة المطاعم";
                                            break;
                                        case 13:
                                            $deptName = "ادارة شركات الملاحة";
                                            break;
                                        case 14:
                                            $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                            break;
                                        case 15:
                                            $deptName = "ادارة الاحداث الخاصة";
                                            break;
                                        case 16:
                                            $deptName = "ادارة فنون الطهي";
                                            break;
                                        case 17:
                                            $deptName = "ادارة الاحداث الرياضية";
                                            break;
                                        case 18:
                                            $deptName = "ادارة خدمة العملاء";
                                            break;
                                    }
                                @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="registered-students-subject"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">كشف الطلاب الغير مسجلين</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="unregistered-students-subject" method="post"
                          action="{{route('unregistered.students.subject.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                    @php
                                    $deptName = '';
                                    switch($value) {
                                        case 1:
                                            $deptName = "عام";
                                            break;
                                        case 2:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                            break;
                                        case 3:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                            break;
                                        case 4:
                                            $deptName = "سياحة عام";
                                            break;
                                        case 5:
                                            $deptName = "الدراسات السياحية و ادارة الضيافة";
                                            break;
                                        case 6:
                                            $deptName = "ارشاد سياحي";
                                            break;
                                        case 7:
                                            $deptName = "ادارة الاعمال السياحية";
                                            break;
                                        case 8:
                                            $deptName = "ادارة الضيافة";
                                            break;
                                        case 9:
                                            $deptName = "الدراسات السياحية";
                                            break;
                                        case 10:
                                            $deptName = "ادارة الفنادق";
                                            break;
                                        case 11:
                                            $deptName = "ادارة شركات الطيران";
                                            break;
                                        case 12:
                                            $deptName = "ادارة المطاعم";
                                            break;
                                        case 13:
                                            $deptName = "ادارة شركات الملاحة";
                                            break;
                                        case 14:
                                            $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                            break;
                                        case 15:
                                            $deptName = "ادارة الاحداث الخاصة";
                                            break;
                                        case 16:
                                            $deptName = "ادارة فنون الطهي";
                                            break;
                                        case 17:
                                            $deptName = "ادارة الاحداث الرياضية";
                                            break;
                                        case 18:
                                            $deptName = "ادارة خدمة العملاء";
                                            break;
                                    }
                                @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="unregistered-students-subject"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">كشف الامتحانات</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="seating-number" method="post" action="{{route('seating.number.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                    @php
                                    $deptName = '';
                                    switch($value) {
                                        case 1:
                                            $deptName = "عام";
                                            break;
                                        case 2:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                            break;
                                        case 3:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                            break;
                                        case 4:
                                            $deptName = "سياحة عام";
                                            break;
                                        case 5:
                                            $deptName = "الدراسات السياحية و ادارة الضيافة";
                                            break;
                                        case 6:
                                            $deptName = "ارشاد سياحي";
                                            break;
                                        case 7:
                                            $deptName = "ادارة الاعمال السياحية";
                                            break;
                                        case 8:
                                            $deptName = "ادارة الضيافة";
                                            break;
                                        case 9:
                                            $deptName = "الدراسات السياحية";
                                            break;
                                        case 10:
                                            $deptName = "ادارة الفنادق";
                                            break;
                                        case 11:
                                            $deptName = "ادارة شركات الطيران";
                                            break;
                                        case 12:
                                            $deptName = "ادارة المطاعم";
                                            break;
                                        case 13:
                                            $deptName = "ادارة شركات الملاحة";
                                            break;
                                        case 14:
                                            $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                            break;
                                        case 15:
                                            $deptName = "ادارة الاحداث الخاصة";
                                            break;
                                        case 16:
                                            $deptName = "ادارة فنون الطهي";
                                            break;
                                        case 17:
                                            $deptName = "ادارة الاحداث الرياضية";
                                            break;
                                        case 18:
                                            $deptName = "ادارة خدمة العملاء";
                                            break;
                                    }
                                @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="seating-number" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">كشف الطلاب الحاصلين على انذارات</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="student-warning" method="post" action="{{route('student.warning.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="year">السنة الدراسية</label>
                                <select class="custom-select" name="year" id="year" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['year'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="semester">الفصل الدراسية</label>
                                <select class="custom-select" name="semester" id="semester" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['semester'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                    @php
                                    $deptName = '';
                                    switch($value) {
                                        case 1:
                                            $deptName = "عام";
                                            break;
                                        case 2:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                            break;
                                        case 3:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                            break;
                                        case 4:
                                            $deptName = "سياحة عام";
                                            break;
                                        case 5:
                                            $deptName = "الدراسات السياحية و ادارة الضيافة";
                                            break;
                                        case 6:
                                            $deptName = "ارشاد سياحي";
                                            break;
                                        case 7:
                                            $deptName = "ادارة الاعمال السياحية";
                                            break;
                                        case 8:
                                            $deptName = "ادارة الضيافة";
                                            break;
                                        case 9:
                                            $deptName = "الدراسات السياحية";
                                            break;
                                        case 10:
                                            $deptName = "ادارة الفنادق";
                                            break;
                                        case 11:
                                            $deptName = "ادارة شركات الطيران";
                                            break;
                                        case 12:
                                            $deptName = "ادارة المطاعم";
                                            break;
                                        case 13:
                                            $deptName = "ادارة شركات الملاحة";
                                            break;
                                        case 14:
                                            $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                            break;
                                        case 15:
                                            $deptName = "ادارة الاحداث الخاصة";
                                            break;
                                        case 16:
                                            $deptName = "ادارة فنون الطهي";
                                            break;
                                        case 17:
                                            $deptName = "ادارة الاحداث الرياضية";
                                            break;
                                        case 18:
                                            $deptName = "ادارة خدمة العملاء";
                                            break;
                                    }
                                @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="student-warning" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">سجل المالية</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="finance" method="post" action="{{route('finance.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                    @php
                                    $deptName = '';
                                    switch($value) {
                                        case 1:
                                            $deptName = "عام";
                                            break;
                                        case 2:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                            break;
                                        case 3:
                                            $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                            break;
                                        case 4:
                                            $deptName = "سياحة عام";
                                            break;
                                        case 5:
                                            $deptName = "الدراسات السياحية و ادارة الضيافة";
                                            break;
                                        case 6:
                                            $deptName = "ارشاد سياحي";
                                            break;
                                        case 7:
                                            $deptName = "ادارة الاعمال السياحية";
                                            break;
                                        case 8:
                                            $deptName = "ادارة الضيافة";
                                            break;
                                        case 9:
                                            $deptName = "الدراسات السياحية";
                                            break;
                                        case 10:
                                            $deptName = "ادارة الفنادق";
                                            break;
                                        case 11:
                                            $deptName = "ادارة شركات الطيران";
                                            break;
                                        case 12:
                                            $deptName = "ادارة المطاعم";
                                            break;
                                        case 13:
                                            $deptName = "ادارة شركات الملاحة";
                                            break;
                                        case 14:
                                            $deptName = "ادارة خدمات الضيافة الجوية و البحرية";
                                            break;
                                        case 15:
                                            $deptName = "ادارة الاحداث الخاصة";
                                            break;
                                        case 16:
                                            $deptName = "ادارة فنون الطهي";
                                            break;
                                        case 17:
                                            $deptName = "ادارة الاحداث الرياضية";
                                            break;
                                        case 18:
                                            $deptName = "ادارة خدمة العملاء";
                                            break;
                                    }
                                @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="finance" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>

<div class="col-lg-6">
    <div class="card card-primary collapsed-card">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title float-left"> تحميل التسجيلات </h2>
            <div class="card-tools float-right">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="regist" method="post" action="{{route('exportRegistrationReport')}}">
                @csrf
                <div class="row justify-content-around">
                    <div class="form-group col-md-6">
                        <label for="year">السنة الدراسية</label>
                        <select class="custom-select" name="year" id="year" required>
                            <option value="" hidden></option>
                            @foreach($filter_data['year'] as $data)
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="course_code">كود المقرر الدراسي</label>
                        <select class="custom-select" name="course_code" id="course_code" required>
                            <option value="" hidden></option>
                            @foreach($filter_data_course['course_code'] as $data)
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button type="submit" form="regist" class="btn btn-primary mx-3 align-self-center"
                    style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
            </button>
        </div>
    </div>
</div>
<div class="col-lg-6"></div>
    </div>
@endsection
@section('scripts')
    <script>
        @if($errors->any())
            toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        toastr.options.timeOut = 0;
        toastr.options.extendedTimeOut = 0;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-center";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif
    </script>
@endsection
