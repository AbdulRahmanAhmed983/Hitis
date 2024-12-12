@extends('layout.layout')
@section('title', 'إضافة طالب')
@section('styles')
@endsection
@section('content')
    <form method="post" action="{{route('add.student')}}" enctype="multipart/form-data" autocomplete="off">
        @csrf
        @if(session()->has('success'))
            <div class="alert alert-success mt-3 col-12 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                <h6>برجاء حفظ اسم المستخدم و كلمة السر</h6>
                <h6>اسم المستخدم {{session('data')['username']}}</h6>
                <h6>كلمة السر {{session('data')['password']}}</h6>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger mt-3 col-12 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">اسم الطالب</label>
                            <input type="text" class="form-control {{$errors->has('name')?'is-invalid':''}}" name="name"
                                   id="name" placeholder="اسم الطالب رباعي" value="{{old('name')}}" required>
                            @if($errors->has('name'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('name')}}</h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            {{-- <label for="photo">صورة الطالب</label>
                            <div class="row col-12 justify-content-between">
                                <input type="file" name="photo" id="photo" value="{{old('photo')}}" accept="image/*"
                                       class="form-control-file btn btn-dark col-8">
                                <button type="button" class="btn btn-dark col-3" onclick="scanToJpg();">Scan</button>
                            </div>
                            <img id="photo_image" src="" class="mx-auto d-none" style="max-width: 70%" alt="">
                            <input type="hidden" name="photo" id="photo-string" class="form-control-file"
                                   disabled="disabled">
                            @if($errors->has('photo'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('photo')}}
                                    </h6>
                                </div>
                            @endif --}}
                            {{-- @if($errors->any() and !$errors->has('photo'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> لم يتم تحميل الصوره</h6>
                                </div>
                            @endif --}}

                        </div>
                        {{-- <div class="form-group">
                            <label for="religion">الديانة</label>
                            <select class="custom-select {{$errors->has('religion')?'is-invalid':''}}"
                                    name="religion" id="religion">
                                @foreach($data['religion'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('religion') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('religion'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('religion')}}</h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="birth_date">تاريخ الميلاد</label>
                            <input type="date" value="{{old('birth_date')}}" name="birth_date" id="birth_date"
                                   class="form-control {{$errors->has('birth_date')?'is-invalid':''}}" >
                            @if($errors->has('birth_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('birth_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div>--}}

                         {{-- <div class="form-group">
                            <label for="certificate_obtained_date">تاريخ الحصول علي الشهادة</label>
                            <input type="number" min="2000" step="1"
                                   class="form-control {{$errors->has('certificate_obtained_date')?'is-invalid':''}}"
                                   name="certificate_obtained_date" value="{{old('certificate_obtained_date')}}"
                                   id="certificate_obtained_date">
                            @if($errors->has('certificate_obtained_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('certificate_obtained_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        {{-- <div class="form-group">
                            <label for="certificate_seating_number">رقم جلوس الشهادة</label>
                            <input type="text" name="certificate_seating_number" id="certificate_seating_number"
                                   class="form-control {{$errors->has('certificate_seating_number')?'is-invalid':''}}"
                                   value="{{old('certificate_seating_number')}}">
                            @if($errors->has('certificate_seating_number'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('certificate_seating_number')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="certificate_degree">مجموع الطالب</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" step="0.1"  min="0"
                                           class="form-control {{$errors->has('certificate_degree')?'is-invalid':''}}"
                                           name="certificate_degree" value="{{old('certificate_degree')}}"
                                           id="certificate_degree">
                                </div>
                                <span style="font-size: 30px;" class="mx-1">&#8260;</span>
                                <div class="col">
                                    <input type="number" step="1"  min="0"
                                           class="form-control" id="certificate_degree_total"
                                           name="certificate_degree_total"
                                           value="{{old('certificate_degree_total')}}">
                                </div>
                                <span class="font-weight-bold align-self-center">النسبه المئويه</span>
                                <div class="col">
                                    <input type="number" min="50" max="100" readonly
                                           class="form-control" id="certificate_degree_percentage"
                                           name="certificate_degree_percentage"
                                           value="{{old('certificate_degree_percentage')}}">
                                </div>
                            </div>
                            @if($errors->has('certificate_degree'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('certificate_degree')}}
                                    </h6>
                                </div>
                            @endif
                            @if($errors->has('certificate_degree_percentage'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('certificate_degree_percentage')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        <div class="form-group">
                            <label for="english_degree">درجة اللغه الإنجليزية</label>
                            <input type="number" step="0.1" min="25" max="50" required
                                   class="form-control {{$errors->has('english_degree')?'is-invalid':''}}"
                                   name="english_degree" value="{{old('english_degree')}}"
                                   id="english_degree">
                            @if($errors->has('english_degree'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('english_degree')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="apply_classification">تصنيف التقديم</label>
                            <select class="custom-select {{$errors->has('apply_classification')?'is-invalid':''}}"
                                    required name="apply_classification" id="apply_classification">
                                    <option readonly>اختر التصنيف </option>
                                @foreach($data['apply_classification'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('apply_classification') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('apply_classification'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('apply_classification')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        {{-- <div class="form-group">
                            <label for="apply_classification_notes">ملاحظات تصنيف التقديم</label>
                            <input type="text" name="apply_classification_notes"
                                   class="form-control {{$errors->has('apply_classification_notes')?'is-invalid':''}}"
                                   value="{{old('apply_classification_notes')}}" id="apply_classification_notes">
                            @if($errors->has('apply_classification_notes'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('apply_classification_notes')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        <div class="form-group">
                            <label for="study_group">الفرقة الدراسية</label>
                            <select class="custom-select {{$errors->has('study_group')?'is-invalid':''}}" required
                                    name="study_group" id="study_group">
                                @foreach($data['study_group'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('study_group') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('study_group'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('study_group')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="studying_status">الحالة الدراسية</label>
                            <select class="custom-select {{$errors->has('studying_status')?'is-invalid':''}}" required
                                    name="studying_status" id="studying_status">
                                @foreach($data['studying_status'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('studying_status') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('studying_status'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('studying_status')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="notes">بيانات اخري</label>
                            <textarea name="notes" id="notes" rows="1"
                                      class="form-control {{$errors->has('notes')?'is-invalid':''}}">{{old('notes')}}</textarea>
                            @if($errors->has('notes'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('notes')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-primary">
                    <div class="card-body">
                        {{-- <div class="form-group">
                            <label for="birth_country">دولة الميلاد</label>
                            <select class="custom-select {{$errors->has('birth_country')?'is-invalid':''}}"
                                    name="birth_country" id="birth_country">
                                @foreach($data['birth_country'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('birth_country') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('birth_country'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('birth_country')}}</h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <input type="text" value="{{old('other_birth_country')}}"
                                   class="form-control {{$errors->has('other_birth_country')?'is-invalid':''}}"
                                   name="other_birth_country" id="other_birth_country" placeholder="دولة الميلاد">
                            @if($errors->has('other_birth_country'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('other_birth_country')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="birth_province">محافظة الميلاد</label>
                            <select class="custom-select {{$errors->has('birth_province')?'is-invalid':''}}"
                                    name="birth_province" id="birth_province">
                                @foreach($data['birth_province'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('birth_province') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('birth_province'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('birth_province')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="address">العنوان</label>
                            <input type="text" value="{{old('address')}}"
                                   class="form-control {{$errors->has('address')?'is-invalid':''}}"
                                   name="address" id="address">
                            @if($errors->has('address'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('address')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        <div class="form-group">
                            <label for="nationality">الجنسية</label>
                            <select class="custom-select {{$errors->has('nationality')?'is-invalid':''}}" required
                                    name="nationality" id="nationality">
                                @foreach($data['nationality'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('nationality') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('nationality'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('nationality')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                         <div class="form-group">
                            <input type="text" name="other_nationality" id="other_nationality"
                                   class="form-control {{$errors->has('other_nationality')?'is-invalid':''}}"
                                   value="{{old('other_nationality')}}" placeholder="الجنسية">
                            @if($errors->has('other_nationality'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('other_nationality')}}</h6>
                                </div>
                            @endif
                        </div> 
                        {{-- <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="immigrant_student"
                                       id="immigrant_student" value="1"
                                @if(old('immigrant_student') == "1")
                                    {{ 'checked' }}
                                    @endif>
                                <label for="immigrant_student" class="custom-control-label">طالب وافد</label>
                            </div>
                            @if($errors->has('immigrant_student'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('immigrant_student')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        <div class="form-group">
                            <label for="national_id">الرقم القومي</label>
                            <input type="text" value="{{old('national_id')}}" required
                                   class="form-control {{$errors->has('national_id')?'is-invalid':''}}"
                                   name="national_id" id="national_id">
                            @if($errors->has('national_id'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('national_id')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        
                        <!-- <div class="form-group">-->
                        <!--    <label for="issuer_national_number">جهة الإصدار الرقم القومي</label>-->
                        <!--    <input type="text" value="{{old('issuer_national_number')}}"-->
                        <!--           class="form-control {{$errors->has('issuer_national_number')?'is-invalid':''}}"-->
                        <!--           name="issuer_national_number" id="issuer_national_number">-->
                        <!--    @if($errors->has('issuer_national_number'))-->
                        <!--        <div class="alert alert-danger mt-3 text-center">-->
                        <!--            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
                        <!--                &times;-->
                        <!--            </button>-->
                        <!--            <h6><i class="icon fas fa-ban"></i> {{$errors->first('issuer_national_number')}}-->
                        <!--            </h6>-->
                        <!--        </div>-->
                        <!--    @endif-->
                        <!--</div> -->
                      
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-body">
                        {{-- <div class="form-group">
                            <label for="gender">الجنس</label>
                            <div class="d-flex">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio1" name="gender"
                                           value="ذكر" @if(old('gender') == "ذكر") checked @endif >
                                    <label for="customRadio1" class="custom-control-label">ذكر</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio2" name="gender"
                                           value="أنثى" @if(old('gender') == "أنثى") checked @endif>
                                    <label for="customRadio2" class="custom-control-label">أنثى</label>
                                </div>
                            </div>
                            @if($errors->has('gender'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('gender')}}
                                    </h6>
                                </div>
                            @endif

                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="recruitment_area">منطقة التجنيد</label>
                            <input type="text" value="{{old('recruitment_area')}}"
                                   class="form-control {{$errors->has('recruitment_area')?'is-invalid':''}}"
                                   name="recruitment_area" id="recruitment_area">
                            @if($errors->has('recruitment_area'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('recruitment_area')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="military_number">الرقم العسكري</label>
                            <div class="row">
                                <div class="col">
                                    <input type="text" value="{{old('military_number.0')}}"
                                           class="form-control {{$errors->has('military_number.0')?'is-invalid':''}}"
                                           name="military_number[0]" id="military_number_1">
                                </div>
                                <span style="font-size: 30px;" class="mx-1">&#8260;</span>
                                <div class="col">
                                    <input type="text" value="{{old('military_number.1')}}"
                                           class="form-control {{$errors->has('military_number[1]')?'is-invalid':''}}"
                                           name="military_number[1]" id="military_number_2">
                                </div>
                                <span style="font-size: 30px;" class="mx-1">&#8260;</span>
                                <div class="col">
                                    <input type="text" value="{{old('military_number.2')}}"
                                           class="form-control {{$errors->has('military_number.2')?'is-invalid':''}}"
                                           name="military_number[2]" id="military_number_3">
                                </div>
                                @if($errors->has('military_number'))
                                    <div class="alert alert-danger col-11 mx-auto mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$errors->first('military_number')}}
                                        </h6>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="enlistment_status">حالة التجنيد</label>
                            <select class="custom-select {{$errors->has('enlistment_status')?'is-invalid':''}}"
                                    name="enlistment_status"  id="enlistment_status">
                                @foreach($data['enlistment_status'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('enlistment_status') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('enlistment_status'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('enlistment_status')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        <div class="form-group">
                            <label for="position_of_recruitment">موقف الطالب من التجنيد</label>
                            <input type="text" value="{{old('position_of_recruitment')}}"
                                   class="form-control {{$errors->has('position_of_recruitment')?'is-invalid':''}}"
                                   name="position_of_recruitment" id="position_of_recruitment">
                            @if($errors->has('position_of_recruitment'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('position_of_recruitment')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="decision_number">رقم القرار</label>
                            <input type="text" value="{{old('decision_number')}}"
                                   class="form-control {{$errors->has('decision_number')?'is-invalid':''}}"
                                   name="decision_number" id="decision_number">
                            @if($errors->has('decision_number'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('decision_number')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        {{-- <div class="form-group">
                            <label for="decision_date">تاريخ القرار</label>
                            <input type="date" value="{{old('decision_date')}}" name="decision_date" id="decision_date"
                                   class="form-control {{$errors->has('decision_date')?'is-invalid':''}}" >
                            @if($errors->has('decision_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('decision_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="expiry_date">تاريخ الانتهاء الاعفاء</label>
                            <input type="date" value="{{old('expiry_date')}}" name="expiry_date" id="expiry_date"
                                   class="form-control {{$errors->has('expiry_date')?'is-invalid':''}}" >
                            @if($errors->has('expiry_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('expiry_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="recruitment_notes">ملاحظات التجنيد</label>
                            <input type="text" value="{{old('recruitment_notes')}}"
                                   class="form-control {{$errors->has('recruitment_notes')?'is-invalid':''}}"
                                   name="recruitment_notes" id="recruitment_notes">
                            @if($errors->has('recruitment_notes'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('recruitment_notes')}}
                                    </h6>
                                </div>
                            @endif
                        </div> --}}
                         
                        <div class="form-group">
                            <label for="student_classification">تصنيف الطلاب</label>
                            <select class="custom-select {{$errors->has('student_classification')?'is-invalid':''}}"
                                    name="student_classification"  id="student_classification">
                                @foreach($data['student_classification'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('student_classification') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('student_classification'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('student_classification')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="classification_notes">ملاحظات التصنيف</label>
                            <input type="text" value="{{old('classification_notes')}}"
                                   class="form-control {{$errors->has('classification_notes')?'is-invalid':''}}"
                                   name="classification_notes" id="classification_notes">
                            @if($errors->has('classification_notes'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('classification_notes')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="specialization">التخصص</label>
                            <select class="custom-select {{$errors->has('specialization')?'is-invalid':''}}" required
                                    name="specialization" id="specialization">
                                <option value="" hidden></option>
                                @foreach($data['specialization'] as $d)
                                    <option
                                        value="{{$d}}" @if(old('specialization') == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('specialization'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('specialization')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="departments_id">الشعبة</label>
                            <select class="custom-select {{$errors->has('departments_id')?'is-invalid':''}}" required
                                    name="departments_id" id="department">
                                <option value="" hidden></option>
                              <option value="1">عام</option>
                              <option value="2">ترميم الأثار والمقتنيات الفنية العضوية</option>
                              <option value="3">ترميم الأثار والمقتنيات الفنية غيرالعضوية</option>
                            </select>

                            @if($errors->has('departments_id'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('departments_id')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                      
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="username">إسم المستخدم</label>
                            <input type="text" readonly required name="username" id="username"
                                   class="form-control {{$errors->has('username')?'is-invalid':''}}">
                            @if($errors->has('username'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('username')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password">كلمة المرور</label>
                            <input type="text" value="{{old('password')}}" readonly required
                                   class="form-control {{$errors->has('password')?'is-invalid':''}}"
                                   name="password" id="password">
                            @if($errors->has('password'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('password')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary col-6">إدخال</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@section('scripts')
    <script src="https://cdn.asprise.com/scannerjs/scanner.js" type="text/javascript"></script>
    <script>
        const en_cer = ["ثانويه عامه ادبي", "ثانويه ازهريه علمي", "ثانويه ازهريه ادبي", "ثانويه عامه علمي",
            "شهاده معادله"];
        const cases = ["عذر", "وقف قيد"];
        let degrees = @json($data['certificate_degree']);

        if ($('input[name="gender"]:checked').val() === "ذكر" && $('#nationality').val() === "مصري") {
            $('#recruitment_area').attr('disabled', false).parent().show();
            $('input[id*="military_number_"]').attr('disabled', false).parent().parent().parent().show();
            $('#recruitment_notes').attr('disabled', false).parent().show();
            $('#enlistment_status').attr('disabled', false).parent().show();
            $('#position_of_recruitment').attr('disabled', false).parent().show();
            if ($('#enlistment_status').val() === 'اعفاء مؤقت' || $('#enlistment_status').val() === 'اعفاء نهائي') {
                $('#decision_number').attr('disabled', false).parent().show();
                $('#decision_date').attr('disabled', false).parent().show();
            } else {
                $('#decision_number').attr('disabled', true).parent().hide();
                $('#decision_date').attr('disabled', true).parent().hide();
            }
            if ($('#enlistment_status').val() === 'اعفاء مؤقت') {
                $('#expiry_date').attr('disabled', false).parent().show();
            } else {
                $('#expiry_date').attr('disabled', true).parent().hide();
            }
        } else {
            $('#recruitment_area').attr('disabled', true).parent().hide();
            $('input[id*="military_number_"]').attr('disabled', true).parent().parent().parent().hide();
            $('#recruitment_notes').attr('disabled', true).parent().hide();
            $('#enlistment_status').attr('disabled', true).parent().hide();
            $('#position_of_recruitment').attr('disabled', true).parent().hide();
            $('#decision_number').attr('disabled', true).parent().hide();
            $('#decision_date').attr('disabled', true).parent().hide();
            $('#expiry_date').attr('disabled', true).parent().hide();
        }

        if ($('#specialization').val() !== '') {
            $.ajax({
                type: 'get',
                url: '{{route('student.username')}}',
                data: {
                    'specialization': $('#specialization').val(),
                },
                success: function (data) {
                    $('#username').val(data);
                },
                error: function (data) {
                    alert(data['responseText']);
                    $('#username').val('');
                },
                complete: function () {
                }
            });
        }

        if ($('#birth_country').val() === "أخرى") {
            $('#other_birth_country').attr('disabled', false).parent().show();
            $('#birth_province').attr('disabled', true).parent().hide();
        } else {
            $('#other_birth_country').attr('disabled', true).parent().hide();
            $('#birth_province').attr('disabled', false).parent().show();
        }

        if ($('#nationality').val() === "مصري") {
            $('#national_id').attr('disabled', false).parent().show();
            $('#passport_id').attr('disabled', true).parent().hide();
            $('#other_nationality').attr('disabled', true).hide();
            $('#issuer_national_number').attr('disabled', false).parent().show();
        } else if ($('#nationality').val() === "") {
            $('#national_id').attr('disabled', false).parent().show();
            $('#passport_id').attr('disabled', true).parent().hide();
            $('#other_nationality').attr('disabled', true).hide();
            $('#issuer_national_number').attr('disabled', false).parent().show();
        } else if ($('#nationality').val() !== "مصري" && $('#nationality').val() !== "أخرى") {
            $('#national_id').attr('disabled', true).parent().hide();
            $('#passport_id').attr('disabled', false).parent().show();
            $('#other_nationality').attr('disabled', true).hide();
            $('#issuer_national_number').attr('disabled', true).parent().hide();
        } else {
            $('#national_id').attr('disabled', true).parent().hide();
            $('#passport_id').attr('disabled', false).parent().show();
            $('#other_nationality').attr('disabled', false).show();
            $('#issuer_national_number').attr('disabled', true).parent().hide();
        }

        if ($('#certificate_obtained').val() === "شهاده معادله") {
            $('#other_certificate_obtained').show().attr('disabled', false);
            $('#certificate_seating_number').attr('required', false);
        } else {
            $('#other_certificate_obtained').hide().attr('disabled', true);
            $('#certificate_seating_number').attr('required', false);
        }

        if (en_cer.includes($('#certificate_obtained').val())) {
            $('#english_degree').attr('disabled', false).parent().show();
        } else {
            $('#english_degree').attr('disabled', true).parent().hide();
        }

        if ($('#apply_classification').val() === 'مرشح') {
            $('#apply_classification_notes').attr('disabled', true).parent().hide();
        } else {
            $('#apply_classification_notes').attr('disabled', false).parent().show();
        }

        if (cases.includes($('#student_classification').val())) {
            $('#classification_notes').attr('disabled', false).parent().show();
        } else {
            $('#classification_notes').attr('disabled', true).parent().hide();
        }

        if (degrees[$('#certificate_obtained').val()] !== undefined) {
            $('#certificate_degree_total').prop('readonly', true).val(degrees[$('#certificate_obtained').val()]);
            $('#certificate_degree').prop('min', degrees[$('#certificate_obtained').val()] / 2.0)
                .prop('max', degrees[$('#certificate_obtained').val()]);
        } else {
            $('#certificate_degree_total').prop('readonly', false);
            $('#certificate_degree').prop('min', 0).removeAttr('max');
        }

        $(document).ready(function () {
            generatePassword();
        });

        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });

        $('input[name="gender"]').on('input', function () {
            if ($(this).val() === "ذكر" && $('#nationality').val() === "مصري") {
                $('#recruitment_area').attr('disabled', false).parent().show();
                $('input[id*="military_number_"]').attr('disabled', false).parent().parent().parent().show();
                $('#recruitment_notes').attr('disabled', false).parent().show();
                $('#enlistment_status').attr('disabled', false).parent().show();
                $('#position_of_recruitment').attr('disabled', false).parent().show();
                if ($('#enlistment_status').val() === 'اعفاء مؤقت' || $('#enlistment_status').val() === 'اعفاء نهائي') {
                    $('#decision_number').attr('disabled', false).parent().show();
                    $('#decision_date').attr('disabled', false).parent().show();
                } else {
                    $('#decision_number').attr('disabled', true).parent().hide();
                    $('#decision_date').attr('disabled', true).parent().hide();
                }
                if ($('#enlistment_status').val() === 'اعفاء مؤقت') {
                    $('#expiry_date').attr('disabled', false).parent().show();
                } else {
                    $('#expiry_date').attr('disabled', true).parent().hide();
                }
                if ($('#enlistment_status').val() === 'له حق التأجيل') {
                    $('#position_of_recruitment').val(' لسن 28');
                } else {
                    $('#position_of_recruitment').val('');
                }
            } else {
                $('#recruitment_area').attr('disabled', true).parent().hide();
                $('input[id*="military_number_"]').attr('disabled', true).parent().parent().parent().hide();
                $('#recruitment_notes').attr('disabled', true).parent().hide();
                $('#enlistment_status').attr('disabled', true).parent().hide();
                $('#position_of_recruitment').attr('disabled', true).parent().hide();
                $('#decision_number').attr('disabled', true).parent().hide();
                $('#decision_date').attr('disabled', true).parent().hide();
                $('#expiry_date').attr('disabled', true).parent().hide();
            }
        });

        $('#birth_country').on('input', function () {
            if ($(this).val() === "أخرى") {
                $('#other_birth_country').attr('disabled', false).parent().show();
                $('#birth_province').attr('disabled', true).parent().hide();
            } else {
                $('#other_birth_country').attr('disabled', true).parent().hide();
                $('#birth_province').attr('disabled', false).parent().show();
            }
        });

        $('#nationality').on('input', function () {
            if ($(this).val() === "مصري") {
                $('#national_id').attr('disabled', false).parent().show();
                $('#passport_id').attr('disabled', true).parent().hide();
                $('#other_nationality').attr('disabled', true).hide();
                $('#issuer_national_number').attr('disabled', false).parent().show();
            } else if ($(this).val() !== "مصري" && $(this).val() !== "أخرى") {
                $('#national_id').attr('disabled', true).parent().hide();
                $('#passport_id').attr('disabled', false).parent().show();
                $('#other_nationality').attr('disabled', true).hide();
                $('#issuer_national_number').attr('disabled', true).parent().hide();
            } else {
                $('#national_id').attr('disabled', true).parent().hide();
                $('#passport_id').attr('disabled', false).parent().show();
                $('#other_nationality').attr('disabled', false).show();
                $('#issuer_national_number').attr('disabled', true).parent().hide();
            }
            if ($('input[name="gender"]:checked').val() === "ذكر" && $(this).val() === "مصري") {
                $('#recruitment_area').attr('disabled', false).parent().show();
                $('input[id*="military_number_"]').attr('disabled', false).parent().parent().parent().show();
                $('#recruitment_notes').attr('disabled', false).parent().show();
                $('#enlistment_status').attr('disabled', false).parent().show();
                $('#position_of_recruitment').attr('disabled', false).parent().show();
                if ($('#enlistment_status').val() === 'اعفاء مؤقت' || $('#enlistment_status').val() === 'اعفاء نهائي') {
                    $('#decision_number').attr('disabled', false).parent().show();
                    $('#decision_date').attr('disabled', false).parent().show();
                } else {
                    $('#decision_number').attr('disabled', true).parent().hide();
                    $('#decision_date').attr('disabled', true).parent().hide();
                }
                if ($('#enlistment_status').val() === 'اعفاء مؤقت') {
                    $('#expiry_date').attr('disabled', false).parent().show();
                } else {
                    $('#expiry_date').attr('disabled', true).parent().hide();
                }
                if ($('#enlistment_status').val() === 'له حق التأجيل') {
                    $('#position_of_recruitment').val(' لسن 28');
                } else {
                    $('#position_of_recruitment').val('');
                }
            } else {
                $('#recruitment_area').attr('disabled', true).parent().hide();
                $('input[id*="military_number_"]').attr('disabled', true).parent().parent().parent().hide();
                $('#recruitment_notes').attr('disabled', true).parent().hide();
                $('#enlistment_status').attr('disabled', true).parent().hide();
                $('#position_of_recruitment').attr('disabled', true).parent().hide();
                $('#decision_number').attr('disabled', true).parent().hide();
                $('#decision_date').attr('disabled', true).parent().hide();
                $('#expiry_date').attr('disabled', true).parent().hide();
            }
        });

        $('#enlistment_status').on('input', function () {
            if ($(this).val() === 'اعفاء مؤقت' || $(this).val() === 'اعفاء نهائي') {
                $('#decision_number').attr('disabled', false).parent().show();
                $('#decision_date').attr('disabled', false).parent().show();
            } else {
                $('#decision_number').attr('disabled', true).parent().hide();
                $('#decision_date').attr('disabled', true).parent().hide();
            }
            if ($(this).val() === 'اعفاء مؤقت') {
                $('#expiry_date').attr('disabled', false).parent().show();
            } else {
                $('#expiry_date').attr('disabled', true).parent().hide();
            }
            if ($('#enlistment_status').val() === 'له حق التأجيل') {
                $('#position_of_recruitment').val(' لسن 28');
            } else {
                $('#position_of_recruitment').val('');
            }
        });

        $('#certificate_obtained').on('input', function () {
            if ($(this).val() === "شهاده معادله") {
                $('#other_certificate_obtained').show().attr('disabled', false);
                $('#certificate_seating_number').attr('required', false);
            } else {
                $('#other_certificate_obtained').hide().attr('disabled', true);
                $('#certificate_seating_number').attr('required', true);
            }
            if (degrees[$(this).val()] !== undefined) {
                $('#certificate_degree_total').val(degrees[$(this).val()]).prop('readonly', true);
                $('#certificate_degree').prop('min', degrees[$(this).val()] / 2.0).prop('max', degrees[$(this).val()]);
            } else {
                $('#certificate_degree_total').val('').prop('readonly', false);
                $('#certificate_degree').prop('min', 0).removeAttr('max');
            }
            if (en_cer.includes($(this).val())) {
                $('#english_degree').attr('disabled', false).parent().show();
            } else {
                $('#english_degree').attr('disabled', true).parent().hide();
            }
        });

        $('#student_classification').on('input', function () {
            if (cases.includes($(this).val())) {
                $('#classification_notes').attr('disabled', false).parent().show();
            } else {
                $('#classification_notes').attr('disabled', true).parent().hide();
            }
        });

        $('#apply_classification').on('input', function () {
            if ($(this).val() === "مرشح") {
                $('#apply_classification_notes').attr('disabled', true).parent().hide();
            } else {
                $('#apply_classification_notes').attr('disabled', false).parent().show();
            }
        });

        $('#certificate_degree').on('input', function () {
            let total = $('#certificate_degree_total').val();
            if (total !== '') {
                $('#certificate_degree_percentage').val((($(this).val() / total) * 100).toFixed(1));
            }
        });

        $('#certificate_degree_total').on('input', function () {
            let total = $('#certificate_degree').prop('min', $(this).val() / 2.0).prop('max', $(this).val()).val();
            if (total !== '') {
                $('#certificate_degree_percentage').val(((total / $(this).val()) * 100).toFixed(1));
            }
        });

        $('#specialization').on('input', function () {
            let me = $(this);
            me.attr('disabled', true);
            $.ajax({
                type: 'get',
                url: '{{route('student.username')}}',
                data: {
                    'specialization': $('#specialization').val(),
                },
                success: function (data) {
                    $('#username').val(data);
                },
                error: function (data) {
                    alert(data['responseText']);
                    $('#username').val('');
                },
                complete: function () {
                    me.attr('disabled', false);
                }
            });
        });

        function generatePassword() {
            let num = '0123456789';
            let upper_char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let password = '';
            for (let i = 0; i < 4; i++) {
                password += num[Math.floor(Math.random() * num.length)] + upper_char[Math.floor(Math.random() * upper_char.length)];
            }
            $('#password').val(password.split('').sort(function () {
                return 0.5 - Math.random()
            }).join(''));
        }

        function scanToJpg() {
            scanner.scan(displayImagesOnPage,
                {
                    "output_settings": [
                        {
                            "type": "return-base64",
                            "format": "jpg"
                        }
                    ]
                }
            );
            $('#photo-string').attr('disabled', false);
            $('#photo').attr('disabled', true);
            $('#photo_image').removeClass('d-none').addClass('d-block');
        }

        function displayImagesOnPage(successful, mesg, response) {
            if (!successful) { // On error
                console.error('Failed: ' + mesg);
                return;
            }

            if (successful && mesg != null && mesg.toLowerCase().indexOf('user cancel') >= 0) { // User cancelled.
                console.info('User cancelled');
                return;
            }

            var scannedImages = scanner.getScannedImages(response, true, false); // returns an array of ScannedImage
            for (var i = 0; (scannedImages instanceof Array) && i < scannedImages.length; i++) {
                var scannedImage = scannedImages[i];
                processScannedImage(scannedImage);
            }
        }

        var imagesScanned = [];

        function processScannedImage(scannedImage) {
            imagesScanned.push(scannedImage);
            $('#photo_image').attr('src', scannedImage.src).show();
            $('#photo-string').val(scannedImage.src);
        }

        $('#apply_classification').change(function () {
            var selectedClassification = $(this).val();

            if (selectedClassification === 'مرشح') {
                $('#department').val('1');
                $('#department').prop('readonly', true);
            } else {
                $('#department').prop('readonly', false);
            }
        });

        $('#specialization').change(function () {
            var selectedClassification = $('#apply_classification').val();
            var selectedSpecialization = $(this).val();
            if (selectedClassification === 'محول') {
                if (selectedSpecialization === 'ترميم الاثار و المقتنيات الفنية') {
                    $('#department option[value="2"]').show();
                    $('#department option[value="3"]').show();
                    $('#department option[value="4"]').show();
                    $('#department option[value="5"]').hide();
                    $('#department option[value="6"]').hide();
                    $('#department option[value="7"]').hide();
                    $('#department option[value="1"]').hide();
                }
                else if (selectedSpecialization === 'سياحة') {
                    $('#department option[value="2"]').hide();
                    $('#department option[value="3"]').hide();
                    $('#department option[value="4"]').hide();
                    $('#department option[value="1"]').hide();
                    $('#department option[value="5"]').show();
                    $('#department option[value="6"]').show();
                    $('#department option[value="7"]').show();
                }
            }
        });



    </script>
@endsection
