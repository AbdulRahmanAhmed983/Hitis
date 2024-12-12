<?php

namespace App\Http\Controllers;

use App\Http\Traits\StudentTrait;
use App\Http\Traits\UserTrait;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use StudentTrait, UserTrait {
        logout as traitLogout;
    }

    public function loginIndex()
    {
        return view('user.login');
    }

    public function login(Request $request)
    {
        $executed = RateLimiter::attempt($request->ip(), 5, function () {
        }, 120);
        if (!$executed) {
            $seconds = RateLimiter::availableIn($request->ip());
            return redirect()->back()->withErrors('برجاء المحاوله مره أخرى بعد ' . $seconds . ' ثانية');
        }
        $rules = [
            'username' => 'required|string|max:20',
            'password' => 'required'
        ];
        $credentials = $request->validate($rules);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $this->loginInfo($request);
            RateLimiter::clear($request->ip());
            if (auth()->user()->role == 'chairman') {
                session()->flash('login', true);
                return redirect()->route('show.semester.registration');
            }
            if (auth()->user()->role == 'student') {
                $status = $this->checkStudentClassification(auth()->id());
                if ($status == 0) {
                    return $this->traitLogout($request, 'لا يمكنك تسجيل الدخول');
                }
            }
            return redirect()->intended();
        }
        return redirect()->back()->withErrors(['خطأ في اسم المستخدم أو كلمة مرور',
            'بعد ' . RateLimiter::remaining($request->ip(), 5) . ' محاولات أخرى سيتم قفل تسجيل الدخول لمدة 120 ثانية']);
    }

    public function logout(Request $request)
    {
        return $this->traitLogout($request);
    }

    public function dashboard()
    {
        $notifications = DB::table('notifications')->where('username', Auth::id())->get()->toArray();
        return view('user.dashboard', compact('notifications'));
    }

    public function changePasswordIndex()
    {
        return view('user.change_password');
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'current_password' => ['required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->getAuthPassword())) {
                        $fail('كلمة السر غير صحيحة');
                    }
                },],
            'new_password' => 'required|string|between:8,16
            |regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@_#$%\/*-+?.])[A-Za-z\d@_#$%\/*-+?.]{8,16}$/u',
            'confirm_password' => 'same:new_password',
        ];
        $data = $request->validate($rules);
        try {
            DB::transaction(function () use ($data) {
                if (Auth::user()->role == 'student') {
                    Student::find(Auth::id())->update(['password' => $data['new_password']]);
                    $response = $this->updateMoodlePassword(Auth::id(), $data['new_password']);
                    $response_book = $this->updateMoodleBookPassword(Auth::id(), $data['new_password']);
                    if ($response == 'error') {
                        abort(500);
                    }                }
                Auth::user()->password = Hash::make($data['new_password']);
                Auth::user()->password_status = 1;
                Auth::user()->save();
            });
            if (Auth::user()->role == 'student') {
                return redirect()->back()->with([
                    'success' => 'تم تغيير كلمة السر بنجاح فى جميع المنصات',
                    'warning' => 'شؤون الطلاب لها الحق في رؤية كلمة المرور الخاصة بك'
                ]);
            }
            return redirect()->back()->with('success', 'تم تغيير كلمة السر بنجاح');
        } catch (Exception $e) {
            dd($e);
            if (Auth::user()->role == 'student') {
                $this->updateMoodlePassword(Auth::id(),$data['current_password']);
                $this->updateMoodleBookPassword(Auth::id(),$data['current_password']);
            }
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function changeDataIndex()
    {
        $user = auth()->user()->getOriginal();
        return view('user.change_data', compact('user'));
    }

    public function changeData(Request $request)
    {
        $rules = [
            'name' => 'required|string|regex:/^[\x{0621}-\x{064A}a-zA-Z\/-_() ]+$/u',
            'mobile' => ['required', 'digits:11', 'regex:/^(01)[0125]/',
                function ($attribute, $value, $fail) {
                    if ($this->checkUniqueUser(Auth::id(), $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
            'email' => ['nullable', 'email:rfc,dns', 'regex:/^[^\x{0621}-\x{064A}٠-٩ ]+$/u',
                function ($attribute, $value, $fail) {
                    if ($this->checkUniqueUser(Auth::id(), $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
        ];
        $data = $request->validate($rules);
        $data['updated_by'] = null;
        try {
            if ($data['email'] != Auth::user()->email) {
                DB::transaction(function () use ($request, $data) {
                    unset($data['email']);
                    $code = Str::random(6);
                    $validator = Validator::make(['code' => $code], [
                        'code' => 'unique:email_verification,code',
                    ]);
                    while ($validator->fails()) {
                        $code = Str::random(6);
                        $validator = Validator::make(['code' => $code], [
                            'code' => 'unique:email_verification,code',
                        ]);
                    }
                    if (DB::table('email_verification')->where('username', Auth::id())->exists()) {
                        DB::table('email_verification')->where('username', Auth::id())
                            ->update([
                                'email' => $request->email,
                                'code' => $code,
                                'created_at' => Carbon::now()
                            ]);
                    } else {
                        DB::table('email_verification')->insert([
                            'username' => Auth::id(),
                            'email' => $request->email,
                            'code' => $code,
                            'created_at' => Carbon::now()
                        ]);
                    }
                    Mail::send('email.email_verification', ['code' => $code], function ($message) use ($request) {
                        $message->to($request->email);
                        $message->subject('Email Verification');
                    });
                    User::find(Auth::id())->update($data);
                    if (Auth::user()->role == 'student') {
                        Student::find(Auth::id())->update($data);
                    }
                    if (Auth::user()->role == 'academic_advising') {
                        DB::table('academic_advisors')->where('username', Auth::id())->update([
                            'name' => $data['name']
                        ]);
                    }
                });
                return redirect()->back()->with([
                    'success' => 'تم تغيير البيانات بنجاح<br>لقد أرسلنا إليك رمز التحقق عبر البريد الإلكتروني!',
                    'email_code' => true,
                ]);
            } else {
                DB::transaction(function () use ($data) {
                    User::find(Auth::id())->update($data);
                    if (Auth::user()->role == 'student') {
                        Student::find(Auth::id())->update($data);
                    }
                    if (Auth::user()->role == 'academic_advising') {
                        DB::table('academic_advisors')->where('username', Auth::id())->update([
                            'name' => $data['name']
                        ]);
                    }
                });
                return redirect()->back()->with('success', 'تم تغيير البيانات بنجاح');
            }
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'خطأ في الإتصال');
        }
    }

    public function emailConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:6',
                function ($attribute, $value, $fail) {
                    if (!DB::table('email_verification')->where('username', auth()->id())
                        ->where('code', $value)->exists())
                        $fail('');
                },
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with([
                'email_code' => true,
                'error' => 'رمز التحقق غير صحيح'
            ]);
        }
        try {
            DB::transaction(function () use ($request) {
                $data = DB::table('email_verification')->where('code', $request->code)->first();
                Auth::user()->email = $data->email;
                Auth::user()->save();
                DB::table('email_verification')->where('code', $request->code)->delete();
            });
            return redirect()->back()->with('success', 'تم تغيير البريد الإلكتروني');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function showForgetPasswordForm()
    {
        return view('user.forget_password');
    }

    public function submitForgetPasswordForm(Request $request)
    {
        $executed = RateLimiter::attempt($request->ip(), 5, function () {
        }, 300);
        if (!$executed) {
            $seconds = RateLimiter::availableIn($request->ip());
            return redirect()->back()->withErrors('برجاء المحاوله مره أخرى بعد ' . $seconds . ' ثانية');
        }
        $request->validate([
            'email' => 'required|email:rfc,dns|exists:users,email',
        ]);
        $token = Str::random(64);
        $validator = Validator::make(['token' => $token], [
            'token' => 'unique:password_resets,token',
        ]);
        while ($validator->fails()) {
            $token = Str::random(64);
            $validator = Validator::make(['token' => $token], [
                'token' => 'unique:password_resets,token',
            ]);
        }
        try {
            DB::transaction(function () use ($token, $request) {
                $username = User::where('email', $request->email)->select('username')->first()->username;
                if (DB::table('password_resets')->where('username', $username)->exists()) {
                    DB::table('password_resets')->where('username', $username)
                        ->update([
                            'email' => $request->email,
                            'token' => $token,
                            'created_at' => Carbon::now()
                        ]);
                } else {
                    DB::table('password_resets')->insert([
                        'username' => $username,
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => Carbon::now()
                    ]);
                }
                Mail::send('email.forget_password', ['token' => $token], function ($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Reset Password');
                });
            });
            return back()->with('success', 'لقد أرسلنا رابط إعادة تعيين كلمة المرور بالبريد الإلكتروني!');
        } catch (Exception $e) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function showResetPasswordForm($token)
    {
        $validator = Validator::make(['token' => $token], [
            'token' => 'string|exists:password_resets,token',
        ]);
        if ($validator->fails()) {
            abort(404);
        }
        $created_at = DB::table('password_resets')->where('token', $token)->first();
        $now = Carbon::now();
        if ($now->diffInMinutes($created_at->created_at) > 90) {
            DB::table('password_resets')->where((array)$created_at)->delete();
            return redirect()->route('forget.password.get')->withErrors('برجاء المحاوله مره اخرى');
        }
        return view('user.forget_password_link', ['token' => $token]);
    }

    public function submitResetPasswordForm($token, Request $request)
    {
        $data = $request->validate([
            'new_password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@_#$%\/*-+?.])[A-Za-z\d@_#$%\/*-+?.]{8,}$/u',
            'confirm_password' => 'same:new_password',
        ]);
        $validator = Validator::make(['token' => $token], [
            'token' => 'string|exists:password_resets,token',
        ]);
        if ($validator->fails()) {
            abort(404);
        }
        try {
            $updatePassword = DB::table('password_resets')->where(['token' => $request->token])->first();
            $user = User::find($updatePassword->username);
            DB::transaction(function () use ($user, $data, $request) {
                if ($user->password_status == 0) {
                    $user->password_status = 1;
                }
                if ($user->role == 'student') {
                    Student::find($user->username)->update(['password' => $data['new_password']]);
                }
                $user->password = Hash::make($data['new_password']);
                $user->save();
                DB::table('password_resets')->where('token', $request->token)->delete();
            });
            if ($user->role == 'student') {
                return redirect()->route('login')->with([
                    'success' => 'تم تغيير كلمة السر بنجاح',
                    'warning' => 'شؤون الطلاب لها الحق في رؤية كلمة المرور الخاصة بك'
                ]);
            }
            return redirect()->route('login')->with('success', 'تم تغيير كلمة السر الخاصة بك!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function removeNotification(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'exists:notifications,id',
                    function ($attribute, $value, $fail) {
                        if (!DB::table('notifications')->where('id', $value)
                            ->where('username', Auth::id())->exists())
                            $fail('');
                    },
                ]
            ]);
            if ($validator->fails()) {
                return Response('بيانات غير صالحة', 400);

            }
            try {
                DB::table('notifications')->where('id', $request->id)
                    ->where('username', Auth::id())->delete();
                return Response('تم إستلام الإشعار', 200);
            } catch (Exception $e) {
                return Response('خطأ في الإتصال', 400);
            }
        }
        abort(404);
        return Response('no data found', 404);
    }
}
