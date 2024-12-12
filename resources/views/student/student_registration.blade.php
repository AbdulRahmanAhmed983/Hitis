@extends('layout.layout')
@section('title', 'مراجعة تسجيل مواد')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @if($regis_exists and $can_register)
                <form id="delete" action="{{route('delete.registration')}}" method="post">
                    @csrf
                    @method('delete')
                </form>
            @endif
            @if($can_print)
                <form id="print" action="{{route('print.registration')}}" method="post">
                    @csrf
                </form>
            @endif
            <div class="card card-default">
                <div class="card-body">
                    @if(!$can_register)
                        <div class="mt-3 text-center">
                            <div class="alert alert-warning text-danger">
                                <h6><i class="icon fas fa-exclamation-triangle"></i> تعديل او حذف المواد غير متاح فى
                                    الوقت
                                    الحالى</h6>
                            </div>
                        </div>
                    @endif
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
                                   value="{{$semester}}" @if($regis_exists and $can_register)form="delete" @endif
                                   {{--@if($can_print)--}}form="print" {{--@endif--}}>
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
                                   value="{{$year[0]}}/{{$year[1]}}" @if($regis_exists and $can_register)form="delete"
                                   @endif
                                   {{--@if($can_print)--}}form="print"{{--@endif--}}>
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
                    <table class="table table-hover table-bordered text-center">
                        <thead>
                        <tr>
                            <th class="col-2">كود المادة</th>
                            <th class="col-6">اسم المادة</th>
                            <th class="col-2">عدد ساعات المادة</th>
                            <th class="col-2">تصنيف المادة</th>
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
                <div class="card-footer">
                    @if($can_delete)
                        <button type="submit" class="btn btn-danger" form="delete">حذف التسجيل</button>
                    @endif
                    @if($can_print)
                        <button type="submit" class="btn btn-primary" form="print">طباعة التسجيل</button>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
