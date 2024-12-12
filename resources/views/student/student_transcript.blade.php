@extends('layout.layout')
@section('title', 'التسجيلات السابقة')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
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
                                    <td @class(['align-middle','font-weight-bold','text-danger'=>(in_array($course->grade,['F','FX'])),
                                                'text-success'=>(!in_array($course->grade,['F','FX','P','IC','W']))])>{{$course->grade}}</td>
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
                        <form id="courses_trans" action="{{route('print.status')}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success">طباعة</button>
                        </form>
                    </div>
                </div>
            @endif
            @if(isset($registrations))
                @forelse($registrations as $year => $value)
                    @foreach($value as $semester => $val)
                        <div class="card card-gray">
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
                                        <th class="align-middle">الإرشاد</th>
                                        <th class="align-middle">الماليه</th>
                                        <th class="align-middle">التقدير المكتسب</th>
                                        <th class="align-middle">النقاط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($val as $course)
                                        <tr>
                                            <td class="align-middle">{{$course->course_code}}</td>
                                            <td class="align-middle">{{$course->name}}</td>
                                            <td class="align-middle">{{$course->hours}}</td>
                                            <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                            <td class="align-middle">{!! $course->guidance ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                            <td class="align-middle">{!!$course->payment ? '<i class="icon fas fa-check text-success"></i>' : '<i class="icon fas fa-times text-danger"></i>'!!}</td>
                                            <td @class(['align-middle','font-weight-bold','text-danger'=>(in_array($course->grade,['F','FX'])),
                                                'text-success'=>(!in_array($course->grade,['F','FX','P','IC','W']))])>{{$course->grade}}</td>
                                            <td class="align-middle font-weight-bold">{{$grades[$year][$semester]['courses'][$course->course_code]}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <table class="table text-left table-borderless">
                                    <tbody>
                                    <tr>
                                        <th>المعدل الفصلي للترم (GPA)</th>
                                        <th>{{$grades[$year][$semester]['gpa']}}</th>
                                        <th>عدد ساعات المسجلة في الترم</th>
                                        <th>{{$grades[$year][$semester]['hours']}}</th>
                                    </tr>
                                    <tr>
                                        <th>المعدل التراكمي الإجمالي (CGPA)</th>
                                        <th>{{$grades[$year][$semester]['cgpa']}}</th>
                                        <th>عدد ساعات المكتسبه في الترم</th>
                                        <th>{{$grades[$year][$semester]['earned_hours']}}</th>
                                    </tr>
                                    </tbody>
                                </table>
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
@endsection
