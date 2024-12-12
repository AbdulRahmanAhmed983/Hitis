@extends('layout.layout')
@section('title', 'إعدادات الكنترول')
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
    <div class="row justify-content-around">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">Excel ارقام الجلوس</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="seating_numbers_excel" method="post" action="{{route('sitting.numbers.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4 col-12">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value=""></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-12">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value=""></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-12">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                        @php
                                            $deptName = '';
                                            switch($value) {
                                                case 1:
                                                    $deptName = "عام";
                                                    break;
                                                case 2:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                                    break;
                                                case 3:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                                    break;
                                            }
                                        @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="seating_numbers_excel" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content"><i class="fas fa-download"> تنزيل</i>
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
        toastr.options.timeOut = 0;
        toastr.options.extendedTimeOut = 0;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-center";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif
    </script>
@endsection
