@extends('layout.layout')
@if(isset($days))
    @section('title', 'التقارير من '.$start_date.' الى '.$end_date)
@else
    @section('title', 'تحميل التقارير')
@endif
@section('styles')
    @if(isset($days))
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    @endif
@endsection
@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success mt-3 text-center col-12">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
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
    <div class="row justify-content-around">
        <div class="col-lg-12">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">سجل المالية (طبقاً لفتح و غلق اليومية)</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="daily_payments_daily" method="post"
                          action="{{route('daily.payments.report',['type'=>'daily'])}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="start_date">من</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" required
                                       value="{{old('start_date')}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">الى</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" required
                                       value="{{old('end_date')}}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" name="action" form="daily_payments_daily"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="excel">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                     <button type="submit" name="action" form="daily_payments_daily"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="excel_administrative">تنزيل الادارية <i class="fas fa-file-excel"></i>
                    </button>
                    <button type="submit" name="action" form="daily_payments_daily"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="total">اجمالى الفتره <i class="fas fa-chart-pie"></i>
                    </button>
                    <button type="submit" name="action" form="daily_payments_daily"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="days">تحليل الفتره <i class="fas fa-chart-line"></i>
                    </button>
                </div>
            </div>
            @if(isset($count))
                <div class="card card-primary">
                    <div class="card-body">
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th><h3>من</h3></th>
                                <th><h3>{{($start_date->date)}}</h3></th>
                                <th><h3>الى</h3></th>
                                <th><h3>{{$end_date->date}}</h3></th>
                            </tr>
                            <tr>
                                <th><h5>اجمالى المدفوع</h5></th>
                                <th><h5>{{($cash+$credit)}}</h5></th>
                                <th><h5>عدد الايصالات</h5></th>
                                <th><h5>{{$count}}</h5></th>
                            </tr>
                            <tr>
                                <th>اجمالى المدفوع كاش</th>
                                <th>{{$cash}}</th>
                                <th>اجمالى المدفوع كريدت</th>
                                <th>{{$credit}}</th>
                            </tr>
                            </tbody>
                        </table>
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th>اجمالى المدفوع الدراسية</th>
                                <th>{{$study}}</th>
                                <th>اجمالى المدفوع الاخرى</th>
                                <th>{{$other}}</th>
                                <th>اجمالى المدفوع المحفظة</th>
                                <th>{{$wallet}}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--manage-->
                    
                </div>
            @endif
            @if(isset($days))
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="example" class="table text-center table-bordered table-hover rounded">
                            <thead>
                            <tr>
                                <th>اليوم</th>
                                <th>اجمالى المدفوع</th>
                                <th>كاش</th>
                                <th>كريدت</th>
                                <th>مصاريف دراسية</th>
                                <th>مصاريف اخرى</th>
                                <th>مصاريف محفظة</th>
                                <th>اجمالى الحافظات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($days as $day => $value)
                                <tr @class(["font-weight-bolder"=>(($value['cash']+$value['credit'])>0)])>
                                    <td>{{$day}}</td>
                                    <td>{{$value['cash']+$value['credit']}}</td>
                                    <td>{{$value['cash']}}</td>
                                    <td>{{$value['credit']}}</td>
                                    <td>{{$value['study']}}</td>
                                    <td>{{$value['other']}}</td>
                                    <td>{{$value['wallet']}}</td>
                                    <td>{{$value['count']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="row justify-content-around">
        <div class="col-lg-12">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">سجل المالية (بتاريخ وقت السداد)</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="daily_payments_interval" method="post"
                          action="{{route('daily.payments.report',['type'=>'interval'])}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="start_date">من</label>
                                <input type="datetime-local" class="form-control" name="start_date" id="start_date" required
                                       value="{{old('start_date')}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">الى</label>
                                <input type="datetime-local" class="form-control" name="end_date" id="end_date" required
                                       value="{{old('end_date')}}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" name="action" form="daily_payments_interval"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="excel">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                     <button type="submit" name="action" form="daily_payments_daily"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="excel_administrative">تنزيل الادارية <i class="fas fa-file-excel"></i>
                    </button>
                    <button type="submit" name="action" form="daily_payments_interval"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="total">اجمالى الفتره <i class="fas fa-chart-pie"></i>
                    </button>
                    <button type="submit" name="action" form="daily_payments_interval"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="days">تحليل الفتره <i class="fas fa-chart-line"></i>
                    </button>
                </div>
            </div>
            @if(isset($count))
                <div class="card card-primary">
                    <div class="card-body">
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th><h3>من</h3></th>
                                <th><h3>{{($start_date->date)}}</h3></th>
                                <th><h3>الى</h3></th>
                                <th><h3>{{$end_date->date}}</h3></th>
                            </tr>
                            <tr>
                                <th><h5>اجمالى المدفوع</h5></th>
                                <th><h5>{{($cash+$credit)}}</h5></th>
                                <th><h5>عدد الايصالات</h5></th>
                                <th><h5>{{$count}}</h5></th>
                            </tr>
                            <tr>
                                <th>اجمالى المدفوع كاش</th>
                                <th>{{$cash}}</th>
                                <th>اجمالى المدفوع كريدت</th>
                                <th>{{$credit}}</th>
                            </tr>
                            </tbody>
                        </table>
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th>اجمالى المدفوع الدراسية</th>
                                <th>{{$study}}</th>
                                <th>اجمالى المدفوع الاخرى</th>
                                <th>{{$other}}</th>
                                <th>اجمالى المدفوع المحفظة</th>
                                <th>{{$wallet}}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if(isset($days))
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="example" class="table text-center table-bordered table-hover rounded">
                            <thead>
                            <tr>
                                <th>اليوم</th>
                                <th>اجمالى المدفوع</th>
                                <th>كاش</th>
                                <th>كريدت</th>
                                <th>مصاريف دراسية</th>
                                <th>مصاريف اخرى</th>
                                <th>مصاريف محفظة</th>
                                <th>اجمالى الحافظات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($days as $day => $value)
                                <tr @class(["font-weight-bolder"=>(($value['cash']+$value['credit'])>0)])>
                                    <td>{{$day}}</td>
                                    <td>{{$value['cash']+$value['credit']}}</td>
                                    <td>{{$value['cash']}}</td>
                                    <td>{{$value['credit']}}</td>
                                    <td>{{$value['study']}}</td>
                                    <td>{{$value['other']}}</td>
                                    <td>{{$value['wallet']}}</td>
                                    <td>{{$value['count']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            @endif
        </div>
    </div>
        <div class="row justify-content-around">
        <div class="col-lg-12">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">سجل الادارية و العسكرية (بتاريخ وقت السداد)</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="daily_payments_interval" method="post"
                          action="{{route('daily.payments.report',['type'=>'interval'])}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="start_date">من</label>
                                <input type="datetime-local" class="form-control" name="start_date" id="start_date" required
                                       value="{{old('start_date')}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">الى</label>
                                <input type="datetime-local" class="form-control" name="end_date" id="end_date" required
                                       value="{{old('end_date')}}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    {{-- <button type="submit" name="action" form="daily_payments_interval"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="excel">تنزيل <i class="fas fa-file-excel"></i>
                    </button> --}}
                    <button type="submit" name="action" form="daily_payments_interval"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="total">اجمالى الفتره <i class="fas fa-chart-pie"></i>
                    </button>
                    <button type="submit" name="action" form="daily_payments_interval"
                            class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content" value="days">تحليل الفتره <i class="fas fa-chart-line"></i>
                    </button>
                </div>
            </div>
            @if(isset($count))
                <div class="card card-primary">
                    <div class="card-body">
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th><h3>من</h3></th>
                                <th><h3>{{($start_date->date)}}</h3></th>
                                <th><h3>الى</h3></th>
                                <th><h3>{{$end_date->date}}</h3></th>
                            </tr>
                            <tr>
                                <th><h5>اجمالى المدفوع</h5></th>
                                <th><h5>{{($cash_administrative+$credit_administrative)}}</h5></th>
                                <th><h5>عدد الايصالات</h5></th>
                                <th><h5>{{ $count_administrative }}</h5></th>
                             </tr>
                            <tr>
                                <tr>
                                    <th>اجمالى المدفوع كاش</th>
                                    <th>{{$cash_administrative}}</th>
                                    <th>اجمالى المدفوع كريدت</th>
                                    <th>{{$credit_administrative}}</th>
                                </tr>
                                 <tr>
                                        <th>اجمالى المدفوع ادارية كاش</th>
                                        <th>{{$cash_administrative - $cash_administrative_military}}</th>
                                        <th>اجمالى المدفوع ادارية كريدت</th>
                                        <th>{{$credit_administrative - $credit_administrative_military}}</th>
                                    </tr>
                            </tr>

                            </tbody>
                        </table>
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                 <th>اجمالى العسكرية كاش</th>
                                   <th>{{$cash_administrative_military}}</th>
                                   <th>اجمالى العسكرية  كريدت</th>
                                   <th>{{$credit_administrative_military}}</th>
                                   <th>اجمالى العسكرية</th>
                                   <th>{{$military_expenses}}</th>
                                {{-- <th>اجمالى المدفوع عسكرية خصم من المحفظة</th>
                                <th>{{$wallet}}</th> --}}
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if(isset($days))
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="example" class="table text-center table-bordered table-hover rounded">
                            <thead>
                            <tr>
                                <th>اليوم</th>
                                <th>اجمالى المدفوع اداري</th>
                                <th>كاش اداري</th>
                                <th>كريدت اداري</th>
                                <th>اجمالى العسكرية </th>
                                <th> اجمالى العسكرية كاش</th>
                                <th> اجمالى العسكرية  كريدت</th>
                                <th> اجمالى الحافظات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($days as $day => $value)

                                <tr @class(["font-weight-bolder"=>(($value['cash_administrative']+$value['credit_administrative']) > 0)])>
                                    <td>{{$day}}</td>
                                    <td>{{$value['cash_administrative']+$value['credit_administrative']- $value['military_expenses']}}</td>
                                    <td>{{$value['cash_administrative']}}</td>
                                    <td>{{$value['credit_administrative']}}</td>
                                    <td>{{$value['military_expenses']}}</td>
                                    <td>{{$value['cash_administrative_military']}}</td>
                                    <td>{{$value['credit_administrative_military']}}</td>
                                    <td>{{$value['count_administrative']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    @if(isset($days))
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.colVis.min.js"></script>

        <script>
            var table = $('#example').DataTable({
                "aaSorting": [],
                "paging": false,
                "lengthChange": false,
                "info": false,
                "autoWidth": false,
                // "scrollX": true,
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: 'نسخ <i class="fas fa-copy"></i>',
                        exportOptions: {
                            columns: ":visible"
                        }
                    },
                    {
                        extend: 'print',
                        text: 'طباعة <i class="fas fa-print"></i>',
                        exportOptions: {
                            columns: ":visible"
                        },
                    },
                ]
            });
            table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');
            $('#example_wrapper button').removeClass('btn-secondary').not('.buttons-colvis')
                .addClass(['btn-outline-info', 'mx-3', 'rounded']);
        </script>
    @endif
@endsection
