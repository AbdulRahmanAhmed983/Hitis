@extends('layout.layout')
@section('title', 'تغير البيانات')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-10 mx-auto">
            @if(!session()->has('email_code'))
                <div class="card card-primary">
                    <form role="form" method="post" action="{{route('update.data')}}" autocomplete="off">
                        @csrf
                        @method('put')
                        <div class="card-body">
                            @if(session()->has('success'))
                                <div class="alert alert-success mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-check"></i> {!! session('success') !!}</h6>
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
                                <label for="name">الاسم</label>
                                <input type="text" autocomplete="off" class="form-control" id="name" name="name"
                                       required
                                       value="{{$user['name']}}">
                                @if($errors->has('name'))
                                    <div class="alert alert-danger mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$errors->first('name')}}</h6>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="mobile">رقم الهاتف</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" required
                                       value="{{$user['mobile']}}">
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
                                       value="{{$user['email']}}">
                                @if($errors->has('email'))
                                    <div class="alert alert-danger mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$errors->first('email')}}</h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            @endif
            @if(session()->has('email_code'))
                <form role="form" method="post" action="{{route('update.email')}}" autocomplete="off">
                    @csrf
                    @method('put')
                    @if(session()->has('success'))
                        <div class="alert alert-success mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-check"></i> {!! session('success') !!}</h6>
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
                            <div class="form-group">
                                <label for="code">رمز التحقق</label>
                                <input type="text" class="form-control" id="code" name="code" required>
                                @if($errors->has('code'))
                                    <div class="alert alert-danger mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$errors->first('code')}}</h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">تأكيد</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
@endsection
