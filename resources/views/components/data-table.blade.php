<div class="row">
    <div class="col-12">
        <div class="col-12 overflow-auto">
            <div class="mx-auto" style="width: fit-content">
                {{$pages->onEachSide(5)->links('pagination::bootstrap-4')}}
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div id="acb"></div>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 table-responsive">
                @if(session()->has('success'))
                    <div class="alert alert-success mt-3 mx-auto text-center col-11">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            &times;
                        </button>
                        <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger mt-3 mx-auto text-center col-11">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            &times;
                        </button>
                        <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                    </div>
                @endif
                <form id="search" action="" method="get"></form>
                <table id="example" class="table table-hover table-bordered rounded">
                    <thead class="bg-light text-capitalize">
                    <tr>
                        {{--                        @php--}}
                        {{--                            $c = 0;--}}
                        {{--                        @endphp--}}
                        @foreach($keys as $key)
                            <th class="align-middle text-center">{{$key}}{{--$c++--}}</th>
                        @endforeach
                        @if($edit != '#' or $delete != '#')
                            <th class="align-middle text-center">action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($values as $value)
                        <tr @if(!empty($status))
                            {{!is_null($status[$value[$primaryKey]])? (($status[$value[$primaryKey]] == 0)? 'class=bg-warning' : 'class=bg-success'): ''}}
                            @endif>
                            @foreach($value as $v)
                                <td class="align-middle text-center">{{$v}}</td>
                            @endforeach
                            @if($edit != '#' or $delete != '#')
                                <td>
                                    <div class="btn-group-vertical col-12">
                                        @if($edit != '#')
                                            <a class="btn btn-outline-info rounded my-1"
                                               href="{{route($edit, [$primaryKey => $value[$primaryKey]])}}">تعديل</a>
                                        @endif
                                        @if($delete != '#')
                                            <form id="delete_{{$value[$primaryKey]}}"
                                                  action="{{route($delete, [$primaryKey => $value[$primaryKey]])}}"
                                                  method="post">
                                                @csrf
                                                @method('delete')
                                            </form>
                                            <input type="submit" class="btn btn-outline-danger rounded my-1 col-12"
                                                   value="حذف" name="delete_{{$value[$primaryKey]}}">
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 overflow-auto">
        <div class="mx-auto" style="width: fit-content">
            {{$pages->onEachSide(5)->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>
@if($delete != '#')
    <div class="modal fade" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content bg-danger">
                <div class="modal-header">
                    <h4 class="modal-title">تأكيد الحذف</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-outline-light" id="sub">حذف</button>
                </div>
            </div>
        </div>
    </div>
@endif
@section('component-style')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <style>
        table.dataTable thead th,
        table.dataTable tbody td {
            background: transparent !important;
            white-space: nowrap;
        }
    </style>
@endsection
@section('component-script')
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
        $(document).ready(function () {
            $(window).keydown(function (event) {
                if (event.keyCode === 13 && $('[name=search]').val().length > 0) {
                    $('form#search').submit();
                }
            });
        });
        @if($edit != '#' or $delete != '#')
        $("tr[class*='bg-'] div.btn-group-vertical [class*='btn-outline-']").each(function () {
            $(this)[0].className = $(this)[0].className.replace('-outline', '');
        });
        @endif
        @if($delete != '#')
        $('[name^=delete_]').click(function () {
            $('#modal-danger').modal();
            $('.modal-backdrop.fade.show').hide();
            let primary_key = $(this).attr('name').substr($(this).attr('name').indexOf('_') + 1, $(this).attr('name').length);
            let delete_message = @json($deleteMessage);
            delete_message = delete_message.replace('primaryKey', primary_key);
            $('.modal-body p').text(delete_message);
            $('#sub').attr({
                form: $(this).attr('name'),
                type: 'submit'
            });
        });
        $('#modal-danger').on('hidden.bs.modal', function () {
            $('#sub').attr('type', 'button').removeAttr('form');
        });
        @endif
        $(window).on("load", function () {
            $('nav ul.pagination').parent().addClass(['mx-lg-0', 'mx-auto'])
            let search = @if($search) @json($search)@else '' @endif;
            $('#example_filter input').attr({
                form: 'search',
                name: 'search',
            }).addClass('align-middle').val(search);
            $('#example_filter label').addClass('my-auto');
            $('#example_filter').append('<button form="search" type="submit"' +
                'class="btn btn-outline-info mx-1 my-auto"><span class="fas fa-search"></span></button>');
        });
        var table = $('#example').DataTable({
            "aaSorting": [],
            "paging": false,
            "lengthChange": false,
            "info": false,
            "autoWidth": false,
            // "scrollX": true,
            "columnDefs": [
                {
                    "targets": @json($hiddenKeys),
                    "visible": false,
                },
            ],
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="fas fa-copy"></i>',
                    exportOptions: {
                        columns: ":visible"
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i>',
                    exportOptions: {
                        columns: ":visible"
                    },
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>',
                    exportOptions: {
                        columns: ":visible"
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fas fa-file-csv"></i>',
                    exportOptions: {
                        columns: ":visible"
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i>',
                    exportOptions: {
                        columns: ":visible"
                    },
                },
                {
                    extend: 'colvis',
                    text: 'اظهار الأعمدة',
                },
            ]
        });
        table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');

        $('#example_wrapper button').removeClass('btn-secondary').not('.buttons-colvis')
            .addClass(['btn-outline-info', 'm-1', 'rounded']);
        $('#example_wrapper .buttons-colvis').addClass(['btn-info', 'rounded']).parent().addClass(['m-1']);
        $('#example_wrapper .row:first-child .col-sm-12').first().addClass(['d-flex', 'py-2']);
        $('#example_wrapper .col-sm-12').first().removeClass('col-md-6').addClass(['col-lg-6']);
        $('#example_wrapper .col-md-6').removeClass('col-md-6').addClass(['col-lg-3', 'p-0']);

        $('#example_wrapper .row:first-child').appendTo('#acb');
        $('#acb .row').addClass('justify-content-around');
    </script>
@endsection
