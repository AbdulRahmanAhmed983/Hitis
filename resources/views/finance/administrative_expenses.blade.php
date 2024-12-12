@extends('layout.layout')
@section('title', '  سداد مصاريف ادارية')
@section('styles')
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
@if(session()->has('error'))
<div class="alert alert-danger mt-3 text-center">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
        &times;
    </button>
    <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
</div>
@endif

<div class="row justify-content-around">
    <div class="col-lg-10">
        <form  method="post" action="{{route('pay.dministrative.expenses')}}" id="paymentForm">
            @csrf
            @method('put')
            <div class="card">
                <div class="card-body">
                    <div class="form-group col-6 mx-auto">
                        <label for="ticket_id">رقم الحافظة</label>
                        <input type="text" required class="form-control" name="ticket_id" id="ticket_id">
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
                        <label for="payment_type">نوع السداد</label>
                        <select type="number" required class="form-control" name="payment_type" id="payment_type">
                             <option hidden>اختر نوع السداد</option>
                             <!--<option value>اختر نوع السداد</option>-->
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
                    <div class="form-group col-6 mx-auto">
                        <label for="total" id="totalLabel"></label>
                    </div>
                    <div id="errorLabel"></div>

                </div>
               <div class="card-footer">
                    <button type="submit" class="btn btn-primary">تأكيد</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-10">
        <div class="card-header d-flex">
            <h3 class="col-6 text-dark my-auto">حافظة سداد خدمات تعليمية</h3>
            <div class="col-2"></div>
            <div class="col-4 float-right">
            </div>
        </div>
            <form  method="post" action="{{route('pay.extra.fees')}}" id="paymentForm">
                @csrf
                @method('put')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group col-6 mx-auto">
                            <label for="ticket_id">رقم الحافظة</label>
                            <input type="text" required class="form-control" name="ticket_id" id="ticket_id">
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
                            <label for="payment_type">نوع السداد</label>
                            <select type="number" required class="form-control" name="payment_type" id="payment_type1">
                                 <option hidden>اختر نوع السداد</option>
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
                                   name="visa_number" id="visa_number1" value="{{old('visa_number')}}">
                            @error('visa_number')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col-6 mx-auto">
                            <label for="total" id="totalLabel"></label>
                        </div>
                        <div id="errorLabel"></div>
    
                    </div>
                   <div class="card-footer">
                        <button type="submit" class="btn btn-primary">تأكيد</button>
                    </div>
                </div>
            </form>
        
            </div>
    </div>
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
        <script>
            if ($('#payment_type1').val() === 'كريدت') {
                $('#visa_number1').attr('disabled', false).parent().show();
            } else {
                $('#visa_number1').attr('disabled', true).parent().hide();
            }
            $('#payment_type1').on('input', function () {
                if ($(this).val() === 'كريدت') {
                    $('#visa_number1').attr('disabled', false).parent().show();
                } else {
                    $('#visa_number1').attr('disabled', true).parent().hide();
                }
            });
        </script>
        <script>
                document.getElementById('ticket_id').addEventListener('keyup', function() {
        var ticketId = this.value;
        fetch('/getTotalValue/' + ticketId)
            .then(function(response) {
                if (response.ok) {
                    return response.text();
                } 
            })
            .then(function(total) {
                document.getElementById('totalLabel').textContent = 'Total: ' + total;
            })
            .catch(function(error) {
                document.getElementById('errorLabel').textContent = 'خطأ: ' + error.message;
            });
    });
        </script>
    @endsection
