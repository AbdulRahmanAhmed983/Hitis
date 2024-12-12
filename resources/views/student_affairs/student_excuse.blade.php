@extends('layout.layout')
@section('title', 'الاعذار و وقف القيد')
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
                    <form method="get" action="{{route('add.excuses.index')}}">
                        <div class="row">
                            <div class="forms-group col-md-6 my-2 my-md-0">
                                <input type="search"
                                       class="form-control {{$errors->has('student_code')?'is-invalid':''}}"
                                       name="student_code" id="student_code" required value="{{old('student_code')}}"
                                       placeholder="كود او اسم الطالب" list="students">
                                <datalist id="students"></datalist>
                            </div>
                            <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                <button type="submit" class="btn btn-primary col-12"><i class="fas fa-search"></i>
                                    بحث
                                </button>
                            </div>
                        </div>
                        @error('student_code')
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
            @if(isset($excuses) and isset($student))
                <div class="card card-primary">
                    <div class="card-body">
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
                        <h4 class="text-center">{{$student['username']}}</h4>
                        <h4 class="text-center">{{$student['name']}}</h4>
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">السنة الدراسية</th>
                                <th class="align-middle">الترم الدراسي</th>
                                <th class="align-middle">نوع العذر</th>
                                <th class="align-middle">سبب العذر</th>
                                <th class="align-middle">action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($excuses as $excuse)
                                <tr>
                                    <td class="align-middle">{{$excuse->year}}</td>
                                    <td class="align-middle">{{$excuse->semester}}</td>
                                    <td class="align-middle">{{$excuse->type}}</td>
                                    <td class="align-middle">{{$excuse->excuse}}</td>
                                    <td class="align-middle">
                                        @if($excuse->remove)
                                            <form
                                                action="{{route('delete.excuses',['student_code'=>$excuse->student_code,
                                                    'year'=>str_replace('/','-',$excuse->year),'semester'=>$excuse->semester])}}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-outline-danger rounded">حذف
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <form action="{{route('add.excuses')}}" method="post">
                @csrf
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">أضف عذر أو وقف القيد</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="username">كود الطالب</label>
                                <input type="search" class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                       name="username" id="username" required value="{{old('username')}}"
                                       placeholder="كود او اسم الطالب" list="students">
                                <datalist id="students"></datalist>
                            </div>
                            @error('username')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type">النوع</label>
                                <input type="text" class="form-control" name="type" value="عذر عن ترم" readonly>
                                <!--<select class="custom-select {{$errors->has('type')?'is-invalid':''}}" required-->
                                <!--        name="type" id="type">-->
                                <!--    <option value="" hidden></option>-->
                                <!--    <option value="عذر عن ترم" {{old('type')=='عذر عن ترم'?'selected':''}}>عذر عن ترم-->
                                <!--    </option>-->
                                <!--    <option value="وقف قيد" {{old('type')=='وقف قيد'?'selected':''}}>وقف قيد</option>-->
                                <!--</select>-->
                            </div>
                            @error('type')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        @error('courses')
                        <div class="alert alert-danger mt-3 text-center col-11 mx-auto">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                        @error('courses.*')
                        <div class="alert alert-danger mt-3 text-center col-11 mx-auto">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                        
                        
                         <div class="col-6">
                            <div class="form-group">
                                <label for="semester">الفصل الدراسي</label>
                               <select class="custom-select {{$errors->has('semester')?'is-invalid':''}}"
                                    name="semester" id="semester">
                                <option value="" hidden></option>
                                    <option value="ترم أول">ترم أول</option>
                                     <option value="ترم ثاني">ترم ثاني</option>
                            </select>
                            </div>
                            @error('semester')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                         <div class="col-6">
                            <div class="form-group">
                                <label for="semester">السنة الدراسي</label>
                               <select class="custom-select" required name="year" id="year">
                                            <option value="" hidden></option>
                                            @foreach($data_filter['year'] as $value)
                                                <option
                                                    value="{{$value}}"> {{$value}}</option>
                                            @endforeach
                                        </select>
                            </div>
                            @error('year')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        
                        
                        
                        
                        
                        
                        <div id="courses"></div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="excuse">السبب</label>
                                <textarea id="excuse" name="excuse" class="form-control" rows="2"
                                          style="resize: none" required dir="auto">{{old('excuse')}}</textarea>
                            </div>
                            @error('excuse')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">ادخال</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });
        let done = true;
        $('#student_code').on('input', function (e) {
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
                        $('#students').html('').parent().parent()
                            .append('<div class="alert alert-danger mt-3 text-center mx-auto col-11">' +
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
        if ($('#type').val() === "عذر عن ماده او اكثر") {
            $.ajax({
                type: 'get',
                url: '{{route('get.registered.courses')}}',
                data: {
                    'student_code': $('#username').val(),
                },
                success: function (data) {
                    $('#courses').html('').addClass('col-12');
                    let output = '';
                    data.forEach(element => {
                        output += '<tr><td><b>' + element['full_code'] + '</b></td>' +
                            '<td><b>' + element['name'] + '</b></td>' +
                            '<td><div class="icheck-primary d-inline">' +
                            '<input type="checkbox" name="courses[]" id="' + element['full_code'] + '" ' +
                            'value="' + element['full_code'] + '">' +
                            '<label for="' + element['full_code'] + '"></label></div></td></tr>';
                    });

                    $('#courses').append('<table class="table table-bordered table-hover text-center col-12">' +
                        '<thead><tr><th>كود الماده</th><th>اسم الماده</th><th>اختيار</th></tr></thead><tbody>' +
                        output +
                        '</tbody> </table>');
                },
                error: function (data) {
                    $('#courses').html('').removeClass('col-12');
                    $('#students').html('').parent()
                        .append('<div class="alert alert-danger mt-3 text-center">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                            '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                },
                complete: function () {
                    done2 = true;
                }
            });
        }
        let done2 = true;
        $('#type').on('input', function (e) {
            let me = $(this);
            let student_code = $('#username').val();
            if (me.val() === "عذر عن ماده او اكثر" && done2) {
                done2 = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('get.registered.courses')}}',
                    data: {
                        'student_code': student_code,
                    },
                    success: function (data) {
                        $('#courses').html('').addClass('col-12');
                        let output = '';
                        data.forEach(element => {
                            output += '<tr><td><b>' + element['full_code'] + '</b></td>' +
                                '<td><b>' + element['name'] + '</b></td>' +
                                '<td><div class="icheck-primary d-inline">' +
                                '<input type="checkbox" name="courses[]" id="' + element['full_code'] + '" ' +
                                'value="' + element['full_code'] + '">' +
                                '<label for="' + element['full_code'] + '"></label></div></td></tr>';
                        });

                        $('#courses').append('<table class="table table-bordered table-hover text-center col-12">' +
                            '<thead><tr><th>كود الماده</th><th>اسم الماده</th><th>اختيار</th></tr></thead><tbody>' +
                            output +
                            '</tbody> </table>');
                    },
                    error: function (data) {
                        $('#courses').html('').removeClass('col-12');
                        $('#students').html('').parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done2 = true;
                    }
                });
            } else {
                $('#courses').html('').removeClass('col-12');
            }
        });
    </script>
@endsection
