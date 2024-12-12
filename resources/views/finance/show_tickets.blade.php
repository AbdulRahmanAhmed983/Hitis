@extends('layout.layout')
@section('title', 'مراجعة الحافظات')
@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @error('username')
            <div class="alert alert-danger mt-3 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
            </div>
            @enderror
            @error('ticket_id')
            <div class="alert alert-danger mt-3 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
            </div>
            @enderror
            <div class="card card-default">
                <div class="card-header">
                    <h2 class="card-title float-left">الطالب {{$student['name']}}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                    </div>
                    <table id="example" class="table table-hover table-responsive-md table-bordered text-center">
                        <thead>
                        <tr>
                            <th>رقم الحافظه</th>
                            <th>المبلغ</th>
                            <th>نوع الحافظة</th>
                            <th>التاريخ</th>
                            <th>الترم</th>
                            <th>السنة الدراسية</th>
                            <th>طريقة الدفع</th>
                            <th>حالة الحافظه</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>{{$ticket->ticket_id}}</td>
                                <td>{{$ticket->amount}}</td>
                                <td>{{$ticket->type}}</td>
                                <td>{{$ticket->date}}</td>
                                <td>{{$ticket->semester}}</td>
                                <td>{{$ticket->year}}</td>
                                <td>{{$ticket->payment_type}}</td>
                                <td>{{$ticket->used ? 'مسدده' : 'غير مسدده'}}</td>
                                    <td>
                                    <a href="{{route('print.receipt',['username'=>$student['username'],
                                                'ticket_id'=>$ticket->ticket_id])}}"
                                       class="btn btn-primary">طباعة</a>
                                    </td>
                                    @if(Auth::user()->role === 'owner')
                                    <td>
                                    <a href="{{route('edit.type.payments',['username'=>$student['username'],
                                                'ticket_id'=>$ticket->ticket_id])}}"
                                       class="btn btn-primary">تعديل</a>
                                    </td>
                                    @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>

        var table = $('#example').DataTable({
            "aaSorting": [],
            "paging": false,
            "lengthChange": false,
            "info": false,
            "searching": false,
            "autoWidth": false,
        });
    </script>
@endsection
