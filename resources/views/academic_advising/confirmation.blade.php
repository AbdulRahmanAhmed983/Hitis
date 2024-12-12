@extends('layout.layout')
@section('title', 'مراجعة الطلاب')
@section('styles')
@endsection
@section('content')
    <x-data-table :keys="$keys" :primaryKey="$primaryKey" :hiddenKeys="$hidden_keys" :removedKeys="$removed_keys"
                  :pages="$students" :status="$status" :search="$search" edit="{{$edit}}"
                  delete="{{$delete}}"
                  deleteMessage="هل انت متأكد من حذف التسجيل للطالب primaryKey ؟"></x-data-table>
@endsection
@section('scripts')
    <script>
        @if($errors->any())
            toastr.options.closeButton = true;
        toastr.options.newestOnTop = false;
        // toastr.options.timeOut = 0;
        // toastr.options.extendedTimeOut = 0;
        toastr.options.rtl = true;
        toastr.options.positionClass = "toast-top-right";
        toastr.options.progressBar = true;
        @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
        @endforeach
        @endif
    </script>
    @yield('component-style')
    @yield('component-script')
@endsection
