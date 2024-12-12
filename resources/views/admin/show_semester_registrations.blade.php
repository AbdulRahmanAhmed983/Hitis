@extends('layout.layout')
@section('title', 'chairman')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @if(!$login)
                <div class="modal fade" id="modal-login">
                    <div class="modal-dialog">
                        <div class="modal-content bg-primary">
                            <div class="modal-header">
                                <h4 class="modal-title">تأكيد الدخول</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('show.semester.registration')}}" method="post" id="login">
                                    @csrf
                                    <div class="form-group row justify-content-around">
                                        <label class="control-label" for="username">username</label>
                                        <div>
                                            <input type="text" class="form-control" name="username" id="username"
                                                   dir="rtl">
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-around">
                                        <label class="control-label" for="password">password</label>
                                        <div>
                                            <input type="password" class="form-control" name="password"
                                                   id="password" dir="rtl">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="submit" form="login" class="btn btn-outline-light">دخول</button>
                                <button type="button" class="btn btn-outline-light" data-dismiss="modal">إلغاء</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card card-info">
                    <div class="card-body" style="overflow-x: auto;">
                        <form action="" method="get">
                            <table class="table table-borderless rounded bg-dark text-center mb-3">
                                <tr>
                                    <th class="align-middle">
                                        <label class="form-text" for="year">السنة الدراسية</label>
                                    </th>
                                    <td class="align-middle">
                                        <select class="custom-select" required name="year" id="year">
                                            <option value="" hidden></option>
                                            @foreach($data_filter['year'] as $value)
                                                <option
                                                    value="{{$value}}" @if ($year == $value)
                                                    {{ 'selected' }}
                                                    @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <th class="align-middle">
                                        <label class="form-text" for="semester">الفصل الدراسى</label>
                                    </th>
                                    <td class="align-middle">
                                        <select class="custom-select" required name="semester" id="semester">
                                            <option value="" hidden></option>
                                            @foreach($data_filter['semester'] as $value)
                                                <option
                                                    value="{{$value}}" @if ($semester == $value)
                                                    {{ 'selected' }}
                                                    @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-eye text-dark"></i>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <table class="table table-bordered table-hover table-responsive table-dark rounded text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">الفرقة</th>
                                @foreach($study_groups as $study_group)
                                    <th class="align-middle"
                                        colspan="{{$col_counter[$study_group]['col']}}">{{$study_group}}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="align-middle">التخصص</th>
                                @foreach($study_groups as $study_group)
                                    @foreach($specializations as $specialization)
                                        <th colspan="{{$col_counter[$study_group][$specialization]['col']}}"
                                            class="align-middle">{{$specialization}}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                            <tr>
                                <th class="align-middle">التخصص</th>
                                @foreach($study_groups as $study_group)
                                    @foreach($specializations as $specialization)
                                        @foreach($studying_status as $status)
                                            <th class="align-middle">{{$status}}</th>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($info as $des => $study_groups)
                                <tr>
                                    <th class="align-middle">{{$des}}</th>
                                    @foreach($study_groups as $study_group => $specializations)
                                        @foreach($specializations as $specialization => $studying_status)
                                            @foreach($studying_status as $status => $value)
                                                <td class="align-middle">{{number_format($value)}}</td>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
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
        toastr.options.positionClass = "toast-top-right";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif
        @if(!$login)
        $('#modal-login').modal();
        @endif
        $('#modal-login').on('hidden.bs.modal', function () {
            window.location.replace("{{route('dashboard')}}");
        });
    </script>
@endsection
