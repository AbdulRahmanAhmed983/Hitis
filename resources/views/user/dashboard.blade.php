@extends('layout.layout')
@section('title', 'الصفحة الرئيسية')
@section('styles')
@endsection
@section('content')
    <div style="display:flex;    justify-content: center;">
         <a href="https://aboukir-institutes.edu.eg/" target="_blank" class="brand-link" style="width:360px!important">
            <img src="{{asset('images/logo.png')}}"  alt="Logo" class="img-thumbnail" style="background:transparent;border:0;width:-webkit-fill-available;">
        </a>
    </div>
    @foreach($notifications as $notification)
        <div class="alert alert-{{$notification->type}} my-2">
            <h5 class="text-left">{{$notification->title}}</h5>
            <div class="text-center h4">
                {!!$notification->message!!}
            </div>
            <button type="button" class="btn btn-primary received" data-id="{{$notification->id}}">تم الإستلام</button>
        </div>
    @endforeach
    @if(auth()->user()->role == 'student')
        <x-student-home-page></x-student-home-page>
    @endif
    @if(auth()->user()->role == 'student_affairs')
        <x-student-affairs-home-page></x-student-affairs-home-page>
    @endif
@endsection
@section('scripts')
    <script>
        toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        toastr.options.timeOut = 0;
        toastr.options.extendedTimeOut = 0;
        toastr.options.positionClass = "toast-top-right";
        toastr.options.progressBar = true;

        @if(!empty($notifications))
        $('.received').on('click', function () {
            let me = $(this);
            let id = me.attr('data-id');
            me.attr('disabled', true);
            $.ajax({
                type: 'delete',
                url: '{{route('remove.notification')}}',
                data: {
                    "_token": "{{csrf_token()}}",
                    'id': id,
                },
                success: function (data) {
                    toastr.options.rtl = true;
                    toastr.success(data);
                    me.parent().remove();
                },
                error: function (data) {
                    toastr.options.rtl = true;
                    toastr.error(data['responseText']);
                },
                complete: function () {
                    me.attr('disabled', false);
                }
            });
        });
        @endif
    </script>
    @yield('component-style')
    @yield('component-script')
@endsection
