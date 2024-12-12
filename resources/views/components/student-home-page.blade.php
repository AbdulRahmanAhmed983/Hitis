
    
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-body">
                 
                <div @class(['h4','text-danger'=>($current_warning->warning==2),
                    'text-warning'=>($current_warning->warning==1),'text-success'=>($current_warning->warning==0)])>
                    عدد الانذارات الاكاديمية
                    ({{$current_warning->warning}})
                </div>
                <h5>تنبيه</h5>
                <div class="text-gray-dark" style="font-size: large">
                    <p>على الطالب الذى وجه له انذار اكاديمي ان يرفع <b>معدله التراكمي (CGPA)</b> الى
                        <b>({{$warning_threshold}})</b> فما فوق لالغاء مفعول الانذار في مده اقصاها فصلين دراسيين.</p>
                    <p>يفصل الطالب من التخصص اذا اخفق في رفع <b>معدله التراكمي (CGPA)</b> الى
                        <b>({{$warning_threshold}})</b> بعد مرور مدة فصلين دراسيين متتاليين.</p>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($advisor))
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body">
                    <h4>المرشد الأكاديمي الخاص بك هو/هي: {{$advisor->name}}</h4>
                  

                </div>
            </div>
        </div>
    @endif
    @if(!empty($alerts))
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body">
                    @foreach($alerts as $alert)
                        <div class="alert alert-{{$alert->status}} my-1 mx-auto text-center col-12">
                            <span
                                class="font-weight-bolder float-left col-12 text-left">تنبيه من {{$alert->category}}</span>
                            <h4>{{$alert->reason}}</h4>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if(!in_array('danger', array_column(json_decode(json_encode($alerts), true), 'status')))
        @if(!$old_payment)
            <div class="col-md-6 col-12">
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">المعدل التراكمي للدرجات (CGPA)</h2>
                        <div class="card-tools  float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="progress" style="height: 40px;">
                            <div role="progressbar" style="width: {{100 - ($student['cgpa']/4.0)*100}}%"
                                 aria-valuenow="{{100 - ($student['cgpa']/4.0)*100}}"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                            <div
                                @class(['progress-bar',
                                    'bg-success'=>($student['cgpa']>=3),
                                    'bg-warning'=>($student['cgpa']<3 and $student['cgpa']>=2),
                                    'bg-danger'=>($student['cgpa']<2)])
                                role="progressbar" title="{{$student['cgpa']}}"
                                style="width: {{($student['cgpa']/4.0)*100}}%"
                                aria-valuenow="{{($student['cgpa']/4.0)*100}}" aria-valuemin="0" aria-valuemax="100">
                                <span class="font-weight-bold" style="font-size:20px;">{{$student['cgpa']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">الساعات المكتسبة</h2>
                        <div class="card-tools  float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="progress" style="height: 40px;">
                            <div role="progressbar" style="width: {{100 - ($student['earned_hours']/132)*100}}%"
                                 aria-valuenow="{{100 - ($student['earned_hours']/4.0)*100}}"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar" role="progressbar" title="{{$student['earned_hours']}}"
                                 style="width: {{($student['earned_hours']/132.0)*100}}%"
                                 aria-valuenow="{{($student['earned_hours']/132.0)*100}}" aria-valuemin="0"
                                 aria-valuemax="100">
                                <span class="font-weight-bold"
                                      style="font-size:20px;">{{$student['earned_hours']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!--<div class="col-12">-->
            <!--    <div class="card card-primary">-->
            <!--        <div class="card-body">-->
            <!--            <div class="alert alert-danger my-1 mx-auto text-center">-->
            <!--                <h4>برجاء اكمال المستحقات المالية التى تقدر ب {{$pay}} جنيه او اكمال الحافظات السابقة</h4>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        @endif
    @endif
     @if ($administrativeExpenses and $payment_used == 0)
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-body">
                <div class="alert alert-danger my-1 mx-auto text-center">
                    <h4>برجاء اكمال المستحقات الماليه الادارية </h4>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if(!is_null($exam_place))
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h2 class="card-title float-left">رقم اللجنة</h2>
                </div>
                <div class="card-body">
                        <h4 class="text-center">رقم لجنة الامتحان الخاصة بك هو {{$exam_place->place}}</h4>
                    <!--@if(!$old_payment)-->
                    <!--@else-->
                    <!--    <div class="alert alert-danger my-1 mx-auto text-center">-->
                    <!--        <h4 class="text-center bg-danger">برجاء اكمال المستحقات المالية او اكمال الحافظات-->
                    <!--            السابقة</h4>-->
                    <!--    </div>-->
                    <!--@endif-->
                </div>
            </div>
        </div>
    @endif
    @if(!is_null($seat_number))
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h2 class="card-title float-left">رقم الجلوس</h2>
                </div>
                <div class="card-body">
                        <h4 class="text-center">رقم الجلوس الخاصة بك هو {{$seat_number->seating_number}}</h4>
                    <!--@if(!$old_payment)-->
                    <!--@else-->
                    <!--    <div class="alert alert-danger my-1 mx-auto text-center">-->
                    <!--        <h4 class="text-center bg-danger">برجاء اكمال المستحقات المالية او اكمال الحافظات-->
                    <!--            السابقة</h4>-->
                    <!--    </div>-->
                    <!--@endif-->
                </div>
            </div>
        </div>
    @endif
    @if(!empty($exam_table) and $payment)
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h2 class="card-title float-left">جدول الامتحانات الترم الحالى</h2>
                </div>
                <div class="card-body" style="overflow-x: auto">
                    <table class="table table-striped table-bordered table-secondary text-center">
                        <tr>
                            <th class="align-middle">كود المادة</th>
                            <th class="align-middle">اسم المادة</th>
                            <th class="align-middle">التاريخ</th>
                            <th class="align-middle">الوقت</th>
                        </tr>
                        @if($old_payment)
                            @foreach($exam_table as $course)
                                <tr @class(['text-danger'=>$course['remaining']])>
                                    <td class="align-middle">{{$course['full_code']}}</td>
                                    <td class="align-middle">
                                        {{$course['name'].($course['remaining']?' (مادة تخلف)':'')}}
                                    </td>
                                    <td class="align-middle">{{$course['day']}}</td>
                                    <td class="align-middle"
                                        style="direction: ltr!important;">{{$course['time']}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="align-middle">{{$exam_table[0]['full_code']}}</td>
                                <td class="align-middle">{{$exam_table[0]['name']}}</td>
                                <!--<th class="align-middle bg-danger" colspan="2" rowspan="{{count($exam_table)}}">-->
                                <!--    برجاء اكمال المستحقات المالية او اكمال الحافظات السابقة-->
                                <!--</th>-->
                            </tr>
                            @for($i = 0; $i < count($exam_table); $i++)
                                <tr>
                                    <td class="align-middle">{{$exam_table[$i]['full_code']}}</td>
                                    <td class="align-middle">{{$exam_table[$i]['name']}}</td>
                                </tr>
                            @endfor
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif
    @if(!is_null($section))
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h2 class="card-title float-left">رقم السكشن</h2>
                </div>
                <div class="card-body">
                    <h4 class="text-center">انت مُسجل فى السكشن رقم {{$section->section_number}}</h4>
                </div>
            </div>
        </div>
    @endif
    @if(!is_null($wallet))
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h2 class="card-title float-left">المحفظة الداخلية</h2>
                </div>
                <div class="card-body">
                    <table class="table table-borderless text-center h4">
                        <tr>
                            <td class="col-6">قيمة المحفظة</td>
                            <td class="col-6">{{$wallet->amount}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h2 class="card-title float-left">حالة تسجيل الترم الحالي</h2>
            </div>
            <div class="card-body">
                <h3 class="text-center">
                    @if(!is_null($guidance))
                        @if($guidance)
                            <div class="alert alert-success mt-3 mx-auto text-center col-12">
                                <h6><i class="icon fas fa-check"></i> تم تأكيد التسجيل من قبل الإرشاد</h6>
                            </div>
                        @else
                            <div class="alert alert-warning mt-3 text-center col-12">
                                <h6><i class="icon fas fa-exclamation-triangle"></i> في إنتظار موافقة الإرشاد</h6>
                            </div>
                        @endif
                        @if($payment)
                            <div class="alert alert-success mt-3 mx-auto text-center col-12">
                                <h6><i class="icon fas fa-check"></i> تم تأكيد التسجيل من قبل الشئون المالية</h6>
                            </div>
                        @else
                            <div class="alert alert-warning mt-3 text-center col-12">
                                <h6><i class="icon fas fa-exclamation-triangle"></i> في إنتظار موافقة الشئون المالية
                                </h6>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mt-3 text-center col-12">
                            <h6><i class="icon fas fa-exclamation-triangle"></i> أنت لم تقم بالتسجيل في هذا الترم
                                حتى الآن</h6>
                        </div>
                    @endif
                </h3>
            </div>
        </div>
    </div>
</div>
@section('component-style')
@endsection
@section('component-script')
    <script>
        @error('alert')
            toastr.options.rtl = true;
        toastr.error('{{$message}}');
        @enderror
    </script>
@endsection
