@extends('layout.layout')
@section('title', 'site configuration')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success mt-3 text-center">
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
    @if($errors->any())
        <div class="alert alert-danger mt-3 text-center">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">
                &times;
            </button>
            @foreach($errors->all() as $error)
                <h6>{{ $error }}</h6>
            @endforeach
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="mx-auto" style="width: fit-content">
                <ul class="nav nav-pills justify-content-center mb-1" id="custom-content-below-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="semester_tab" data-toggle="pill"
                           href="#semester_content" role="tab" aria-controls="semester_content"
                           aria-selected="true">Semester</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="student_arabic_registration_tab" data-toggle="pill"
                           href="#student_arabic_registration_content" role="tab"
                           aria-controls="student_arabic_registration_content" aria-selected="false">Student tarmem
                            Registration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="student_english_registration_tab" data-toggle="pill"
                           href="#student_english_registration_content" role="tab"
                           aria-controls="student_english_registration_content" aria-selected="false">Student tourm
                            Registration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="students_payment_exception_tab" data-toggle="pill"
                           href="#students_payment_exception_content" role="tab"
                           aria-controls="students_payment_exception" aria-selected="false">Students Payment
                            Exception</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="military_education_tab" data-toggle="pill"
                           href="#military_education_content" role="tab"
                           aria-controls="military_education" aria-selected="false">Military Education</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="courses_tab" data-toggle="pill"
                           href="#courses_content" role="tab" aria-controls="courses_content"
                           aria-selected="false">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="students_status_tab" data-toggle="pill"
                           href="#students_status_content" role="tab" aria-controls="students_status_content"
                           aria-selected="false">Students Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="students_affairs_tab" data-toggle="pill"
                           href="#students_affairs_content" role="tab" aria-controls="students_affairs_content"
                           aria-selected="false">Students Affairs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="finance_tab" data-toggle="pill"
                           href="#finance_content" role="tab" aria-controls="finance_content"
                           aria-selected="false">Finance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data_key_tab" data-toggle="pill"
                           href="#data_key_content" role="tab"
                           aria-controls="data_key_content" aria-selected="false">Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="administrative_expenses_tab" data-toggle="pill"
                           href="#administrative_expenses_content" role="tab"
                           aria-controls="administrative_expenses_content" aria-selected="false">
                           Administrative Expenses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="extra_fees_tab" data-toggle="pill"
                           href="#extra_fees_content" role="tab"
                           aria-controls="extra_fees_content" aria-selected="false">
                           Extra Fees</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="custom-content-below-tabContent">
                <div class="tab-pane fade" id="semester_content" role="tabpanel"
                     aria-labelledby="semester_tab">
                    <form action="{{route('update.semester')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">registration setting</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group clearfix">
                                    <h5 class="mb-3">الفصل الدراسي الأول</h5>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioFirst1" name="first_semester"
                                               {{($first_semester == 1)? 'checked' : ''}} value="1">
                                        <label for="radioFirst1"></label>
                                        <span>فتح التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioFirst2" name="first_semester"
                                               {{($first_semester == 2)? 'checked' : ''}} value="2">
                                        <label for="radioFirst2"></label>
                                        <span>غلق التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioFirst0" name="first_semester"
                                               {{($first_semester == 0)? 'checked' : ''}} value="0">
                                        <label for="radioFirst0"></label>
                                        <span>تعطيل</span>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <h5 class="mb-3">الفصل الدراسي الثاني</h5>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioSecond1" name="second_semester"
                                               {{($second_semester == 1)? 'checked' : ''}} value="1">
                                        <label for="radioSecond1"></label>
                                        <span>فتح التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioSecond2" name="second_semester"
                                               {{($second_semester == 2)? 'checked' : ''}} value="2">
                                        <label for="radioSecond2"></label>
                                        <span>غلق التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioSecond0" name="second_semester"
                                               {{($second_semester == 0)? 'checked' : ''}} value="0">
                                        <label for="radioSecond0"></label>
                                        <span>تعطيل</span>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <h5 class="mb-3">الفصل الصيفي</h5>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioSummer1" name="summer_semester"
                                               {{($summer_semester == 1)? 'checked' : ''}} value="1">
                                        <label for="radioSummer1"></label>
                                        <span>فتح التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioSummer2" name="summer_semester"
                                               {{($summer_semester == 2)? 'checked' : ''}} value="2">
                                        <label for="radioSummer2"></label>
                                        <span>غلق التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioSummer0" name="summer_semester"
                                               {{($summer_semester == 0)? 'checked' : ''}} value="0">
                                        <label for="radioSummer0"></label>
                                        <span>تعطيل</span>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <h5 class="mb-3">تسجيل الإرشاد الأكاديمي</h5>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioAcademic1" name="academic_registration"
                                               {{($academic_registration == 1)? 'checked' : ''}} value="1">
                                        <label for="radioAcademic1"></label>
                                        <span>فتح التسجيل</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radioAcademic2" name="academic_registration"
                                               {{($academic_registration == 0)? 'checked' : ''}} value="0">
                                        <label for="radioAcademic2"></label>
                                        <span>غلق التسجيل</span>
                                    </div>
                                </div>

                                <div class="form-group clearfix mt-5" style="float: left">
                                    <a href="{{ url('/delete-all-registrations') }}" onclick="return confirm('Are you sure To Delete')"  class="btn btn-danger" style="color: white">حذف التسجيلات الغير مسددة</a>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('moodle.setting')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">moodle setting</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group clearfix">
                                    <h5 class="mb-3">انشاء حساب المنصة التعليمية</h5>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radio_moodler1" name="moodle_registration"
                                               {{($moodle_registration == 1)? 'checked' : ''}} value="1">
                                        <label for="radio_moodler1"></label>
                                        <span>فتح</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radio_moodler2" name="moodle_registration"
                                               {{($moodle_registration == 0)? 'checked' : ''}} value="0">
                                        <label for="radio_moodler2"></label>
                                        <span>غلق</span>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <h5 class="mb-3">تسجيل الدخول المنصة التعليمية</h5>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radio_moodlel1" name="moodle_login"
                                               {{($moodle_login == 1)? 'checked' : ''}} value="1">
                                        <label for="radio_moodlel1"></label>
                                        <span>فتح</span>
                                    </div>
                                    <div class="icheck-primary d-inline mx-2">
                                        <input type="radio" id="radio_moodlel2" name="moodle_login"
                                               {{($moodle_login == 0)? 'checked' : ''}} value="0">
                                        <label for="radio_moodlel2"></label>
                                        <span>غلق</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>

                        {{-- Start maintenance mode --}}
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">Maintenance Mode setting</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('update.maintenance.mode') }}" method="POST">
                                    @csrf
                                    <div class="form-group clearfix">
                                        <h5 class="mb-3"> Maintenance Mode </h5>
                                        <div class="icheck-primary d-inline mx-2">
                                            <input type="radio" id="radio_maintenance1" name="maintenance_mood"
                                                   {{($maintenance_mood == 0)? 'checked' : ''}} value="0">
                                            <label for="radio_maintenance1"></label>
                                            <span>فتح</span>
                                        </div>
                                        <div class="icheck-primary d-inline mx-2">
                                            <input type="radio" id="radio_maintenance2" name="maintenance_mood"
                                                   {{($maintenance_mood == 1)? 'checked' : ''}} value="1">
                                            <label for="radio_maintenance2"></label>
                                            <span>غلق</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        {{-- End maintenance mode --}}
                </div>
                <div class="tab-pane fade" id="student_arabic_registration_content" role="tabpanel"
                     aria-labelledby="student_arabic_registration_tab">
                    <form action="{{route('update.payment',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">hour payment (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$hour_payment['arabic']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$hour_payment['arabic']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$hour_payment['arabic']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$hour_payment['arabic']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$hour_payment['arabic']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.ministerial.payment',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">ministerial payment (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$ministerial_payment['arabic']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$ministerial_payment['arabic']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$ministerial_payment['arabic']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$ministerial_payment['arabic']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$ministerial_payment['arabic']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.payment.remaining',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">hour payment for remaining students (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$hour_payment_remaining['arabic']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$hour_payment_remaining['arabic']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$hour_payment_remaining['arabic']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$hour_payment_remaining['arabic']['fourth']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.ministerial.payment.remaining',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">ministerial payment for remaining students (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$ministerial_payment_remaining['arabic']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$ministerial_payment_remaining['arabic']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$ministerial_payment_remaining['arabic']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$ministerial_payment_remaining['arabic']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$ministerial_payment_remaining['arabic']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.total.payment',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">total student payment (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$total_payment['arabic']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$total_payment['arabic']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$total_payment['arabic']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$total_payment['arabic']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$total_payment['arabic']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.registration.hour',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">student registration hour (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_1">الفرقة الأولى</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_1" name="study_group_1" required
                                                   value="{{$registration_hour['arabic']['study_group_1']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_2">الفرقة الثانية</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_2" name="study_group_2" required
                                                   value="{{$registration_hour['arabic']['study_group_2']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_3">الفرقة الثالثة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_3" name="study_group_3" required
                                                   value="{{$registration_hour['arabic']['study_group_3']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_4">الفرقة الرابعة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_4" name="study_group_4" required
                                                   value="{{$registration_hour['arabic']['study_group_4']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer">فصل صيفي</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="summer" name="summer" required
                                                   value="{{$registration_hour['arabic']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.section.number',['type'=>'arabic'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">number of students per section (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_1">الفرقة الأولى</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_1" name="first" required
                                                   value="{{$section_numbers['arabic']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_2">الفرقة الثانية</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_2" name="second" required
                                                   value="{{$section_numbers['arabic']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_3">الفرقة الثالثة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_3" name="third" required
                                                   value="{{$section_numbers['arabic']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_4">الفرقة الرابعة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_4" name="fourth" required
                                                   value="{{$section_numbers['arabic']['fourth']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="action" value="save" class="btn btn-primary">Save</button>
                                <button type="submit" name="action" value="reset" class="btn btn-primary mx-5">Reset
                                    Sections
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="student_english_registration_content" role="tabpanel"
                     aria-labelledby="student_english_registration_tab">
                    <form action="{{route('update.payment',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">hour payment (english)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$hour_payment['english']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$hour_payment['english']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$hour_payment['english']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$hour_payment['english']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$hour_payment['english']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.ministerial.payment',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">ministerial payment (english)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$ministerial_payment['english']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$ministerial_payment['english']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$ministerial_payment['english']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$ministerial_payment['english']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$ministerial_payment['english']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.payment.remaining',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">hour payment for remaining students (english)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$hour_payment_remaining['english']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$hour_payment_remaining['english']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$hour_payment_remaining['english']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$hour_payment_remaining['english']['fourth']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.ministerial.payment.remaining',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">ministerial payment for remaining students (english)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$ministerial_payment_remaining['english']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$ministerial_payment_remaining['english']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$ministerial_payment_remaining['english']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$ministerial_payment_remaining['english']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$ministerial_payment_remaining['english']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.total.payment',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">total student payment (arabic)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="first_payment">الفرقة الأولى</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="first_payment" name="first_payment" required
                                                   value="{{$total_payment['english']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="second_payment">الفرقة الثانية</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="second_payment" name="second_payment" required
                                                   value="{{$total_payment['english']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="third_payment">الفرقة الثالثة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="third_payment" name="third_payment" required
                                                   value="{{$total_payment['english']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="fourth_payment">الفرقة الرابعة</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="fourth_payment" name="fourth_payment" required
                                                   value="{{$total_payment['english']['fourth']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer_payment">فصل صيفي</label>
                                            <input type="number" step=".01" autocomplete="off" class="form-control"
                                                   id="summer_payment" name="summer_payment" required
                                                   value="{{$total_payment['english']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.registration.hour',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">student registration hour (english)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_1">الفرقة الأولى</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_1" name="study_group_1" required
                                                   value="{{$registration_hour['english']['study_group_1']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_2">الفرقة الثانية</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_2" name="study_group_2" required
                                                   value="{{$registration_hour['english']['study_group_2']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_3">الفرقة الثالثة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_3" name="study_group_3" required
                                                   value="{{$registration_hour['english']['study_group_3']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_4">الفرقة الرابعة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_4" name="study_group_4" required
                                                   value="{{$registration_hour['english']['study_group_4']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="summer">فصل صيفي</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="summer" name="summer" required
                                                   value="{{$registration_hour['english']['summer']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.section.number',['type'=>'english'])}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">number of students per section (english)</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-md-around">
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_1">الفرقة الأولى</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_1" name="first" required
                                                   value="{{$section_numbers['english']['first']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_2">الفرقة الثانية</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_2" name="second" required
                                                   value="{{$section_numbers['english']['second']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_3">الفرقة الثالثة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_3" name="third" required
                                                   value="{{$section_numbers['english']['third']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-5">
                                        <div class="form-group">
                                            <label for="study_group_4">الفرقة الرابعة</label>
                                            <input type="number" step="1" autocomplete="off" class="form-control"
                                                   id="study_group_4" name="fourth" required
                                                   value="{{$section_numbers['english']['fourth']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="action" value="save" class="btn btn-primary">Save</button>
                                <button type="submit" name="action" value="reset" class="btn btn-primary mx-5">Reset
                                    Sections
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="students_payment_exception_content" role="tabpanel"
                     aria-labelledby="students_payment_exception_tab">
                    <form action="{{route('update.total.payment.exception')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">students total payment exception</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label for="username">اسماء الطلاب</label>
                                    <select class="select2" multiple="multiple"
                                            data-placeholder="ادخل اسم او كود الطالب"
                                            style="width: 100%;" id="username" name="usernames[]">
                                        @foreach($exception_students as $student_code)
                                            <option selected value="{{$student_code}}">{{$student_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                      <div class="card card-primary text-capitalize">
                    <form action="{{route('delete.exception.students')}}" method="post">
                        @csrf
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">delete students exception</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label for="username">اسماء الطلاب</label>
                                    <select class="select2"
                                            data-placeholder="ادخل اسم او كود الطالب"
                                            style="width: 100%;" id="std_code" name="student_code">
                                        @foreach($exception_students as $student_code)
                                            <option selected value="{{$student_code}}">{{$student_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
              </div>
                </div>
                <div class="tab-pane fade" id="military_education_content" role="tabpanel"
                     aria-labelledby="military_education_tab">
                    <form action="{{route('update.military.education')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">military education</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="military_education_number">رقم الدورة</label>
                                    <input type="text" autocomplete="off" class="form-control"
                                           id="military_education_number" name="military_education_number"
                                           value="{{$military_education[0]}}">
                                </div>
                                <div class="form-group">
                                    <label for="military_education_payment">مبلغ الدورة</label>
                                    <input type="number" autocomplete="off" class="form-control"
                                           id="military_education_payment" name="military_education_payment" required
                                           min="0" max="5000" step="0.01"
                                           value="{{$military_education[1]}}">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="courses_content" role="tabpanel"
                     aria-labelledby="courses_tab">
                    <form action="{{route('update.courses')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">courses availability</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-around">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="type">التخصص</label>
                                            <select class="custom-select course" required name="type" id="type">
                                                <option value="" hidden></option>
                                                <option value="R" @if (old('type') == "R")
                                                    {{ 'selected' }}
                                                    @endif>ترميم الاثار و المقتنيات الفنية
                                                </option>
                                                <option value="T" @if (old('type') == "T")
                                                    {{ 'selected' }}
                                                    @endif>سياحة
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="semester">فصل دراسي</label>
                                            <select class="custom-select course" required name="semester" id="semester">
                                                <option value="" hidden></option>
                                                <option value="1" @if (old('semester') == "1")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    الأول
                                                </option>
                                                <option value="2" @if (old('semester') == "2")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    الثاني
                                                </option>
                                                <option value="3" @if (old('semester') == "3")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    الثالث
                                                </option>
                                                <option value="4" @if (old('semester') == "4")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    الرابع
                                                </option>
                                                <option value="5" @if (old('semester') == "5")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    الخامس
                                                </option>
                                                <option value="6" @if (old('semester') == "6")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    السادس
                                                </option>
                                                <option value="7" @if (old('semester') == "7")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    السابع
                                                </option>
                                                <option value="8" @if (old('semester') == "8")
                                                    {{ 'selected' }}
                                                    @endif>الفصل الدراسي
                                                    الثامن
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="elective">تصنيف المقرر</label>
                                            <select class="custom-select course" required name="elective" id="elective">
                                                <option value="0" @if (old('elective') == "0")
                                                    {{ 'selected' }}
                                                    @endif>اجباري
                                                </option>
                                                <option value="1" @if (old('elective') == "1")
                                                    {{ 'selected' }}
                                                    @endif>اختياري
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="type">الشعبة</label>
                                     <select class="custom-select course" required name="departments_id" id="departments_id">
                                            <option value="" hidden></option>
                                            <option value="1" @if (old('departments_id') == "عام")
                                                {{ 'selected' }}
                                                @endif>عام
                                            </option>
                                            <option value="2" @if (old('departments_id') == "2")
                                                {{ 'selected' }}
                                                @endif>ترميم الأثار والمقتنيات الفنية العضوية
                                            </option>
                                            <option value="3" @if (old('departments_id') == "3")
                                                {{ 'selected' }}
                                                @endif>ترميم الأثار والمقتنيات الفنية غيرالعضوية
                                            </option>

                                        </select>
                                    </div>
                                    <div class="col-12" id="courses"></div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('add.courses')}}" method="post">
                        @csrf
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">add courses</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <table class="table table-hover table-bordered text-center" id="add-courses-table">
                                        <thead>
                                        <tr>
                                            <th class="col-1">Code</th>
                                            <th class="col-2">Type</th>
                                            <th class="col-4">Name</th>
                                            <th class="col-2">semester</th>
                                            <th class="col-1">Hours</th>
                                            <th class="col-2">elective</th>
                                            <th class="col-2">Department</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="course_code[]" required>
                                            </td>
                                            <td><select class="custom-select" required name="course_type[]">
                                                    <option value="" hidden></option>
                                                    <option value="R">ترميم الاثار و المقتنيات الفنية</option>
                                                    <option value="T">سياحة</option>
                                                </select></td>
                                            <td>
                                                <input type="text" class="form-control" name="course_name[]" required>
                                            </td>
                                            <td>
                                                <select class="custom-select" required name="course_semester[]">
                                                    <option value="" hidden></option>
                                                    <option value="1">الفصل الدراسي الأول</option>
                                                    <option value="2">الفصل الدراسي الثاني</option>
                                                    <option value="3">الفصل الدراسي الثالث</option>
                                                    <option value="4">الفصل الدراسي الرابع</option>
                                                    <option value="5">الفصل الدراسي الخامس</option>
                                                    <option value="6">الفصل الدراسي السادس</option>
                                                    <option value="7">الفصل الدراسي السابع</option>
                                                    <option value="8">الفصل الدراسي الثامن</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="course_hours[]"
                                                       max="6" min="1" step="1" required>
                                            </td>
                                            <td>
                                                <select class="custom-select" required name="course_elective[]">
                                                    <option value="0">اجباري</option>
                                                    <option value="1">اختياري</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="custom-select" required name="departments_id[]">
                                                    @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" onclick="addCourse()">add 1 more
                                    </button>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="students_status_content" role="tabpanel"
                     aria-labelledby="students_status_tab">
                    @if(session()->has('counters'))
                        @php $counters = session('counters');@endphp
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">الوصف</th>
                                <th class="align-middle">الفرقة الاولي</th>
                                <th class="align-middle">الفرقة الثانية</th>
                                <th class="align-middle">الفرقة الثالثة</th>
                                <th class="align-middle">الفرقة الرابعة</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="align-middle">اجمالى الفرقة</th>
                                <th class="align-middle">{{$counters['الاولي']['total']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['total']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['total']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['total']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">المقيدون هذا العام</th>
                                <th class="align-middle">{{$counters['الاولي']['restricted']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['restricted']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['restricted']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['restricted']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">وقف القيد</th>
                                <th class="align-middle">{{$counters['الاولي']['stop restriction']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['stop restriction']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['stop restriction']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['stop restriction']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">الناجحون الى المستوى التالى</th>
                                <th class="align-middle">{{$counters['الاولي']['next level']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['next level']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['next level']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['next level']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">الباقون</th>
                                <th class="align-middle">{{$counters['الاولي']['remaining']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['remaining']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['remaining']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['remaining']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">الباقون من الخارج 1</th>
                                <th class="align-middle"></th>
                                <th class="align-middle">{{$counters['الثانية']['remaining outside']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['remaining outside 1']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['remaining outside 1']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">الباقون من الخارج 2</th>
                                <th class="align-middle"></th>
                                <th class="align-middle"></th>
                                <th class="align-middle">{{$counters['الثالثة']['remaining outside 2']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['remaining outside 2']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">الباقون من الخارج 3</th>
                                <th class="align-middle"></th>
                                <th class="align-middle"></th>
                                <th class="align-middle">{{$counters['الثالثة']['remaining outside 3']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['remaining outside 3']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">المفصولون</th>
                                <th class="align-middle">{{$counters['الاولي']['separate']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['separate']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['separate']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['separate']}}</th>
                            </tr>
                            <tr>
                                <th class="align-middle">الانذارات</th>
                                <th class="align-middle">{{$counters['الاولي']['warning']}}</th>
                                <th class="align-middle">{{$counters['الثانية']['warning']}}</th>
                                <th class="align-middle">{{$counters['الثالثة']['warning']}}</th>
                                <th class="align-middle">{{$counters['الرابعة']['warning']}}</th>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                    <form action="{{route('change.warning.threshold')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">warning threshold</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="warning_threshold">warning threshold</label>
                                    <input type="number" step=".01" autocomplete="off" class="form-control"
                                           id="warning_threshold" name="warning_threshold" required
                                           value="{{$warning_threshold}}">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form id="update-level" action="{{route('change.students.level')}}" method="post">
                        @csrf
                        @method('put')
                    </form>
                    <form id="simulation-update-level" action="{{route('simulation.students.level')}}" method="get">
                    </form>
                    <div class="card card-primary text-capitalize">
                        <div class="card-header">
                            <h2 class="card-title">change student level and alerts</h2>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="mx-3 row justify-content-between">
                                <button type="submit" form="update-level" class="btn btn-primary">Update</button>
                                <button type="submit" form="simulation-update-level" class="btn btn-primary">simulation
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="students_affairs_content" role="tabpanel"
                     aria-labelledby="students_affairs_tab">
                    <form action="{{route('update.english.degree')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">data</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-lg-around">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="english_degree">English Degree</label>
                                            <input type="number" min="25" max="50" step="0.1" class="form-control"
                                                   id="english_degree" name="english_degree" required
                                                   value="{{$english_degree}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="finance_content" role="tabpanel"
                     aria-labelledby="finance_tab">
                    <form action="{{route('update.ministerial.receipt.number')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">data</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-lg-around">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ministerial_receipt_start">ministerial receipt start
                                                number</label>
                                            <input type="number" step="1" class="form-control"
                                                   id="ministerial_receipt_start" name="ministerial_receipt_start"
                                                   required
                                                   value="{{$ministerial_receipt['ministerial_receipt_start'][0]}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ministerial_receipt_end">ministerial receipt end number</label>
                                            <input type="number" step="1" class="form-control"
                                                   id="ministerial_receipt_end" name="ministerial_receipt_end" required
                                                   value="{{$ministerial_receipt['ministerial_receipt_end'][0]}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="data_key_content" role="tabpanel"
                     aria-labelledby="data_key_tab">
                    <form action="{{route('add.data')}}" method="post">
                        @csrf
                        <div class="card card-primary text-capitalize collapsed-card">
                            <div class="card-header">
                                <h2 class="card-title">add new data</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-lg-around">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="data_key">key</label>
                                            <input type="text" autocomplete="off" class="form-control" id="data_key"
                                                   name="data_key" required value="{{old('data_key')}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="value">value</label>
                                            <input type="text" autocomplete="off" class="form-control" id="value"
                                                   name="value" required value="{{old('value')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('update.key.values')}}" method="post">
                        @csrf
                        @method('put')
                        <div class="card card-primary text-capitalize">
                            <div class="card-header">
                                <h2 class="card-title">edit data</h2>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="data_key2">key</label>
                                        <select class="custom-select" required
                                                name="data_key2" id="data_key2">
                                            <option value="" hidden></option>
                                            @foreach($data_key as $d)
                                                <option
                                                    value="{{$d->data_key}}" @if (old('data_key2') == $d->data_key)
                                                    {{ 'selected' }}
                                                    @endif>{{$d->data_key}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>




                <div class="tab-pane fade" id="administrative_expenses_content" role="tabpanel"
                aria-labelledby="administrative_expenses_tab">
               <form action="{{route('update.insurance.payment')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Insurance</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="first_payment">الفرقة الأولى</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="first_payment" name="first_payment" required
                                              value="{{$administrative_expenses_insurance['first']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="second_payment">الفرقة الثانية</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="second_payment" name="second_payment" required
                                              value="{{$administrative_expenses_insurance['second']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="third_payment">الفرقة الثالثة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="third_payment" name="third_payment" required
                                              value="{{$administrative_expenses_insurance['third']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="fourth_payment">الفرقة الرابعة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="fourth_payment" name="fourth_payment" required
                                              value="{{$administrative_expenses_insurance['fourth']}}">
                                   </div>
                               </div>
                               {{-- <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="summer_payment">فصل صيفي</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="summer_payment" name="summer_payment_expenses" required
                                              value="{{$administrative_expenses_insurance['summer']}}">
                                   </div>
                               </div> --}}
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" class="btn btn-primary">Save</button>
                       </div>
                   </div>
               </form>
               <form action="{{route('update.ProfileExpenses.payment')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Profile Expenses</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="first_payment">الفرقة الأولى</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="first_payment" name="first_payment" required
                                              value="{{$administrative_expenses_profile['first']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="second_payment">الفرقة الثانية</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="second_payment" name="second_payment" required
                                              value="{{$administrative_expenses_profile['second']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="third_payment">الفرقة الثالثة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="third_payment" name="third_payment" required
                                              value="{{$administrative_expenses_profile['third']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="fourth_payment">الفرقة الرابعة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="fourth_payment" name="fourth_payment" required
                                              value="{{$administrative_expenses_profile['fourth']}}">
                                   </div>
                               </div>
                               {{-- <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="summer_payment">فصل صيفي</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="summer_payment" name="summer_payment_expenses" required
                                              value="{{$administrative_expenses_profile['summer']}}">
                                   </div>
                               </div> --}}
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" class="btn btn-primary">Save</button>
                       </div>
                   </div>
               </form>
               <form action="{{route('update.registration.fees')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Registration Fees</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="first_payment">الفرقة الأولى</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="first_payment" name="first_payment" required
                                              value="{{$administrative_expenses_registration_fees['first']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="second_payment">الفرقة الثانية</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="second_payment" name="second_payment" required
                                              value="{{$administrative_expenses_registration_fees['second']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="third_payment">الفرقة الثالثة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="third_payment" name="third_payment" required
                                              value="{{$administrative_expenses_registration_fees['third']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="fourth_payment">الفرقة الرابعة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="fourth_payment" name="fourth_payment" required
                                              value="{{$administrative_expenses_registration_fees['fourth']}}">
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" class="btn btn-primary">Save</button>
                       </div>
                   </div>
               </form>
               <form action="{{route('update.cardEmail.payment')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Card and Email</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="first_payment">الفرقة الأولى</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="first_payment" name="first_payment" required
                                              value="{{$administrative_expenses_card_email['first']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="second_payment">الفرقة الثانية</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="second_payment" name="second_payment" required
                                              value="{{$administrative_expenses_card_email['second']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="third_payment">الفرقة الثالثة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="third_payment" name="third_payment" required
                                              value="{{$administrative_expenses_card_email['third']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="fourth_payment">الفرقة الرابعة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="fourth_payment" name="fourth_payment" required
                                              value="{{$administrative_expenses_card_email['fourth']}}">
                                   </div>
                               </div>
                               {{-- <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="summer_payment">فصل صيفي</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="summer_payment" name="summer_payment_expenses" required
                                              value="{{$administrative_expenses_card_email['summer']}}">
                                   </div>
                               </div> --}}
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" class="btn btn-primary">Save</button>
                       </div>
                   </div>
               </form>
               <form action="{{route('update.renwecardEmail.payment')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Renew Card Email</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="first_payment">الفرقة الأولى</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="first_payment" name="first_payment" required
                                              value="{{$administrative_expenses_renew_card_email['first']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="second_payment">الفرقة الثانية</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="second_payment" name="second_payment" required
                                              value="{{$administrative_expenses_renew_card_email['second']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="third_payment">الفرقة الثالثة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="third_payment" name="third_payment" required
                                              value="{{$administrative_expenses_renew_card_email['third']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="fourth_payment">الفرقة الرابعة</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="fourth_payment" name="fourth_payment" required
                                              value="{{$administrative_expenses_renew_card_email['fourth']}}">
                                   </div>
                               </div>
                               {{-- <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="summer_payment">فصل صيفي</label>
                                       <input type="number" step=".01" autocomplete="off" class="form-control"
                                              id="summer_payment" name="summer_payment_expenses" required
                                              value="{{$administrative_expenses_renew_card_email['summer']}}">
                                   </div>
                               </div> --}}
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" class="btn btn-primary">Save</button>
                       </div>
                   </div>
               </form>
               <form action="{{route('update.Military.payment')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Military Expenses</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_1">الفرقة الأولى</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_1" name="first_payment" required
                                              value="{{$administrative_expenses_military['first']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_2">الفرقة الثانية</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_2" name="second_payment" required
                                              value="{{$administrative_expenses_military['second']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_3">الفرقة الثالثة</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_3" name="third_payment" required
                                              value="{{$administrative_expenses_military['third']}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_4">الفرقة الرابعة</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_4" name="fourth_payment" required
                                              value="{{$administrative_expenses_military['fourth']}}">
                                   </div>
                               </div>
                               {{-- <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="summer">فصل صيفي</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="summer" name="summer_payment_expenses" required
                                              value="{{$administrative_expenses_military['summer']}}">
                                   </div>
                               </div> --}}
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" class="btn btn-primary">Save</button>
                       </div>
                   </div>
               </form>
               <form action="{{route('update.Total.expenses')}}" method="post">
                   @csrf
                   @method('put')
                   <div class="card card-primary text-capitalize">
                       <div class="card-header">
                           <h2 class="card-title">Total Administrative Expenses</h2>
                           <div class="card-tools">
                               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                   <i class="fas fa-minus"></i>
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="row justify-content-md-around">
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_1">الفرقة الأولى</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_1" name="first_payment" required
                                              value="{{$administrative_expenses_total_first}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_2">الفرقة الثانية</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_2" name="second_payment" required
                                              value="{{$administrative_expenses_total_second}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_3">الفرقة الثالثة</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_3" name="third_payment" required
                                              value="{{$administrative_expenses_total_third}}">
                                   </div>
                               </div>
                               <div class="col-lg-2 col-md-5">
                                   <div class="form-group">
                                       <label for="study_group_4">الفرقة الرابعة</label>
                                       <input type="number" step="1" autocomplete="off" class="form-control"
                                              id="study_group_4" name="fourth_payment" required
                                              value="{{$administrative_expenses_total_fourth}}">
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="card-footer">
                           <button type="submit" name="action" value="save" class="btn btn-primary">Save</button>

                       </div>
                   </div>
               </form>
           </div>
            </div>
        </div>
    </div>

     <div class="tab-pane fade" id="extra_fees_content" role="tabpanel"
           aria-labelledby="extra_fees_tab">

                        <form action="{{route('update.extra.fees')}}" method="post">
                            @csrf
                            @method('put')
                            <div class="card card-primary text-capitalize">

                                <div class="card-header">
                                    <h2 class="card-title">Extra Fees</h2>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach ($extra_fees as $index => $extra_fee)
                                        <div class="row justify-content-md-around">
                                            <div class="col-lg-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="name_fees_{{ $index }}">Label</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="name_fees_{{ $index }}" name="name_fees[{{ $index }}]" required value="{{ $extra_fee->name_fees }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="amount_{{ $index }}">Amount</label>
                                                    <input type="number" step=".01" autocomplete="off" class="form-control" id="amount_{{ $index }}" name="amount[{{ $index }}]" required value="{{ $extra_fee->amount }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4">
                                                <div class="form-group clearfix">
                                                    <h5 class="mb-3">Status ON / OFF</h5>
                                                    <div class="icheck-primary d-inline mx-2">
                                                        <input type="radio" id="active_{{ $index }}_1" name="active[{{ $index }}]" {{ $extra_fee->active == 1 ? 'checked' : '' }} value="1">
                                                        <label for="active_{{ $index }}_1"></label>
                                                        <span>ON</span>
                                                    </div>
                                                    <div class="icheck-primary d-inline mx-2">
                                                        <input type="radio" id="active_{{ $index }}_2" name="active[{{ $index }}]" {{ $extra_fee->active == 0 ? 'checked' : '' }} value="0">
                                                        <label for="active_{{ $index }}_2"></label>
                                                        <span>OFF</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
        </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        @if(session()->has('counters'))
        $('.fade').removeClass(['active', 'show']);
        $('li.nav-item > a.nav-link').removeClass(['active', 'show']).attr('aria-selected', false);
        $('#students_status_tab').addClass('active').attr('aria-selected', true);
        $('#students_status_content').addClass(['active', 'show']);
        @endif
        $('.clearfix span').addClass('font-weight-bolder');
        $('#data_key2').on('input', function () {
            let me = $(this);
            me.attr('disabled', true);
            $.ajax({
                type: 'get',
                url: '{{route('key.values')}}',
                data: {
                    'data_key': me.val(),
                },
                success: function (data) {
                    let output = '';
                    data.forEach(element => {
                        output += '<tr><td><input type="text" class="form-control" name="' + 'values[]' +
                            '" value="' + element['value'] + '"></td>' +
                            '<td><input type="number" class="form-control" name="' + 'index[]' +
                            '" value="' + element['sorting_index'] + '"></td></tr>';
                    });
                    if (me.parent().parent().parent().children('table').length > 0) {
                        me.parent().parent().parent().children('table').remove();
                    }
                    me.parent().parent().parent().append('<table class="table table-bordered table-hover text-center">' +
                        '<thead> <tr> <th class="col-10">Value</th> <th class="col-2">Index</th></tr></thead><tbody>' +
                        output +
                        '</tbody> </table>');
                },
                error: function (data) {
                    if (me.parent().parent().parent().children('table').length > 0) {
                        me.parent().parent().parent().children('table').remove();
                    }
                    me.parent().parent().parent().append('<div id="data_key_error" class="alert alert-danger mt-3 text-center">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">' +
                        ' &times; </button> <h6><i class="icon fas fa-ban"></i> ' + data['responseJSON']['error'] +
                        '</h6></div>');
                },
                complete: function () {
                    me.attr('disabled', false);
                }
            });
        });
        $('.course').on('input', function () {
            let ek = $('.course').map((_, el) => el.value).get();
            let me = $('#courses');
            if (ek[0] !== "" && ek[1] !== "") {
                $('.course').attr('disabled', true);
                $.ajax({
                    type: 'get',
                    url: '{{route('get.courses')}}',
                    data: {
                        'data': ek,
                    },
                    success: function (data) {
                        let output = '';
                        let i = 0;
                        data.forEach(element => {
                            output += '<tr><td><input type="text" class="form-control" name="' + 'code[]' +
                                '" value="' + element['code'] + '"></td>' +
                                '<td><input type="text" class="form-control" name="' + 'name[]' +
                                '" value="' + element['name'] + '"></td>' +
                                '<td><div class="icheck-primary d-inline">' +
                                '<input type="checkbox" id="' + i + '" name="is_selected[' + i + ']"' +
                                (element['is_selected'] ? 'checked' : '') + ' value="1">' +
                                '<label for="' + i + '"></label></div></td>' +
                                '<td><input type="text" class="form-control" name="' + 'hours[]' +
                                '" value="' + element['hours'] + '"></td></tr>';
                            i++;
                        });
                        if (me.children('table').length > 0) {
                            me.children('table').remove();
                        }
                        me.append('<table class="table table-hover table-bordered text-center">' +
                            '<thead><tr><th class="col-2">Code</th><th class="col-8">Name</th>' +
                            '<th class="col-1">Selected</th><th class="col-1">Hours</th></tr></thead><tbody>' +
                            output +
                            '</tbody> </table>');
                    },
                    error: function (data) {
                        console.log(data);
                        if (me.children('table').length > 0) {
                            me.children('table').remove();
                        }
                        me.append('<div id="data_key_error" class="alert alert-danger mt-3 text-center">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">' +
                            ' &times; </button> <h6><i class="icon fas fa-ban"></i> ' + data['responseJSON']['error'] +
                            '</h6></div>');
                    },
                    complete: function () {
                        $('.course').attr('disabled', false);
                    }
                });
            }
        });

        @if(session()->getOldInput())
            @php
            $oldInput = session()->getOldInput();
            $count = count($oldInput);
            @endphp
        $('#add-courses-table tbody tr').remove();
         @for($i=0; $i < $count; $i++)
        $('#add-courses-table tbody').append('<tr>' +
            '<td><input type="text" class="form-control" name="course_code[]" required value="{{empty(session()->getOldInput('course_code')[$i])? '':session()->getOldInput('course_code')[$i]}}"></td>' +
            '<td><select class="custom-select course" required name="course_type[]">' +
            '<option value="" hidden></option>' +
            '<option value="R" @if(!empty(session()->getOldInput('course_type')[$i]) and session()->getOldInput('course_type')[$i] == 'R') selected @endif>ترميم الاثار و المقتنيات الفنية</option>' +
              '<option value="T" @if(!empty(session()->getOldInput('course_type')[$i]) and session()->getOldInput('course_type')[$i] == 'T') selected @endif>سياحة</option>' +
            '</select></td>' +
            '<td><input type="text" class="form-control" name="course_name[]" required value="{{empty(session()->getOldInput('course_name')[$i])? '':session()->getOldInput('course_name')[$i]}}"></td>' +
            '<td><select class="custom-select course" required name="course_semester[]">' +
            '<option value="" hidden></option>' +
            '<option value="1" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '1') selected @endif>الفصل الدراسي الأول </option>' +
            '<option value="2" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '2') selected @endif>الفصل الدراسي الثاني</option>' +
            '<option value="3" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '3') selected @endif>الفصل الدراسي الثالث</option>' +
            '<option value="4" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '4') selected @endif>الفصل الدراسي الرابع</option>' +
            '<option value="5" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '5') selected @endif>الفصل الدراسي الخامس</option>' +
            '<option value="6" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '6') selected @endif>الفصل الدراسي السادس</option>' +
            '<option value="7" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '7') selected @endif>الفصل الدراسي السابع</option>' +
            '<option value="8" @if(!empty(session()->getOldInput('course_semester')[$i]) and session()->getOldInput('course_semester')[$i] == '8') selected @endif>الفصل الدراسي الثامن</option>' +
            '</select></td>' +
            '<td><select class="custom-select course" required name="departments_id[]">' +
            '<option value="" hidden></option>' +
            '<option value="1" @if(!empty(session()->getOldInput('departments_id')[$i]) and session()->getOldInput('departments_id')[$i] == '1') selected @endif>عام</option>' +
            '<option value="2" @if(!empty(session()->getOldInput('departments_id')[$i]) and session()->getOldInput('departments_id')[$i] == '2') selected @endif>ترميم الأثار والمقتنيات الفنية العضوية</option>' +
            '<option value="3" @if(!empty(session()->getOldInput('departments_id')[$i]) and session()->getOldInput('departments_id')[$i] == '3') selected @endif>الترميم الأثار والمقتنيات الفنية غيرالعضوية</option>' +
            '</select></td>' +
            '<td><input type="number" class="form-control" name="course_hours[]" required value="{{empty(session()->getOldInput('course_hours')[$i])?'':session()->getOldInput('course_hours')[$i]}}"></td>' +
            '<td><select class="custom-select course" required name="course_elective[]">' +
            '<option value="0" @if(!empty(session()->getOldInput('course_elective')[$i]) and session()->getOldInput('course_elective')[$i] == '0') selected @endif>اجباري</option>' +
            '<option value="1" @if(!empty(session()->getOldInput('course_elective')[$i]) and session()->getOldInput('course_elective')[$i] == '1') selected @endif>اختياري</option>' +
            '</select></td>' +
            '</tr>');
        @endfor
        @endif

        function addCourse() {
            $('#add-courses-table tbody').append('<tr>' +
                '<td><input type="text" class="form-control" name="course_code[]" required></td>' +
                '<td><select class="custom-select course" required name="course_type[]">' +
                '<option value="" hidden></option>' +
                '<option value="R">ترميم الاثار و المقتنيات الفنية</option>' +
                 '<option value="T">سياحة</option>' +
                '</select></td>' +
                '<td><input type="text" class="form-control" name="course_name[]" required></td>' +
                '<td><select class="custom-select course" required name="course_semester[]">' +
                '<option value="" hidden></option>' +
                '<option value="1">الفصل الدراسي الأول </option>' +
                '<option value="2">الفصل الدراسي الثاني</option>' +
                '<option value="3">الفصل الدراسي الثالث</option>' +
                '<option value="4">الفصل الدراسي الرابع</option>' +
                '<option value="5">الفصل الدراسي الخامس</option>' +
                '<option value="6">الفصل الدراسي السادس</option>' +
                '<option value="7">الفصل الدراسي السابع</option>' +
                '<option value="8">الفصل الدراسي الثامن</option>' +
                '</select></td>' +
                '<td><input type="number" class="form-control" name="course_hours[]" required></td>' +
                '<td><select class="custom-select course" required name="course_elective[]">' +
                '<option value="0">اجباري</option>' +
                '<option value="1">اختياري</option>' +
                '</select></td>' +
                '<td><select class="custom-select course" required name="departments_id[]">' +
                '<option value="1">عام</option>' +
                '<option value="2">ترميم الأثار والمقتنيات الفنية العضوية</option>' +
                '<option value="3">ترميم الأثار والمقتنيات الفنية غيرالعضوية</option>' +
                '<option value="4">التسويق والتجارة الالكترونية</option>' +
                '</select></td>' +
                '</tr>');
        }

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
@endsection
