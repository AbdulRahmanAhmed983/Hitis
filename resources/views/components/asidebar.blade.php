<aside class="main-sidebar  elevation-4 text-capitalize" style="background: darkslategrey !important;>
    <!-- Brand Logo -->
    <a href="https://aboukir-institutes.edu.eg/" target="_blank" class="brand-link">
        <img src="{{asset('images/logo.png')}}" alt="Logo" class="img-thumbnail" style="background:transparent;border:0";>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel pr-3s my-3 pb-3 d-flex" title="{{auth()->user()->name}}">
            <div class="image">
                <img src="{{asset('images/download.png')}}" class="img-circle elevation-2" alt="User Image"
                     style="margin-right: 13px;">
            </div>
            <div class="info">
                <a href="#profile" class="d-block">{{auth()->user()->name}}</a>
            </div>
            {{--            <div class="">--}}
            {{--                <a href="#profile" class="d-block">--}}
            {{--                    <div class="image mx-2">--}}
            {{--                        <img src="{{asset('images/download.png')}}" class="img-circle elevation-2"--}}
            {{--                             alt="User Image">--}}
            {{--                    </div>{{auth()->user()->name}}</a>--}}
            {{--            </div>--}}
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 pb-5">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            الصفحة الرئيسية
                        </p>
                    </a>
                </li>
                @if(in_array(auth()->user()->role, ['chairman','owner']))
                    <li class="nav-item">
                        <a href="{{route('show.semester.registration')}}" class="nav-link">
                            <i class="fas fa-list nav-icon"></i>
                            <p>chairman</p>
                        </a>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin','owner']))
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                admin
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('add.user')}}" class="nav-link">
                                    <i class="fas fa-user-plus nav-icon"></i>
                                    <p>إضافة مستخدم</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('users.list')}}" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>قائمة المستخدمين</p>
                                </a>
                            </li>
                            @if(auth()->user()->role == 'owner')
                                <li class="nav-item">
                                    <a href="{{route('academic.list')}}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>academic advisors</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('configuration')}}" class="nav-link">
                                        <i class="fas fa-cogs nav-icon"></i>
                                        <p>site configuration</p>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{route('student.status.admin')}}" class="nav-link">
                                    <i class="fas fa-info-circle nav-icon"></i>
                                    <p>بيان حالة الطالب</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin','owner','student_affairs']))
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                شئون الطلبة
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            
                            @if($has_routes or in_array('student.form',$routes))
                                <li class="nav-item">
                                    <a href="{{route('student.form')}}" class="nav-link">
                                        <i class="fas fa-user-plus nav-icon"></i>
                                        <p>إضافة طالب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('student.search',$routes))
                                <li class="nav-item">
                                    <a href="{{route('student.search')}}" class="nav-link">
                                        <i class="fas fa-search nav-icon"></i>
                                        <p>بحث عن طالب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('student.list',$routes))
                                <li class="nav-item">
                                    <a href="{{route('student.list')}}"
                                       class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>قائمه الطلاب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('student.status',$routes))
                                <li class="nav-item">
                                    <a href="{{route('student.status')}}"
                                       class="nav-link">
                                        <i class="fas fa-info-circle nav-icon"></i>
                                        <p>بيان حالة الطالب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('finance.student.status',$routes))
                                <li class="nav-item">
                                    <a href="{{route('finance.student.status')}}"
                                       class="nav-link">
                                        <i class="fas fa-info-circle nav-icon"></i>
                                        <p>بيان حالة المالية للطالب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('student.alerts',$routes))
                                <li class="nav-item">
                                    <a href="{{route('student.alerts')}}"
                                       class="nav-link">
                                        <i class="fas fa-bell nav-icon"></i>
                                        <p>تنبيه الطلاب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('add.courses.transfer',$routes))
                                <li class="nav-item">
                                    <a href="{{route('add.courses.transfer')}}"
                                       class="nav-link">
                                        <i class="fas fa-equals nav-icon"></i>
                                        <p>معادلة المواد للمحولين</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('add.excuses.index',$routes))
                                <li class="nav-item">
                                    <a href="{{route('add.excuses.index')}}"
                                       class="nav-link">
                                        <i class="fas fa-ban nav-icon"></i>
                                        <p>الاعذار و وقف القيد</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('create.wallet.ticket',$routes))
                                <li class="nav-item">
                                    <a href="{{route('create.wallet.ticket')}}"
                                       class="nav-link">
                                        <i class="fas fa-wallet nav-icon"></i>
                                        <p>إصدار حافظة محفظة</p>
                                    </a>
                                </li>
                           
                            @endif
                            <!--@if($has_routes or in_array('create.ticket',$routes))-->
                            <!--    <li class="nav-item">-->
                            <!--        <a href="{{route('create.ticket')}}"-->
                            <!--           class="nav-link">-->
                            <!--            <i class="fas fa-file-invoice nav-icon"></i>-->
                            <!--            <p>إصدار حافظة مصاريف دراسية</p>-->
                            <!--        </a>-->
                            <!--    </li>-->
                            <!--@endif-->
                            @if($has_routes or in_array('add.other.ticket',$routes))
                                <li class="nav-item">
                                    <a href="{{route('add.other.ticket')}}"
                                       class="nav-link">
                                        <i class="fas fa-file-invoice nav-icon"></i>
                                        <p>مصاريف اخرى</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('create.wallet.administrative',$routes))
                                <li class="nav-item">
                                    <a href="{{route('create.wallet.administrative')}}"
                                       class="nav-link">
                                        <i class="fas fa-file-invoice nav-icon"></i>
                                        <p> محفظة مصاريف ادارية</p>
                                    </a>
                                </li>
                            @endif
                           
                               @if($has_routes or in_array('convert.administraitve',$routes))
                                <li class="nav-item">
                                    <a href="{{route('convert.administraitve')}}"
                                       class="nav-link">
                                        <i class="fas fa-equals nav-icon"></i>
                                        <p>تحويل المصاريف الادارية</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('search.administrative',$routes))
                                <li class="nav-item">
                                    <a href="{{route('search.administrative')}}"
                                       class="nav-link">
                                        <i class="fas fa-id-card nav-icon"></i>
                                        <p>طباعة مصاريف ادارية سابقة </p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('print.student.cards.index',$routes))
                                <li class="nav-item">
                                    <a href="{{route('print.student.cards.index')}}"
                                       class="nav-link">
                                        <i class="fas fa-id-card nav-icon"></i>
                                        <p>طباعة الكارنيهات</p>
                                    </a>
                                </li>
                            @endif
                             @if($has_routes or in_array('smartId.index',$routes))
                                <li class="nav-item">
                                    <a href="{{route('smartId.index')}}" class="nav-link">
                                        <i class="fas fa-user-plus nav-icon"></i>
                                        <p> تقرير الكارنيهات</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('print.student.seating.number.cards.index',$routes))
                                <li class="nav-item">
                                    <a href="{{route('print.student.seating.number.cards.index')}}"
                                       class="nav-link">
                                        <i class="fas fa-id-card nav-icon"></i>
                                        <p>طباعة ارقام الجلوس</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('exam.place.time',$routes))
                                <li class="nav-item">
                                    <a href="{{route('exam.place.time')}}"
                                       class="nav-link">
                                        <i class="fas fa-pen nav-icon"></i>
                                        <p>لجان الامتحانات</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin','owner','student_affairs','finance']))
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>
                                التقارير
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($has_routes or in_array('student.reports',$routes))
                                <li class="nav-item">
                                    <a href="{{route('student.reports')}}" class="nav-link">
                                        <i class="fas fa-file nav-icon" aria-hidden="true"></i>
                                        <p>التقارير مجمعه</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(auth()->user()->role == 'student')
                    <li class="nav-item">
                        <a href="{{route('login.moodle.quiz')}}" target="_blank" class="nav-link">
                            <i class="fas fa-university nav-icon"></i>
                            <p>المنصة التعليمية و الرقمية</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>الخيارات
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('student.new.subjects')}}" class="nav-link">
                                    <i class="fas fa-edit nav-icon"></i>
                                    <p>تسجيل جديد</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('display.registration')}}" class="nav-link">
                                    <i class="far fa-check-circle nav-icon"></i>
                                    <p>مراجعة التسجيل</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('all.registrations')}}" class="nav-link">
                                    <i class="fas fa-scroll nav-icon"></i>
                                    <p>التسجيلات السابقة</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('show.student.data')}}" class="nav-link">
                                    <i class="fas fa-user-check nav-icon"></i>
                                    <p>مراجعة البيانات الشخصية</p>
                                </a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{route('login.moodle.quiz')}}" target="_blank" class="nav-link">-->
                            <!--        <i class="fas fa-university nav-icon"></i>-->
                            <!--        <p>الدخول الى المنصة التعليمية</p>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{route('login.moodle.book')}}" target="_blank" class="nav-link">-->
                            <!--        <i class="fas fa-book nav-icon"></i>-->
                            <!--        <p>الدخول الى المنصة الرقمية</p>-->
                            <!--    </a>-->
                            <!--</li>-->
                        </ul>
                    </li>
                @endif
                @if(auth()->user()->role == 'academic_advising')
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>الخيارات
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('registrations')}}" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>مراجعة الطلاب</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('aa.student.alerts')}}"
                                   class="nav-link">
                                    <i class="fas fa-bell nav-icon"></i>
                                    <p>تنبيه الطلاب</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('student.register')}}"
                                   class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon"></i>
                                    <p>تسجيل المواد لطالب</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('getRegistration')}}"
                                   class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon"></i>
                                    <p>تقرير التسجيلات </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['finance','owner']))
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-money-check-alt"></i>
                            <p>خيارات الماليه
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($has_routes or in_array('registrations.payments',$routes))
                                <li class="nav-item">
                                    <a href="{{route('registrations.payments')}}" class="nav-link">
                                        <i class="fas fa-list nav-icon"></i>
                                        <p>مراجعة الطلاب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('pay.ticket',$routes))
                                <li class="nav-item">
                                    <a href="{{route('pay.ticket')}}" class="nav-link">
                                        <i class="fas fa-money-bill nav-icon"></i>
                                        <p>سداد حافظة</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('create.pay.administrative.expenses',$routes))
                                <li class="nav-item">
                                    <a href="{{route('create.pay.administrative.expenses')}}" class="nav-link">
                                        <i class="fas fa-money-bill nav-icon"></i>
                                        <p>سداد مصاريف ادارية </p>
                                    </a>
                                </li>
                            @endif
                             @if($has_routes or in_array('dministrative.expenses.discount',$routes))
                                <li class="nav-item">
                                    <a href="{{route('dministrative.expenses.discount')}}" class="nav-link">
                                        <i class="fas fa-money-bill nav-icon"></i>
                                        <p>دفع مصاريف  ادارية من  المحفظة</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('daily.payments',$routes))
                                <li class="nav-item">
                                    <a href="{{route('daily.payments')}}" class="nav-link">
                                        <i class="fas fa-cash-register nav-icon"></i>
                                        <p>مراجعة اليوميات</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('finance.reports',$routes))
                                <li class="nav-item">
                                    <a href="{{route('finance.reports')}}" class="nav-link">
                                        <i class="fas fa-file nav-icon"></i>
                                        <p>تقارير</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('finance.student.status',$routes))
                                <li class="nav-item">
                                    <a href="{{route('finance.student.status')}}" class="nav-link">
                                        <i class="fas fa-info-circle nav-icon"></i>
                                        <p>بيان حالة الطالب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('f.student.alerts',$routes))
                                <li class="nav-item">
                                    <a href="{{route('f.student.alerts')}}"
                                       class="nav-link">
                                        <i class="fas fa-bell nav-icon"></i>
                                        <p>تنبيه الطلاب</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('discount.index',$routes))
                                <li class="nav-item">
                                    <a href="{{route('discount.index')}}"
                                       class="nav-link">
                                        <i class="fas fa-percent nav-icon"></i>
                                        <p>الخصومات المالية</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin','owner']))
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                إعدادات الإرشاد الأكاديمي
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('admin.registrations')}}" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>مراجعة التسجيلات</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.student.register')}}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon"></i>
                                    <p>تسجيل لطالب</p>
                                </a>
                            </li>
                            <!-- <li class="nav-item">-->
                            <!--    <a href="{{route('getRegistration')}}" class="nav-link">-->
                            <!--        <i class="fas fa-plus-circle nav-icon"></i>-->
                            <!--        <p> تقرير التسجيلات</p>-->
                            <!--    </a>-->
                            <!--</li>-->
                        </ul>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin','owner','control']))
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-poll"></i>
                            <p>
                                إعدادات الكنترول
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($has_routes or in_array('control.config',$routes))
                                <li class="nav-item">
                                    <a href="{{route('control.config')}}" class="nav-link">
                                        <i class="fas fa-cogs nav-icon"></i>
                                        <p>Configuration</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('control.uploads',$routes))
                                <li class="nav-item">
                                    <a href="{{route('control.uploads')}}" class="nav-link">
                                        <i class="fas fa-upload nav-icon"></i>
                                        <p>رفع النتائج</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('control.report',$routes))
                                <li class="nav-item">
                                    <a href="{{route('control.report')}}" class="nav-link">
                                        <i class="fas fa-download nav-icon"></i>
                                        <p>طباعة النتائج</p>
                                    </a>
                                </li>
                            @endif
                            @if($has_routes or in_array('edit.results.index',$routes))
                                <li class="nav-item">
                                    <a href="{{route('edit.results.index')}}" class="nav-link">
                                        <i class="fas fa-edit nav-icon"></i>
                                        <p>لجنة فرقة</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            الإعدادات
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('change.password')}}" class="nav-link">
                                <i class="fas fa-key nav-icon"></i>
                                <p>تغير كلمة السر</p>
                            </a>
                        </li>
                        @if(auth()->user()->role != 'student')
                            <li class="nav-item">
                                <a href="{{route('change.data')}}" class="nav-link">
                                    <i class="fas fa-user-edit nav-icon"></i>
                                    <p>تغير البيانات</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{route('logout')}}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            تسجيل الخروج
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>





<style>
   p{
        color:white !important;
    }
</style>






