@extends('layout.layout')
@section('title', 'تغيير بيانات المستخدم '.(!empty($data) ? $data['username'] : ''))
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection
@section('content')
    @if(empty($data))
        <div class="mt-3 text-center">
            <div class="alert alert-danger">
                <h6><i class="icon fas fa-ban"></i> {{$errors->first()}}</h6>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card card-primary">
                    <form role="form" method="post"
                          action="{{route('user.update.data')}}" autocomplete="off">
                        @csrf
                        @method('put')
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">الاسم</label>
                                        <input type="text" autocomplete="off" class="form-control" id="name" name="name"
                                               required value="{{$data['name']}}">
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
                                               value="{{$data['mobile']}}">
                                        @if($errors->has('mobile'))
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$errors->first('mobile')}}</h6>
                                            </div>
                                        @endif
                                    </div>
                                    @if(auth()->user()->role == 'owner')
                                        <div class="card card-primary collapsed-card">
                                            <div class="card-header" data-card-widget="collapse"
                                                 style="cursor:pointer;">
                                                <h2 class="card-title">Permissions</h2>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered table-hover text-center">
                                                    <thead>
                                                    <tr>
                                                        <th class="align-middle">Action</th>
                                                        <th class="align-middle">Check</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($actions as $action)
                                                        <tr>
                                                            <td class="align-middle">
                                                                <label for="action{{$action}}"
                                                                       class="d-block w-100 h-100">{{$action}}</label>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="icheck-primary d-block">
                                                                    <input type="checkbox" id="action{{$action}}"
                                                                           {{($have[$action])? 'checked':''}}
                                                                           name="action[]" value="{{$action}}">
                                                                    <label for="action{{$action}}"></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @if($errors->has('action'))
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$errors->first('action')}}</h6>
                                            </div>
                                        @endif
                                        @if($errors->has('action.*'))
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$errors->first('action.*')}}</h6>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="{{$data['email']}}">
                                        @if($errors->has('email'))
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$errors->first('email')}}</h6>
                                            </div>
                                        @endif
                                    </div>
                                    @if(auth()->user()->role == 'owner')
                                        <div class="form-group">
                                            <label for="password">كلمة السر</label>
                                            <input type="text" class="form-control" id="password" name="password">
                                            @if($errors->has('password'))
                                                <div class="alert alert-danger mt-3 text-center">
                                                    <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">
                                                        &times;
                                                    </button>
                                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('password')}}
                                                    </h6>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="password_status">حالة كلمة السر</label>
                                        <input type="number" class="form-control" id="password_status"
                                               name="password_status"
                                               min="0" max="2" step="1"
                                               value="{{$data['password_status']}}">
                                        @if($errors->has('password_status'))
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6>
                                                    <i class="icon fas fa-ban"></i> {{$errors->first('password_status')}}
                                                </h6>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12">

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
    @endif
@endsection
@section('scripts')
@endsection
