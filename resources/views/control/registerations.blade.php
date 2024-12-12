@extends('layout.layout')
@section('title', 'تحميل التسجيلات')
@section('styles')
@endsection
@section('content')




<div class="col-lg-6">
    <div class="card card-primary collapsed-card">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title float-left"> تحميل التسجيلات </h2>
            <div class="card-tools float-right">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="regist" method="post" action="{{route('exportRegistration')}}">
                @csrf
                <div class="row justify-content-around">
                    <div class="form-group col-md-6">
                        <label for="year">السنة الدراسية</label>
                        <select class="custom-select" name="year" id="year" required>
                            <option value="" hidden></option>
                            @foreach($filter_data['year'] as $data)
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="form-group col-md-6">
                        <label for="course_code">كود المقرر الدراسي</label>
                        <select class="custom-select" name="course_code" id="course_code" required>
                            <option value="" hidden></option>
                            @foreach($filter_data['course_code'] as $data)
                                <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button type="submit" form="regist" class="btn btn-primary mx-3 align-self-center"
                    style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
            </button>
        </div>
    </div>
</div>
@endsection
