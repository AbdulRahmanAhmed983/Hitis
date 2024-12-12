@extends('layout.layout')
@section('title', 'بيان حالة طالب')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
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
                        @error('username')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                        @if(session()->has('error'))
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                            </div>
                        @endif
                    </form>
                    @if(isset($student))
                        <div class="mb-0 mt-3 h4 text-center">
                            <div>الطالب {{$student[0]}}</div>
                            <div>الكود {{$student[1]}}</div>
                        </div>
                    @endif
                </div>
            </div>
            @if(isset($trans_courses))
                <div class="card card-gray collapsed-card">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">مواد معادلة من الخارج</h2>
                        <div class="card-tools  float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table table-hover table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">كود المادة</th>
                                <th class="align-middle">اسم المادة</th>
                                <th class="align-middle">عدد ساعات المادة</th>
                                <th class="align-middle">تصنيف المادة</th>
                                <th class="align-middle">ترم المادة</th>
                                <th class="align-middle">التقدير المكتسب</th>
                                <th class="align-middle">النقاط</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($trans_courses as $course)
                                <tr>
                                    <td class="align-middle">{{$course->course_code}}</td>
                                    <td class="align-middle">{{$course->name}}</td>
                                    <td class="align-middle">{{$course->hours}}</td>
                                    <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                    <td class="align-middle">{{$course->courses_semester}}</td>
                                    <td @class(['align-middle','font-weight-bold','text-danger'=>(in_array($course->grade,['F','FX'])),
                                                'text-success'=>(!in_array($course->grade,['F','FX','P','IC','W']))])>{{$course->grade}}</td>
                                    <td class="align-middle font-weight-bold">{{$grades[0][0]['courses'][$course->course_code]}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th>المعدل الفصلي للترم</th>
                                <th>{{$grades[0][0]['gpa']}}</th>
                                <th>عدد ساعات المسجلة في الترم</th>
                                <th>{{$grades[0][0]['hours']}}</th>
                            </tr>
                            <tr>
                                <th>المعدل التراكمي الإجمالي</th>
                                <th>{{$grades[0][0]['cgpa']}}</th>
                                <th>عدد ساعات المكتسبه في الترم</th>
                                <th>{{$grades[0][0]['earned_hours']}}</th>
                            </tr>
                            </tbody>
                        </table>
                        <form id="courses_trans" action="{{route('print.status')}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success">طباعة</button>
                        </form>
                    </div>
                </div>
            @endif
            @if(isset($registrations))
                @forelse($registrations as $year => $value)
                    @foreach($value as $semester => $val)
                        <div class="card card-gray">
                            <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                                <h2 class="card-title float-left">{{$year}} {{$semester}}
                                    @if(!empty($seating_numbers[$year]))
                                        <b class="ml-3">
                                            رقم الجلوس :({{$seating_numbers[$year][0]->seating_number}})
                                        </b>
                                    @endif
                                </h2>
                                <div class="card-tools  float-right">
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
                                        <th class="align-middle">عدد ساعات المادة</th>
                                        <th class="align-middle">تصنيف المادة</th>
                                        <th class="align-middle">ترم المادة</th>
                                        <th class="align-middle">الإرشاد</th>
                                        <th class="align-middle">الماليه</th>
                                        <th class="align-middle">التقدير المكتسب</th>
                                        <th class="align-middle">النقاط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($val as $course)
                                        <tr>
                                            <td class="align-middle">{{$course->course_code}}</td>
                                            <td class="align-middle">{{$course->name}}</td>
                                            <td class="align-middle">{{$course->hours}}</td>
                                            <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                            <td class="align-middle">{{$course->courses_semester}}</td>
                                            <td class="align-middle">{!! $course->guidance ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                            <td class="align-middle">{!!$course->payment ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                            <td @class(['align-middle','font-weight-bold','text-danger'=>(in_array($course->grade,['F','FX'])),
                                                'text-success'=>(!in_array($course->grade,['F','FX','P','IC','W']))])>{{$course->grade}}</td>
                                            <td class="align-middle font-weight-bold">{{$grades[$year][$semester]['courses'][$course->course_code]}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <table class="table text-left table-borderless">
                                    <tbody>
                                    <tr>
                                        <th>المعدل الفصلي للترم</th>
                                        <th>{{$grades[$year][$semester]['gpa']}}</th>
                                        <th>عدد ساعات المسجلة في الترم</th>
                                        <th>{{$grades[$year][$semester]['hours']}}</th>
                                    </tr>
                                    <tr>
                                        <th>المعدل التراكمي الإجمالي</th>
                                        <th>{{$grades[$year][$semester]['cgpa']}}</th>
                                        <th>عدد ساعات المكتسبه في الترم</th>
                                        <th>{{$grades[$year][$semester]['earned_hours']}}</th>
                                    </tr>
                                    </tbody>
                                </table>
                                <form id="courses_{{$year}}" action="{{route('print.status')}}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-success">طباعة</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="mt-3 text-center">
                        <div class="alert alert-warning">
                            <h6><i class="icon fas fa-ban"></i> لا توجد بيانات مسجلة</h6>
                        </div>
                    </div>
                @endforelse
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });
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
        @if(isset($student))
        $("form[id^='courses_']").on('submit', function (e) {
            e.preventDefault();
            e.returnValue = false;
            let me = $(this);
            let year = me.attr('id').split('_')[1];
            let courses = null;
            let grades = null;
            if (year === 'trans') {
                courses = @json($trans_courses);
                grades = @json($grades)[0];
            } else {
                courses = @json($registrations)[year];
                grades = @json($grades)[year];
            }
            let username = @json($student[1]);
            $.ajax({
                type: 'post',
                url: '{{route('add.session')}}',
                data: {
                    '_token': '{{csrf_token()}}',
                    'key': 'courses',
                    'value': {
                        'username': username,
                        'courses': courses,
                        'year': year,
                        'grades': grades
                    },
                },
                success: function () {
                    me.off('submit');
                    me.submit();
                },
                error: function () {
                },
                complete: function () {
                    me.off('submit');
                }
            });
        });
        @endif
    </script>
@endsection
