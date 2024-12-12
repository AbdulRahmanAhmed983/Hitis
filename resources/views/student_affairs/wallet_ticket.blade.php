@extends('layout.layout')
@section('title', 'إصدار حافظة محفظة')
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
            <div class="card card-primary">
                <div class="card-body">
                    <form method="get" action="{{route('create.wallet.ticket')}}">
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
            @if(isset($student))
                <div class="card">
                    <div class="card-body">
                        <div class="h4 text-center">
                            <div>الطالب {{$student['name']}}</div>
                            <div>الكود {{$student['username']}}</div>
                        </div>
                        <div>
                            @if(!is_null($wallet))
                                <table class="table table-borderless h4 text-center">
                                    <tr>
                                        <td class="col-6">الرصيد الحالى</td>
                                        <td class="col-6">{{$wallet->amount}}</td>
                                    </tr>
                                </table>
                                @if(!empty($transactions))
                                    <table class="table table-bordered text-center">
                                        <tr>
                                            <th>السنة</th>
                                            <th>الترم</th>
                                            <th>التاريخ</th>
                                            <th>نوع العملية</th>
                                            <th>المبلغ</th>
                                            <th>وصف</th>
                                        </tr>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>{{$transaction->year}}</td>
                                                <td>{{$transaction->semester}}</td>
                                                <td>{{$transaction->date}}</td>
                                                <td>{{$transaction->type}}</td>
                                                <td>{{$transaction->amount}}</td>
                                                <td>{{$transaction->reason}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            @else
                                <div class="alert alert-warning text-center">
                                    <h6>الطالب ليس لديه محفظة</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($student) and isset($ticket_id))
                <form action="{{route('store.wallet.ticket')}}" method="post">
                    @csrf
                    <div class="card card-primary collapsed-card">
                        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                            <h2 class="card-title float-left">إصدار حافظة محفظة</h2>
                            <div class="card-tools  float-right">
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
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
                                        <div class="alert alert-danger mx-auto col-11 mt-3 text-center">
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
                                               value="{{$year}}-{{$semester}}" class="form-control"
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
                                    <div class="form-group">
                                        <label for="amount">مبلغ</label>
                                        <input type="number" required placeholder="0" min="0"
                                               step="0.25" max="20000" value="{{old('amount')}}" class="form-control"
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
                                    <div class="form-group">
                                        <label for="amount">بيان السداد</label>
                                            <textarea class="form-control" rows="5" id="comment" style="max-width:100%;" name="note"></textarea>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
                        </div>
                    </div>
                </form>
            @elseif(isset($student))
                <div class="card card-primary collapsed-card">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">إصدار حافظة محفظة قديمة</h2>
                        <div class="card-tools  float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('delete.wallet.ticket',['student_code'=>$student['username'],
                            'ticket_id'=>$student['ticket_id']])}}" id="delete-wallet" method="post">
                            @csrf
                            @method('delete')
                        </form>
                        <form action="{{route('store.wallet.ticket')}}" id="store-wallet" method="post">
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
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ticket_id">رقم الحافظة</label>
                                                <input type="text" value="{{$student['ticket_id']}}" readonly required
                                                       class="form-control" name="ticket_id" id="ticket_id">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="date">تاريخ الحافظة</label>
                                                <input type="date" required value="{{$student['date']}}" readonly
                                                       class="form-control" name="date" id="date">
                                            </div>
                                        </div>
                                        @error('ticket_id')
                                        <div class="alert alert-danger mx-auto col-11 mt-3 text-center">
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
                                               value="{{$student['semester']}}" class="form-control"
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
                                    <div class="form-group">
                                        <label for="amount">مبلغ</label>
                                        <input type="number" required placeholder="0" min="0"
                                               step="0.25" max="20000" value="{{$student['amount']}}" readonly
                                               class="form-control" name="amount" id="amount">
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
                    </div>
                    <div class="card-footer">
                        <button type="submit" form="store-wallet" class="btn btn-success"><i class="fas fa-print"></i>
                            طباعة
                        </button>
                        <button type="submit" form="delete-wallet" class="btn btn-danger"><i class="fas fa-trash"></i>
                            حذف
                        </button>
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


