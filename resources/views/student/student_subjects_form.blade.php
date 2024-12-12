@extends('layout.layout')
@section('title', 'تسجيل مواد')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @if(!$can_register)
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                <h6><i class="icon fas fa-ban"></i> تسجيل المواد غير متاح فى الوقت الحالى</h6>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($payment)
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                <h6><i class="icon fas fa-ban"></i> تسجيل المواد غير متاح بسبب عدم استكمال مدفوعات ترم
                                    سابقة</h6>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($ticket)
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                <h6><i class="icon fas fa-ban"></i> تسجيل المواد غير متاح بسبب عدم استكمال حافظات سابقة
                                    برجاء مراجعة شئون الطلاب</h6>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($excuse)
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                <h6><i class="icon fas fa-ban"></i>انت الان فى وضع العذر عن الترم للالغاء برجاء التوجه
                                    الى شئون الطلاب</h6>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(!$student_new)
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                @if(!is_null($academic))
                                    <h6><i class="icon fas fa-ban"></i> يمكنك التسجيل من خلال {{$academic->name}} المرشد
                                        الأكاديمي فقط </h6>
                                @else
                                    <h6><i class="icon fas fa-ban"></i> يمكنك التسجيل من خلال المرشد الأكاديمي فقط </h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($can_summer)
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                <h6><i class="icon fas fa-ban"></i> لا يمكن التسجيل فى الترم صيفي</h6>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(empty($courses[0]) and empty($courses[1]) and empty($courses[4]))
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="mt-3 text-center">
                            <div class="alert alert-danger">
                                @if(!is_null($academic))
                                    <h6><i class="icon fas fa-ban"></i> لا يوجد مواد متاحة لك ، يرجى مراجعة
                                        المرشدالأكاديمي {{$academic->name}}</h6>
                                @else
                                    <h6><i class="icon fas fa-ban"></i> لا يوجد مواد متاحة لك ، يرجى مراجعة المرشد </h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <form action="{{route('subjects.registration')}}" method="post">
                    @csrf
                    <div class="card card-default">
                        <div class="card-body">
                            @if(session()->has('success'))
                                <div class="alert alert-success mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                                </div>
                            @endif
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="semester">فصل دراسي</label>
                                    <input type="text" class="form-control" id="semester" name="semester" readonly
                                           value="{{$semester}}">
                                    @if($errors->has('semester'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('semester')}}</h6>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="year">عام</label>
                                    <input type="text" class="form-control" id="year" name="year" readonly
                                           value="{{$year}}">
                                    @if($errors->has('year'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('year')}}</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    @if($errors->has('courses'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('courses')}}</h6>
                                        </div>
                                    @endif
                                    @if($errors->has('courses.*'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('courses.*')}}</h6>
                                        </div>
                                    @endif
                                    @if(!$mobile)
                                        @if(!empty($courses[1]))
                                            <div class="form-group">
                                                <label for="course1">المواد الدراسية الغير مجتازة</label>
                                                <select name="courses[]" id="course1" class="duallistbox"
                                                        multiple="multiple" style="height: 150px;">
                                                    @foreach($courses[1] as $course)
                                                        <option style="font-weight:bold;"
                                                                value="{{$course->full_code}}">
                                                            ({{$course->full_code}}) {{$course->name}}
                                                            عدد {{$course->hours}} ساعات
                                                            ({{($course->elective ? 'اختياري' : 'إجباري')}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <label for="course">المواد الدراسية المتاحة</label> &nbsp;
                                            <label style="color: blue"> (يمكنك تسجيل المواد الدراسية من مستوي اعلي عن طريق المرشد الاكاديمي)</label>
                                            <select name="courses[]" id="course" class="duallistbox"
                                                    multiple="multiple" style="height: 150px;">
                                                @foreach($courses[0] as $course)
                                                    <option style="font-weight:bold;
                                                    {{($student_semester > $course->semester)? 'color: blue;':''}}
                                                    " value="{{$course->full_code}}">
                                                        ({{$course->full_code}}) {{$course->name}}
                                                        عدد {{$course->hours}} ساعات
                                                        ({{($course->elective ? 'اختياري' : 'إجباري')}})
                                                    </option>
                                                @endforeach
                                                @foreach($courses[4] as $course)
                                                @php
                                                $grades = ['A+', 'A', 'B', 'B+','C+'];
                                            @endphp
                                               @unless (in_array($course->grade, $grades))
                                                    <option style="font-weight:bold;color: green;"
                                                            value="{{$course->full_code}}">
                                                        ({{$course->full_code}}) {{$course->name}}
                                                        عدد {{$course->hours}} ساعات
                                                        ({{($course->elective ? 'اختياري' : 'إجباري')}})
                                                        تحسين {{$course->grade}}
                                                    </option>
                                                    @endunless
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        @if(!empty($courses[1]))
                                            <div class="form-group">
                                                <label for="course1">المواد الدراسية الغير مجتازة</label>
                                                <select name="courses[]" id="course1" multiple="multiple"
                                                        class="col-12">
                                                    @foreach($courses[1] as $course)
                                                        <option style="font-weight:bold;color: green;"
                                                                value="{{$course->full_code}}">
                                                            ({{$course->full_code}}) {{$course->name}}
                                                            عدد {{$course->hours}} ساعات
                                                            ({{($course->elective ? 'اختياري' : 'إجباري')}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <label for="course">المواد الدراسية المتاحة</label>
                                            <select name="courses[]" id="course" multiple="multiple"
                                                    class="col-12">
                                                @foreach($courses[0] as $course)
                                                    <option style="font-weight:bold;
                                                    {{($student_semester > $course->semester)? 'color: blue;':''}}"
                                                            value="{{$course->full_code}}">
                                                        ({{$course->full_code}}) {{$course->name}}
                                                        عدد {{$course->hours}} ساعات
                                                        ({{($course->elective ? 'اختياري' : 'إجباري')}})
                                                    </option>
                                                @endforeach
                                                @foreach($courses[4] as $course)
                                                @php
                                                $grades = ['A+', 'A', 'B', 'B+','C+'];
                                            @endphp
                                               @unless (in_array($course->grade, $grades))
                                                    <option style="font-weight:bold;color: green;"
                                                            value="{{$course->full_code}}">
                                                        ({{$course->full_code}}) {{$course->name}}
                                                        عدد {{$course->hours}} ساعات
                                                        ({{($course->elective ? 'اختياري' : 'إجباري')}})
                                                        تحسين {{$course->grade}}
                                                    </option>
                                                    @endunless
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">حفظ التسجيل</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>
    <script>
        console.log = function () {
        };
        $('.duallistbox').bootstrapDualListbox({
            infoText: false,
        });
    </script>
@endsection
