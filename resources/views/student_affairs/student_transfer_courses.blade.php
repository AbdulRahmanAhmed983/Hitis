@extends('layout.layout')
@section('title', 'معادلة المواد للمحولين')
@section('styles')
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
                        @error('username')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                    </form>
                </div>
            </div>
            @if(isset($student))
                <form action="{{route('store.courses.transfer')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>بيانات الطالب</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="name">إسم الطالب</label>
                                    <input type="text" value="{{$student['name']}}" readonly required
                                           class="form-control" name="name" id="name">
                                </div>
                                <div class="form-group col-6">
                                    <label for="study_group">الفرقة الدراسية</label>
                                    <input type="text" value="{{$student['study_group']}}" readonly required
                                           class="form-control" name="study_group" id="study_group">
                                </div>
                                <div class="form-group col-6">
                                    <label for="student_code">كود الطالب</label>
                                    <input type="text" value="{{$student['username']}}" readonly required
                                           class="form-control" name="student_code" id="student_code">
                                </div>
                                <div class="form-group col-6">
                                    <label for="specialization">التخصص</label>
                                    <input type="text" value="{{$student['specialization']}}" readonly required
                                           class="form-control" name="specialization" id="specialization">
                                </div>
                                <div class="form-group col-6">
                                    <label for="departments">الشعبة</label>
                                    <input type="text" value="{{$departments}}" readonly required
                                           class="form-control" name="departments_id" id="specialization">
                                </div>
                                <div class="form-group col-12">
                                    <label for="grades">كشف تقدير المواد</label>
                                    <input type="file" name="grades" id="grades" required
                                           accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                           class="form-control-file btn btn-secondary">
                                    @if($errors->any())
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            @foreach ($errors->all() as $error)
                                                <h6><i class="icon fas fa-ban"></i> {{$error}}</h6>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">تسجيل</button>
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
                    url: '{{route('student.transfer.datalist')}}',
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


