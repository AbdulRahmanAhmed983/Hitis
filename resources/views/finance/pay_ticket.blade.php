@extends('layout.layout')
@section('title', 'سداد حافظة ترميم')
@section('styles')
@endsection
@section('content')
    <div class="row">
        @if(session()->has('success'))
            <div class="alert alert-success col-11 mx-auto mt-3 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger col-11 mx-auto mt-3 text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                    &times;
                </button>
                <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
            </div>
        @endif
                    <h3 style="color:green; margin:2% auto">اليومية المفتوحة ({{$daily_payments_datetime}})</h3>

        <div class="col-11 mx-auto">
            <form action="{{route('pay')}}" method="post">
                @csrf
                @method('put')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group col-6 mx-auto">
                            <label for="ticket_id">رقم الحافظة</label>
                            <input type="text" required class="form-control" name="ticket_id" id="ticket_id"
                                   value="{{old('ticket_id')}}">
                            @error('ticket_id')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col-6 mx-auto">
                            <label for="ministerial_receipt">رقم الإيصال الوزاري</label>
                            <input type="number" required class="form-control" readonly
                                   name="ministerial_receipt" id="ministerial_receipt"
                                   value="{{$ministerial_receipt}}">
                            @error('ministerial_receipt')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col-6 mx-auto">
                            <label for="type">نوع الحافظة</label>
                            <select type="number" required class="form-control" name="type" id="type">
                                <option value="دراسية" {{old('type')=='دراسية'?'selected':''}}>دراسية</option>
                                <option value="اخرى" {{old('type')=='اخرى'?'selected':''}}>اخرى</option>
                                <option value="محفظة" {{old('type')=='محفظة'?'selected':''}}>محفظة</option>
                            </select>
                            @error('type')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col-6 mx-auto">
                            <label for="payment_type">نوع السداد</label>
                            <select type="number" required class="form-control" name="payment_type" id="payment_type">
                                <option value="كاش" {{old('payment_type')=='كاش'?'selected':''}}>كاش</option>
                                <option value="كريدت" {{old('payment_type')=='كريدت'?'selected':''}}>كريدت</option>
                            </select>
                            @error('payment_type')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col-6 mx-auto">
                            <label for="visa_number">اخر اربع ارقام الفيزا</label>
                            <input type="text" pattern="\d*" maxlength="4" required class="form-control"
                                   name="visa_number" id="visa_number" value="{{old('visa_number')}}">
                            @error('visa_number')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">تأكيد</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        if ($('#payment_type').val() === 'كريدت') {
            $('#visa_number').attr('disabled', false).parent().show();
        } else {
            $('#visa_number').attr('disabled', true).parent().hide();
        }

        $('#payment_type').on('input', function () {
            if ($(this).val() === 'كريدت') {
                $('#visa_number').attr('disabled', false).parent().show();
            } else {
                $('#visa_number').attr('disabled', true).parent().hide();
            }
        });
    </script>
@endsection
