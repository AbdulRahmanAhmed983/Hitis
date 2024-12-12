@extends('layout.layout')
@section('title', 'تسجيل المواد لطالب')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
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
            <div class="card card-primary">
                <div class="card-body">
                    <form method="get" action="">
                        <div class="row">
                            <div class="forms-group col-md-6 my-2 my-md-0">
                                <input type="search" class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                       name="username" id="username" required value="{{old('username')}}"
                                       placeholder="كود او اسم الطالب" list="students">
                                <datalist id="students"></datalist>
                            </div>
                            <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                <button type="submit" class="btn btn-primary col-12"><i class="fas fa-search"></i>
                                    بحث
                                </button>
                            </div>
                        </div>
                    </form>
                    @if(isset($student))
                        <div class="mb-0 mt-3 h4 text-center">
                            <div>الطالب {{$student['name']}}</div>
                            <div><span class="align-middle">الكود {{$student['username']}}</span> <a
                                    href="{{route('student.status',['username'=>$student['username']])}}"
                                    class="btn btn-primary mt-1"
                                    target="_blank">بيان حالة الطالب</a></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if(isset($student))
            <div class="col-lg-6">
                <form action="{{route('admin.store.registration',['student_code' => $student['username']])}}"
                      method="post">
                    @csrf
                    <div class="card card-info">
                        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                            <h2 class="card-title float-left">المواد المسجلة في الترم الحالي</h2>
                            <div class="card-tools float-right">
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            <table class="table table-hover table-bordered text-center">
                                <thead>
                                <tr>
                                    <th class="align-middle">كود المادة</th>
                                    <th class="align-middle">اسم المادة</th>
                                    <th class="align-middle">ترم المادة</th>
                                    <th class="align-middle">عدد ساعات المادة</th>
                                    <th class="align-middle">تصنيف المادة</th>
                                    <th class="align-middle">موافق
                                        <div class="icheck-primary d-block">
                                            <input type="checkbox" id="guidance" value="1">
                                            <label for="guidance"></label>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="text-bold">
                                    @php
                                    $sortedData = collect($courses[0])->sortBy('semester');
                                    @endphp
                                    @foreach($sortedData as $course)
                                    <tr class="
                                        @switch($course->semester)
                                            @case(1)text-danger @break
                                            @case(2)text-danger @break
                                            @case(3)text-success @break
                                            @case(4)text-success @break
                                            @case(5)text-info @break
                                            @case(6)text-info @break
                                            @case(7)text-dark @break
                                            @case(8)text-dark @break
                                        @endswitch
                                    ">
                                        <td class="align-middle">{{$course->full_code}}</td>
                                        <td class="align-middle">{{$course->name}}</td>
                                        <td class="align-middle">{{$course->semester}}</td>
                                        <td class="align-middle">{{$course->hours}}</td>
                                        <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                        <td class="align-middle">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="guidance{{$course->full_code}}"
                                                       name="guidance[{{$course->full_code}}]"
                                                       value="1">
                                                <label for="guidance{{$course->full_code}}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($courses[1] as $course)
                                    <tr>
                                        <td class="align-middle">{{$course->full_code}}</td>
                                        <td class="align-middle">{{$course->name}}</td>
                                        <td class="align-middle">{{$course->semester}}</td>
                                        <td class="align-middle">{{$course->hours}}</td>
                                        <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                        <td class="align-middle">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="guidance{{$course->full_code}}"
                                                       name="guidance[{{$course->full_code}}]"
                                                       value="1">
                                                <label for="guidance{{$course->full_code}}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($courses[4] as $course)
                                @php
                                    $grades = ['A+', 'A', 'B', 'B+','C+'];
                                    @endphp
                                    @unless (in_array($course->grade, $grades))
                                    <tr class="text-success">
                                        <td class="align-middle">{{$course->full_code}}</td>
                                        <td class="align-middle">{{$course->name}} (تحسين) {{$course->grade}}</td>
                                        <td class="align-middle">{{$course->semester}}</td>
                                        <td class="align-middle">{{$course->hours}}</td>
                                        <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                        <td class="align-middle">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="guidance{{$course->full_code}}"
                                                       name="guidance[{{$course->full_code}}]"
                                                       value="1">
                                                <label for="guidance{{$course->full_code}}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endunless
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">تأكيد التسجيل</button>
                        </div>
                    </div>
                </form>
                <div class="card card-info">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">المواد التي سبق الرسوب فيها</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table table-hover table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">كود المادة</th>
                                <th class="align-middle">اسم المادة</th>
                                <th class="align-middle">ترم المادة</th>
                                <th class="align-middle">عدد ساعات المادة</th>
                                <th class="align-middle">تصنيف المادة</th>
                                <th class="align-middle">التقدير المكتسب</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($courses[3] as $course)
                                <tr>
                                    <td class="align-middle">{{$course->full_code}}</td>
                                    <td class="align-middle">{{$course->name}}</td>
                                    <td class="align-middle">{{$course->semester}}</td>
                                    <td class="align-middle">{{$course->hours}}</td>
                                    <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                    <td class="align-middle">{{$course->grade}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-info">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">المواد المتاحة في الترم الحالي</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table table-hover table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">كود المادة</th>
                                <th class="align-middle">اسم المادة</th>
                                <th class="align-middle">ترم المادة</th>
                                <th class="align-middle">عدد ساعات المادة</th>
                                <th class="align-middle">تصنيف المادة</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($courses[0] as $course)
                                <tr>
                                    <td class="align-middle">{{$course->full_code}}</td>
                                    <td class="align-middle">{{$course->name}}</td>
                                    <td class="align-middle">{{$course->semester}}</td>
                                    <td class="align-middle">{{$course->hours}}</td>
                                    <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card card-info">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">المواد التي سبق درستها</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        @foreach($previous_courses as $year=>$semesters)
                            @foreach($semesters as $semester=>$courses)
                                <div
                                    class="card card-info{{($loop->parent->first and $loop->first)? '': ' collapsed-card'}}">
                                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                                        <h2 class="card-title float-left">{{$year}}  {{$semester}}</h2>
                                        <div class="card-tools float-right">
                                            <button type="button" class="btn btn-tool">
                                                <i class="fas {{($loop->parent->first and $loop->first)? 'fa-minus': 'fa-plus'}}"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="overflow-x: auto;">
                                        <table class="table table-hover table-bordered text-center">
                                            <thead>
                                            <tr>
                                                <th class="align-middle">كود المادة</th>
                                                <th class="align-middle">اسم المادة</th>
                                                <th class="align-middle">ترم المادة</th>
                                                <th class="align-middle">عدد ساعات المادة</th>
                                                <th class="align-middle">تصنيف المادة</th>
                                                <th class="align-middle">التقدير المكتسب</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($courses as $course)
                                                <tr>
                                                    <td class="align-middle">{{$course->full_code}}</td>
                                                    <td class="align-middle">{{$course->name}}</td>
                                                    <td class="align-middle">{{$course->semester}}</td>
                                                    <td class="align-middle">{{$course->hours}}</td>
                                                    <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                                    <td class="align-middle">{{$course->grade}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('scripts')
    <script>
        $("#guidance").click(function () {
            $("input[name*='guidance']").prop('checked', $(this).prop('checked'));
        });
        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });
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
        let done = true;
        $('#username').on('input', function (e) {
            let me = $(this);
            if (me.val().length >= 3 && done) {
                done = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('student.datalist')}}',
                    data: {
                        'search': me.val(),
                    },
                    success: function (data) {
                        $('#students').html('');
                        for (let i = 0; i < data.length; i++) {
                            $('#students').append('<option value="' + data[i]['username'] + '">'
                                + data[i]['name'] + '</option>');
                        }
                    },
                    error: function (data) {
                        $('#students').html('').parent().parent().parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done = true;
                    }
                });
            } else {
                $('#students').html('');
            }
        });
    </script>
@endsection
