<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="credit hour system for abu-qir institute">
    <meta name="developer" content="Eng. Kirollous Victor">
    <title>طباعة الكارنيهات</title>
    <style>
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
@for($i = 0; $i < count($students); $i+=2)
    @if($i%8 == 0)
        <div class="page-break">
            @endif
            <div style="display: flex;justify-content: space-between">
                <div style="height: 235px;width: 46%;background-color: #144935">
                    <div style="display: flex;height: 50px;padding: 5px 0;border-bottom: 4px solid yellow;">
                        <div style="width: 40%">
                            <img src="{{$students[$i]->photo}}"
                                 style="width: 100px;height: 110px;display: block;margin-left: 25px;margin-top: 15px;
                         border: 3px solid black;border-radius: 5px" alt="">
                        </div>
                        <div style="width: 20%;display: flex;justify-content: center">
                            <img src="{{asset('images/logo.png')}}"
                                 style="width: 80px;height: 50px;display: block;margin: auto" alt="">
                        </div>
                       <div style="width: 40%;text-align: center;margin-right: 10px;">
                            <div
                                style="font-weight: bolder;color: white;font-size: large;">
                                معاهد أبي-قير العليا
                            </div>
                            <div style="font-weight: bold;color: yellow;font-size: small;">المعهد العالي للسياحة والفنادق وترميم الاثار
                            </div>
                        </div>
                    </div>
                    <div style="height: 120px;background-color: white;text-align: right;font-weight: normal;
                    display: flex;border-bottom: 4px solid yellow;">
                        <div style="width: 40%;"></div>
                        <div style="width: 60%;">
                            <div id="name">إسم الطالب : {{$students[$i]->name}}</div>
                            <div id="seating_number" dir="rtl">رقم الجلوس : {{$students[$i]->seating_number}} - {{ $students[$i]->exam_place }}</div>
                            <div id="student_code" dir="rtl">كود الطالب : {{$students[$i]->username}}</div>
                            <div id="study_group">الفرقة الدراسية : {{$students[$i]->study_group}}</div>
                         <div id="specialization">التخصص : شعبة نظم المعلومات الادارية
                                باللغة {{($students[$i]->specialization=='ترميم الاثار و المقتنيات الفنية')? 'ترميم الاثار':'سياحة و فنادق'}}
                               </div>
                                <div>
                                <span style="margin-left: 12px;">الشعبة</span>
                                <span id="departments_id">
                                    {{ $students[$i]->departments_id }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style="height: 24px;font-size: large;padding: 10px;margin-left: 20px;display:flex;
                        align-items: center;color: yellow;">
                       {{$year}}

                    </div>
                </div>
                <div style="height: 231px;width: 0;border: 2px solid black;margin: 0 5px"></div>
                @if(!empty($students[$i+1]))
                    <div style="height: 235px;width: 46%;background-color: #144935">
                        <div style="display: flex;height: 50px;padding: 5px 0;border-bottom: 4px solid yellow;">
                            <div style="width: 40%">
                                <img src="{{$students[$i+1]->photo}}"
                                     style="width: 100px;height: 110px;display: block;margin-left: 25px;margin-top: 15px;
                         border: 3px solid black;border-radius: 5px" alt="">
                            </div>
                           <div style="width: 20%;display: flex;justify-content: center">
                            <img src="{{asset('images/logo.png')}}"
                                 style="width: 80px;height: 50px;display: block;margin: auto" alt="">
                        </div>
                       <div style="width: 40%;text-align: center;margin-right: 10px;">
                           <div
                                style="font-weight: bolder;color: white;font-size: large;">
                                معاهد أبي-قير العليا
                            </div>
                            <div style="font-weight: bold;color: yellow;font-size: small;">المعهد العالي للسياحة والفنادق وترميم الاثار
                            </div>
                        </div>
                        </div>
                        <div style="height: 120px;background-color: white;text-align: right;font-weight: normal;
                    display: flex;border-bottom: 4px solid yellow;">
                            <div style="width: 40%;"></div>
                            <div style="width: 60%;">
                                <div id="name">إسم الطالب : {{$students[$i+1]->name}}</div>
                             <div id="seating_number" dir="rtl">رقم الجلوس : {{$students[$i+1]->seating_number}} - {{ $students[$i+1]->exam_place }}</div>
                                <div id="student_code" dir="rtl">كود الطالب : {{$students[$i+1]->username}}</div>
                                <div id="study_group">الفرقة الدراسية : {{$students[$i+1]->study_group}}</div>
                       <div id="specialization">التخصص : شعبة نظم المعلومات الادارية
                                باللغة {{($students[$i+1]->specialization=='ترميم الاثار و  الفنية')? 'ترميم الاثار':'سياحة و فنادق'}}
                               </div>

                                    <div>
                                    <span style="margin-left: 12px;">الشعبة</span>
                                    <span id="departments_id">
                                        {{ $students[$i+1]->departments_id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div style="height: 24px;font-size: large;padding: 10px;margin-left: 20px;display:flex;
                        align-items: center;color: yellow;">
                       {{$year}}
                        </div>
                    </div>
                @endif
            </div>
            <hr>
            @if($i%8 == 0)
        </div>
    @endif
@endfor
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<script type="text/javascript">
    window.print();
    window.onafterprint = function () {
        window.location.replace("{{route('print.student.seating.number.cards.index')}}");
    };
</script>
</body>
</html>
