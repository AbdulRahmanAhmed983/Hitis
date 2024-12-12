<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="credit hour system for abu-qir institute">
    <meta name="developer" content="Eng. Kirollous Victor">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{asset('images/lo.jpg')}}"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Bootstrap 4 RTL -->
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
    <!-- Custom style for RTL -->
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <style>
        .no-js {
            display: none;
        }
    </style>
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<noscript>
    <meta http-equiv="refresh" content="0.0;url={{route('logout')}}">
</noscript>
<div class="wrapper no-js">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light sticky-top">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        {{--        <form class="form-inline ml-3">--}}
        {{--            <div class="input-group input-group-sm">--}}
        {{--                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">--}}
        {{--                <div class="input-group-append">--}}
        {{--                    <button class="btn btn-navbar" type="submit">--}}
        {{--                        <i class="fas fa-search"></i>--}}
        {{--                    </button>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </form>--}}

    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <x-asidebar></x-asidebar>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" {{--style="width: fit-content;"--}}>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-12">
                        <h1 class="m-0 text-dark text-capitalize">@yield('title')</h1>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
        <!-- /.content -->
        <div id="f-height"></div>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer fixed-bottom">
        <div class="mx-auto" style="width: fit-content">
            <strong>Copyright &copy; 2021 <a target="_blank"
                                             href="https://www.dahab-informatics.com/">Dahab Informatics</a> and <a
                    target="_blank"
                    href="https://aboukir-institutes.edu.eg/">Abu Qir High Institutes</a>. All Rights Reserved</strong>
        </div>
    </footer>
</div>
<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('assets/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.0.4/popper.js"></script>
<!-- Bootstrap 4 rtl -->
<script src="https://cdn.rtlcss.com/bootstrap/v4.2.1/js/bootstrap.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- Toastr -->
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('js/demo.js')}}"></script>
<script>
    $('.no-js').removeClass('no-js');
    $(function () {
        //The passed argument has to be at least a empty object or a object with your desired options
        $('body').overlayScrollbars({});
    });
    $(window).on('load', function () {
        $('#f-height').height($('footer').height() + 30);
    });
    // var w = $(window).width();
    // $('.content-wrapper').css('width', w);
    activeLink();

    function activeLink() {
        let l = window.location.href;
        let link = l.substr(0, l.indexOf('#') !== -1 ? l.indexOf('#') : l.length);
        link = link.substr(0, link.indexOf('?') !== -1 ? link.indexOf('?') : link.length);
        $('a[href="' + link + '"]').first().addClass('active');
        $('li.has-treeview:has(a[href="' + link + '"])').first().addClass('menu-open');
        $('li.menu-open a').first().addClass('active');
    }

    @if(session()->has('expired_session'))
        toastr.options.closeButton = true;
    toastr.options.newestOnTop = false;
    toastr.options.timeOut = 0;
    toastr.options.extendedTimeOut = 0;
    // toastr.options.rtl = true;
    toastr.options.positionClass = "toast-top-center";
    toastr.options.progressBar = true;
    toastr.error('{{session('expired_session')}}');
    $('.toast-message').addClass('text-right', 'text-capitalize');
    @endif
        @if(session()->has('rate-limit'))
        toastr.options.closeButton = true;
    toastr.options.newestOnTop = false;
    toastr.options.timeOut = 0;
    toastr.options.extendedTimeOut = 0;
    toastr.options.rtl = true;
    toastr.options.positionClass = "toast-top-center";
    toastr.options.progressBar = true;
    toastr.error('{{session('rate-limit')}}');
    @endif
</script>
 <script>
     // Disable right-click context menu
//     document.addEventListener('contextmenu', function (e) {
//     e.preventDefault();
// });

// // Disable keyboard shortcuts for developer tools
// document.onkeydown = function (e) {
//     // F12
//     if (event.keyCode == 123) {
//         return false;
//     }
//     // Ctrl+Shift+I
//     if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
//         return false;
//     }
//     // Ctrl+Shift+C
//     if (e.ctrlKey && e.shiftKey && e.keyCode == 67) {
//         return false;
//     }
//     // Ctrl+Shift+J
//     if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
//         return false;
//     }
//     // Ctrl+U
//     if (e.ctrlKey && e.keyCode == 85) {
//         return false;
//     }
// };

// Disable inspect element function
document.onmousedown = function (e) {
    if (e.button === 2) {
        return false;
    }
};

// Block console.log output
(function () {
    var oldLog = console.log;
    console.log = function (message) {
        if (typeof message === 'string' && message.indexOf('Blocked console.log') !== -1) {
            oldLog.apply(console, arguments);
        }
    };
    console.log('Blocked console.log');
})();
 </script>
@yield('scripts')
</body>
</html>
