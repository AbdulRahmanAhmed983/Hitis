@extends('layout.layout')
@section('title', 'إصدار حافظة')
@section('style')
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
            <form action="{{route('store.ticket')}}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
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
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="hours">عدد الساعات المسجلة</label>
                                            <input type="text" value="{{$payment->hours}}" readonly required
                                                   class="form-control" name="hours" id="hours">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="payment">إجمالي المبلغ للساعات المسجله</label>
                                            <input type="text" value="{{$payment->payment}}" readonly required
                                                   class="form-control" name="payment" id="payment">
                                        </div>
                                    </div>
                                    @error('hours')
                                    <div class="alert alert-danger col-12 mt-3 text-center">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">
                                            &times;
                                        </button>
                                        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ticket_id">رقم الحافظة</label>
                                            <input type="text" value="{{$ticket_id}}" readonly required
                                                   class="form-control" name="ticket_id" id="ticket_id">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="date">تاريخ الحافظة</label>
                                            <input type="date" required value="{{old('date',date('Y-m-d'))}}"
                                                   class="form-control" name="date" id="date">
                                        </div>
                                    </div>
                                    @error('ticket_id')
                                    <div class="alert alert-danger col-12 mt-3 text-center">
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
                                {{--                                <div class="form-group">--}}
                                {{--                                    <label for="ticket_number">رقم الحافظة الخارجية</label>--}}
                                {{--                                    <input type="text" required value="{{old('ticket_number')}}"--}}
                                {{--                                           class="form-control" name="ticket_number" id="ticket_number">--}}
                                {{--                                    @error('ticket_number')--}}
                                {{--                                    <div class="alert alert-danger mt-3 text-center">--}}
                                {{--                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">--}}
                                {{--                                            &times;--}}
                                {{--                                        </button>--}}
                                {{--                                        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>--}}
                                {{--                                    </div>--}}
                                {{--                                    @enderror--}}
                                {{--                                </div>--}}
                                <div class="form-group">
                                    <label for="semester">الفصل الدراسي</label>
                                    <input type="text" required readonly
                                           value="{{$payment->year}}-{{$payment->semester}}" class="form-control"
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
                                    <input type="number" required placeholder="0" min="{{$payment->payment}}"
                                           step="0.25" max="{{$payment->payment}}"
                                           value="{{old('amount',$payment->payment)}}" class="form-control"
                                           name="amount" id="amount">
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
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> طباعة</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#amount').on('input', function (e) {
            let me = $(this);
            let amount = @json($payment->payment) - me.val();
            if (amount < 0) {
                alert('القيمة التي ادخلتها كبيرة');
            } else {
                $('#remaining').val(amount);
            }
        })
    </script>
@endsection


