<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="credit hour system for abu-qir institute">
    <meta name="developer" content="Eng. Kirollous Victor">
    <title>طباعة النتائج</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{asset('images/lo.jpg')}}"/>
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <style>
        .border {
            border: 1px black solid !important;
        }

        .table-bordered, .table-bordered tr, .table-bordered td, .table-bordered th {
            border: 1px black solid !important;
            height: 0 !important;
            font-weight: bold !important;
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }

        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    @php $i=0; @endphp
    @foreach($pages as $page)
        @php extract($page); $i++;@endphp
        <div class="page-break">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h1 class="m-0 text-dark text-capitalize">{{$student['username'].' - '.$i}}</h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <div class="row p-1">
                        <div class="col-12">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <p class="text-center font-weight-bolder">وزارة التعليم العالى <br>المعهد العالى
                                        للحاسب
                                        الالي و نظم
                                        المعلومات <br> ابو قير الاسكندرية</p>
                                </div>
                                <div class="col-4">
                                    <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                                </div>
                            </div>
                            <div class="col-12 text-center border">
                                <h3>نتيجة العام الجامعى {{$year}}</h3>
                                <div class="row justify-content-around h4">
                                    <div>الفرقة {{$student['study_group']}}</div>
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
                                    <!--<div class="col-4 mt-3">موقف القبول : {{$student['apply_classification']}}</div>-->
                                    <div class="col-4 mt-3">الساعات التى تم تسجيلها : {{$total_hour}}</div>
                                    <div class="col-4 mt-3">الساعات المكتسبة : {{$total_earned_hour}}</div>
                                    <div class="col-4 mt-3">رقم الجلوس : {{$student['seating_number']}}</div>
                                </div>
                            </div>
                            @foreach($courses[$year] as $semester => $val)
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
                                            @php $course = (array)$course; @endphp
                                            <tr class="mx-0 px-0">
                                                <td class="align-middle">{{$course['course_code']}}</td>
                                                <td class="align-middle">{{$course['name']}}</td>
                                                <td class="align-middle">{{$course['hours']}}</td>
                                                <td class="align-middle">{{$course['elective'] ? 'اختياري' : 'اجباري'}}</td>
                                                <td class="align-middle">{{$course['grade']}}</td>
                                                <td class="align-middle">{{round(floatval($grades[$year][$semester]['courses'][$course['course_code']]),1)}}</td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table class="text-left table-borderless col-12">
                                        <tbody>
                                        <tr class="mx-0 px-0">
                                            <th>المعدل الفصلي للترم</th>
                                            <th>{{$grades[$year][$semester]['gpa']}}</th>
                                            <th>عدد ساعات المسجلة في الترم</th>
                                            <th>{{$grades[$year][$semester]['hours']}}</th>
                                        </tr>
                                        <tr class="mx-0 px-0">
                                            <th>المعدل التراكمي الإجمالي</th>
                                            <th>{{$grades[$year][$semester]['cgpa']}}</th>
                                            <th>عدد ساعات المكتسبه في الترم</th>
                                            <th>{{$grades[$year][$semester]['earned_hours']}}</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                            @if(!isset($courses[$year]['ترم صيفي']))
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
                                            @if ($grades[$year])
                                                    <th>{{end($grades[$year])['cgpa']}}</th>
                                                @endif
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
                                        <td class="align-middle">د. جوزيف جورج
<br><img src="{{asset('images/Joseph_sig.jpg')}}">
</td>
                                        <td class="align-middle">د. سوزان ابو الدهب
<br><img src="{{asset('images/Suzan_sig.jpg')}}">
</td>

                                        <td class="align-middle">د.احمد جنيدى
<br><img src="{{asset('images/blank_sig.png')}}">
</td>

                                        <td class="align-middle">أ.د.م. خالد تعيلب
<br><img src="{{asset('images/blank_sig.png')}}">
</td>
                                        <td class="align-middle">أ.د. شحاتة السيد شحاتة
<br><img src="{{asset('images/shehata_sig.jpg')}}">
</td>


                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div id="f-height"></div>
        </div>
    @endforeach
</div>
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.0.4/popper.js"></script>
<script src="https://cdn.rtlcss.com/bootstrap/v4.2.1/js/bootstrap.min.js"></script>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<script src="{{asset('js/adminlte.js')}}"></script>
<script src="{{asset('js/demo.js')}}"></script>
<script>
    $(window).on('load', function () {
        $('#f-height').height($('footer').height() + 30);
    });
    window.print();
</script>
</body>
</html>
