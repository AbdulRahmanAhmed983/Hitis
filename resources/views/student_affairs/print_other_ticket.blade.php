@extends('layout.layout')
@section('title',$student['student_code'])
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            <div class="card">
                <div class="card-header d-flex">
                    <h2 class="col-6 text-dark my-auto">حافظة سداد</h2>
                    <div class="col-2"></div>
                    <div class="col-4 float-right">
                        <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 row justify-content-around">
                        <div class="col-6">
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="name">إسم الطالب</label>
                                <div class="col-8 font-weight-bolder">
                                    <h5 id="name">{{$student['name']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="student_code">كود الطالب</label>
                                <div class="col-8">
                                    <h5 id="student_code">{{$student['student_code']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="study_group">الفرقة الدراسية</label>
                                <div class="col-8">
                                    <h5 id="study_group">{{$student['study_group']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="specialization">التخصص</label>
                                <div class="col-8">
                                    <h5 id="specialization">{{$student['specialization']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="type">سبب الحافظه</label>
                                <div class="col-8">
                                    <h5 id="type">{{$student['type']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="payment">إجمالي مبلغ</label>
                                <div class="col-8">
                                    <h5 id="payment">{{$student['payment']}}</h5>
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                <label class="col-4 control-label h6" for="withdrawn">خصم من محفظة الطالب</label>
                                <div class="col-8">
                                    <h5 id="withdrawn">{{$student['withdrawn']}}</h5>
                                </div>
                            </div> --}}
                        </div>
                        <div class="col-6">
                            <div class="form-group text-right">
                                <svg id="barcode"></svg>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="ticket_id">رقم الحافظة</label>
                                <div class="col-8 font-weight-bolder">
                                    <h5 id="ticket_id">{{$student['ticket_id']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="date">تاريخ الحافظة</label>
                                <div class="col-8">
                                    <h5 id="date">{{$student['date']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="semester">الفصل الدراسي</label>
                                <div class="col-8">
                                    <h5 id="semester">{{$student['semester']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6 my-auto" for="amount">المبلغ المراد سداده</label>
                                <div class="col-8">
                                    <h2 id="amount">{{$student['payment']}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex">
                    <h2 class="col-6 text-dark my-auto">حافظة سداد</h2>
                    <div class="col-2"></div>
                    <div class="col-4 float-right">
                        <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 row justify-content-around">
                        <div class="col-6">
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="name">إسم الطالب</label>
                                <div class="col-8 font-weight-bolder">
                                    <h5 id="name">{{$student['name']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="student_code">كود الطالب</label>
                                <div class="col-8">
                                    <h5 id="student_code">{{$student['student_code']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="study_group">الفرقة الدراسية</label>
                                <div class="col-8">
                                    <h5 id="study_group">{{$student['study_group']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="specialization">التخصص</label>
                                <div class="col-8">
                                    <h5 id="specialization">{{$student['specialization']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="type">سبب الحافظه</label>
                                <div class="col-8">
                                    <h5 id="type">{{$student['type']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="payment">إجمالي مبلغ</label>
                                <div class="col-8">
                                    <h5 id="payment">{{$student['payment']}}</h5>
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                <label class="col-4 control-label h6" for="withdrawn">خصم من محفظة الطالب</label>
                                <div class="col-8">
                                    <h5 id="withdrawn">{{$student['withdrawn']}}</h5>
                                </div>
                            </div> --}}
                        </div>
                        <div class="col-6">
                            <div class="form-group text-right">
                                <svg id="barcode"></svg>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="ticket_id">رقم الحافظة</label>
                                <div class="col-8 font-weight-bolder">
                                    <h5 id="ticket_id">{{$student['ticket_id']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="date">تاريخ الحافظة</label>
                                <div class="col-8">
                                    <h5 id="date">{{$student['date']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6" for="semester">الفصل الدراسي</label>
                                <div class="col-8">
                                    <h5 id="semester">{{$student['semester']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label h6 my-auto" for="amount">المبلغ المراد سداده</label>
                                <div class="col-8">
                                    <h2 id="amount">{{$student['payment']}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('js/JsBarcode.all.min.js')}}"></script>
    <script>
        JsBarcode("#barcode", "{{$student['ticket_id']}}", {
            displayValue: false,
            fontSize: 25
        });
        window.print();
        window.onbeforeprint = function () {
            $('nav.main-header').hide();
            $('aside.main-sidebar').hide();
            $('footer.main-footer').hide();
            $('.content-wrapper').removeClass('content-wrapper');
        };
        window.onafterprint = function () {
            window.location.replace("{{route('add.other.ticket')}}");
        };
    </script>
@endsection


