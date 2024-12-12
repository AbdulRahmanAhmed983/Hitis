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
                    @if(isset($student))
                        <div class="mb-0 mt-3 h4 text-center">
                            <div>الطالب {{$student[0]}}</div>
                            <div>الكود {{$student[1]}}</div>
                        </div>
                    @endif
                </div>
            </div>
            @if(isset($year_semester) and !empty($year_semester))
                @foreach($year_semester as $year => $value)
                    @foreach($value as $semester)
                        <div class="card card-gray collapsed-card">
                            <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                                <h2 class="card-title float-left">حافظات {{$year}} {{$semester}} المسدده</h2>
                                <div class="card-tools  float-right">
                                    <button type="button" class="btn btn-tool">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body" style="overflow-x: auto;">
                                @if(isset($semester_payment[$year][$semester]))
                                    <h5>مصاريف دراسية</h5>
                                    <table class="table table-bordered text-center">
                                        <thead>
                                        <tr>
                                            <th class="align-middle">عدد الساعات المسجله</th>
                                            <th class="align-middle">ثمن الساعه</th>
                                            <th class="align-middle">مصاريف ادارية</th>
                                            <th class="align-middle">المصاريف</th>
                                            <th class="align-middle">المدفوع</th>
                                            <th class="align-middle">الخصومات</th>
                                            <th class="align-middle">باقي المصاريف</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="align-middle">{{$semester_payment[$year][$semester][0]->hours}}</td>
                                            <td class="align-middle">{{$semester_payment[$year][$semester][0]->hour_payment}}</td>
                                            <td class="align-middle">{{$semester_payment[$year][$semester][0]->ministerial_payment}}</td>
                                            <td class="align-middle">{{$semester_payment[$year][$semester][0]->payment}}</td>
                                            <td class="align-middle">{{$semester_payment[$year][$semester][0]->paid_payments}}</td>
                                            <td class="align-middle">{{$total_discount[$year][$semester]['دراسية']['amount'] ?? 0}}</td>
                                            <td class="align-middle">{{$semester_payment[$year][$semester][0]->payment-
                                                        $semester_payment[$year][$semester][0]->paid_payments -
                                                        ($total_discount[$year][$semester]['دراسية']['amount'] ?? 0)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center">
                                        <div class="alert alert-warning">
                                            <h6><i class="icon fas fa-ban"></i> لا توجد مصاريف دراسية مسجلة</h6>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($other_payment[$year][$semester]))
                                    <h5>مصاريف اخرى</h5>
                                    <table class="table table-bordered text-center">
                                        <thead>
                                        <tr>
                                            <th class="align-middle">المصاريف</th>
                                            <th class="align-middle">المدفوع</th>
                                            <th class="align-middle">الخصومات</th>
                                            <th class="align-middle">باقي المصاريف</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="align-middle">{{$total_other_payment[$year][$semester]['payment']}}</td>
                                            <td class="align-middle">{{$total_other_payment[$year][$semester]['paid_payments']}}</td>
                                            <td class="align-middle">{{$total_discount[$year][$semester]['اخرى']['amount'] ?? 0}}</td>
                                            <td class="align-middle">{{$total_other_payment[$year][$semester]['payment']-
                                                        $total_other_payment[$year][$semester]['paid_payments']-
                                                        ($total_discount[$year][$semester]['اخرى']['amount'] ?? 0)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif
                                @if(isset($payment[$year][$semester]))
                                    <table class="table table-hover table-bordered text-center mt-3">
                                        <thead>
                                        <tr>
                                            <th class="align-middle">رقم الحافظة</th>
                                            <th class="align-middle">رقم الايصال</th>
                                            <th class="align-middle">نوع الحافظه</th>
                                            <th class="align-middle">مبلغ الحافظة</th>
                                            <th class="align-middle">تاريخ الحافظة</th>
                                            <th class="align-middle">انشئت من قبل</th>
                                            <th class="align-middle">مسدده</th>
                                            <th class="align-middle">تاريخ التأكيد</th>
                                            <th class="align-middle">مؤكده بواسطة</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($payment[$year][$semester] as $ticket)
                                            <tr>
                                                <td class="align-middle">{{$ticket->ticket_id}}</td>
                                                <td class="align-middle">{{$ticket->ministerial_receipt}}</td>
                                                <td class="align-middle">{{$ticket->type}}</td>
                                                <td class="align-middle">{{$ticket->amount}}</td>
                                                <td class="align-middle">{{$ticket->date}}</td>
                                                <td class="align-middle">{{$ticket->created_by}}</td>
                                                <td class="align-middle">{!!$ticket->used ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                                <td class="align-middle">{{$ticket->confirmed_at}}</td>
                                                <td class="align-middle">{{$ticket->confirmed_by}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="mt-3 text-center">
                                        <div class="alert alert-warning">
                                            <h6><i class="icon fas fa-ban"></i> لا توجد حافظات مسجلة</h6>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @elseif(isset($payment) and empty($payment))
                <div class="mt-3 text-center">
                    <div class="alert alert-warning">
                        <h6><i class="icon fas fa-ban"></i> لا توجد بيانات مسجلة</h6>
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
