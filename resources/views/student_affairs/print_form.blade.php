@extends('layout.layout')
@section('title',$student['username'])
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            <div class="card">
                <div class="card-header d-flex">
                    <h2 class="col-6 text-dark my-auto">  </h2>
                    <div class="col-2"></div>
                    <div class="col-4 float-right">
                        <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 row justify-content-around">
                        <div class="col-6">
                            <div class="form-group row">
                                <label class="col-4 control-label" for="name">إسم الطالب</label>
                                <div class="col-8 font-weight-bolder">
                                    <h5 id="name">{{$student['name']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="username">كود الطالب</label>
                                <div class="col-8">
                                    <h5 id="username">{{$student['username']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="username">كلمة المرور </label>
                                <div class="col-8">
                                    <h5 id="username">{{$student['password']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="study_group">الفرقة الدراسية</label>
                                <div class="col-8">
                                    <h5 id="study_group">{{$student['study_group']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="specialization">التخصص</label>
                                <div class="col-8">
                                    <h5 id="specialization">{{$student['specialization']}}</h5>
                                </div>
                            </div>
                        <div class="col-12">
                            <div class="form-group text-right">
                                <svg id="barcode"></svg>
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
                    <!--<h2 class="col-6 text-dark my-auto">حافظة محفظةادارية </h2>-->
                    <div class="col-2"></div>
                    <div class="col-4 float-right">
                        <img class="img-fluid" src="{{asset('images/logo.jpg')}}" alt="logo">
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 row justify-content-around">
                        <div class="col-6">
                            <div class="form-group row">
                                <label class="col-4 control-label" for="name">إسم الطالب</label>
                                <div class="col-8 font-weight-bolder">
                                    <h5 id="name">{{$student['name']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="username">كود الطالب</label>
                                <div class="col-8">
                                    <h5 id="username">{{$student['username']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="username">كلمة المرور </label>
                                <div class="col-8">
                                    <h5 id="username">{{$student['password']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="study_group">الفرقة الدراسية</label>
                                <div class="col-8">
                                    <h5 id="study_group">{{$student['study_group']}}</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 control-label" for="specialization">التخصص</label>
                                <div class="col-8">
                                    <h5 id="specialization">{{$student['specialization']}}</h5>
                                </div>
                            </div>
                        <div class="col-12">
                            <div class="form-group text-right">
                                <svg id="barcode"></svg>
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
        JsBarcode("#barcode", "{{$student['username']}}", {
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
        // window.onafterprint = function () {
        //     window.location.replace("{{route('create.wallet.administrative')}}");
        // };
    </script>
@endsection
<style>
    #barcode {
        width:100%;
    }
</style>