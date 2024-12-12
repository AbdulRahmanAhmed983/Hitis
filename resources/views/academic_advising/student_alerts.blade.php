@extends('layout.layout')
@section('title', 'تنبيه الطلاب')
@section('styles')
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
            <form action="" method="get">
                <div class="card card-primary">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">تنبيهات سابقه</h2>
                        <div class="card-tools float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="username">اكواد الطلاب</label>
                            <input type="search" class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                   name="username" id="username" required value="{{old('username')}}"
                                   placeholder="كود او اسم الطالب" list="students">
                            <datalist id="students"></datalist>
                        </div>
                        @error('username')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">بحث</button>
                    </div>
                </div>
            </form>
            @if(isset($alerts))
                @if(!empty($alerts))
                    <form action="{{route('aa.student.delete.alert',['student_code'=>$alerts[0]->student_code])}}"
                          method="post">
                        @csrf
                        @method('delete')
                        <div class="card card-primary">
                            <div class="card-body">
                                <table class="table table-hover table-borderless text-center">
                                    <thead>
                                    <tr>
                                        <th class="align-middle">السبب</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($alerts as $alert)
                                        <tr @class(['bg-warning'=>($alert->status == 'warning'),
                                                'bg-danger'=>($alert->status == 'danger'),'font-weight-bolder'])>
                                            <td class="align-middle">{{$alert->reason}}</td>
                                            <td class="align-middle">
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" id="alert{{$alert->id}}"
                                                           name="alert[{{$alert->id}}]" value="1">
                                                    <label for="alert{{$alert->id}}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @error('alert')
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                </div>
                                @enderror
                                @error('alert.*')
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                </div>
                                @enderror
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">حذف</button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-success mt-3 text-center">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            &times;
                        </button>
                        <h6><i class="icon fas fa-check"></i> لا يوجد تنبيهات لهذا الطالب</h6>
                    </div>
                @endif
            @endif
            <form action="{{route('aa.student.add.alert')}}" method="post">
                @csrf
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="usernames">اكواد الطلاب</label>
                            <textarea id="usernames" name="usernames" class="form-control" rows="2" style="resize: none"
                                      required placeholder="ادخل اكواد الطلاب"
                                      dir="auto">{{old('usernames')}}</textarea>
                        </div>
                        @error('usernames')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                        <div class="form-group">
                            <label for="reason">السبب</label>
                            <textarea id="reason" name="reason" class="form-control" rows="1" style="resize: none"
                                      required placeholder="ادخل السبب" dir="auto">{{old('reason')}}</textarea>
                        </div>
                        @error('reason')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                        <div class="form-group">
                            <label for="status">حالة التنبيه</label>
                            <div class="d-flex">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio1" name="status"
                                           value="danger" @if(old('status') == "danger") checked @endif required>
                                    <label for="customRadio1" class="custom-control-label">خطر</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio2" name="status"
                                           value="warning" @if(old('status') == "warning") checked @endif>
                                    <label for="customRadio2" class="custom-control-label">تحذير</label>
                                </div>
                            </div>
                        </div>
                        @error('status')
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                        </div>
                        @enderror
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">إدخال</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });
        let done = true;
        $('#username').on('input', function (e) {
            let me = $(this);
            if (me.val().length >= 3 && done) {
                done = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('student.datalist')}}',
                    data: {
                        'search': me.val(),
                    },
                    success: function (data) {
                        $('#students').html('');
                        for (let i = 0; i < data.length; i++) {
                            $('#students').append('<option value="' + data[i]['username'] + '">'
                                + data[i]['name'] + '</option>');
                        }
                    },
                    error: function (data) {
                        $('#students').html('').parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done = true;
                    }
                });
            } else {
                $('#students').html('');
            }
        });
    </script>
@endsection
