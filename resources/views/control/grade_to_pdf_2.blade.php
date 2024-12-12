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

        @page {
            size: A3 landscape;
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
    @php $i=0;$per_page = 6;@endphp
    @foreach($pages as $page)
        @php extract($page); $i++;@endphp
        <div @class(['page-break'=>($i%$per_page==1)])>
            @if($i%$per_page == 1)
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-12">
                                <h1 class="m-0 text-dark text-capitalize">Page-{{((int)($i/$per_page)+1)}}</h1>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
            <section class="content">
                <div class="container-fluid p-1">
                    <div class="w-100">
                        @if($i%$per_page == 1)
                            <div class="row w-100 m-0 p-0 justify-content-around">
                                <div class="col-4">
                                    <p class="text-center p-0 m-0 font-weight-bolder" style="font-size: x-small">وزارة
                                        التعليم
                                        العالى
                                        <br>المعهد العالى
                                        للحاسب
                                        الالي و نظم
                                        المعلومات <br> ابو قير الاسكندرية</p>
                                </div>
                                <div class="col-4">
                                    <img class="img-fluidd" style="width: 50%;height: 50px"
                                         src="{{asset('images/logo.jpg')}}"
                                         alt="logo">
                                </div>
                                <div class="col-12 text-center h6">
                                    نتيجة امتحانات الفرقة {{$student['study_group']}} (شعبة نظم معلومات الاعمال باللغة
                                    {{$student['lang']}}) للعام الدراسي {{$year}}
                                </div>
                            </div>
                        @endif
                        <div class="card-body m-0 p-0" style="overflow-x: auto;font-size: xx-small">
                            <table class="font-weight-bold text-center col-12">
                                <tbody>
                                <tr>
                                    <td colspan="3"></td>
                                    @foreach($courses[$year] as $semester => $val)
                                        <td colspan="{{count($val)+1}}" class="border">{{$semester}}</td>
                                    @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border">كود الطالب</td>
                                    <td class="align-middle border">{{$student['username']}}</td>
                                    <td class="align-middle border">كود المقرر</td>
                                    @foreach($courses[$year] as $semester => $val)
                                        @foreach($val as $course)
                                            <td class="align-middle border">{{$course->course_code}}</td>
                                        @endforeach
                                        <td rowspan="2" class="align-middle border">المعدل الفصلى</td>
                                    @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border" rowspan="2">اسم الطالب</td>
                                    <td class="align-middle border" rowspan="2">{{$student['name']}}</td>
                                    <td class="align-middle border">اسم المقرر</td>
                                    @foreach($courses[$year] as $semester => $val)
                                        @foreach($val as $course)
                                            <td class="align-middle border">{{$course->name}}</td>
                                        @endforeach
                                    @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border">المجموع</td>
                                    @foreach($courses[$year] as $semester => $val)
                                        @foreach($val as $course)
                                            @if(is_numeric($course->yearly_performance_score) and is_numeric($course->written))
                                                <td class="align-middle border">{{$course->yearly_performance_score + $course->written}}</td>
                                            @else
                                                <td class="align-middle border">{{$course->yearly_performance_score}}</td>
                                            @endif
                                        @endforeach
                                        <td class="align-middle border">{{$grades[$year][$semester]['gpa']}}</td>
                                    @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border">المستوى</td>
                                    <td class="align-middle border">{{$student['study_group-2']}}</td>
                                    <td class="align-middle border">التقدير</td>
                                    @foreach($courses[$year] as $semester => $val)
                                        @foreach($val as $course)
                                            <td class="align-middle border">{{$course->grade}}</td>
                                        @endforeach
                                        <td rowspan="2" class="align-middle border">المعدل التراكمى</td>
                                    @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border">التخصص</td>
                                    <td class="align-middle border">{{$student['specialization']}}</td>
                                    <td class="align-middle border">النقاط</td>
                                    @foreach($courses[$year] as $semester => $val)
                                        @foreach($val as $course)
                                    <td class="align-middle border">{{round(floatval($grades[$year][$semester]['courses'][$course->course_code]), 1)}}</td>
                                    @endforeach
                                 @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border">الساعات المكتسبة</td>
                                    <td class="align-middle border">{{$page['total_earned_hour']}}</td>
                                    <td class="align-middle border" rowspan="2">ملاحظة</td>
                                    @foreach($courses[$year] as $semester => $val)
                                        @foreach($val as $course)
                                            <td class="align-middle border"
                                                rowspan="2">{{$course->note}}</td>
                                        @endforeach
                                        <td rowspan="2"
                                            class="align-middle border">{{$grades[$year][$semester]['cgpa']}}</td>
                                    @endforeach
                                </tr>
                                <tr class="mx-0 px-0">
                                    <td class="align-middle border">المعدل التراكى</td>
                                    @if(is_array($grades[$year]) && $lastGrade = last($grades[$year]))
                                     <td class="align-middle border">{{ $lastGrade['cgpa'] }}</td>
                                      @endif                               
                                       </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            @if($i%$per_page == 0 or $loop->last)
                <div class="card-body m-0 p-0" style="overflow-x: auto;">
                    <table class="table-borderless text-center col-12">
                        <thead>
                        <tr class="mx-0 px-0 h6">
                            <th class="align-middle">كتبه</th>
                            <th class="align-middle">املاه</th>
                            <th class="align-middle">راجعه</th>
                            <th class="align-middle">رئيس الكنترول</th>
                            <th class="align-middle">عميد المعهد</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="mx-0 px-0">
                            <td class="align-middle">د. منى خليل</td>
                            <td class="align-middle">د. اروي بشر  </td>
                            <td class="align-middle">د. سوزان ابو الدهب<br><img src="{{asset('images/Suzan_sig.jpg')}}"></td>
                            <td class="align-middle">أ.م.د. خالد تعيلب</td>
                            <td class="align-middle">أ.د. محمد شومان<br><img src="{{asset('images/shosho.jpeg')}}"></td>                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
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
    window.print();
</script>
</body>
</html>
