@extends('layout.layout')
@section('title', 'الخصومات المالية')
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
                    <form method="get" action="{{route('discount.index')}}">
                        <div class="row">
                            <div class="forms-group col-md-6 my-2 my-md-0">
                                <input type="search" name="username" id="username2" required
                                       class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                       value="{{old('username')}}" placeholder="كود او اسم الطالب" list="student">
                                <datalist id="student"></datalist>
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
            @if(isset($discounts) and isset($student))
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
                                <th class="align-middle">نوع الخصم</th>
                                <th class="align-middle">سبب الخصم</th>
                                <th class="align-middle">قيمة الخصم</th>
                                <th class="align-middle">action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($discounts as $discount)
                                <tr>
                                    <td class="align-middle">{{$discount->year}}</td>
                                    <td class="align-middle">{{$discount->semester}}</td>
                                    <td class="align-middle">{{$discount->type}}</td>
                                    <td class="align-middle">{{$discount->reason}}</td>
                                    <td class="align-middle">{{$discount->amount}}</td>
                                    <td class="align-middle">
                                        @if($discount->remove)
                                            <form
                                                action="{{route('delete.discount',
                                                    ['student_code'=>$discount->student_code,'id'=>$discount->id])}}"
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
            <form action="{{route('add.discount')}}" method="post">
                @csrf
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">أضف خصم</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="username">كود الطالب</label>
                                    <input type="search"
                                           class="form-control username {{$errors->has('username')?'is-invalid':''}}"
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
                                    <label for="type">النوع</label>
                                    <select class="custom-select username {{$errors->has('type')?'is-invalid':''}}"
                                            required
                                            name="type" id="type">
                                        <option value="" hidden></option>
                                        <option value="دراسية" {{old('type')=='دراسية'?'selected':''}}>دراسية</option>
                                        <option value="محفظة" {{old('type')=='محفظة'?'selected':''}}>محفظة</option>
                                        <option value="ادارية" {{old('type')=='ادارية'?'selected':''}}>ادارية</option>
                                         <option value="خدمات تعليمية" {{old('type')=='خدمات تعليمية'?'selected':''}}>خدمات تعليمية</option>
                                    </select>
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
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="amount">المبلغ</label>
                                    <input type="number" class="form-control {{$errors->has('amount')?'is-invalid':''}}"
                                           required min="1" name="amount" id="amount" value="{{old('amount')}}">
                                </div>
                                @error('amount')
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
                                    <label for="reason">السبب</label>
                                    <input type="text" class="form-control {{$errors->has('reason')?'is-invalid':''}}"
                                           name="reason" id="reason" required value="{{old('reason')}}">
                                </div>
                                @error('reason')
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">ادخال</button>
                    </div>
                </div>
            </form>
            <form action="{{route('add.discount')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">أضف خصم Excel</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="file">الملف</label>
                            <input type="file"
                                   accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                   class="form-control-file btn btn-primary {{$errors->has('file')?'is-invalid':''}}"
                                   name="file" id="file" required value="{{old('file')}}">
                        </div>
                        @error('discounts')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                        @error('discounts.*')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
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
        let done3 = true;
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
                        $('#students').html('').parent()
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
        let done4 = true;
        $('.username').on('input', function (e) {
            let ek = $('.username').map((_, el) => el.value).get();
            if (ek[0].length === 7 && ek[1] === 'دراسية' && done4) {
                done4 = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('get.discount')}}',
                    data: {
                        'student_code': ek[0],
                    },
                    success: function (data) {
                        $('<div class="col-6">' +
                            '<div class="form-group">' +
                            '<label for="type">الترم</label>' +
                            '<select class="custom-select" required ' +
                            'name="semester" id="semester">' +
                            '<option value="" hidden></option>' +
                            '<option value="' + data['value'] + '">' + data['text'] + '</option>' +
                            '</select>' +
                            '</div>' +
                            '</div>').insertAfter($('#type').parent().parent());
                    },
                    error: function (data) {
                        $('#semester').parent().parent().remove();
                        $('<div class="alert alert-danger mt-3 text-center">' +
                            '<button type="button" class="close" data-dismiss="alert"' +
                            ' aria-hidden="true">&times;</button>' +
                            '<h6><i class="icon fas fa-ban"></i> ' + data['responseJSON']['error'] + '</h6>' +
                            '</div>').insertAfter($('#username').parent());
                    },
                    complete: function () {
                        done4 = true;
                    }
                });
            } else {
                $('#semester').parent().parent().remove();
            }
        });
        let done2 = true;
        $('#username2').on('input', function (e) {
            let me = $(this);
            if (me.val().length >= 3 && done2) {
                done2 = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('student.datalist')}}',
                    data: {
                        'search': me.val(),
                    },
                    success: function (data) {
                        $('#student').html('');
                        for (let i = 0; i < data.length; i++) {
                            $('#student').append('<option value="' + data[i]['username'] + '">'
                                + data[i]['name'] + '</option>');
                        }
                    },
                    error: function (data) {
                        $('#student').html('').parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done2 = true;
                    }
                });
            } else {
                $('#student').html('');
            }
        });
    </script>
@endsection
