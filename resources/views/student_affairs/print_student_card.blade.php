<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="credit hour system for abu-qir institute">
    <meta name="developer" content="Eng. Kirollous Victor">
    <title>طباعة الكارنيهات</title>
    <style>
        html {
            transform: rotateY(180deg);
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
                margin-top: 5px;
            }

        }
    </style>
</head>
<body>
@php $i=0;@endphp
@foreach($students as $student)
    @if($i%4 == 0)
        <div class="page-break">
            @endif
            <div style="display: flex;justify-content: space-between">
                <div style="height: 235px;width: 46%;background-color: white">
                    <div style="height: 200px;display: flex;justify-content: space-around;align-items: center">
                        <div>
                            <div class="qrcode"></div>
                        </div>
                        <div>
                            <img src="{{asset('images/lo5.png')}}"
                                 style="width: 135px;height: 135px;display: block;margin: auto" alt="">
                        </div>
                        <div>
                            <div class="username" data-username="{{$student->username}}"></div>
                        </div>
                    </div>
                    <div style="height: 35px;text-align: center;font-size: x-large;font-weight: bold">
                        aboukir-institutes.edu.eg
                    </div>
                </div>
                <div style="height: 231px;width: 0;border: 2px solid black;margin: 0 5px"></div>
                <div style="height: 235px;width: 46%;background-color: #007BFF">
                    <div style="display: flex;height: 50px;padding: 5px 0;border-bottom: 4px solid yellow;">
                        <div style="width: 40%">
                            <img src="{{$student->photo}}"
                                 style="width: 100px;height: 110px;display: block;margin-left: 25px;margin-top: 15px;
                         border: 3px solid black;border-radius: 5px" alt="">
                        </div>
                        <div style="width: 20%;display: flex;justify-content: center">
                            <img src="{{asset('images/lo4.png')}}"
                                 style="width: 50px;height: 40px;display: block;margin: auto" alt="">
                        </div>
                        <div style="width: 40%;text-align: center;margin-right: 10px;">
                            <div
                                style="font-weight: bolder;color: white;font-size: large;">
                                معاهد أبوقير العليا
                            </div>
                            <div style="font-weight: bold;color: yellow;font-size: small;">المعهد العالى للسياحة والفنادق وترميم الآثار
                            </div>
                        </div>
                    </div>
                    <div style="height: 120px;background-color: white;text-align: right;font-weight: normal;
                    display: flex;border-bottom: 4px solid yellow;">
                        <div style="width: 40%;"></div>
                        <div style="width: 60%;">
                            <div id="name">إسم الطالب : {{$student->name}}</div>
                            <div id="student_code" dir="rtl">كود الطالب : {{$student->username}}</div>
                            <div id="study_group">الفرقة الدراسية : {{$student->study_group}}</div>
                            {{-- <div id="specialization">التخصص : شعبة نظم المعلومات الادارية
                                باللغة {{($student->specialization=='ترميم الاثار و المقتنيات الفنية')? 'العربية':'الانجليزية'}}
                                // {{$student->studying_status}}</div> --}}
                                <div><span style="margin-left: 12px;">الشعبة  </span><span id="departments_id">
                                    {{$student->departments_id}}</span></div>
                        </div>
                    </div>
            <!--        <div style="height: 24px;font-size: x-large;padding: 10px;margin-left: 20px;display:flex;-->
            <!--align-items: center;color: yellow;">-->
            <!--            {{$year}}-->
            <!--        </div>-->
                </div>
            </div>
            <hr>
            @if($i%4 == 0)
        </div>
    @endif
    @php $i++;@endphp
@endforeach
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('js/qrcode.min.js')}}"></script>
<script type="text/javascript">
    $('.qrcode').each(function () {
        new QRCode($(this).get(0), {
            width: 100,
            height: 100,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H,
        }).makeCode('https://www.facebook.com/groups/ahicisc2026/permalink/1330278544175934/');
    });
    $('.username').each(function () {
        new QRCode($(this).get(0), {
            width: 100,
            height: 100,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H,
        }).makeCode($(this).data('username'));
    });
    window.print();
    window.onafterprint = function () {
        window.location.replace("{{route('print.student.cards.index')}}");
    };
</script>
</body>
</html>

