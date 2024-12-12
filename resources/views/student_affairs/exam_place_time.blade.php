@extends('layout.layout')
@section('title', 'رفع لجان الامتحانات')
@section('styles')
@endsection
@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success mt-3 text-center col-12">
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
    <div class="row justify-content-around">
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">رفع لجان الامتحانات</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="exam-places" method="post" action="{{route('update.exam.places')}}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row justify-content-around">
                            <div class="form-group col-md-4">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value=""></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value=""></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="specialization">الشعبة</label>
                                <select class="custom-select" name="departments_id" id="departments" required>
                                    <option value=""></option>
                                    @foreach($filter_data['departments_id'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="specialization">الملف</label>
                                <input type="file" name="places" id="places" required
                                       class="form-control-file btn btn-secondary"
                                       accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="exam-places" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">رفع
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">رفع جدول الامتحانات</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="exam-time" method="post" action="{{route('update.exam.time')}}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row justify-content-around">
                            <div class="form-group col-12">
                                <label for="specialization">الملف</label>
                                <input type="file" name="time" id="time" required
                                       class="form-control-file btn btn-secondary"
                                       accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="exam-time" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">رفع
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        @if($errors->any())
            toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        toastr.options.timeOut = 1500;
        toastr.options.extendedTimeOut = 1500;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-right";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif
    </script>
@endsection
