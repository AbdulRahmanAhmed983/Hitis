<?php

use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentAffairsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AcademicAdvisingController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\FormController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|,'EnsureGoogleFormSubmitted'
*/



Route::get('/delete-all-registrations', [AdminController::class, 'deleteAllRegistration'])->name('admin.Allregistrations.delete')->middleware(['auth', 'check-user:owner']);
Route::get('/getTotalValue/{ticket_id}',[FinanceController::class, 'getTotalValue'])->name('getTotalValue');

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode1 = Artisan::call('config:cache');
    $exitCode1 = Artisan::call('route:cache');

    // return what you want
    return [$exitCode,$exitCode1];
});


Route::group(['middleware' => 'prevent-back-history'], function () {
    Artisan::call('up');
    Route::get('/test', function (\Illuminate\Http\Request $request) {
        abort(404);
//        Artisan::call('database:backup');
        dd('done');
    })->name('test')->middleware(['auth', 'check-user:owner']);
    Route::group(['prefix' => '/', 'middleware' => ['check.login']], function () {
        Route::get('/', [UserController::class, 'loginIndex'])->name('login');
        Route::post('/login', [UserController::class, 'login'])->name('login.check');
        Route::get('forget-password', [UserController::class, 'showForgetPasswordForm'])->name('forget.password.get');
        Route::post('forget-password-mail', [UserController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
        Route::get('reset-password/{token}', [UserController::class, 'showResetPasswordForm'])->name('reset.password.get');
        Route::post('reset-password/{token}/reset', [UserController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    });
    Route::group(['prefix' => '/', 'middleware' => ['rate.limit.request', 'auth', 'check-user:*', 'last.session']], function () {
        Route::get('/logout', [UserController::class, 'logout'])->name('logout');
        Route::get('/change-password', [UserController::class, 'changePasswordIndex'])->name('change.password');
        Route::put('/update-password', [UserController::class, 'changePassword'])->name('update.password');
    });
    Route::group(['prefix' => '/', 'middleware' => ['rate.limit.request', 'auth', 'check-status', 'last.session']], function () {
        Route::group(['prefix' => '/', 'middleware' => ['check-user:*']], function () {
            Route::get('/main', [UserController::class, 'dashboard'])->name('dashboard');
            Route::group(['prefix' => '/', 'middleware' => ['check-user:*;student']], function () {
                Route::get('/change-data', [UserController::class, 'changeDataIndex'])->name('change.data');
                Route::put('/update-data', [UserController::class, 'changeData'])->name('update.data');
                Route::put('/update-email', [UserController::class, 'emailConfirmation'])->name('update.email');
            });
            Route::delete('/remove-notification', [UserController::class, 'removeNotification'])->name('remove.notification');
        });
        Route::group(['prefix' => '/admin', 'middleware' => ['check-user:owner;admin;chairman']], function () {
            Route::match(['get', 'post'], '/show-semester-registration', [AdminController::class, 'showSemesterRegistration'])->name('show.semester.registration');
        });
        Route::group(['prefix' => '/admin', 'middleware' => ['check-user:owner;admin']], function () {
            Route::get('/add-user', [AdminController::class, 'addNewUserIndex'])->name('add.user');
            Route::post('/insert-user', [AdminController::class, 'addNewUser'])->name('insert.user');
            Route::get('/users-list', [AdminController::class, 'usersListIndex'])->name('users.list');
            Route::get('/change-data/{username}', [AdminController::class, 'changeUserDataIndex'])->name('user.change.data');
            Route::put('/update-user-data', [AdminController::class, 'changeUserData'])->name('user.update.data');
            Route::delete('/delete-data/{username}', [AdminController::class, 'deleteUser'])->name('user.delete');
            Route::get('/student-status', [AdminController::class, 'studentStatus'])->name('student.status.admin');
            Route::get('/registrations', [AdminController::class, 'confirmationIndex'])->name('admin.registrations');
            Route::delete('/delete-registrations/{username}', [AdminController::class, 'deleteRegistration'])->name('admin.registrations.delete');
            Route::get('/registrations/{username}', [AdminController::class, 'showRegistration'])->name('admin.show.registration');
            Route::put('/confirm-registration/{username}', [AdminController::class, 'confirmRegistration'])->name('admin.confirm.registration');
            Route::get('/student-registration', [AdminController::class, 'studentRegisterIndex'])->name('admin.student.register');
            Route::post('/store-registration/{student_code}', [AdminController::class, 'storeStudentRegister'])->name('admin.store.registration');
            Route::group(['prefix' => '', 'middleware' => ['check-user:owner']], function () {
                Route::get('/configuration', [AdminController::class, 'configIndex'])->name('configuration');
                Route::post('/add-data', [AdminController::class, 'addData'])->name('add.data');
                Route::get('/key-values', [AdminController::class, 'getDataKeyValues'])->name('key.values');
                Route::put('/update-key-values', [AdminController::class, 'updateData'])->name('update.key.values');
                Route::put('/update-semester', [AdminController::class, 'updateSemester'])->name('update.semester');
                Route::put('/moodle-setting', [AdminController::class, 'moodleSetting'])->name('moodle.setting');
                Route::get('/get-courses', [AdminController::class, 'getCourses'])->name('get.courses');
                Route::put('/update-courses', [AdminController::class, 'updateCourses'])->name('update.courses');
                Route::post('/add-courses', [AdminController::class, 'addCourses'])->name('add.courses');
                Route::put('/remove-academic-advisor/{advisor}', [AdminController::class, 'removeStudentsAcademic'])->name('remove.academic.advisor');
                Route::put('/add-academic-advisor/{advisor}', [AdminController::class, 'addStudentsAcademic'])->name('add.academic.advisor');
                Route::put('/update-payment/{type}', [AdminController::class, 'updatePayment'])->name('update.payment');
                Route::put('/update-payment-remaining/{type}', [AdminController::class, 'updatePaymentRemaining'])->name('update.payment.remaining');
                Route::put('/update-ministerial-payment/{type}', [AdminController::class, 'updateMinisterialPayment'])->name('update.ministerial.payment');
                Route::put('/update-ministerial-payment-remaining/{type}', [AdminController::class, 'updateMinisterialPaymentRemaining'])->name('update.ministerial.payment.remaining');
                Route::put('/update-total-payment/{type}', [AdminController::class, 'updateTotalPayment'])->name('update.total.payment');
                Route::put('/update-total-payment-exception', [AdminController::class, 'updateTotalPaymentException'])->name('update.total.payment.exception');
                Route::put('/update-military-education', [AdminController::class, 'updateMilitaryEducation'])->name('update.military.education');
                Route::put('/update-registration-hour/{type}', [AdminController::class, 'updateRegistrationHour'])->name('update.registration.hour');
                Route::put('/update-section-number/{type}', [AdminController::class, 'updateSectionNumber'])->name('update.section.number');
                Route::put('/update-english-degree', [AdminController::class, 'updateEnglishDegree'])->name('update.english.degree');
                Route::put('/update-ministerial-receipt', [AdminController::class, 'updateMinisterialReceiptNumber'])->name('update.ministerial.receipt.number');
                Route::get('/academic-list', [AdminController::class, 'academicIndex'])->name('academic.list');
                Route::get('/edit-academic/{username}', [AdminController::class, 'editAcademicIndex'])->name('edit.academic');
                Route::put('/update-academic/{username}', [AdminController::class, 'updateAcademicNumbers'])->name('update.academic');
                Route::get('/show-students/{username}', [AdminController::class, 'showAcademicStudent'])->name('show.academic.students');
                Route::put('/warning-threshold', [AdminController::class, 'changeWarningThreshold'])->name('change.warning.threshold');
                Route::put('/student-level', [AdminController::class, 'changeStudentLevel'])->name('change.students.level');
                Route::get('/student-level-simulation', [AdminController::class, 'changeStudentLevelSimulation'])->name('simulation.students.level');
                Route::post('/delete-exception-students',[AdminController::class,'deleteExceptionStudents'])->name('delete.exception.students');
                Route::post('/update-maintenance-mode',[AdminController::class, 'updateMaintenanceMode'])->name('update.maintenance.mode');
                Route::put('/update-extra-fees', [AdminController::class, 'updateExtraFees'])->name('update.extra.fees');

                // Route::get('/getRegistration',[AdminController::class, 'getRegistration'])->name('getRegistration');
                // Route::post('/exportRegistration',[AdminController::class, 'exportRegistration'])->name('exportRegistration');


                 // Management_Expenses
                Route::put('/update-insurance', [AdminController::class, 'updateInsurancePayment'])->name('update.insurance.payment');
                Route::put('/update-profile_expenses', [AdminController::class, 'updateProfileExpenses'])->name('update.ProfileExpenses.payment');
                Route::put('/update-registration_fees', [AdminController::class, 'updateRegistrationFees'])->name('update.registration.fees');
                Route::put('/update-card_email', [AdminController::class, 'updateCardEmail'])->name('update.cardEmail.payment');
                Route::put('/update-renew_card_email', [AdminController::class, 'updateRenewCardEmail'])->name('update.renwecardEmail.payment');
                Route::put('/update-military', [AdminController::class, 'updateMilitaryPayment'])->name('update.Military.payment');
                Route::put('/update-total_expenses', [AdminController::class, 'updateTotalExpenses'])->name('update.Total.expenses');

            });
        });
         Route::group(['prefix' => '/student-affairs', 'middleware' => ['check-user:owner;admin;student_affairs']], function () {
            Route::get('/student-form', [StudentAffairsController::class, 'index'])->name('student.form');
            Route::get('/student-username', [StudentAffairsController::class, 'getLastUsername'])->name('student.username');
            Route::post('/add-student', [StudentAffairsController::class, 'addStudent'])->name('add.student');
            Route::get('/student-search', [StudentAffairsController::class, 'searchStudent'])->name('student.search');
            Route::post('/add-session', [StudentAffairsController::class, 'addSession'])->name('add.session');
            Route::post('/print-student', [StudentAffairsController::class, 'printStudent'])->name('print.student');
            Route::get('/administrative-search', [StudentAffairsController::class, 'searchAdministrative'])->name('search.administrative');
            Route::get('/student-list', [StudentAffairsController::class, 'studentList'])->name('student.list');
            Route::get('/student-transfer-datalist', [StudentAffairsController::class, 'searchTransferStudentDataList'])->name('student.transfer.datalist');
            Route::get('/student-alerts', [StudentAffairsController::class, 'studentAlertIndex'])->name('student.alerts');
            Route::post('/add-alert', [StudentAffairsController::class, 'studentAlert'])->name('student.add.alert');
            Route::delete('/delete-alert/{student_code}', [StudentAffairsController::class, 'deleteStudentAlert'])->name('student.delete.alert');
            Route::get('/student-transfer-courses', [StudentAffairsController::class, 'addCoursesToTransferIndex'])->name('add.courses.transfer');
            Route::post('/add-transfer-courses', [StudentAffairsController::class, 'addCoursesToTransfer'])->name('store.courses.transfer');
            Route::post('/print-status', [StudentAffairsController::class, 'printStatus'])->name('print.status');
            Route::get('/student-excuses', [StudentAffairsController::class, 'addExcuseIndex'])->name('add.excuses.index');
            Route::get('/student-registered-courses', [StudentAffairsController::class, 'getStudentRegisteredCourses'])->name('get.registered.courses');
            Route::post('/add-student-excuses', [StudentAffairsController::class, 'addExcuse'])->name('add.excuses');
            Route::delete('/delete-student-excuses/{student_code}/{year}/{semester}', [StudentAffairsController::class, 'deleteStudentExcuse'])->name('delete.excuses');
            Route::get('/create-ticket', [StudentAffairsController::class, 'createTicket'])->name('create.ticket');
            Route::post('/store-ticket', [StudentAffairsController::class, 'storeTicket'])->name('store.ticket');
            Route::delete('/delete-ticket/{ticket_id}', [StudentAffairsController::class, 'deleteTicket'])->name('delete.ticket');
            Route::get('/create-wallet-ticket', [StudentAffairsController::class, 'createWalletTicket'])->name('create.wallet.ticket');
            Route::post('/store-wallet-ticket', [StudentAffairsController::class, 'storeWalletTicket'])->name('store.wallet.ticket');
            Route::delete('/delete-wallet-ticket/{student_code}/{ticket_id}', [StudentAffairsController::class, 'deleteWalletTicket'])->name('delete.wallet.ticket');
            Route::get('/add-other-ticket', [StudentAffairsController::class, 'addOtherPayment'])->name('add.other.ticket');
            Route::post('/store-other-ticket', [StudentAffairsController::class, 'storeOtherTicket'])->name('store.other.ticket');
            Route::post('/store-other-payment', [StudentAffairsController::class, 'storeOtherPayment'])->name('store.other.payment');
            Route::delete('/delete-other-payment/{id}', [StudentAffairsController::class, 'deleteOtherPayment'])->name('delete.other.payment');
            Route::get('/print-cards', [StudentAffairsController::class, 'printStudentCardsIndex'])->name('print.student.cards.index');
            Route::post('/print-students-card', [StudentAffairsController::class, 'printStudentCards'])->name('print.student.cards');
            Route::get('/check-students-card-number', [StudentAffairsController::class, 'checkStudentCards'])->name('check.student.cards.number');
            Route::get('/print-seating-number-cards', [StudentAffairsController::class, 'printStudentSeatingNumberCardsIndex'])->name('print.student.seating.number.cards.index');
            Route::post('/print-students-seating-number-card', [StudentAffairsController::class, 'printStudentSeatingNumberCards'])->name('print.student.seating.number.cards');
            Route::get('/check-students-seating-number-card-number', [StudentAffairsController::class, 'checkStudentSeatingNumberCards'])->name('check.student.seating.number.cards.number');
            Route::get('/change-student-data/{username}', [StudentAffairsController::class, 'changeStudentDataIndex'])->name('student.change.data');
            Route::put('/update-student-data/{username}', [StudentAffairsController::class, 'updateStudentData'])->name('student.update.data');
            Route::delete('/delete-student/{username}', [StudentAffairsController::class, 'deleteStudent'])->name('student.delete');
            Route::get('/exam-place-time', [StudentAffairsController::class, 'examPlaceAndTimeIndex'])->name('exam.place.time');
            Route::put('/update-exam-places', [StudentAffairsController::class, 'updateExamPlaces'])->name('update.exam.places');
            Route::put('/update-exam-time', [StudentAffairsController::class, 'updateExamTime'])->name('update.exam.time');
            Route::get('/smart-id', [StudentAffairsController::class, 'smartIdIndex'])->name('smartId.index');
            Route::post('/smartId-report', [StudentAffairsController::class, 'smartIdReport'])->name('smartId.report');
            Route::post('/upload-smartId-report', [StudentAffairsController::class, 'uploadSmartIdReport'])->name('uploadSmartId.report');
            Route::get('/create-wallet-administrative-expenses',[StudentAffairsController::class, 'createAdministrativeExpenses'])->name('create.wallet.administrative');
            Route::post('/store-wallet-administrative-expenses',[StudentAffairsController::class, 'storeAdministrativeExpenses'])->name('store.wallet.administrative');
            Route::get('/convert-administrative-expenses',[StudentAffairsController::class, 'convertAdministraitve'])->name('convert.administraitve');
            Route::post('/store-converted-administrative-expenses',[StudentAffairsController::class, 'storeconvertedAdministraitve'])->name('store.converted.administraitve');
            Route::post('/import-convert-administrative-expenses',[StudentAffairsController::class, 'importAdministraitve'])->name('import.administraitve');
            Route::post('/store-wallet-extra-fees',[StudentAffairsController::class, 'storeExtraFees'])->name('store.extra.fees');
            Route::get('/get-amount', [StudentAffairsController::class, 'getAmountExtraFees'])->name('getAmountExtraFees');;
        });
        Route::group(['prefix' => '/reports', 'middleware' => ['auth', 'check-status', 'check-user:owner;admin;student_affairs;finance']], function () {
            Route::get('/student-reports', [ReportsController::class, 'reportsIndex'])->name('student.reports');
            Route::post('/enlistment-report', [ReportsController::class, 'enlistmentReport'])->name('enlistment.report');
            Route::post('/study-report', [ReportsController::class, 'studyReport'])->name('study.report');
            Route::post('/finance-report', [ReportsController::class, 'financeReport'])->name('finance.report');
            Route::post('/seating-number-report', [ReportsController::class, 'seatingNumberReport'])->name('seating.number.report');
            Route::post('/student-warning-report', [ReportsController::class, 'studentWarningReport'])->name('student.warning.report');
            Route::post('/paying-students-subject-report', [ReportsController::class, 'payingStudentsSubjectReport'])->name('paying.students.subject.report');
            Route::post('/registered-students-subject-report', [ReportsController::class, 'registeredStudentsSubjectReport'])->name('registered.students.subject.report');
            Route::post('/unregistered-students-subject-report', [ReportsController::class, 'unregisteredStudentsSubjectReport'])->name('unregistered.students.subject.report');
            Route::post('/exportRegistration',[ReportsController::class, 'exportRegistration'])->name('exportRegistrationReport');

        });
        Route::group(['prefix' => '/student', 'middleware' => ['check-user:student', 'student.checks','CheckMaintenanceMode']], function () {
            Route::get('/subjects-registration', [StudentController::class, 'subjectsRegistrationIndex'])->name('student.new.subjects');
            Route::post('/registration', [StudentController::class, 'subjectRegistration'])->name('subjects.registration');
            Route::get('/show-registration', [StudentController::class, 'showRegistrationIndex'])->name('display.registration');
            Route::delete('/delete-registration', [StudentController::class, 'deleteRegistration'])->name('delete.registration');
            Route::post('/print-registration', [StudentController::class, 'printRegistration'])->name('print.registration');
            Route::get('/all-registrations', [StudentController::class, 'studentTranscript'])->name('all.registrations')->withoutMiddleware('student.checks');
            Route::get('/login-moodle-quiz', [StudentController::class, 'loginToMoodleQuiz'])->name('login.moodle.quiz');
            Route::get('/login-moodle-book', [StudentController::class, 'loginToMoodleBook'])->name('login.moodle.book');
            Route::get('/my-data', [StudentController::class, 'showStudentData'])->name('show.student.data');
        });
        Route::group(['prefix' => '/academic', 'middleware' => ['check-user:academic_advising']], function () {
            Route::get('/registrations', [AcademicAdvisingController::class, 'confirmationIndex'])->name('registrations');
            Route::delete('/delete-registrations/{username}', [AcademicAdvisingController::class, 'deleteRegistration'])->name('registrations.delete');
            Route::get('/registrations/{username}', [AcademicAdvisingController::class, 'showRegistration'])->name('show.registration');
            Route::put('/confirm-registration/{username}', [AcademicAdvisingController::class, 'confirmRegistration'])->name('confirm.registration');
            Route::get('/student-alerts', [AcademicAdvisingController::class, 'studentAlertIndex'])->name('aa.student.alerts');
            Route::post('/add-alert', [AcademicAdvisingController::class, 'studentAlert'])->name('aa.student.add.alert');
            Route::delete('/delete-alert/{student_code}', [AcademicAdvisingController::class, 'deleteStudentAlert'])->name('aa.student.delete.alert');
            Route::get('/student-registration', [AcademicAdvisingController::class, 'studentRegisterIndex'])->name('student.register');
            Route::post('/store-registration/{student_code}', [AcademicAdvisingController::class, 'storeStudentRegister'])->name('store.registration');
             Route::get('/getRegistration',[AcademicAdvisingController::class, 'getRegistration'])->name('getRegistration');
                Route::post('/exportRegistration',[AcademicAdvisingController::class, 'exportRegistration'])->name('exportRegistration');
        });
        Route::group(['prefix' => '/finance', 'middleware' => ['check-user:owner;finance']], function () {
            Route::get('/payments', [FinanceController::class, 'paymentList'])->name('registrations.payments');
            Route::get('/pay-ticket', [FinanceController::class, 'payTicketIndex'])->name('pay.ticket');
            Route::put('/pay', [FinanceController::class, 'payTicket'])->name('pay');
            Route::get('/show-tickets/{student_code}', [FinanceController::class, 'showTickets'])->name('show.tickets');
            Route::get('/print-receipt/{username}/{ticket_id}', [FinanceController::class, 'printReceipt'])->name('print.receipt');
            Route::get('/student-alerts', [FinanceController::class, 'studentAlertIndex'])->name('f.student.alerts');
            Route::post('/add-alert', [FinanceController::class, 'studentAlert'])->name('f.student.add.alert');
            Route::delete('/delete-alert/{student_code}', [FinanceController::class, 'deleteStudentAlert'])->name('f.student.delete.alert');
            Route::get('/daily-payments', [FinanceController::class, 'dailyPaymentIndex'])->name('daily.payments');
            Route::post('/set-daily-payments', [FinanceController::class, 'setDailyPaymentDateTime'])->name('set.daily.payments');
            Route::get('/reports', [FinanceController::class, 'reportIndex'])->name('finance.reports');
            Route::post('/daily-payments-reports/{type}', [FinanceController::class, 'dailyPaymentsReport'])->name('daily.payments.report');
            Route::get('/student-discounts', [FinanceController::class, 'discountIndex'])->name('discount.index');
            Route::get('/get-student-discounts', [FinanceController::class, 'getUnpaidPayments'])->name('get.discount');
            Route::post('/add-student-discounts', [FinanceController::class, 'storeDiscount'])->name('add.discount');
            Route::delete('/delete-student-discounts/{student_code}/{id}', [FinanceController::class, 'deleteDiscount'])->name('delete.discount');
            Route::get('/create-pay-administrative-expenses',[FinanceController::class, 'createpayAdministrativeExpenses'])->name('create.pay.administrative.expenses');
            Route::put('/pay-administrative-expenses',[FinanceController::class, 'payAdministrativeExpenses'])->name('pay.dministrative.expenses');
            Route::get('/administrative-expenses-discount',[FinanceController::class, 'AdministrativeExpensesDiscount'])->name('dministrative.expenses.discount');
            Route::put('/pay-administrative-expenses-discount',[FinanceController::class, 'payAdministrativeExpensesDiscount'])->name('pay.dministrative.expenses.discount');
             Route::get('/edit-type-payments/{ticket_id}', [FinanceController::class, 'editTypePayment'])->name('edit.type.payments');
            Route::post('/update-type-payments/{ticket_id}', [FinanceController::class, 'updateTypePayment'])->name('update.type.payments');
            Route::put('/pay-extra-fees',[FinanceController::class, 'payExtraFees'])->name('pay.extra.fees');

        });
        Route::group(['prefix' => '/student-data', 'middleware' => ['check-user:owner;admin;finance;student_affairs;academic_advising']], function () {
            Route::get('/student-datalist', [StudentAffairsController::class, 'searchStudentDataList'])->name('student.datalist');
            Route::group(['prefix' => '/', 'middleware' => ['check-user:owner;admin;student_affairs;academic_advising']], function () {
                Route::get('/student-status', [StudentAffairsController::class, 'studentStatus'])->name('student.status');
            });
            Route::group(['prefix' => '', 'middleware' => ['check-user:owner;admin;finance;student_affairs']], function () {
                Route::get('/student-finance-status', [FinanceController::class, 'studentStatus'])->name('finance.student.status');
            });
        });
        Route::group(['prefix' => '/control', 'middleware' => ['check-user:owner;admin;control']], function () {
            Route::get('/upload_results', [ControlController::class, 'uploadResultsIndex'])->name('control.uploads');
            Route::post('/upload_result', [ControlController::class, 'uploadResults'])->name('control.upload.result');
            Route::get('/config', [ControlController::class, 'configIndex'])->name('control.config');
            Route::post('/sitting-numbers-excel', [ControlController::class, 'seatingNumbersExcel'])->name('sitting.numbers.excel');
            Route::get('/reports', [ControlController::class, 'reportIndex'])->name('control.report');
            Route::get('/check-print-number', [ControlController::class, 'checkGradeNumber'])->name('check.print.number');
            Route::post('/print-grades-report', [ControlController::class, 'printStudentsGrade'])->name('print.grades.report');
            Route::post('/print-grades-report2', [ControlController::class, 'printStudentsGrade2'])->name('print.grades.report2');
            Route::post('/print-grades-summer-report', [ControlController::class, 'printSummerGrade'])->name('print.grades.summer.report');
            Route::get('/edit_results', [ControlController::class, 'editResultsIndex'])->name('edit.results.index');
            Route::put('/edit_result', [ControlController::class, 'editResults'])->name('edit.results');
        });
    });
});
