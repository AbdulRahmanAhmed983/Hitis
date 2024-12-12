<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="credit hour system for abu-qir institute">
    <meta name="author" content="Eng. Kirollous Victor">
    <title>Error 404</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{asset('images/lo.jpg')}}"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Bootstrap 4 -->
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .no-js {
            display: none;
        }

        body {
            background: #e9ecef;
        }
    </style>
</head>
<body>
<noscript>
    <div style="color: red;text-align: center;font-weight: 800;font-size: x-large;margin-top: 100px;">
        <meta http-equiv="refresh" content="0.0;url={{route('login')}}">
    </div>
</noscript>
<div class="no-js" style="margin-top: 10%">
    <div class="mx-auto col-8 text-center" style="height:fit-content;width:fit-content;">
        <h2 class="text-warning col-12" style="font-size: 120px;">404</h2>
        <div class="mx-2" dir="rtl">
            <h2><i class="fas fa-exclamation-triangle text-warning"></i> عفوا! الصفحة
                غير موجودة.</h2>
            <p class="h5">لم نتمكن من العثور على الصفحة التي تبحث عنها.
                في هذه الأثناء ، يمكنك العودة إلى <a href="{{route('dashboard')}}">صفحتك</a> او <a
                    href="{{route('login')}}">تسجيل الدخول</a>.
            </p>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script>
    $('.no-js').removeClass('no-js');
</script>
</body>
</html>
