@extends('layout.layout')
@section('title', 'بحث عن طالب')
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
                                       name="username" id="username" required
                                       @if(isset($student)) value="{{$student['username']}}"
                                       @else value="{{old('username')}}" @endif
                                       placeholder="كود او اسم الطالب" list="students">
                                <datalist id="students"></datalist>
                            </div>
                            <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                <button type="submit" class="btn btn-primary col-12"><i class="fas fa-search"></i>
                                    بحث
                                </button>
                            </div>
                        </div>
                        @if($errors->has('username'))
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$errors->first('username')}}</h6>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            @if(isset($student))
                <form id="form2" action="{{route('print.student')}}" method="post">
                    @csrf
                    <div class="card card-success">
                        <div class="card-header">
                            <h2>بيانات الطالب</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="name">إسم الطالب</label>
                                        <input type="text" value="{{$student['name']}}" readonly required
                                               class="form-control" name="name" id="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="username">كود الطالب</label>
                                        <input type="text" value="{{$student['username']}}" readonly required
                                               class="form-control" name="username" id="username">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">كلمة المرور</label>
                                        <input type="text" value="{{$student['password']}}" readonly required
                                               class="form-control" name="password" id="password">
                                    </div>
                                    @if(!is_null($exam_place))
                                        <div class="form-group">
                                            <label for="exam_place">رقم اللجنة</label>
                                            <input type="text" value="{{$exam_place->place}}" readonly required
                                                   class="form-control" name="exam_place" id="exam_place">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="study_group">الفرقة الدراسية</label>
                                        <input type="text" value="{{$student['study_group']}}" readonly required
                                               class="form-control" name="study_group" id="study_group">
                                    </div>
                                    <div class="form-group">
                                        <label for="specialization">التخصص</label>
                                        <input type="text" value="{{$student['specialization']}}" readonly required
                                               class="form-control" name="specialization" id="specialization">
                                    </div>
                                    <div class="form-group">
                                        <label for="department">الشعبة</label>
                                        <input type="text" value="{{$department}}" readonly required
                                               class="form-control" name="departments_id" id="department">
                                    </div>
                                    <div class="form-group">
                                        <label for="studying_status">الحالة الدراسية</label>
                                        <input type="text" value="{{$student['studying_status']}}" readonly required
                                               class="form-control" name="studying_status" id="studying_status">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
                        </div>
                    </div>
                </form>
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
        $('#form2').on('submit', function (e) {
            e.preventDefault();
            e.returnValue = false;
            let student = @if(isset($student)) @json($student) @else null @endif;
            let me = $(this);
            if (student) {
                $.ajax({
                    type: 'post',
                    url: '{{route('add.session')}}',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'key': 'student',
                        'value': student,
                    },
                    success: function (data) {
                        me.off('submit');
                        me.submit();
                    },
                    error: function () {
                    },
                    complete: function () {
                    }
                });
            }
        });
    </script>
@endsection


