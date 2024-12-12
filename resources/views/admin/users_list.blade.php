@extends('layout.layout')
@section('title', 'قائمة المستخدم')
@section('styles')
@endsection

@section('content')
    <x-data-table :keys="$keys" primaryKey="username" :hiddenKeys="$hidden_keys" :removedKeys="$removed_keys"
                  :pages="$users" :search="$search" edit="user.change.data" delete="user.delete"
                  deleteMessage='هل أنت متأكد من حذف المستخدم primaryKey ؟'></x-data-table>
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
