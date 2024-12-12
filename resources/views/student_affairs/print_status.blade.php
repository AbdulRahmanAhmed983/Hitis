@extends('layout.layout')
@section('title', $student['username'])
@section('styles')
    <style>
        .border {
            border: 1px black solid !important;
        }

        .table-bordered, .table-bordered tr, .table-bordered td, .table-bordered th {
            border: 1px black solid !important;
            height: 0 !important;
            font-weight: bold !important;
        }
    </style>
@endsection
@section('content')
    <div class="row p-1">
        <div class="col-12">
            <div class="row justify-content-between">
                <div class="col-4">
                    <p class="text-center font-weight-bolder">وزارة التعليم العالى <br>المعهد العالى للحاسب الالي و نظم
                        المعلومات <br> ابو قير الاسكندرية</p>
                </div>
                <div class="col-4">
                    <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                </div>
            </div>
            <div class="col-12 text-center border">
                @if($year == 'trans')
                    <h3>نتيجة معادلة مواد من الخارج</h3>
                @else
                    <h3>نتيجة العام الجامعى {{$year}}</h3>
                @endif
                <div class="row justify-content-around h4">
                    <div>المستوى {{$student['study_group']}}</div>
                    @if($student['specialization'] == 'ترميم الاثار و المقتنيات الفنية')
                        <div>شعبة نظم معلومات الاعمال باللغة العربية</div>
                    @else
                        <div>شعبة نظم معلومات الاعمال باللغة الانجليزية</div>
                    @endif
                </div>
            </div>
            <div class="col-12 text-left">
                <div class="row h6">
                    <div class="col-4 mt-3">اسم الطالب : {{$student['name']}}</div>
                    <div class="col-4 mt-3">كود الطالب : {{$student['username']}}</div>
                    <div class="col-4 mt-3">تاريخ الميلاد : {{$student['birth_date']}}</div>
                    <div class="col-4 mt-3">المؤهل : {{$student['certificate_obtained']}}</div>
                    <div class="col-4 mt-3">سنة المؤهل : {{$student['certificate_obtained_date']}}</div>
                    <div class="col-4 mt-3">تاريخ الالتحاق : {{$student['registration_date']}}</div>
                    <div class="col-4 mt-3">موقف القبول : {{$student['apply_classification']}}</div>
                    <div class="col-4 mt-3">الساعات التى تم تسجيلها : {{$total_hour}}</div>
                    <div class="col-4 mt-3">الساعات المكتسبة : {{$total_earned_hour}}</div>
                    {{--                    <div class="col-4 mt-3">حالة الطالب : {{$student['studying_status']}}</div>--}}
                </div>
            </div>
            @if($year == 'trans')
                <div class="card-body mx-0 px-0" style="overflow-x: auto;">
                    <h4 class="font-weight-bolder col-12">مواد معادلة من الخارج</h4>
                    <table class="table-bordered text-center col-12">
                        <thead>
                        <tr class="mx-0 px-0">
                            <th class="align-middle">كود المادة</th>
                            <th class="align-middle">اسم المادة</th>
                            <th class="align-middle">عدد ساعات المادة</th>
                            <th class="align-middle">تصنيف المادة</th>
                            <th class="align-middle">التقدير المكتسب</th>
                            <th class="align-middle">النقاط</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($courses as $course)
                            <tr class="mx-0 px-0">
                                <td class="align-middle">{{$course['course_code']}}</td>
                                <td class="align-middle">{{$course['name']}}</td>
                                <td class="align-middle">{{$course['hours']}}</td>
                                <td class="align-middle">{{$course['elective'] ? 'اختياري' : 'اجباري'}}</td>
                                <td class="align-middle">{{$course['grade']}}</td>
                                <td class="align-middle">{{round($grades[0]['courses'][$course['course_code']],1)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <table class="text-left table-borderless col-12">
                        <tbody>
                        <tr class="mx-0 px-0">
                            <th>المعدل الفصلي للترم</th>
                            <th>{{$grades[0]['gpa']}}</th>
                            <th>عدد ساعات المسجلة في الترم</th>
                            <th>{{$grades[0]['hours']}}</th>
                        </tr>
                        <tr class="mx-0 px-0">
                            <th>المعدل التراكمي الإجمالي</th>
                            <th>{{$grades[0]['cgpa']}}</th>
                            <th>عدد ساعات المكتسبه في الترم</th>
                            <th>{{$grades[0]['earned_hours']}}</th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @else
                @foreach($courses as $semester => $val)
                    <div class="card-body mx-0 px-0" style="overflow-x: auto;">
                        <h4 class="font-weight-bolder col-12">{{$semester}}</h4>
                        <table class="table-bordered text-center col-12">
                            <thead>
                            <tr class="mx-0 px-0">
                                <th class="align-middle">كود المادة</th>
                                <th class="align-middle">اسم المادة</th>
                                <th class="align-middle">عدد ساعات المادة</th>
                                <th class="align-middle">تصنيف المادة</th>
                                <th class="align-middle">التقدير المكتسب</th>
                                <th class="align-middle">النقاط</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($val as $course)
                                <tr class="mx-0 px-0">
                                    <td class="align-middle">{{$course['course_code']}}</td>
                                    <td class="align-middle">{{$course['name']}}</td>
                                    <td class="align-middle">{{$course['hours']}}</td>
                                    <td class="align-middle">{{$course['elective'] ? 'اختياري' : 'اجباري'}}</td>
                                    <td class="align-middle">{{$course['grade']}}</td>
                                    <td class="align-middle">{{round($grades[$semester]['courses'][$course['course_code']],1)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table class="text-left table-borderless col-12">
                            <tbody>
                            <tr class="mx-0 px-0">
                                <th>المعدل الفصلي للترم</th>
                                <th>{{$grades[$semester]['gpa']}}</th>
                                <th>عدد ساعات المسجلة في الترم</th>
                                <th>{{$grades[$semester]['hours']}}</th>
                            </tr>
                            <tr class="mx-0 px-0">
                                <th>المعدل التراكمي الإجمالي</th>
                                <th>{{$grades[$semester]['cgpa']}}</th>
                                <th>عدد ساعات المكتسبه في الترم</th>
                                <th>{{$grades[$semester]['earned_hours']}}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
                @if(!isset($courses['ترم صيفي']))
                    <div class="card-body mx-0 px-0" style="overflow-x: auto;">
                        <h4 class="font-weight-bolder col-12">ترم صيفي</h4>
                        <table class="table-bordered text-center col-12">
                            <thead>
                            <tr class="mx-0 px-0">
                                <th class="align-middle">كود المادة</th>
                                <th class="align-middle">اسم المادة</th>
                                <th class="align-middle">عدد ساعات المادة</th>
                                <th class="align-middle">تصنيف المادة</th>
                                <th class="align-middle">التقدير المكتسب</th>
                                <th class="align-middle">النقاط</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="mx-0 px-0">
                                <td class="align-middle">&nbsp;</td>
                                <td class="align-middle">&nbsp;</td>
                                <td class="align-middle">&nbsp;</td>
                                <td class="align-middle">&nbsp;</td>
                                <td class="align-middle">&nbsp;</td>
                                <td class="align-middle">&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="text-left table-borderless col-12">
                            <tbody>
                            <tr class="mx-0 px-0">
                                <th>المعدل الفصلي للترم</th>
                                <th>0</th>
                                <th>عدد ساعات المسجلة في الترم</th>
                                <th>0</th>
                            </tr>
                            <tr class="mx-0 px-0">
                                <th>المعدل التراكمي الإجمالي</th>
                                <th>{{end($grades)['cgpa']}}</th>
                                <th>عدد ساعات المكتسبه في الترم</th>
                                <th>0</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                @if(!is_null($notes))
                    <div class="card-body mx-0 px-0" style="overflow-x: auto;">
                        <h4 class="font-weight-bolder col-12">ملاحظات</h4>
                        <table class="table-bordered text-center col-12">
                            <thead>
                            <tr class="mx-0 px-0">
                                <th class="align-middle">الترم</th>
                                <th class="align-middle">الملحوظه</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notes as $note)
                                <tr class="mx-0 px-0">
                                    <td class="align-middle">{{$note->semester}}</td>
                                    <td class="align-middle">{{$note->note}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body mx-0 px-0" style="overflow-x: auto;">
                        <h4 class="font-weight-bolder col-12">ملاحظات</h4>
                        <table class="table-bordered text-center col-12">
                            <thead>
                            <tr class="mx-0 px-0">
                                <th class="align-middle">الترم</th>
                                <th class="align-middle">الملحوظه</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="mx-0 px-0">
                                <td class="align-middle">&nbsp;</td>
                                <td class="align-middle">&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="card-body mx-0 px-0" style="overflow-x: auto;">
                    <table class="table-borderless text-center col-12">
                        <thead>
                        <tr class="mx-0 px-0 h4">
                            <th class="align-middle">كتبه</th>
                            <th class="align-middle">املاه</th>
                            <th class="align-middle">راجعه</th>
                            <th class="align-middle">رئيس الكنترول</th>
                            <th class="align-middle">عميد المعهد</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="mx-0 px-0 h5">
                            <td class="align-middle">د. نسرين نصر الدين</td>
                            <td class="align-middle">د. محمد ابو زيد</td>
                            <td class="align-middle">د. سوزان ابو الدهب</td>
                            <td class="align-middle">أ.م.د. خالد تعيلب</td>
                            <td class="align-middle">أ.د. محمد شومان</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        window.print();
        window.onbeforeprint = function () {
            $('nav.main-header').hide();
            $('aside.main-sidebar').hide();
            $('footer.main-footer').hide();
            $('.content-wrapper').removeClass('content-wrapper');
        };
        window.onafterprint = function () {
            window.location.replace("{{url()->previous()}}");
        };
    </script>
@endsection
