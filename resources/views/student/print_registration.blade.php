@extends('layout.layout')
@section('title','طباعة التسجيل')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex">
                    <h2 class="col-6 text-dark my-auto">إستمارة تسجيل</h2>
                    <div class="col-2"></div>
                    <div class="col-4 float-right">
                        <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 row justify-content-around">
                        <div class="col-12">
                            <div class="row justify-content-around">
                                <div class="form-group col-md-5">
                                    <label for="name">الإ سم</label>
                                    <input type="text" class="form-control" id="name" name="name" readonly
                                           value="{{$student['name']}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="username">كود</label>
                                    <input type="text" class="form-control" id="username" name="username" readonly
                                           value="{{$student['username']}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="semester">فصل دراسي</label>
                                    <input type="text" class="form-control" id="semester" name="semester" readonly
                                           value="{{$semester}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="year">عام</label>
                                    <input type="text" class="form-control" id="year" name="year" readonly
                                           value="{{$year}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <table class="table table-hover table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>كود المادة</th>
                                    <th>اسم المادة</th>
                                    <th>عدد ساعات المادة</th>
                                    <th>تصنيف المادة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($courses as $course)
                                    <tr>
                                        <td>{{$course['full_code']}}</td>
                                        <td>{{$course['name']}}</td>
                                        <td>{{$course['hours']}}</td>
                                        <td>{{$course['elective'] ? 'اختياري' : 'اجباري'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
            window.location.replace("{{route('display.registration')}}");
        };
    </script>
@endsection


