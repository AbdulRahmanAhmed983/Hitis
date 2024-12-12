@extends('layout.layout')
@section('title', 'تحويل المصاريف الادارية')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')

           {{-- start convert --}}
           <div class="row">
            <div class="col-12">
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
                <div class="card card-primary">
                    <div class="card-body">
                        <form action="{{route('store.converted.administraitve')}}" method="post">
                            @csrf
                            <div class="card card-primary">
                                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                                    <h2 class="card-title float-left"> تحويل المصاريف الادارية</h2>
                                    <div class="card-tools float-right">
                                        <button type="button" class="btn btn-tool">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="student_code1">كود الطالب من</label>
                                                <input type="search"
                                                       class="form-control student_code1 {{$errors->has('student_code1')?'is-invalid':''}}"
                                                       name="student_code1" id="student_code1" required 
                                                       placeholder="كودالطالب" list="students">
                                                <datalist id="students"></datalist>
                                            </div>
                                            @error('student_code1')
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="student_code">كود الطالب الي</label>
                                                <input type="search"
                                                       class="form-control student_code {{$errors->has('student_code')?'is-invalid':''}}"
                                                       name="student_code" id="student_code" required
                                                       placeholder="كودالطالب" list="students">
                                                <datalist id="students"></datalist>
                                            </div>
                                            @error('student_code')
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                            @enderror
                                        </div>


                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">ادخال</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--<form action="{{route('import.administraitve')}}" method="post" enctype="multipart/form-data">-->
        <!--    @csrf-->
            <!--<div class="card card-primary">-->
            <!--    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">-->
            <!--        <h2 class="card-title float-left"> تحويل المصاريف الادارية Excel</h2>-->
            <!--        <div class="card-tools float-right">-->
            <!--            <button type="button" class="btn btn-tool">-->
            <!--                <i class="fas fa-minus"></i>-->
            <!--            </button>-->
            <!--        </div>-->
            <!--    </div>-->
                <!--<div class="card-body">-->
                <!--    <div class="form-group">-->
                <!--        <label for="file">الملف</label>-->
                <!--        <input type="file"-->
                <!--               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"-->
                <!--               class="form-control-file btn btn-primary {{$errors->has('file')?'is-invalid':''}}"-->
                <!--               name="file" id="file" required value="{{old('file')}}">-->
                <!--    </div>-->
                <!--    @error('student_code1')-->
                <!--    <div class="alert alert-danger mt-3 text-center">-->
                <!--        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
                <!--            &times;-->
                <!--        </button>-->
                <!--        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>-->
                <!--    </div>-->
                <!--    @enderror-->
                <!--    @error('student_code')-->
                <!--    <div class="alert alert-danger mt-3 text-center">-->
                <!--        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
                <!--            &times;-->
                <!--        </button>-->
                <!--        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>-->
                <!--    </div>-->
                <!--    @enderror-->
                <!--</div>-->
        <!--        <div class="card-footer">-->
        <!--            <button type="submit" class="btn btn-primary">ادخال</button>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</form>-->
{{-- end conert --}}

@endsection
