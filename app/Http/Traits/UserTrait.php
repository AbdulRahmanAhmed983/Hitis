<?php

namespace App\Http\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait UserTrait
{
    public function loginInfo($request): void
    {
        Auth::user()->last_signin = date('Y-m-d H:i:s');
        Auth::user()->last_session = session()->getId();
        Auth::user()->save();
        $data = DB::table('track')->select()->where('username', Auth::id())
            ->where('ip', $request->ip())->exists();
        if (!$data) {
            DB::table('track')->insert([
                'username' => Auth::id(),
                'time' => Auth::user()->last_signin,
                'ip' => $request->ip(),
            ]);
        }
//        dd(json_decode( file_get_contents('http://ip-get-geolocation.com/api/json/197.55.65.17'), true));
//        dd(json_decode( file_get_contents('https://freegeoip.app/json/197.55.65.17'), true));
    }

    public function logout($request, $message = null)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withErrors($message);
    }

    public function checkUniqueUser($username, $col, $value, $del = false): bool
    {
        $output = false;
        if (User::select('username')->where($col, $value)->exists()) {
            if (User::select('username')->where($col, $value)->first()->username != $username) {
                $output = true;
            }
        }
        if ($del and DB::table('deleted_users')->where($col, $value)->exists()) {
            $output |= true;
        }
        return $output;
    }

    public function canUserGoRoute($username, $role, $route_name): bool
    {
        if (in_array($role, ['student', 'admin', 'owner', 'academic_advising', 'chairman'])) {
            return true;
        }
        return DB::table('has_permissions')->where('username', $username)
            ->join('permissions', 'permissions.action', '=', 'has_permissions.action')
            ->where('route', $route_name)->where('role', $role)->exists();
    }

    public function getUserPermissions($username)
    {
        $user = User::find($username);
        $actions = DB::table('permissions')->distinct()->where('role', $user->role)
            ->select('action')->get()->sortBy('action', SORT_NUMERIC)
            ->pluck('action')->toArray();
        $arr = [];
        foreach ($actions as $action) {
            if (DB::table('has_permissions')->where('username', $user->username)
                ->where('action', $action)->exists()) {
                $arr[$action] = true;
            } else {
                $arr[$action] = false;
            }
        }
        return [$actions, $arr];
    }

    public function getUserRoutes($username): array
    {
        return DB::table('has_permissions')->where('username', $username)
            ->join('permissions', 'has_permissions.action', '=', 'permissions.action')
            ->select('route')->distinct()->pluck('route')->toArray();
    }
}
