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
                <div class="card card-gray">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">مواد معادلة من الخارج</h2>
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
                                    <td @class(['align-middle','font-weight-bold','text-danger'=>($course->grade === "F"),
                                                'text-success'=>($course->grade !== "F" and $course->grade !== "P")])>{{$course->grade}}</td>
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
                    </div>
                </div>
            @endif
            @if(isset($year_semester))
                @forelse($year_semester as $year => $value)
                    @foreach($value as $semester)
                        <div class="card card-gray collapsed-card">
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
                                @endif
                                @if(isset($registrations[$year][$semester]))
                                    <table class="table table-hover table-bordered text-center mt-3">
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
                                        @foreach($registrations[$year][$semester] as $course)
                                            <tr>
                                                <td class="align-middle">{{$course->course_code}}</td>
                                                <td class="align-middle">{{$course->name}}</td>
                                                <td class="align-middle">{{$course->hours}}</td>
                                                <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                                <td class="align-middle">{{$course->courses_semester}}</td>
                                                <td class="align-middle">{!! $course->guidance ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                                <td class="align-middle">{!!$course->payment ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                                <td class="align-middle">{{$course->grade}}</td>
                                                <td class="align-middle font-weight-bold">{{$grades[$year][$semester]['courses'][$course->course_code]}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table class="table text-left table-bordered mt-3">
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
                                @endif
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
    </script>
@endsection
