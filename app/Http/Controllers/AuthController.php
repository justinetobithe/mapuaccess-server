<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(UserRequest $request)
    {
        $validated = $request->validated();

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.errors.email_exists'),
            ]);
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);


        if ($user->role === 'student') {
            Student::create([
                'user_id' => $user->id,
                'student_no' =>  $validated['student_no'],
            ]);
        }

        if ($user->role === 'employee') {
            Employee::create([
                'user_id' => $user->id,
                'employee_no' => $validated['employee_no'],
            ]);
        }


        return response()->json([
            'status' => true,
            'message' => __('messages.success.registered'),
            'user' => new UserResource($user),
        ]);
    }

    public function login(AuthLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::with(['student', 'employee'])->find(auth()->id());
            $token = $user->generateToken();

            $data = [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            return response()->json([
                'status' => true,
                'message' => __('messages.success.login'),
                'data' => $data,
            ])->withCookie(cookie('auth_token', $token, 60));
        }

        return response()->json([
            'status' => false,
            'message' => __('messages.invalid.credentials'),
            'data' => null
        ]);
    }

    public function mobileLogin(AuthLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::with(['student', 'employee'])->find(auth()->id());

            if ($user->role !== 'student' && $user->role !== 'employee' && $user->role !== 'guard') {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.errors.role_access_denied'),
                ]);
            }

            $token = $user->generateToken();

            $data = [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            return response()->json([
                'status' => true,
                'message' => __('messages.success.login'),
                'data' => $data,
            ])->withCookie(cookie('auth_token', $token, 60));
        }

        return response()->json([
            'status' => false,
            'message' => __('messages.invalid.credentials'),
            'data' => null
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.success.deleted'),
        ]);
    }

    public function user()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::user(),
        ]);
    }
}
