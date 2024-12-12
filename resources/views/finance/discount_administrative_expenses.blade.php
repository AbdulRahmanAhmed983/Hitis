@extends('layout.layout')
@section('title', 'دفع الادارية من المحفظة')
@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
             @if(session()->has('success'))
                <div class="alert alert-success mt-3 text-center">
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
            <div class="card card-primary">
                <div class="card-body">
                    <form action="{{route('pay.dministrative.expenses.discount')}}" method="post">
                    @csrf
                    @method('put')
         <div class="card card-primary">
        <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
            <h2 class="card-title float-left"> الادارية من المحفظة</h2>
            <div class="card-tools float-right">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group col-6 mx-auto">
                <label for="ticket_id">رقم الحافظة</label>
                <input type="text" required class="form-control" name="ticket_id" id="ticket_id"
                       value="{{ old('ticket_id') }}">
                @error('ticket_id')
                <div class="alert alert-danger mt-3 text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        &times;
                    </button>
                    <h6><i class="icon fas fa-ban"></i> {{ $message }}</h6>
                </div>
                @enderror
            </div>
            <!-- <div class="form-group col-6 mx-auto">-->
            <!--    <label for="payment_type">نوع السداد</label>-->
            <!--    <select required class="form-control" name="payment_type" id="payment_type">-->
            <!--        <option value="كاش" {{ old('payment_type') == 'كاش' ? 'selected' : '' }}>كاش</option>-->
            <!--        <option value="كريدت" {{ old('payment_type') == 'كريدت' ? 'selected' : '' }}>كريدت</option>-->
            <!--    </select>-->
            <!--    @error('payment_type')-->
            <!--    <div class="alert alert-danger mt-3 text-center">-->
            <!--        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
            <!--            &times;-->
            <!--        </button>-->
            <!--        <h6><i class="icon fas fa-ban"></i> {{ $message }}</h6>-->
            <!--    </div>-->
            <!--    @enderror-->
            <!--</div>-->
            <!--<div class="form-group col-6 mx-auto">-->
            <!--    <label for="visa_number">اخر اربع ارقام الفيزا</label>-->
            <!--    <input type="text" pattern="\d*" maxlength="4" required class="form-control"-->
            <!--           name="visa_number" id="visa_number" value="{{ old('visa_number') }}">-->
            <!--    @error('visa_number')-->
            <!--    <div class="alert alert-danger mt-3 text-center">-->
            <!--        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">-->
            <!--            &times;-->
            <!--        </button>-->
            <!--        <h6><i class="icon fas fa-ban"></i> {{ $message }}</h6>-->
            <!--    </div>-->
            <!--    @enderror-->
            <!--</div>-->
            <div class="form-group col-6 mx-auto">
                <label for="total" id="totalLabel"></label>
            </div>
            <div id="errorLabel"></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">دفع من المحفظة</button>
        </div>
    </div>
</form>

                 
                </div>
            </div>
            
            
        </div>
    </div>
@endsection

@section('scripts')
    <!--<script>-->
    <!--    if ($('#payment_type').val() === 'كريدت') {-->
    <!--        $('#visa_number').attr('disabled', false).parent().show();-->
    <!--    } else {-->
    <!--        $('#visa_number').attr('disabled', true).parent().hide();-->
    <!--    }-->

    <!--    $('#payment_type').on('input', function () {-->
    <!--        if ($(this).val() === 'كريدت') {-->
    <!--            $('#visa_number').attr('disabled', false).parent().show();-->
    <!--        } else {-->
    <!--            $('#visa_number').attr('disabled', true).parent().hide();-->
    <!--        }-->
    <!--    });-->
    <!--</script>-->
    <script>
    document.getElementById('ticket_id').addEventListener('keyup', function() {
    var ticketId = this.value;
    fetch('/getTotalValue/' + ticketId)
        .then(function(response) {
            if (response.ok) {
                return response.text();
            }
            else {
                throw new Error('رقم الحافظة ليس له قيمة');
            }
        })
        .then(function(total) {
            document.getElementById('totalLabel').textContent = 'Total: ' + total;
        })
        .catch(function(error) {
            //console.error('Error:', error);
            document.getElementById('errorLabel').textContent = 'خطأ: ' + error.message;
        });
});
    </script>
@endsection

