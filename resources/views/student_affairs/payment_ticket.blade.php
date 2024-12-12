@extends('layout.layout')
@section('title', 'إصدار حافظة')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @if(session()->has('error'))
                <div class="alert alert-danger mt-3 text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                </div>
            @endif
            @if(session()->has('success'))
                <div class="alert alert-success mt-3 text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                </div>
            @endif
            @if(isset($student) and isset($ticket_id) and isset($payment))
                <div class="card">
                    <div class="card-body">
                        <form id="print" action="{{route('store.ticket')}}" method="post">
                            @csrf
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
                                                <label for="hours">عدد الساعات المسجلة</label>
                                                <input type="text" value="{{$payment->hours}}" readonly required
                                                       class="form-control" name="hours" id="hours">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="payment">إجمالي المبلغ للساعات المسجله</label>
                                                <input type="text" value="{{$total_payment}}" readonly required
                                                       class="form-control" name="payment" id="payment">
                                            </div>
                                        </div>
                                        @error('hours')
                                        <div class="alert alert-danger col-11 mx-auto mt-3 text-center">
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
                                        <div class="alert alert-danger col-11 mx-auto mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                        @error('date')
                                        <div class="alert alert-danger col-11 mx-auto mt-3 text-center">
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
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="total_discount">الخصومات</label>
                                                <input type="text" value="{{$total_discount}}" readonly required
                                                       class="form-control" name="total_discount" id="total_discount">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="withdrawn">خصم من محفظة الطالب</label>
                                                <input type="text" value="{{$wallet->withdrawn}}" readonly
                                                       required class="form-control" name="withdrawn" id="withdrawn">
                                            </div>
                                        </div>
                                        @error('total_discount')
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
                                        @error('withdrawn')
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
                                    <div class="form-group">
                                        <label for="amount">مبلغ</label>
                                        <input type="number" required readonly placeholder="0"
                                               min="{{$last_pay}}" max="{{$last_pay}}" name="amount"
                                               value="{{old('amount',$last_pay)}}" class="form-control" id="amount">
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
                        </form>
                        @if($has_ticket)
                            <form id="delete" action="{{route('delete.ticket',['ticket_id'=>$ticket_id])}}"
                                  method="post">
                                @csrf
                                @method('delete')
                            </form>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" form="print" class="btn btn-success"><i class="fas fa-print"></i> طباعة
                        </button>
                        @if($has_ticket)
                            <button type="submit" form="delete" class="btn btn-danger"><i class="fas fa-trash"></i> حذف
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="get" action="{{route('create.ticket')}}">
                            <div class="row">
                                <div class="forms-group col-md-6 my-2 my-md-0">
                                    <input type="search"
                                           class="form-control {{$errors->has('username')?'is-invalid':''}}"
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
                        </form>
                    </div>
                </div>
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
    </script>
@endsection


