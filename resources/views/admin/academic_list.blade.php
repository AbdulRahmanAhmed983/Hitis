@extends('layout.layout')
@section('title', 'قائمة المرشدين الأكاديميين')
@section('styles')
    <style>
        #report th {
            vertical-align: middle !important;
        }
    </style>
@endsection
@section('content')
    <div class="card card-primary text-capitalize">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title">student report</h2>
            <div class="card-tools">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <table id="report" class="table text-center table-bordered rounded">
                <tr>
                    <th class="align-middle">التخصص</th>
                    @foreach($counter as $specialization => $specializations)
                        <th colspan="{{$col_counter[$specialization]['col']}}">{{$specialization}}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="align-middle">الشعبة</th>
                    @foreach($counter as $specialization => $specializations)
                        @foreach($specializations as $department => $departments)
                            <th colspan="{{$col_counter[$specialization][$department]['col']}}">{{$department}}</th>
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th class="align-middle">الفرقة</th>
                    @foreach($counter as $specialization => $specializations)
                    @foreach($specializations as $department => $departments)
                        @foreach($departments as $study_group => $group)
                            <th colspan="{{$col_counter[$specialization][$department][$study_group]['col']}}">{{$study_group}}</th>
                        @endforeach
                     @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th class="align-middle">حالة الطلاب</th>
                    @foreach($counter as $specialization => $specializations)
                        @foreach($specializations as $department => $departments)
                            @foreach($departments as $study_group => $group)
                                @foreach($group as $studying_status => $v)
                                    @php
                                        $col_key = $col_counter[$specialization][$department][$study_group][$studying_status]['col'] ?? 0;
                                    @endphp
                                    <th colspan="{{ $col_key }}">
                                        {{ $studying_status }}
                                    </th>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th class="align-middle">حالة الارشاد</th>
                    @foreach($counter as $specialization => $specializations)
                    @foreach($specializations as $department => $departments)
                        @foreach($departments as $study_group => $group)
                            @foreach($group as $studying_status => $has_advisors)
                                @foreach($has_advisors as $has_advisor => $v)
                                    <th>{{$has_advisor}}</th>
                                @endforeach
                            @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <td class="align-middle">الاعداد</td>
                    @foreach($counter as $specialization => $specializations)
                    @foreach($specializations as $department => $departments)
                        @foreach($departments as $study_group => $group)
                            @foreach($group as $studying_status => $has_advisors)
                                @foreach($has_advisors as $has_advisor => $v)
                                    <td>{{$v}}</td>
                                @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    <x-data-table :keys="$keys" primaryKey="username" :hiddenKeys="$hidden_keys" :removedKeys="$removed_keys"
                  :pages="$advisors" :search="$search" edit="edit.academic" delete="{{--user.delete--}}#"
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
        // $(document).ready(function () {
        let url = '{{route('show.academic.students',['username'=>'username'])}}';
        $('#example tbody tr').each(function () {
            let username = $(this).find("td:nth-child(2)").text();
            $(this).find("td div.btn-group-vertical").append('<a class="btn btn-outline-info rounded my-1"' +
                ' href="' + url.replace('username', username) + '">قائمة الطلاب</a>');
        });
        // });
    </script>
    @yield('component-style')
    @yield('component-script')
@endsection
