@extends('layout.layout')
@section('title', 'مراجعة تسجيل الطالب '.$student['name'])
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
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
                        <div class="form-group col-md-3">
                            <label for="semester">فصل دراسي</label>
                            <input type="text" class="form-control" id="semester" name="semester" readonly
                                   value="{{$semester}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="year">عام</label>
                            <input type="text" class="form-control" id="year" name="year" readonly
                                   value="{{$year}}">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="earned_hours">الساعات المكتسبة</label>
                            <input type="text" class="form-control" id="earned_hours" name="earned_hours" disabled
                                   value="{{$student['earned_hours']}}">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="cgpa">المعدل التراكمي للدرجات</label>
                            <input type="text" class="form-control" id="cgpa" name="cgpa" disabled
                                   value="{{$student['cgpa']}}">
                        </div>
                        <div class="col-md-2 my-auto text-center">
                            <a
                                href="{{route('student.status',['username'=>$student['username']])}}"
                                class="btn btn-primary"
                                target="_blank">بيان حالة الطالب</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <form action="{{route('confirm.registration',['username'=>$student['username']])}}" method="post">
                @csrf
                @method('put')
                <div class="card card-info">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">المواد المسجلة في الترم الحالي</h2>
                        <div class="card-tools float-right">
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
                                <th class="align-middle">ترم المادة</th>
                                <th class="align-middle">عدد ساعات المادة</th>
                                <th class="align-middle">تصنيف المادة</th>
                                <th class="align-middle">موافق
                                    <div class="icheck-primary d-block">
                                        <input type="checkbox" id="guidance" value="1">
                                        <label for="guidance"></label>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="text-bold">
                            @foreach($registered_courses as $course)
                                @php $flag = in_array($course['full_code'],array_column($courses[2],'full_code'));@endphp
                                <tr @if($flag)
                                        class="text-success"
                                    @endif>
                                    <td class="align-middle">{{$course['full_code']}}</td>
                                    <td class="align-middle">{{$course['name']}} @if($flag)
                                            (تحسين)
                                        @endif</td>
                                    <td class="align-middle">{{$course['semester']}}</td>
                                    <td class="align-middle">{{$course['hours']}}</td>
                                    <td class="align-middle">{{$course['elective'] ? 'اختياري' : 'اجباري'}}</td>
                                    <td class="align-middle">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="guidance{{$course['full_code']}}"
                                                   name="guidance[{{$course['full_code']}}]"
                                                   {{$course['guidance'] ? 'checked':''}}
                                                   value="1">
                                            <label for="guidance{{$course['full_code']}}"></label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">تأكيد التسجيل</button>
                    </div>
                </div>
            </form>
            <div class="card card-info">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">المواد التي سبق الرسوب فيها</h2>
                    <div class="card-tools float-right">
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
                            <th class="align-middle">ترم المادة</th>
                            <th class="align-middle">عدد ساعات المادة</th>
                            <th class="align-middle">تصنيف المادة</th>
                            <th class="align-middle">التقدير المكتسب</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($courses[3] as $course)
                            <tr>
                                <td class="align-middle">{{$course->full_code}}</td>
                                <td class="align-middle">{{$course->name}}</td>
                                <td class="align-middle">{{$course->semester}}</td>
                                <td class="align-middle">{{$course->hours}}</td>
                                <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                <td class="align-middle">{{$course->grade}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-info">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">المواد المتاحة في الترم الحالي</h2>
                    <div class="card-tools float-right">
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
                            <th class="align-middle">ترم المادة</th>
                            <th class="align-middle">عدد ساعات المادة</th>
                            <th class="align-middle">تصنيف المادة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($courses[0] as $course)
                            <tr>
                                <td class="align-middle">{{$course->full_code}}</td>
                                <td class="align-middle">{{$course->name}}</td>
                                <td class="align-middle">{{$course->semester}}</td>
                                <td class="align-middle">{{$course->hours}}</td>
                                <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card card-info">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">المواد التي سبق درستها</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="overflow-x: auto;">
                    @foreach($previous_courses as $year=>$semesters)
                        @foreach($semesters as $semester=>$courses)
                            <div
                                class="card card-info{{($loop->parent->first and $loop->first)? '': ' collapsed-card'}}">
                                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                                    <h2 class="card-title float-left">{{$year}}  {{$semester}}</h2>
                                    <div class="card-tools float-right">
                                        <button type="button" class="btn btn-tool">
                                            <i class="fas {{($loop->parent->first and $loop->first)? 'fa-minus': 'fa-plus'}}"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="overflow-x: auto;">
                                    <table class="table table-hover table-bordered text-center">
                                        <thead>
                                        <tr>
                                            <th class="align-middle">كود المادة</th>
                                            <th class="align-middle">اسم المادة</th>
                                            <th class="align-middle">ترم المادة</th>
                                            <th class="align-middle">عدد ساعات المادة</th>
                                            <th class="align-middle">تصنيف المادة</th>
                                            <th class="align-middle">التقدير المكتسب</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($courses as $course)
                                            <tr>
                                                <td class="align-middle">{{$course->full_code}}</td>
                                                <td class="align-middle">{{$course->name}}</td>
                                                <td class="align-middle">{{$course->semester}}</td>
                                                <td class="align-middle">{{$course->hours}}</td>
                                                <td class="align-middle">{{$course->elective ? 'اختياري' : 'اجباري'}}</td>
                                                <td class="align-middle">{{$course->grade}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $("#guidance").click(function () {
            $("input[name*='guidance']").prop('checked', $(this).prop('checked'));
        });
        @if($errors->any())
            toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        toastr.options.timeOut = 0;
        toastr.options.extendedTimeOut = 0;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-right";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif
    </script>
@endsection
