@extends('layout.layout')
@section('title', 'مراجعة مالية الطلاب')
@section('styles')
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <h4>إجمالي عدد الطلاب {{$students->total()}}</h4>
        </div>
    </div>
    <div class="card card-primary collapsed-card">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title text-capitalize">data filter</h2>
            <div class="card-tools">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center justify-content-around">
                <div class="form-group col-lg-3 col-md-6">
                    <label for="year">السنة الدراسية</label>
                    <select form="search" class="custom-select" name="year" id="year">
                        <option value=""></option>
                        @foreach($filter_data['year'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['year']) and $filter['year'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="semester">الفصل الدراسي</label>
                    <select form="search" class="custom-select" name="semester" id="semester">
                        <option value=""></option>
                        @foreach($filter_data['semester'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['semester']) and $filter['semester'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="remaining_study">حالة السداد المصاريف الداراسية</label>
                    <select form="search" class="custom-select" name="remaining_study" id="remaining_study">
                        <option value=""></option>
                        @foreach($filter_data['remaining_study'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['remaining_study']) and $filter['remaining_study'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="remaining_other">حالة السداد المصاريف الاخرى</label>
                    <select form="search" class="custom-select" name="remaining_other" id="remaining_other">
                        <option value=""></option>
                        @foreach($filter_data['remaining_other'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['remaining_other']) and $filter['remaining_other'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="study_group">الفرقة الدراسية</label>
                    <select form="search" class="custom-select" name="study_group" id="study_group">
                        <option value=""></option>
                        @foreach($filter_data['study_group'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['study_group']) and $filter['study_group'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="student_classification">تصنيف الطلاب</label>
                    <select form="search" class="custom-select" name="student_classification"
                            id="student_classification">
                        <option value=""></option>
                        @foreach($filter_data['student_classification'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['student_classification']) and $filter['student_classification'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="specialization">التخصص</label>
                    <select form="search" class="custom-select" name="specialization"
                            id="specialization">
                        <option value=""></option>
                        @foreach($filter_data['specialization'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['specialization']) and $filter['specialization'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="departments">الشعبة</label>
                    <select form="search" class="custom-select" name="departments_id" id="departments">
                        <option value=""></option>
                        @foreach($departments as $data)
                            <option value="{{$data->id}}"
                                @if((isset($filter_data['departments_id']) and $filter_data['departments_id'] == $data->id)) selected @endif>
                                {{$data->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-6">
                    <label for="grade">التقدير</label>
                    <select form="search" class="custom-select" name="grade" id="grade">
                        <option value=""></option>
                        @foreach($filter_data['grade'] as $data)
                            <option value="{{$data}}"
                                    @if((isset($filter['grade']) and $filter['grade'] == $data)) selected @endif>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row align-content-center">
                <div class="col-lg-3 col-md-6">
                    <select form="search" class="custom-select" name="per_page" id="per_page">
                        @foreach($filter_data['per_page'] as $data)
                            <option
                                value="{{$data}}" {{($items_per_pages == $data) ? 'selected': ''}}>{{$data}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" form="search" class="btn btn-primary mx-3">بحث</button>
                <button type="button" id="reset" form="search" class="btn btn-primary mx-3">مسح</button>
            </div>
        </div>
    </div>
    @error('username')
    <div class="alert alert-danger mt-3 text-center">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
            &times;
        </button>
        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
    </div>
    @enderror
    <x-data-table :keys="$keys" :primaryKey="$primaryKey" :hiddenKeys="$hidden_keys" :removedKeys="$removed_keys"
                  :pages="$students" :status="$status" :search="$search" edit="show.tickets" delete="#"
                  deleteMessage="هل انت متأكد من حذف التسجيل للطالب primaryKey ؟"></x-data-table>
@endsection
@section('scripts')
    <script>
        $('td div.btn-group-vertical a:contains("تعديل")').text('الحافظات السابقة');
        {{--let url1 = '{{route('pay.ticket',['username'=>'username'])}}';--}}
        {{--let url2 = '{{route('show.tickets',['username'=>'username'])}}';--}}
        // $('#example tbody tr').each(function () {
        //     let username = $(this).find("td:nth-child(2)").text();
        //     $(this).find("td div.btn-group-vertical").append('<a class="btn btn-outline-info rounded my-1"' +
        //         ' href="' + url2.replace('username', username) + '">الحافظات السابقة</a>');
        // });

        $('form#search').on('submit', function () {
            $('input[form="search"],select[form="search"]').each(function () {
                if ($(this).val() === '')
                    $(this).prop("disabled", true);
            });
            return true;
        });

        $('#reset').on('click', function () {
            $('input[form="search"],select[form="search"]').not('#per_page').val('');
        });
    </script>
    @yield('component-style')
    @yield('component-script')
@endsection
