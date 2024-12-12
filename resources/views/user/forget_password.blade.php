<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="credit hour system for abu-qir institute">
    <meta name="author" content="Eng. Kirollous Victor">
    <title>تسجيل الدخول</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{asset('images/lo.jpg')}}"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .no-js {
            display: none;
        }
    </style>
</head>
<body class="hold-transition login-page">
<noscript>
    <div style="color: red;text-align: center;font-weight: 800;font-size: x-large;margin-top: 100px;">
        <span dir="ltr">Please enable javascript or Your browser does not support JavaScript!</span>
        <br>
        الرجاء تفعيل جافا سكريبت أو ان متصفحك لا يدعم جافا سكريبت!
    </div>
</noscript>
<div class="login-bodx col-lg-4 col-md-6 col-11 mx-auto mt-5 no-js">
    <div class="login-logo">
        <div class="image">
            <img src="{{asset('images/logo2.png')}}" alt="Logo" class="w-100 img-thsumbnail">
        </div>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title float-right font-weight-bold">إعادة تعيين كلمة المرور</h3>
        </div>
        <div class="card-body">
            <form action="{{route('forget.password.post')}}" method="post" autocomplete="off">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" id="email" class="form-control text-right"
                           placeholder="بريد الالكتروني" required>
                    <div class="input-group-append">
                        <label for="email" class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </label>
                    </div>
                </div>
                <div class="row" dir="rtl">
                    <div class="mx-auto">
                        <button type="submit" class="btn btn-primary btn-block">أرسل رابط إعادة تعيين كلمة المرور
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <p class="mt-3 mb-1 float-right" dir="rtl">
                <a href="{{route('login')}}">تسجيل الدخول</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<div id="f-height"></div>
<footer class="w-100 card-footer bg-white text-center fixed-bottom" style="font-size: 24px;">
    <strong>Copyright &copy; 2021 <a target="_blank"
                                     href="https://www.dahab-informatics.com/">Dahab Informatics</a> and <a
            target="_blank"
            href="https://aboukir-institutes.edu.eg/">Abu Qir High Institutes</a>. All Rights Reserved</strong>
</footer>
<!-- /.login-box -->
<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Toastr -->
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<script>
    $('.no-js').removeClass('no-js');
    $(window).on('load', function () {
        $('#f-height').height($('footer').height() + 30);
    });
    @if($errors->any() or session()->has('success'))
        toastr.options.closeButton = true;
    toastr.options.newestOnTop = false;
    toastr.options.timeOut = 0;
    toastr.options.extendedTimeOut = 0;
    toastr.options.rtl = true;
    toastr.options.positionClass = "toast-top-right";
    toastr.options.progressBar = true;
    @if(session()->has('success'))
    toastr.success('{{session('success')}}');
    @endif
    @foreach ($errors->all() as $error)
    toastr.error('{{$error}}');
    @endforeach
    $('.toast-message').addClass('text-right');
    @endif
</script>
</body>
</html>
