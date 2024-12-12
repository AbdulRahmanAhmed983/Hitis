@extends('layout.layout')
@section('title', 'إضافة مستخدم')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card card-primary">
                <form role="form" method="post" action="{{route('insert.user')}}" autocomplete="off">
                    @csrf
                    <div class="card-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                                <h6>برجاء حفظ اسم المستخدم و كلمة السر</h6>
                                <h6>اسم المستخدم {{session('data')['username']}}</h6>
                                <h6>كلمة السر {{session('data')['password']}}</h6>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">الاسم</label>
                                    <input type="text" autocomplete="off" class="form-control" id="name" name="name"
                                           required
                                           value="{{old('name')}}">
                                    @if($errors->has('name'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('name')}}</h6>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="username">اسم االمستخدم</label>
                                    <input type="text" autocomplete="off" class="form-control" id="username"
                                           name="username"
                                           value="{{old('username')}}" required>
                                    @if($errors->has('username'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('username')}}</h6>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="password">كلمة السر</label>
                                    <input type="text" autocomplete="off" class="form-control" id="password"
                                           name="password"
                                           value="{{old('password')}}" readonly required>
                                    @if($errors->has('password'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('password')}}</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile">رقم الهاتف</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile" required
                                           value="{{old('mobile')}}">
                                    @if($errors->has('mobile'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('mobile')}}</h6>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{old('email')}}"
                                           required>
                                    @if($errors->has('email'))

                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('email')}}</h6>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="role">وظيفة</label>
                                    <select class="form-control custom-select" id="role" name="role" required>
                                        <option value="" hidden>اختر</option>
                                        <option value="admin" @if(old('role') == 'admin') selected @endif>Admin
                                        </option>
                                        <option value="chairman" @if(old('role') == 'chairman') selected @endif>Chairman
                                        </option>
                                        <option value="student_affairs"
                                                @if(old('role') == 'student_affairs') selected @endif>
                                            شئون طلبة
                                        </option>
                                        <option value="finance" @if(old('role') == 'finance') selected @endif>مالية
                                        </option>
                                        <option value="control" @if(old('role') == 'control') selected @endif>كنترول
                                        </option>
                                        <option value="academic_advising"
                                                @if (old('role') == 'academic_advising')selected @endif>ارشاد اكاديمي
                                        </option>
                                    </select>
                                    @if($errors->has('role'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('role')}}</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        generatePassword();

        function generatePassword() {
            let num = '0123456789';
            let upper_char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let password = '';
            for (let i = 0; i < 4; i++) {
                password += num[Math.floor(Math.random() * num.length)] + upper_char[Math.floor(Math.random() * upper_char.length)];
            }
            $('#password').val(password.split('').sort(function () {
                return 0.5 - Math.random()
            }).join(''));
        }
    </script>
@endsection
