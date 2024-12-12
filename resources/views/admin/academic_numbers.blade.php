@extends('layout.layout')
@section('title', 'تعديل '.$advisor['name'])
@section('styles')
    <style>
        .middle th, td {
            vertical-align: middle !important;
        }
    </style>
@endsection
@section('content')
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
    @if($errors->any())
        <div class="alert alert-danger mt-3 text-center">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            @foreach ($errors->all() as $error)
                <h6><i class="icon fas fa-ban"></i> {{$error}}</h6>
            @endforeach
        </div>
    @endif
    <div class="card card-primary text-capitalize">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title">current students</h2>
            <div class="card-tools">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <table class="table text-center table-bordered rounded middle">
                <tr>
                    <th>التخصص</th>
                    @foreach($counter as $specialization => $specializations)
                        <th colspan="{{$col_counter[$specialization]['col']}}">{{$specialization}}</th>
                    @endforeach
                </tr>
                <tr>
                    <th>الشعبة</th>
                    @foreach($counter as $specialization => $specializations)
                    @foreach($specializations as $department => $departments)
                        <th colspan="{{$col_counter[$specialization][$department]['col']}}">{{$department}}</th>
                    @endforeach
                @endforeach
                </tr>
                <tr>
                    <th>الفرقة</th>
                    @foreach($counter as $specialization => $specializations)
                    @foreach($specializations as $department => $departments)
                        @foreach($departments as $study_group => $group)
                            <th colspan="{{$col_counter[$specialization][$department][$study_group]['col']}}">{{$study_group}}</th>
                        @endforeach
                     @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th>حالة الطلاب</th>
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
                    <td>الاعداد</td>
                    @foreach($counter as $specialization => $specializations)
                        @foreach($specializations as $department => $departments)
                            @foreach($departments as $study_group => $group)
                                @foreach($group as $studying_status => $has_advisors)
                                    @if(is_array($has_advisors) || is_object($has_advisors))
                                        @foreach($has_advisors as $has_advisor => $v)
                                            <td>{{$v}}</td>
                                        @endforeach
                                    @else
                                        <td>{{$has_advisors}}</td>
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <td>action</td>
                    @foreach($counter as $specialization => $specializations)
                    @foreach($specializations as $department => $departments)
                        @foreach($specializations as $study_group => $group)
                            @foreach($group as $studying_status => $v)
                                <td>
                                    <form
                                        action="{{route('remove.academic.advisor',['advisor'=>$advisor['username']])}}"
                                        method="post">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="specialization" value="{{$specialization}}">
                                        <input type="hidden" name="specialization" value="{{$specialization}}">
                                        <input type="hidden" name="study_group" value="{{$study_group}}">
                                        <input type="hidden" name="studying_status" value="{{$studying_status}}">
                                        <div class="col-12 mb-2">
                                            <input type="number" class="form-control" name="number" max="{{ is_array($v) ? '' : strval($v) }}"
                                            min="1" value="0" step="1">
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-outline-danger">حذف</button>
                                        </div>
                                    </form>
                                </td>
                            @endforeach
                        @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    <div class="card card-primary text-capitalize">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title">current students without advisor</h2>
            <div class="card-tools">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <table class="table text-center table-bordered rounded middle">
                <tr>
                    <th>التخصص</th>
                    @foreach($counter2 as $specialization => $specializations)
                        <th colspan="{{$col_counter2[$specialization]['col']}}">{{$specialization}}</th>
                    @endforeach
                </tr>
                <tr>
                    <th>الفرقة</th>
                    @foreach($counter2 as $specialization => $specializations)
                        @foreach($specializations as $study_group => $group)
                            <th colspan="{{$col_counter2[$specialization][$study_group]['col']}}">{{$study_group}}</th>
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th>حالة الطلاب</th>
                    @foreach($counter2 as $specialization => $specializations)
                        @foreach($specializations as $study_group => $group)
                            @foreach($group as $studying_status => $v)
                                <th>{{$studying_status}}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <td>الاعداد</td>
                    @foreach($counter2 as $specialization => $specializations)
                        @foreach($specializations as $study_group => $group)
                            @foreach($group as $studying_status => $v)
                                <td>{{$v}}</td>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <td>action</td>
                    @foreach($counter2 as $specialization => $specializations)
                        @foreach($specializations as $study_group => $group)
                            @foreach($group as $studying_status => $v)
                                <td>
                                    <form
                                        action="{{route('add.academic.advisor',['advisor'=>$advisor['username']])}}"
                                        method="post">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="specialization" value="{{$specialization}}">
                                        <input type="hidden" name="study_group" value="{{$study_group}}">
                                        <input type="hidden" name="studying_status" value="{{$studying_status}}">
                                        <div class="col-12 mb-2">
                                            <input type="number" class="form-control" name="number" max="{{$v}}"
                                                   min="1" value="0" step="1">
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-outline-success">أضافة</button>
                                        </div>
                                    </form>
                                </td>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    <form action="{{route('update.academic',['username' => $advisor['username']])}}" method="post">
        @csrf
        @method('put')
        <div class="card card-primary text-capitalize">
            <div class="card-body">
                <div class="row justify-content-md-around">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="study_group">الفرقة</label>
                            <select class="custom-select" name="study_group" id="study_group" required>
                                @foreach($data_arr['study_group'] as $data)
                                    <option value="{{$data}}"{{($data == $advisor['study_group'])? ' selected':''}}>
                                        {{$data}}</option>
                                @endforeach
                                <option value="all"{{('all' == $advisor['study_group'])? ' selected':''}}>
                                    الكل
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="specialization">التخصص</label>
                            <select class="custom-select" name="specialization" id="specialization" required>
                                @foreach($data_arr['specialization'] as $data)
                                    <option value="{{$data}}"{{($data == $advisor['specialization'])? ' selected':''}}>
                                        {{$data}}</option>
                                @endforeach
                                <option value="all"{{('all' == $advisor['specialization'])? ' selected':''}}>
                                    الكل
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="departments_id">الشعبة</label>
                            <select class="custom-select" name="departments_id" id="departments_id" required>
                                @foreach($departments_all as $data)

                                    <option value="{{$data}}"{{($data == $advisor['departments_id'])? ' selected':''}}>
                                        {{$data}}</option>
                                @endforeach
                                <option value="all"{{('all' == $advisor['departments_id'])? ' selected':''}}>
                                    الكل
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="studying_status">حالة الطلاب</label>
                            <select class="custom-select" name="studying_status" id="studying_status" required>
                                <option value="مستجد"{{('مستجد' == $advisor['studying_status'])? ' selected':''}}>
                                    مستجد
                                </option>
                                <option value="باقي"{{('باقي' == $advisor['studying_status'])? ' selected':''}}>
                                    باقي
                                </option>
                                <option value="all"{{('all' == $advisor['studying_status'])? ' selected':''}}>
                                    الكل
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="max_students">الحد الأقصى لعدد الطلاب</label>
                            <input type="number" step="1" min="{{$advisor['current_students']}}" class="form-control"
                                   id="max_students" name="max_students" required value="{{$advisor['max_students']}}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="current_students">عدد الطلاب الحالي</label>
                            <input type="number" step="1" class="form-control" name="current_students" readonly
                                   id="current_students" value="{{$advisor['current_students']}}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
@endsection
