@extends('layout.layout')
@section('title', 'إصدار حافظة محفظة مصاريف ادارية')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @if(session()->has('error'))
                <div class="alert alert-danger mt-3 text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                </div>
            @endif
            @if(session()->has('success'))
                <div class="alert alert-success mt-3 text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                </div>
            @endif
            <div class="card card-primary">
                <div class="card-body">
                    <form method="get" action="{{route('create.wallet.administrative')}}">
                        <div class="row">
                            <div class="forms-group col-md-6 my-2 my-md-0">
                                <input type="search"
                                       class="form-control {{$errors->has('username')?'is-invalid':''}}"
                                       name="username" id="username" required value="{{old('username')}}"
                                       placeholder="كود او اسم الطالب" list="students">
                                <datalist id="students"></datalist>
                            </div>
                            <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                <button type="submit" class="btn btn-primary col-12"><i class="fas fa-search"></i>
                                    بحث
                                </button>
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
                    </form>
                </div>
            </div>
            @if(isset($student))
                <div class="card">
                    <div class="card-body">
                        <div class="h4 text-center">
                            <div>الطالب {{$student['name']}}</div>
                            <div>الكود {{$student['username']}}</div>
                        </div>
                        <div>
                            @if(!is_null($wallet_administrative_expenses ))
                                    <table class="table table-bordered text-center">
                                        <tr>
                                            <th>السنة</th>
                                            <th>التاريخ</th>
                                            <th>المبلغ</th>
                                            <th>الحافظة</th>
                                            <th>نوع الحافظة</th>
                                            <th>الحالة</th>
                                        </tr>
                                            <tr>
                                                <td>{{$wallet_administrative_expenses->year}}</td>
                                                <td>{{$wallet_administrative_expenses->date}}</td>
                                                <td>{{$wallet_administrative_expenses->amount}}</td>
                                                <td>{{$wallet_administrative_expenses->ticket_id}}</td>
                                                <td>مصاريف ادارية</td>
                                               <td> @if($wallet_administrative_expenses->used === 0) غير مدفوع @else  مدفوع @endif </td>
                                            </tr>
                                            @if(!is_null($payments_extra_fees))
                                                <tr>
                                                    <td>{{$payments_extra_fees->year}}</td>
                                                    <td>{{$payments_extra_fees->date}}</td>
                                                    <td>{{$payments_extra_fees->amount}}</td>
                                                    <td>{{$payments_extra_fees->ticket_id}}</td>
                                                    <td> رسوم تأخير/ خدمات تعليمية</td>
                                                <td> @if($payments_extra_fees->used === 0) غير مدفوع @else  مدفوع @endif </td>
                                                </tr>
                                            @endif

                                    </table>
                            @else
                                <div class="alert alert-warning text-center">
                                    <h6>الطالب ليس لديه محفظة مصاريف </h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($student) and isset($ticket_id))
                <form action="{{route('store.wallet.administrative')}}" method="post">
                    @csrf
                    <div class="card card-primary collapsed-card">
                        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                            <h2 class="card-title float-left">إصدار حافظة محفظة مصاريف ادارية</h2>
                            <div class="card-tools  float-right">
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-around">
                                <div class="form-group col-md-6">
                                    <label for="student_code"> اسم الطالب</label>
                                   <input type="text" name="name" value="{{$student['name']}}" class="form-control" readonly>
                                   @error('name')
                                   <div class="alert alert-danger mt-3 text-center">
                                       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                           &times;
                                       </button>
                                       <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                   </div>
                                   @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="student_code"> كود الطالب</label>
                                   <input type="text" name="student_code" value="{{$student['username']}}" class="form-control" readonly>
                                   @error('name')
                                   <div class="alert alert-danger mt-3 text-center">
                                       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                           &times;
                                       </button>
                                       <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                   </div>
                                   @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="study_group"> الفرقة الدراسية</label>
                                   <input type="text" name="study_group" value="{{$student['study_group']}}" class="form-control" readonly>
                                   @error('study_group')
                                   <div class="col-12">
                                       <div class="alert alert-danger mt-3 text-center">
                                           <button type="button" class="close" data-dismiss="alert"
                                                   aria-hidden="true">
                                               &times;
                                           </button>
                                           <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                       </div>
                                   </div>
                                   @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="study_group">  التخصص</label>
                                   <input type="text" name="specialization" value="{{$student['specialization']}}" class="form-control" readonly>
                                   @error('specialization')
                                   <div class="col-12">
                                       <div class="alert alert-danger mt-3 text-center">
                                           <button type="button" class="close" data-dismiss="alert"
                                                   aria-hidden="true">
                                               &times;
                                           </button>
                                           <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                       </div>
                                   </div>
                                   @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="year">رقم الحافظة</label>
                                    <input type="text" name="ticket_id" class="form-control" value="{{ $ticket_id }}" readonly>
                                    @error('ticket_id')
                                    <div class="alert alert-danger mx-auto col-11 mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="study_group"> تاريخ الحافظة</label>
                                   <input type="date" name="date" value="{{old('date',date('Y-m-d'))}}" class="form-control" readonly>
                                   @error('date')
                                        <div class="alert alert-danger col-12 mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="year">  السنة الدراسية</label>
                                      <select class="custom-select" required name="year" id="year">
                                            <option value="{{$year}}" >{{$year}}</option>
                                            <option value="{{$next_year}}" >{{$next_year}}</option>
                                        </select>
                                        @error('year')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                            </div>
                         <div class="row justify-content-around">
                            <div class="form-group col-md-3">
                                <label for="study_group"> التأمين</label>
                               <input type="number" name="insurance" value="{{ $payments_administrative_expenses[0] }}" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-3">
                                    <label for="study_group"> سحب الملف</label>
                                   <input type="number" name="profile_expenses" class="form-control" value="{{ $payments_administrative_expenses[1] }}" readonly>
                            </div>
                                <div class="form-group col-md-3">
                                    <label for="year"> رسوم قيد</label>
                                    <input type="number" name="registration_fees" class="form-control" value="{{ $payments_administrative_expenses[2] }}" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="year">الكارنية والايميل</label>
                                    <input type="number" name="card_and_email" class="form-control" value="{{ $payments_administrative_expenses[3] }}" readonly>
                                </div>
                            </div>
                            <div class="row justify-content-around">
                                <div class="form-group col-md-3">
                                    <label for="study_group">تجديد الكارنية و الايميل</label>
                                   <input type="number" name="renew_card_and_email" class="form-control" value="{{ $payments_administrative_expenses[4] }}" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                        <label for="study_group"> العسكرية</label>
                                       <input type="number" name="military_expenses" class="form-control" value="{{ $payments_administrative_expenses[5] }}" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="year"> المبلغ الاجمالي</label>
                                    <input type="number" required placeholder="0" min="0" step="0.25" max="20000"
                                        value="{{ $amount}}" class="form-control" name="amount" id="amount" readonly>
                                        @error('amount')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>

                            </div>


                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
                        </div>
                    </div>
                </form>
            @elseif(isset($student))
                <div class="card card-primary collapsed-card">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">إصدار حافظة محفظة قديمة</h2>
                        <div class="card-tools  float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    {{-- delete wallet --}}
                    {{-- <div class="card-body">
                        <form action="{{route('delete.wallet.ticket',['student_code'=>$student['username'],
                            'ticket_id'=>$student['ticket_id']])}}" id="delete-wallet" method="post">
                            @csrf
                            @method('delete')
                        </form>
                        <form action="{{route('store.wallet.ticket')}}" id="store-wallet" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">إسم الطالب</label>
                                        <input type="text" value="{{$student['name']}}" readonly required
                                               class="form-control" name="name" id="name">
                                        @error('name')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="student_code">كود الطالب</label>
                                        <input type="text" value="{{$student['username']}}" readonly required
                                               class="form-control" name="student_code" id="student_code">
                                        @error('student_code')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="study_group">الفرقة الدراسية</label>
                                                <input type="text" value="{{$student['study_group']}}" readonly required
                                                       class="form-control" name="study_group" id="study_group">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="specialization">التخصص</label>
                                                <input type="text" value="{{$student['specialization']}}" readonly
                                                       required class="form-control" name="specialization"
                                                       id="specialization">
                                            </div>
                                        </div>
                                        @error('study_group')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                        @error('specialization')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ticket_id">رقم الحافظة</label>
                                                <input type="text" value="{{$student['ticket_id']}}" readonly required
                                                       class="form-control" name="ticket_id" id="ticket_id">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="date">تاريخ الحافظة</label>
                                                <input type="date" required value="{{$student['date']}}" readonly
                                                       class="form-control" name="date" id="date">
                                            </div>
                                        </div>
                                        @error('ticket_id')
                                        <div class="alert alert-danger mx-auto col-11 mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                        @error('date')
                                        <div class="alert alert-danger col-12 mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="semester">الفصل الدراسي</label>
                                        <input type="text" required readonly
                                               value="{{$student['semester']}}" class="form-control"
                                               name="semester" id="semester">
                                        @error('semester')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="amount">مبلغ</label>
                                        <input type="number" required placeholder="0" min="0"
                                               step="0.25" max="20000" value="{{$student['amount']}}" readonly
                                               class="form-control" name="amount" id="amount">
                                        @error('amount')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> --}}
                    {{-- <div class="card-footer">
                        <button type="submit" form="store-wallet" class="btn btn-success"><i class="fas fa-print"></i>
                            طباعة
                        </button>
                        <button type="submit" form="delete-wallet" class="btn btn-danger"><i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </div> --}}
                </div>
            @endif
        </div>
    </div>
     <div class="row">
        <div class="col-12">

            @if(isset($student) and isset($ticket_id))
                        <form action="{{route('store.extra.fees')}}" method="post">
                            @csrf
                            <div class="card card-primary collapsed-card">
                                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                                    <h2 class="card-title float-left">إصدار حافظة محفظة الخدمات التعليمية و تأخير التسجيل</h2>
                                    <div class="card-tools  float-right">
                                        <button type="button" class="btn btn-tool">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content-around">
                                        <div class="form-group col-md-3">
                                            <label for="student_code"> اسم الطالب</label>
                                        <input type="text" name="name" value="{{$student['name']}}" class="form-control" readonly>
                                        @error('name')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="student_code"> كود الطالب</label>
                                        <input type="text" name="student_code" value="{{$student['username']}}" class="form-control" readonly>
                                        @error('name')
                                        <div class="alert alert-danger mt-3 text-center">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                &times;
                                            </button>
                                            <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                        </div>
                                        @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="study_group"> الفرقة الدراسية</label>
                                        <input type="text" name="study_group" value="{{$student['study_group']}}" class="form-control" readonly>
                                        @error('study_group')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="study_group">الشعبة</label>
                                        <input type="text" name="departments_id" value="{{$departments}}" class="form-control" readonly>
                                        @error('departments_id')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="study_group">  التخصص</label>
                                        <input type="text" name="specialization" value="{{$student['specialization']}}" class="form-control" readonly>
                                        @error('specialization')
                                        <div class="col-12">
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                        </div>
                                        @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="year">رقم الحافظة</label>
                                            <input type="text" name="ticket_id" class="form-control" value="{{ $ticket_id }}" readonly>
                                            @error('ticket_id')
                                            <div class="alert alert-danger mx-auto col-11 mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-hidden="true">
                                                    &times;
                                                </button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="study_group"> تاريخ الحافظة</label>
                                        <input type="date" name="date" value="{{old('date',date('Y-m-d'))}}" class="form-control" readonly>
                                        @error('date')
                                                <div class="alert alert-danger col-12 mt-3 text-center">
                                                    <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">
                                                        &times;
                                                    </button>
                                                    <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                                </div>
                                                @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="year">  السنة الدراسية</label>
                                            <input type="text" required
                                                value="{{ $year}}" class="form-control" name="year" id="year" readonly>
                                                @error('year')
                                                <div class="alert alert-danger mt-3 text-center">
                                                    <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">
                                                        &times;
                                                    </button>
                                                    <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                                </div>
                                                @enderror
                                            </div>
                                    </div>
                                    <div class="row justify-content-around">
                                        <div class="form-group col-md-4">
                                            <label for="type">نوع الخدمة</label>
                                            <select class="form-control" name="type" id="type_fees" required>
                                                <option hidden> اختر نوع الرسوم</option>
                                                <option value="الخدمات التعليمية">الخدمات التعليمية</option>
                                                <option value="رسوم تأخير التسجيل">رسوم تأخير التسجيل</option>
                                            </select>
                                            @error('type')
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="amount">المبلغ الاجمالي</label>
                                            <input type="number" required placeholder="0" min="0" step="0.25" max="20000"
                                                   value="" class="form-control" name="amount" id="amount_fees" readonly>
                                            @error('amount')
                                            <div class="alert alert-danger mt-3 text-center">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
                                </div>
                            </div>
                        </form>

                </div>
        </div>

            @elseif(isset($student))

            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.is-invalid').on('input', function () {
            $(this).removeClass('is-invalid');
        });
        let done = true;
        $('#username').on('input', function (e) {
            let me = $(this);
            if (me.val().length >= 3 && done) {
                done = false;
                $.ajax({
                    type: 'get',
                    url: '{{route('student.datalist')}}',
                    data: {
                        'search': me.val(),
                    },
                    success: function (data) {
                        $('#students').html('');
                        for (let i = 0; i < data.length; i++) {
                            $('#students').append('<option value="' + data[i]['username'] + '">'
                                + data[i]['name'] + '</option>');
                        }
                    },
                    error: function (data) {
                        $('#students').html('').parent().parent().parent()
                            .append('<div class="alert alert-danger mt-3 text-center">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                '<h6><i class="icon fas fa-ban"></i> ' + data['responseText'] + '</h6></div>');
                    },
                    complete: function () {
                        done = true;
                    }
                });
            } else {
                $('#students').html('');
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#type_fees').change(function() {
            var selectedType = $(this).val();
            alert(selectedType);
            $.ajax({
                url: '/get-amount',
                method: 'GET',
                data: { type_fees: selectedType },
                success: function(response) {
                    $('#amount_fees').val(response.amount);
                },
            });
        });
    });
    </script>

@endsection


