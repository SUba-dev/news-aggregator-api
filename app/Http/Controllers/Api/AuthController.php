<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(AuthService $userService)
    {
        $this->userService = $userService;
    }


    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->userService->registerUser($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'User Registerd Successfully.',
                'token' => $user->createToken('api-token')->plainTextToken,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    public function login(LoginRequest $request)
    {
        $user = $this->userService->login($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully.',
            'token' => $user->createToken('api-token')->plainTextToken,
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['status' => true, 'message' => 'Logged out successfully'], 200);
    }


    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $data = $request->validated();
        $this->userService->forgotPassword($data['email']);

        return response()->json([
            'status' => true,
            'message' => 'Your forget password request sent via email. Please check your email.',
        ], Response::HTTP_CREATED);
    }



    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->userService->resetPwd($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'User password successful reset.',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
