@extends('layout.layout')
@section('title', 'طباعة الكارنيهات')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')
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
            @if(session()->has('no-img'))
                <div class="alert alert-danger text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    <h6>لا توجد صورة للطلاب {{count(session()->get('no-img'))}} التاليين</h6>
                    @foreach (session()->get('no-img') as $username => $name)
                        <h6><i class="icon fas fa-ban"></i> {{$username}} - {{$name}}</h6>
                    @endforeach
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    @foreach ($errors->all() as $error)
                        <h6><i class="icon fas fa-ban"></i> {{$error}}</h6>
                    @endforeach
                </div>
            @endif
            <div class="card card-primary">
                <div class="card-body">
                    <form method="post" action="{{route('print.student.cards')}}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="specialization">نوع الطباعة</label><br>
                                <div class="row col-12 justify-content-around">
                                    <div>
                                        <label for="normal"> <span class="text-bold">طباعة كارت عادى</span></label>
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="normal" required name="type" value="normal">
                                            <label for="normal"></label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="magnetic"><span class="text-bold">طباعة كارت ممغنط</span></label>
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="magnetic" required name="type" value="magnetic">
                                            <label for="magnetic"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-8">
                                <label for="username">اسماء الطلاب</label>
                                <select class="select2" multiple="multiple" required
                                        data-placeholder="ادخل اسم او كود الطالب"
                                        style="width: 100%;" id="username" name="usernames[]">
                                </select>
                            </div>
                            <div class="text-center col-md-3 col-12 align-self-center my-2">
                                <button type="submit" name="action" value="student" class="btn btn-success col-12">
                                    <i class="fas fa-print"></i> طباعة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-body">
                    <form method="post" action="{{route('print.student.cards')}}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" required id="study_group" name="study_group">
                                    <option value="" hidden></option>
                                    @foreach($data_filter['study_group'] as $value)
                                        <option
                                            value="{{$value}}" @if (old('study_group') == $value)
                                            selected
                                            @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" required id="specialization" name="specialization">
                                    <option value="" hidden></option>
                                    @foreach($data_filter['specialization'] as $value)
                                        <option
                                            value="{{$value}}" @if (old('specialization') == $value)
                                            selected
                                            @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="departments">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($data_filter['departments_id'] as $value)
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
                                {{-- <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($data_filter['departments_id'] as $value)
                                        <option
                                            value="{{$value}}" @if (old('departments_id') == $value)
                                            selected
                                            @endif>{{$value}}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                            <div class="form-group col-6">
                                <label for="specialization">نوع الطباعة</label><br>
                                <div class="row col-12 justify-content-around">
                                    <div>
                                        <label for="normal1"><span class="text-bold">طباعة كارت عادى</span></label>
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="normal1" required name="type" value="normal">
                                            <label for="normal1"></label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="magnetic1"><span class="text-bold">طباعة كارت ممغنط</span></label>
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="magnetic1" required name="type" value="magnetic">
                                            <label for="magnetic1"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12"></div>
                            <div class="text-center col-md-3 col-12 align-self-center my-2">
                                <button type="submit" name="action" value="students" class="btn btn-success col-12">
                                    <i class="fas fa-print"></i> طباعة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        let done = true;
        $('#specialization,#departments,#study_group').on('input', function (e) {
            if ($('#specialization').val() !== '' && $('#study_group').val() !== '' && done) {
                done = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('check.student.cards.number')}}',
                    data: {
                        'specialization': $('#specialization').val(),
                        'departments': $('#departments').val(),
                        'study_group': $('#study_group').val(),
                    },
                    success: function (data) {
                        $('#count').parent().remove();
                        $('<div class="form-group col-6">' +
                            '<label for="count">عدد الطباعة</label>' +
                            '<select class="custom-select" name="count" id="count" required>' +
                            '<option value="" hidden></option>' + data +
                            '</select>' +
                            '</div>').insertAfter($('#specialization').parent())
                    },
                    error: function (data) {
                        $('#count').parent().remove();
                    },
                    complete: function () {
                        done = true;
                    }
                });
            }
        });
        $(function () {
            $('#username').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '{{route('student.datalist')}}',
                    dataType: "json",
                    type: "GET",
                    delay: 250,
                    minimumInputLength: 4,
                    data: function (params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name + '-' + item.username,
                                    value: item.username,
                                    id: item.username
                                }
                            })
                        };
                    }
                }
            });
        });
    </script>
@endsection
