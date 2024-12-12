@extends('layout.layout')
@section('title', 'مراجعة اليوميات')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body">
                    @if(session()->has('success'))
                        <div class="alert alert-success mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
                        </div>
                    @endif
                    <form method="post" action="{{route('set.daily.payments')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="col-md-4 text-center">
                                <input type="date" class="form-control" name="date" id="date" required
                                       value="{{$last_date}}">
                            </div>
                            <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                <button type="submit" name="btn" value="open" class="btn btn-primary col-12">
                                    فتح اليومية
                                </button>
                            </div>
                            <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                <button type="submit" name="btn" value="close" class="btn btn-primary col-12">
                                    غلق اليومية
                                </button>
                            </div>
                        </div>
                    </form>
                    @error('date')
                    <div class="alert alert-danger mt-3 text-center">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            &times;
                        </button>
                        <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                    </div>
                    @enderror
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-body">
                    <div>
                        <form method="get" action="{{route('daily.payments')}}">
                            <div class="row">
                                <div class="forms-group col-md-6 my-2 my-md-0">
                                    <select name="day" id="day" class="custom-select">
                                        @foreach($days as $day)
                                            <option
                                                value="{{$day}}" {{(isset($d) and $d==$day)?'selected':''}}>{{$day}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="text-center col-md-4 align-self-center my-2 my-md-0">
                                    <button type="submit" class="btn btn-primary col-12"><i class="fas fa-search"></i>
                                        بحث
                                    </button>
                                </div>
                            </div>
                            @error('day')
                            <div class="alert alert-danger mt-3 text-center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <h6><i class="icon fas fa-ban"></i> {{$message}}</h6>
                            </div>
                            @enderror
                        </form>
                    </div>
                    @if(session()->has('error'))
                        <div class="alert alert-danger mt-3 text-center">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                &times;
                            </button>
                            <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
                        </div>
                    @endif
                </div>
            </div>
            @if(isset($tickets))
                <div class="card card-gray">
                    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                        <h2 class="card-title float-left">كشف مالية يوم {{$d}}</h2>
                        <div class="card-tools  float-right">
                            <button type="button" class="btn btn-tool">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table table-hover table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="align-middle">رقم الحافظة</th>
                                <th class="align-middle">رقم الوصل</th>
                                <th class="align-middle">اسم الطالب</th>
                                <th class="align-middle">كود الطالب</th>
                                <th class="align-middle">نوع الحافظة</th>
                                <th class="align-middle">نوع السداد</th>
                                <th class="align-middle">رقم الفيزا</th>
                                <th class="align-middle">القيمة</th>
                                <th class="align-middle">تم التأكيد بواسطة</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td class="align-middle">{{$ticket->ticket_id}}</td>
                                    <td class="align-middle">{{$ticket->ministerial_receipt}}</td>
                                    <td class="align-middle">{{$ticket->name}}</td>
                                    <td class="align-middle">{{$ticket->student_code}}</td>
                                    <td class="align-middle">{{$ticket->type}}</td>
                                    <td class="align-middle{{($ticket->payment_type == 'كريدت')? ' bg-danger':''}}">
                                        {{$ticket->payment_type}}</td>
                                    <td class="align-middle{{($ticket->payment_type == 'كريدت')? ' bg-danger':''}}">
                                        {{$ticket->visa_number}}</td>
                                    <td class="align-middle">{{$ticket->amount}}</td>
                                    <td class="align-middle">{{$ticket->confirmed_by}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <table class="table text-left table-borderless">
                            <tbody>
                            <tr>
                                <th><h5>اجمالى المدفوع</h5></th>
                                <th><h5>{{($cash+$credit)}}</h5></th>
                                <th><h5>عدد الايصالات</h5></th>
                                <th><h5>{{count($tickets)}}</h5></th>
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
                </div>
            @endif
            <div class="col-12">
            @if(isset($ticketsAdministrative))
            <hr style="border: 2px solid rgb(14, 94, 14)">

               <div class="card card-gray">
                   <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                       <h2 class="card-title float-left">كشف مالية المصاريف الادارية يوم {{$d}}</h2>
                       <div class="card-tools  float-right">
                           <button type="button" class="btn btn-tool">
                               <i class="fas fa-minus"></i>
                           </button>
                       </div>
                   </div>
                   <div class="card-body" style="overflow-x: auto;">
                       <table class="table table-hover table-bordered text-center">
                           <thead>
                           <tr>
                               <th class="align-middle">رقم الحافظة</th>
                               <th class="align-middle">اسم الطالب</th>
                               <th class="align-middle">كود الطالب</th>
                               <th class="align-middle">نوع السداد</th>
                               <th class="align-middle">رقم الفيزا</th>
                               <th class="align-middle">القيمة</th>
                               <th class="align-middle">تم التأكيد بواسطة</th>
                           </tr>
                           </thead>
                           <tbody>
                           @foreach($ticketsAdministrative as $ticketsAdministrative)
                               <tr>
                                   <td class="align-middle">{{$ticketsAdministrative->ticket_id}}</td>
                                   <td class="align-middle">{{$ticketsAdministrative->name}}</td>
                                   <td class="align-middle">{{$ticketsAdministrative->student_code}}</td>
                                   <td class="align-middle{{($ticketsAdministrative->payment_type == 'كريدت')? ' bg-danger':''}}">
                                       {{$ticketsAdministrative->payment_type}}</td>
                                   <td class="align-middle{{($ticketsAdministrative->payment_type == 'كريدت')? ' bg-danger':''}}">
                                       {{$ticketsAdministrative->visa_number}}</td>
                                   <td class="align-middle">{{$ticketsAdministrative->amount}}</td>
                                   <td class="align-middle">{{$ticketsAdministrative->confirmed_by}}</td>
                               </tr>
                           @endforeach
                           </tbody>
                       </table>
                   </div>
                   <div class="card-footer">
                       <!--<hr style="border: 2px solid rgb(14, 94, 14)">-->
                       <div class="header text-center pb-3 pt-3">
                           <h3>المصروفات الادارية</h3>
                       </div>
                       <div class="card-footer">
                            <table class="table text-left table-borderless">
                               <tbody>
                           <tr>
                                   <th>اجمالى المدفوع كاش كلي</th>
                                   <th>{{$cash_administrative}}</th>
                                   <th>اجمالى المدفوع كريدت كلي</th>
                                   <th>{{$credit_administrative}}</th>
                                    <th><h5>عدد الايصالات</h5></th>
                                   <th><h5>{{ count((array) $ticketsAdministrative) }}</h5></th>
                               </tr>
                               
                               </tbody>
                           </table>
                               <hr style="border: 2px solid rgb(14, 94, 14)">
                           <table class="table text-left table-borderless">
                               <tbody>
                                   
                               <tr>
                                   <th><h5>اجمالي المدفوع اداري </h5></th>
                                   <th><h5>{{($cash_administrative+$credit_administrative-$military_expenses-$discount_wallet_administrative)}}</h5></th>
                                   <th><h5>اجمالي المدفوع كاش اداري </h5></th>
                                   <th><h5>{{($cash_administrative-$cash_administrative_military)}}</h5></th>
                                   <th><h5>اجمالى المدفوع كريدت اداري </h5></th>
                                   <th><h5>{{($credit_administrative-$credit_administrative_military)}}</h5></th>
                                  
                                </tr>
                              
                               </tbody>
                           </table>
                           
                               
                         <hr style="border: 2px solid rgb(14, 94, 14)">
                           <table class="table text-left table-borderless">
                               <tbody>
                               <tr>
                                   <th>اجمالى التامين</th>
                                   <th>{{$insurance - $discount_wallet_administrative_insurance}}</th>
                                   <th>اجمالى فتح الملف</th>
                                   <th>{{$profile_expenses - $discount_wallet_administrative_profile_expenses}}</th>
                                   <th>اجمالى تسجيل قيد</th>
                                   <th>{{$registration_fees - $discount_wallet_administrative_registration_fees}}</th>

                               </tr>
                               </tbody>
                           </table>
                           <table class="table text-left table-borderless">
                               <tbody>
                               <tr>

                                   <th>اجمالى الايميل والكارنيه</th>
                                   <th>{{$card_and_email - $discount_wallet_administrative_card_and_email}}</th>
                                   <th>اجمالى تجديد الايميل والكارنيه</th>
                                   <th>{{$renew_card_and_email - $discount_wallet_administrative_renew_card_and_email}}</th>
                               </tr>
                               </tbody>
                           </table>
                        <hr style="border: 2px solid rgb(14, 94, 14)">
                           <table class="table text-left table-borderless">
                               <tbody>
                               <tr>

                                   <th>اجمالى العسكرية كاش</th>
                                   <th>{{$cash_administrative_military}}</th>
                                   <th>اجمالى العسكرية  كريدت</th>
                                   <th>{{$credit_administrative_military}}</th>
                                   <th>اجمالى العسكرية</th>
                                   <th>{{$military_expenses}}</th>
                               </tr>
                               </tbody>
                           </table>
                        <hr style="border: 2px solid rgb(14, 94, 14)">
                           <table class="table text-left table-borderless">
                               <tbody>
                               <tr>

                                   <th style="color:rgb(35, 35, 237);">اجمالى المدفوع من المحفطة </th>
                                   <th>{{$discount_wallet_administrative}}</th>
                                   <th style="color:rgb(35, 35, 237);"> اجمالى التامين من المحفطة</th>
                                   <th>{{$discount_wallet_administrative_insurance}}</th>
                                   <th style="color:rgb(35, 35, 237);">اجمالى فتح الملف  من المحفطة</th>
                                   <th>{{$discount_wallet_administrative_profile_expenses}}</th>
                                  
                               </tr>
                               <tr>
                                    <th style="color:rgb(35, 35, 237);">اجمالى تسجيل قيد  من المحفطة</th>
                                   <th>{{$discount_wallet_administrative_registration_fees}}</th>
                                   <th style="color:rgb(35, 35, 237);">اجمالى الايميل والكارنيه  من المحفطة</th>
                                   <th>{{$discount_wallet_administrative_card_and_email}}</th>
                                   <th style="color:rgb(35, 35, 237);">اجمالى تجديد الايميل والكارنيه  من المحفطة</th>
                                   <th>{{$discount_wallet_administrative_renew_card_and_email}}</th>
                               </tr>
                               </tbody>
                           </table>
                       </div>

                   </div>
               </div>
               </div>
           @endif


        </div>
@endsection
@section('scripts')
@endsection
