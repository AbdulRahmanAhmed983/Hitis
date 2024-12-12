@extends('layout.layout')
@section('title', 'تعديل بيانات الطالب '.$student['username'])
@section('styles')
@endsection
@section('content')
    <form method="post" action="{{route('student.update.data',['username'=> $student['username']])}}"
          enctype="multipart/form-data" autocomplete="off">
        @csrf
        @method('put')
        @if(session()->has('success'))
            <div class="alert alert-success mt-3 col-12 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
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
                                   id="name" placeholder="اسم الطالب رباعي" value="{{$student['name']}}">
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
                            <label for="photo">صورة الطالب</label>
                            <div class="row col-12 justify-content-between">
                                <input type="file" name="photo" id="photo" accept="image/*"
                                       class="form-control-file col-8 btn btn-dark">
                                <button type="button" class="btn btn-dark col-3" onclick="scanToJpg();">Scan</button>
                            </div>
                            <img id="photo_image" src="" class="mx-auto d-none" style="max-width: 70%" alt="">
                            <img id="old_photo" src="{{$student['photo']}}" alt="student photo"
                                 class="mx-auto d-block" style="max-width: 70%">
                            <input type="hidden" name="photo" id="photo-string" class="form-control-file" required
                                   disabled="disabled">
                            @if($errors->has('photo'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('photo')}}
                                    </h6>
                                </div>
                            @endif
                            {{--                            @if($errors->any() and !$errors->has('photo'))--}}
                            {{--                                <div class="alert alert-danger mt-3 text-center">--}}
                            {{--                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">--}}
                            {{--                                        &times;--}}
                            {{--                                    </button>--}}
                            {{--                                    <h6><i class="icon fas fa-ban"></i> لم يتم تحميل الصوره</h6>--}}
                            {{--                                </div>--}}
                            {{--                            @endif--}}
                        </div>
                        <div class="form-group">
                            <label for="religion">الديانة</label>
                            <select class="custom-select {{$errors->has('religion')?'is-invalid':''}}"
                                    name="religion" id="religion">
                                <option value="" hidden></option>
                                @foreach($data['religion'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['religion'] == $d)
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
                        </div>
                        <div class="form-group">
                            <label for="birth_date">تاريخ الميلاد</label>
                            <input type="date" value="{{$student['birth_date']}}" name="birth_date" id="birth_date"
                                   class="form-control {{$errors->has('birth_date')?'is-invalid':''}}">
                            @if($errors->has('birth_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('birth_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="certificate_obtained">الشهادة الحاصل عليها</label>
                            <select class="custom-select {{$errors->has('certificate_obtained')?'is-invalid':''}}"
                                    name="certificate_obtained" id="certificate_obtained">
                                <option value="" hidden></option>
                                @foreach($data['certificate_obtained'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['certificate_obtained'] == $d)
                                        {{ 'selected' }}
                                        @endif>{{$d}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('certificate_obtained'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('certificate_obtained')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="text" name="other_certificate_obtained" id="other_certificate_obtained"
                                   class="form-control {{$errors->has('other_certificate_obtained')?'is-invalid':''}}"
                                   value="{{$student['other_certificate_obtained']}}"
                                   placeholder="برجاء ادخال اسم الشهادة" required>
                            @if($errors->has('other_certificate_obtained'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6>
                                        <i class="icon fas fa-ban"></i> {{$errors->first('other_certificate_obtained')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="certificate_obtained_date">تاريخ الحصول علي الشهادة</label>
                            <input type="number" min="2000" step="1"
                                   class="form-control {{$errors->has('certificate_obtained_date')?'is-invalid':''}}"
                                   name="certificate_obtained_date" value="{{$student['certificate_obtained_date']}}"
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
                        <!--<div class="form-group">-->
                        <!--    <label for="certificate_seating_number">رقم جلوس الشهادة</label>-->
                        <!--    <input type="text" name="certificate_seating_number" id="certificate_seating_number"-->
                        <!--           class="form-control {{$errors->has('certificate_seating_number')?'is-invalid':''}}"-->
                        <!--           value="{{$student['certificate_seating_number']}}">-->
                        <!--    @if($errors->has('certificate_seating_number'))-->
                        <!--        <div class="alert alert-danger mt-3 text-center">-->
                        <!--            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
                        <!--                &times;-->
                        <!--            </button>-->
                        <!--            <h6>-->
                        <!--                <i class="icon fas fa-ban"></i> {{$errors->first('certificate_seating_number')}}-->
                        <!--            </h6>-->
                        <!--        </div>-->
                        <!--    @endif-->
                        <!--</div>-->
                        <div class="form-group">
                            <label for="certificate_degree">مجموع الطالب</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" step="0.1" required min="0"
                                           class="form-control {{$errors->has('certificate_degree')?'is-invalid':''}}"
                                           name="certificate_degree" value="{{$student['certificate_degree']}}"
                                           id="certificate_degree">
                                </div>
                                <span style="font-size: 30px;" class="mx-1">&#8260;</span>
                                <div class="col">
                                    <input type="number" step="1" required min="0"
                                           class="form-control" id="certificate_degree_total"
                                           name="certificate_degree_total"
                                           value="{{$student['certificate_degree_total']}}">
                                </div>
                                <span class="font-weight-bold align-self-center">النسبه المئويه</span>
                                <div class="col">
                                    <input type="number" min="50" max="100" readonly required
                                           class="form-control" id="certificate_degree_percentage"
                                           name="certificate_degree_percentage"
                                           value="{{$student['certificate_degree_percentage']}}">
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
                        </div>
                        <!--<div class="form-group">-->
                        <!--    <label for="english_degree">درجة اللغه الإنجليزية</label>-->
                        <!--    <input type="number" step="0.1" min="25" max="50"-->
                        <!--           class="form-control {{$errors->has('english_degree')?'is-invalid':''}}"-->
                        <!--           name="english_degree" value="{{$student['english_degree']}}" id="english_degree">-->
                        <!--    @if($errors->has('english_degree'))-->
                        <!--        <div class="alert alert-danger mt-3 text-center">-->
                        <!--            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
                        <!--                &times;-->
                        <!--            </button>-->
                        <!--            <h6>-->
                        <!--                <i class="icon fas fa-ban"></i> {{$errors->first('english_degree')}}-->
                        <!--            </h6>-->
                        <!--        </div>-->
                        <!--    @endif-->
                        <!--</div>-->
                        <div class="form-group">
                            <label for="apply_classification">تصنيف التقديم</label>
                            <select class="custom-select {{$errors->has('apply_classification')?'is-invalid':''}}"
                                    required name="apply_classification" id="apply_classification">
                                @foreach($data['apply_classification'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['apply_classification'] == $d)
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
                        <div class="form-group">
                            <label for="apply_classification_notes">ملاحظات تصنيف التقديم</label>
                            <input type="text"
                                   class="form-control {{$errors->has('apply_classification_notes')?'is-invalid':''}}"
                                   name="apply_classification_notes" value="{{$student['apply_classification_notes']}}"
                                   id="apply_classification_notes">
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
                        </div>
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" value="{{$student['email']}}" name="email" id="email"
                                   class="form-control {{$errors->has('email')?'is-invalid':''}}">
                            @if($errors->has('email'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('email')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="mobile">موبايل الطالب</label>
                            <input type="text" value="{{$student['mobile']}}" name="mobile" id="mobile"
                                   class="form-control {{$errors->has('mobile')?'is-invalid':''}}">
                            @if($errors->has('mobile'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('mobile')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="landline_phone">التليفون الارضي</label>
                            <input type="text" value="{{$student['landline_phone']}}" name="landline_phone"
                                   id="landline_phone"
                                   class="form-control {{$errors->has('landline_phone')?'is-invalid':''}}">
                            @if($errors->has('landline_phone'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('landline_phone')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="father_profession">مهنة ولي الامر</label>
                            <input type="text" value="{{$student['father_profession']}}" name="father_profession"
                                   id="father_profession"
                                   class="form-control {{$errors->has('father_profession')?'is-invalid':''}}">
                            @if($errors->has('father_profession'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('father_profession')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="parents_phone1">تليفون ولي الامر الاول</label>
                            <input type="text" value="{{$student['parents_phone1']}}" name="parents_phone1"
                                   id="parents_phone1"
                                   class="form-control {{$errors->has('parents_phone1')?'is-invalid':''}}">
                            @if($errors->has('parents_phone1'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('parents_phone1')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="parents_phone2">تليفون ولي الامر الثاني</label>
                            <input type="text" value="{{$student['parents_phone2']}}" name="parents_phone2"
                                   id="parents_phone2"
                                   class="form-control {{$errors->has('parents_phone2')?'is-invalid':''}}">
                            @if($errors->has('parents_phone2'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('parents_phone2')}}
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
                                      class="form-control {{$errors->has('notes')?'is-invalid':''}}">{{$student['notes']}}</textarea>
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
                        <div class="form-group">
                            <label for="birth_country">دولة الميلاد</label>
                            <select class="custom-select {{$errors->has('birth_country')?'is-invalid':''}}"
                                    name="birth_country" id="birth_country">
                                <option value="" hidden></option>
                                @foreach($data['birth_country'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['birth_country'] == $d)
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
                        </div>
                        <div class="form-group">
                            <input type="text" value="{{$student['other_birth_country']}}" required
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
                        </div>
                        <div class="form-group">
                            <label for="birth_province">محافظة الميلاد</label>
                            <select class="custom-select {{$errors->has('birth_province')?'is-invalid':''}}"
                                    name="birth_province" id="birth_province">
                                <option value="" hidden></option>
                                @foreach($data['birth_province'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['birth_province'] == $d)
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
                        </div>
                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <input type="text" value="{{$student['address']}}"
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
                        </div>
                        <div class="form-group">
                            <label for="nationality">الجنسية</label>
                            <select class="custom-select {{$errors->has('nationality')?'is-invalid':''}}"
                                    name="nationality" id="nationality">
                                <option value="" hidden></option>
                                @foreach($data['nationality'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['nationality'] == $d)
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
                                   value="{{$student['other_nationality']}}" placeholder="الجنسية">
                            @if($errors->has('other_nationality'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('other_nationality')}}</h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="immigrant_student"
                                       id="immigrant_student" value="وافد"
                                @if($student['immigrant_student'] == "وافد")
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
                        </div>
                         <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="military_education"
                                       id="military_education" value="معفي"
                                @if($student['military_education'] == "معفي")
                                    {{ 'checked' }}
                                    @endif>
                                <label for="military_education" class="custom-control-label">طالب معافي من التربية العسكرية</label>
                            </div>
                            @if($errors->has('military_education'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('military_education')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="national_id">الرقم القومي</label>
                            <input type="text" value="{{$student['national_id']}}" required
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
                        <div class="form-group">
                            <label for="issuer_national_number">جهة الإصدار الرقم القومي</label>
                            <input type="text" value="{{$student['issuer_national_number']}}" required
                                   class="form-control {{$errors->has('issuer_national_number')?'is-invalid':''}}"
                                   name="issuer_national_number" id="issuer_national_number">
                            @if($errors->has('issuer_national_number'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('issuer_national_number')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="passport_id">رقم الباسبور</label>
                            <input type="text" value="{{$student['passport_id']}}" required
                                   class="form-control {{$errors->has('passport_id')?'is-invalid':''}}"
                                   name="passport_id" id="passport_id">
                            @if($errors->has('passport_id'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('passport_id')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="gender">الجنس</label>
                            <div class="d-flex">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio1" name="gender"
                                           value="ذكر" @if($student['gender'] == "ذكر") checked @endif>
                                    <label for="customRadio1" class="custom-control-label">ذكر</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio2" name="gender"
                                           value="أنثى" @if($student['gender'] == "أنثى") checked @endif>
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

                        </div>
                        <div class="form-group">
                            <label for="recruitment_area">منطقة التجنيد</label>
                            <input type="text" value="{{$student['recruitment_area']}}"
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
                        </div>
                        <div class="form-group">
                            <label for="military_number">الرقم العسكري</label>
                            <div class="row">
                                <div class="col">
                                    <input type="text" value="{{$student['military_number_1']}}"
                                           class="form-control {{$errors->has('military_number_1')?'is-invalid':''}}"
                                           name="military_number[0]" id="military_number_1">
                                    @if($errors->has('military_number_1'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('military_number_1')}}
                                            </h6>
                                        </div>
                                    @endif
                                </div>
                                <span style="font-size: 30px;" class="mx-1">&#8260;</span>
                                <div class="col">
                                    <input type="text" value="{{$student['military_number_2']}}"
                                           class="form-control {{$errors->has('military_number_2')?'is-invalid':''}}"
                                           name="military_number[1]" id="military_number_2">
                                    @if($errors->has('military_number_2'))
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$errors->first('military_number_2')}}
                                            </h6>
                                        </div>
                                    @endif
                                </div>
                                <span style="font-size: 30px;" class="mx-1">&#8260;</span>
                                <div class="col">
                                    <input type="text" value="{{$student['military_number_3']}}"
                                           class="form-control {{$errors->has('military_number_3')?'is-invalid':''}}"
                                           name="military_number[2]" id="military_number_3">
                                </div>
                                @if($errors->has('military_number'))
                                    <div class="alert alert-danger mx-auto col-11 mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$errors->first('military_number')}}
                                        </h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="enlistment_status">حالة التجنيد</label>
                            <select class="custom-select {{$errors->has('enlistment_status')?'is-invalid':''}}"
                                    name="enlistment_status" required id="enlistment_status">
                                @foreach($data['enlistment_status'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['enlistment_status'] == $d)
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
                        </div>
                        <div class="form-group">
                            <label for="position_of_recruitment">موقف الطالب من التجنيد</label>
                            <input type="text" value="{{$student['position_of_recruitment']}}" required
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
                            <input type="text" value="{{$student['decision_number']}}" required
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
                        <div class="form-group">
                            <label for="decision_date">تاريخ القرار</label>
                            <input type="date" value="{{$student['decision_date']}}" name="decision_date"
                                   id="decision_date"
                                   class="form-control {{$errors->has('decision_date')?'is-invalid':''}}" required>
                            @if($errors->has('decision_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('decision_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="expiry_date">تاريخ الانتهاء الاعفاء</label>
                            <input type="date" value="{{$student['expiry_date']}}" name="expiry_date" id="expiry_date"
                                   class="form-control {{$errors->has('expiry_date')?'is-invalid':''}}" required>
                            @if($errors->has('expiry_date'))
                                <div class="alert alert-danger mt-3 text-center">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h6><i class="icon fas fa-ban"></i> {{$errors->first('expiry_date')}}
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="recruitment_notes">ملاحظات التجنيد</label>
                            <input type="text" value="{{$student['recruitment_notes']}}"
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
                        </div>
                        <div class="form-group">
                            <label for="student_classification">تصنيف الطلاب</label>
                            <select class="custom-select {{$errors->has('student_classification')?'is-invalid':''}}"
                                    name="student_classification" id="student_classification">
                                <option value="" hidden></option>
                                @foreach($data['student_classification'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['student_classification'] == $d)
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
                            <input type="text" value="{{$student['classification_notes']}}"
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
                            <select class="custom-select {{$errors->has('specialization')?'is-invalid':''}}"
                                    name="specialization" id="specialization">
                                <option value="" hidden></option>
                                @foreach($data['specialization'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['specialization'] == $d)
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
                            <label for="specialization">الشعبة</label>
                            <select class="custom-select {{$errors->has('departments')?'is-invalid':''}}"
                                    name="departments_id" id="departments">
                                <option value="" hidden></option>
                                @foreach($departments as $d)
                                    <option
                                        value="{{$d->id}}" @if($student['departments_id'] == $d->id)
                                        {{ 'selected' }}
                                        @endif>{{$d->name}}</option>
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
                            <label for="study_group">الفرقة الدراسية</label>
                            <select class="custom-select {{$errors->has('study_group')?'is-invalid':''}}"
                                    name="study_group" id="study_group">
                                <option value="" hidden></option>
                                @foreach($data['study_group'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['study_group'] == $d)
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
                            <select class="custom-select {{$errors->has('studying_status')?'is-invalid':''}}"
                                    name="studying_status" id="studying_status">
                                <option value="" hidden></option>
                                @foreach($data['studying_status'] as $d)
                                    <option
                                        value="{{$d}}" @if($student['studying_status'] == $d)
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
                            <label for="username">إسم المستخدم</label>
                            <input type="text" value="{{$student['username']}}" readonly
                                   class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                   name="username" id="username">
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
                            <input type="text" value="{{$student['password']}}" id="password"
                                   class="form-control {{$errors->has('password')?'is-invalid':''}}" name="password">
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
                        <button type="submit" class="btn btn-primary col-6">تعديل</button>
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
            $('#certificate_seating_number').attr('required', true);
        }

        if (en_cer.includes($('#certificate_obtained').val())) {
            $('#english_degree').attr('disabled', false).parent().show();
        } else {
            $('#english_degree').attr('disabled', true).parent().hide();
        }

        if (degrees[$('#certificate_obtained').val()] !== undefined) {
            $('#certificate_degree_total').prop('readonly', true).val(degrees[$('#certificate_obtained').val()]);
            $('#certificate_degree').prop('min', degrees[$('#certificate_obtained').val()] / 2.0)
                .prop('max', degrees[$('#certificate_obtained').val()]);
        } else {
            $('#certificate_degree_total').prop('readonly', false);
            $('#certificate_degree').prop('min', 0).removeAttr('max');
        }

        if (cases.includes($('#student_classification').val())) {
            $('#classification_notes').attr('disabled', false).parent().show();
        } else {
            $('#classification_notes').attr('disabled', true).parent().hide();
        }

        if ($('#apply_classification').val() === 'مرشح') {
            $('#apply_classification_notes').attr('disabled', true).parent().hide();
        } else {
            $('#apply_classification_notes').attr('disabled', false).parent().show();
        }

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

        $('#apply_classification').on('input', function () {
            if ($(this).val() === "مرشح") {
                $('#apply_classification_notes').attr('disabled', true).parent().hide();
            } else {
                $('#apply_classification_notes').attr('disabled', false).parent().show();
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
        });

        $('#student_classification').on('input', function () {
            const cases = ["محول", "عذر", "وقف قيد"];
            if (cases.includes($(this).val())) {
                $('#classification_notes').attr('disabled', false).parent().show();
            } else {
                $('#classification_notes').attr('disabled', true).parent().hide();
            }
        });

        $('#certificate_obtained').on('input', function () {
            if ($(this).val() === "شهاده معادله") {
                $('#other_certificate_obtained').show().attr('disabled', false);
            } else {
                $('#other_certificate_obtained').hide().attr('disabled', true);
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
            $('#old_photo').removeClass('d-block').addClass('d-none');
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

        function dataURLtoFile(dataurl, filename) {

            var arr = dataurl.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]),
                n = bstr.length,
                u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new File([u8arr], filename, {type: mime});
        }
    </script>
@endsection
