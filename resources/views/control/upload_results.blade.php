@extends('layout.layout')
@section('title', 'رفع النتائج')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <style>
        @media print {
            * {
                color: black !important;
                font-size: large !important;
            }

            .table *, .table th, .table td {
                border: black solid 1px !important;
                border-collapse: collapse;
            }

            #code {
                font-size: 30px !important;
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div id="main" class="col-12">
            <form id="fileUploadForm" method="post" action="{{route('control.upload.result')}}"
                  enctype="multipart/form-data">
                @csrf
                <div class="card card-primary">
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
                        <div class="form-group">
                            <label for="course_code">كود المادة</label>
                            <select id="course_code" name="course_code" class="form-control select2"
                                    style="width: 100%;" required>
                                @foreach($courses as $course)
                                    <option value="{{$course->full_code}}"
                                            @if(old('course_code') == $course->full_code) selected @endif>{{$course->full_code}}
                                        -{{$course->name}}</option>
                                @endforeach
                                <option value="note" @if(old('course_code') == 'note') selected @endif>ملاحظات</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="file" name="result" id="result"
                                   accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                   required class="form-control-file btn btn-secondary">
                        </div>
                        <div class="form-group">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                     role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                     style="width: 0"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">رفع <i class="fas fa-file-upload nav-icon"></i>
                        </button>
                        <button type="button" id="print" class="btn btn-success float-right">
                            طباعه <i class="fas fa-print nav-icon"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Select2 -->
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script>
        $(function () {
            $('.select2').select2({
                theme: 'bootstrap4'
            })
            $(document).ready(function () {
                let percentage = '0';
                $('#fileUploadForm').ajaxForm({
                    beforeSend: function () {
                        $('button[type=submit]').attr('disabled', true);
                        $('.progress .progress-bar').css("width", "0%");
                        percentage = '0';
                    },
                    uploadProgress: function (event, position, total, percentComplete) {
                        percentage = percentComplete;
                        $('.progress .progress-bar').css("width", percentage + '%', function () {
                            return $(this).attr("aria-valuenow", percentage) + "%";
                        })
                    },
                    success: function (data) {
                        let message;
                        if (typeof data[0].errors != 'undefined' || data[1].length !== 0) {
                            message = 'تم الرفع النتائج  <span class="text-danger">معدا التى بها خطأ</span>';
                        } else {
                            message = 'تم الرفع النتائج بنجاح';
                        }
                        $('#main').append('<div class="alert alert-success mt-3 text-center">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">' +
                            '&times;</button>' +
                            '<h6><i class="icon fas fa-check"></i>' + message + ' </h6></div>');
                        if (typeof data[0].errors != 'undefined' || data[1].length !== 0) {
                            if (typeof data[0].errors != 'undefined') {
                                let wrong = data[0].errors;
                                let output = '<div class="card card-danger">' +
                                    '<div class="card-header">' +
                                    '<h2 class="card-title float-left">هناك أخطاء في السطور التاليه</h2>' +
                                    '<div class="card-tools  float-right">' +
                                    '<button type="button" class="btn btn-tool" data-card-widget="remove">' +
                                    '<i class="fas fa-times"></i>' +
                                    '</button></div></div>' +
                                    '<div class="card-body" style="overflow-x: auto;">' +
                                    '<table class="table table-hover table-borderless text-center bg-danger">' +
                                    '<thead><tr>' +
                                    '<th class="align-middle">سطر الخطأ</th>' +
                                    '<th class="align-middle">وصف الخطأ</th></tr></thead><tbody>';
                                for (let i = 0; i < wrong.length; i++) {
                                    output += '<tr><td class="align-middle">' + wrong[i]['row'] + '</td>' +
                                        '<td class="align-middle">' + wrong[i]['message'] + '</td></tr>';
                                }
                                output += '</tbody></table></div></div>';
                                $('#main').append(output);
                            }
                            if (data[1].length !== 0) {
                                let miss = data[1];
                                let output = '<div class="card card-danger">' +
                                    '<div class="card-header">' +
                                    '<h2 class="card-title float-left">الطلاب المسجلون في الماده و ليس لهم نتائج</h2>' +
                                    '<div class="card-tools  float-right">' +
                                    '<button type="button" class="btn btn-tool" data-card-widget="remove">' +
                                    '<i class="fas fa-times"></i>' +
                                    '</button></div></div>' +
                                    '<div class="card-body" style="overflow-x: auto;">' +
                                    '<table class="table table-hover table-borderless text-center bg-danger">' +
                                    '<thead><tr>' +
                                    '<th class="align-middle">الإسم</th>' +
                                    '<th class="align-middle">الكود</th>' +
                                    '<th class="align-middle">الفرقه الدراسيه</th></tr></thead><tbody>';
                                miss.forEach((item) => {
                                    output += '<tr><td class="align-middle">' + item['name'] + '</td>' +
                                        '<td class="align-middle">' + item['username'] + '</td>' +
                                        '<td class="align-middle">' + item['study_group'] + '</td></tr>';
                                });
                                output += '</tbody></table></div></div>';
                                $('#main').append(output);
                            } else {
                                $('#main').append('<div class="alert alert-success mt-3 text-center">' +
                                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">' +
                                    '&times;</button>' +
                                    '<h6><i class="icon fas fa-check"></i> جميع الطلاب المسجلين في الماده تم الرفع نتائجهم</h6></div>');
                            }
                        }
                    },
                    error: function (data) {
                        let errors = JSON.parse(data.responseText)[0];
                        $('.card-primary .card-body')
                            .append('<div id="errors" class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>');
                        for (let i = 0; i < errors.length; i++) {
                            $('.card-primary .card-body').children('#errors')
                                .append('<h6><i class="icon fas fa-ban"></i> ' + errors[i] + '</h6>');
                        }
                    },
                    complete: function () {
                        $('button[type=submit]').attr('disabled', false);
                        $('#result').val('');
                    }
                });
            });
            $('#print').on('click', function () {
                window.onbeforeprint = function () {
                    $('nav.main-header').hide();
                    $('aside.main-sidebar').hide();
                    $('footer.main-footer').hide();
                    $('progress').hide();
                    $('button').hide();
                    $('input').hide();
                    $('#result').hide();
                    $('.progress').hide();
                    $('.card-footer').hide();
                    $('table').removeClass('table-borderless table-hover').addClass('table-striped table-bordered');
                    let code = $('#course_code').val();
                    $(document).prop('title', code);
                    let course = $('option[value=' + code + ']')[0]['label'];
                    $('.select2').hide().parent().append('<h4 id="code">' + course + '</h4>');
                    $('.content-wrapper').removeClass('content-wrapper').addClass('c-w');
                };
                window.onafterprint = function () {
                    $('nav.main-header').show();
                    $('aside.main-sidebar').show();
                    $('footer.main-footer').show();
                    $('progress').show();
                    $('button').show();
                    $('input').show();
                    $('#result').show();
                    $('.progress').show();
                    $('.card-footer').show();
                    $('table').removeClass('table-striped table-bordered').addClass('table-borderless table-hover');
                    $(document).prop('title', 'رفع النتائج');
                    $('.select2').show();
                    $('#code').remove();
                    $('.c-w').addClass('content-wrapper').removeClass('c-w');
                };
                window.print();
            });
        });
    </script>
@endsection
