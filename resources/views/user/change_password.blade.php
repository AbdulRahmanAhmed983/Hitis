@extends('layout.layout')
@section('title', 'تغير كلمة السر')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-10 mx-auto">
            <!-- general form elements -->
            <div class="card card-primary">
                <!-- form start -->
                <form role="form" method="post" action="{{route('update.password')}}" autocomplete="off">
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
                        @if(session()->has('warning'))
                            <div class="alert alert-warning mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-exclamation-triangle"></i> {{session('warning')}}</h6>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="">
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="current_password">كلمة السر الحاليه</label>
                            <input type="password" required class="form-control" id="current_password"
                                   name="current_password">
                            @if($errors->has('current_password'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('current_password')}}</h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="new_password">كلمة السر الجديده</label>
                            <input type="password" required class="form-control" id="new_password" name="new_password">
                            <code class="text-dark">يجب ان تتكون كلمة السر على الاقل من 8 احرف من التالى:
                                <ul>
                                    <li>1 حرف كبير (A – Z)</li>
                                    <li>1 حرف صغير (a – z)</li>
                                    <li>1 رقم (0 – 9)</li>
                                    <li>1 من الحروف الخاصة التالية (@_#$%/*-+?.)</li>
                                </ul>
                            </code>
                            @if($errors->has('new_password'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('new_password')}}</h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">إعادة كتابة كلمة السر الجديدة</label>
                            <input type="password" required class="form-control" id="confirm_password"
                                   name="confirm_password">
                            @if($errors->has('confirm_password'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('confirm_password')}}</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection
@section('scripts')
    <script>
        @if(session()->has('info'))
            toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        toastr.options.timeOut = 0;
        toastr.options.extendedTimeOut = 0;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-center";
        toastr.options.progressBar = true;
        toastr.info('{{session('info')}}');
        @endif
    </script>
@endsection
