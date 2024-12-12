@extends('layout.layout')
@section('title',$student['username'])
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <div class="row">

                        <div class="col-12 mt-auto text-center">
                            <h5>( استمارة ادارية)</h5>
                        </div>

                        <div class="col-6 text-center mt-3"><h6>
                                رقم الحافظة: {{$data['ticket_id']}}
                            </h6></div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 row justify-content-around">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="col-4 control-label" for="date">تاريخ الحافظة</label>
                                <div class="col-8">
                                    <input type="text" value="{{$date}}" readonly required
                                           class="form-control" name="date" id="date">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group row">
                                <label class="col-4 control-label" for="name">استلمنا من الطالب</label>
                                <div class="col-8 font-weight-bolder">
                                    <input type="text" value="{{$student['name']}}" readonly required
                                           class="form-control" name="name" id="name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="study_group">الفرقة الدراسية</label>
                                <div class="col-8">
                                    <input type="text" value="{{$student['study_group']}}" readonly required
                                           class="form-control" name="study_group" id="study_group">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group row">
                                <label class="col-4 control-label" for="student_code">كود الطالب</label>
                                <div class="col-8">
                                    <input type="text" value="{{$student['username']}}" readonly required
                                           class="form-control" name="student_code" id="student_code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="specialization">التخصص</label>
                                <div class="col-8">
                                    <input type="text" value="{{$student['specialization']}}" readonly required
                                           class="form-control" name="specialization" id="specialization">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-6 control-label" for="amount">المبلغ</label>
                            <div class="col-6">
                                <input type="text" value="{{$ticket_fees->first()->amount}}" readonly required
                                       class="form-control" name="amount" id="amount">
                            </div>
                        </div>
                        <div class="col-6">
                           @if(isset($data['payment_type']) && $data['payment_type'] == "كريدت" && !empty($data['visa_number']))
                                <div class="form-group row">
                                    <label class="col-4 control-label" for="amount">اخر اربع ارقام الفيزا</label>
                                    <div class="col-6">
                                        <input type="text" value="{{$data['visa_number']}}" readonly required
                                               class="form-control" name="amount" id="amount">
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
        window.print();
        window.onbeforeprint = function () {
            $('nav.main-header').hide();
            $('aside.main-sidebar').hide();
            $('footer.main-footer').hide();
            $('.content-wrapper').removeClass('content-wrapper');
        };
        window.onafterprint = function () {
            window.location.replace("{{$url}}");
        };
    </script>
@endsection


