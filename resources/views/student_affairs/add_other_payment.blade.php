@extends('layout.layout')
@section('title', 'مصروفات اخرى')
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
            @if(isset($student) and isset($ticket_id) and isset($payment))
                <form action="{{route('store.other.ticket')}}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">إسم الطالب</label>
                                        <input type="text" value="{{$student['name']}}" readonly required
                                               class="form-control" name="name" id="name">
                                        @error('name')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="student_code">كود الطالب</label>
                                        <input type="text" value="{{$student['username']}}" readonly required
                                               class="form-control" name="student_code" id="student_code">
                                        @error('student_code')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="study_group">الفرقة الدراسية</label>
                                                <input type="text" value="{{$student['study_group']}}" readonly required
                                                       class="form-control" name="study_group" id="study_group">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="specialization">التخصص</label>
                                                <input type="text" value="{{$student['specialization']}}" readonly
                                                       required class="form-control" name="specialization"
                                                       id="specialization">
                                            </div>
                                        </div>
                                        @error('study_group')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                        @error('specialization')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="type">سبب الحافظه</label>
                                                <input type="text" value="{{$payment->type}}" readonly required
                                                       class="form-control" name="type" id="type">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="payment">إجمالي المبلغ</label>
                                                <input type="text" value="{{$total_payment}}" readonly required
                                                       class="form-control" name="payment" id="payment">
                                            </div>
                                        </div>
                                        @error('type')
                                        <div class="alert alert-danger col-12 mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ticket_id">رقم الحافظة</label>
                                                <input type="text" value="{{$ticket_id}}" readonly required
                                                       class="form-control" name="ticket_id" id="ticket_id">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="date">تاريخ الحافظة</label>
                                                <input type="date" required value="{{old('date',date('Y-m-d'))}}"
                                                       class="form-control" name="date" id="date">
                                            </div>
                                        </div>
                                        @error('ticket_id')
                                        <div class="alert alert-danger col-12 mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                        @error('date')
                                        <div class="alert alert-danger col-12 mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="semester">الفصل الدراسي</label>
                                        <input type="text" required readonly
                                               value="{{$payment->year}}-{{$payment->semester}}" class="form-control"
                                               name="semester" id="semester">
                                        @error('semester')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <!--{{-- <div class="form-group">-->
                                    <!--    <label for="withdrawn">خصم من محفظة الطالب</label>-->
                                    <!--    <input type="text" value="{{$wallet->withdrawn}}" readonly-->
                                    <!--           required class="form-control" name="withdrawn" id="withdrawn">-->
                                    <!--</div> --}}-->
                                    <div class="form-group">
                                        <label for="amount">مبلغ</label>
                                        <input type="number" required placeholder="0" min="{{$last_pay}}"
                                               step="0.25" max="{{$last_pay}}" readonly
                                               value="{{$total_payment}}" class="form-control"
                                               name="amount" id="amount">
                                        @error('amount')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
                            <button type="submit" class="btn btn-danger" form="delete-other-payment">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                    </div>
                </form>
                <form id="delete-other-payment" action="{{route('delete.other.payment',['id'=>$payment->id])}}"
                      method="post">
                    @csrf
                    @method('delete')
                </form>
            @else
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="get" action="{{route('add.other.ticket')}}">
                            <div class="row">
                                <div class="forms-group col-md-6 my-2 my-md-0">
                                    <input type="search"
                                           class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                           name="username" id="username1" required value="{{old('username')}}"
                                           placeholder="كود او اسم الطالب" list="students1">
                                    <datalist id="students1"></datalist>
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
            @endif
            <form action="{{route('store.other.payment')}}" method="post">
                @csrf
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">اضافة مصروفات اخرى</h2>
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
                                           class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                           name="username" id="username2" required value="{{old('username')}}"
                                           placeholder="كود او اسم الطالب" list="students2">
                                    <datalist id="students2"></datalist>
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
                                    <label for="type">السبب</label>
                                    <input type="text" class="form-control {{$errors->has('type')?'is-invalid':''}}"
                                           name="type" id="type" required value="{{old('type')}}">
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
                                    <label for="payment">القيمة</label>
                                    <input type="number"
                                           class="form-control {{$errors->has('payment')?'is-invalid':''}}"
                                           name="payment" id="payment" required value="{{old('payment')}}">
                                </div>
                                @error('payment')
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
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });
        let done = true;
        $('#username1').on('input', function () {
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
                        $('#students1').html('');
                        for (let i = 0; i < data.length; i++) {
                            $('#students1').append('<option value="' + data[i]['username'] + '">'
                                + data[i]['name'] + '</option>');
                        }
                    },
                    error: function (data) {
                        $('#students1').html('').parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done = true;
                    }
                });
            } else {
                $('#students1').html('');
            }
        });
        $('#username2').on('input', function () {
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
                        $('#students2').html('');
                        for (let i = 0; i < data.length; i++) {
                            $('#students2').append('<option value="' + data[i]['username'] + '">'
                                + data[i]['name'] + '</option>');
                        }
                    },
                    error: function (data) {
                        $('#students2').html('').parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done = true;
                    }
                });
            } else {
                $('#students2').html('');
            }
        });
    </script>
@endsection
