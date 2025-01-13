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
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     description="Registers a new user with the provided credentials.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="suba@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123") 
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User Registered Successfully."),
     *             @OA\Property(property="token", type="string", example="your_generated_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors or registration failed"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User Login",
     *     description="Authenticate a user and generate an access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="suba@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User Logged In Successfully."),
     *             @OA\Property(property="token", type="string", example="your_generated_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */

    public function login(LoginRequest $request)
    {
        $user = $this->userService->login($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully.',
            'token' => $user->createToken('api-token')->plainTextToken,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User Logout",
     *     description="Logout the current user and invalidate their token.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $user->currentAccessToken()->delete(); 
        return response()->json(['status' => true, 'message' => 'Logged out successfully'], Response::HTTP_OK);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Forgot Password",
     *     description="Initiates the password reset process by sending a reset link to the user's email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="suba@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Password reset link sent successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid email address or other validation errors"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $data = $request->validated();
        $this->userService->forgotPassword($data['email']);
        // Need to handle the email service
        return response()->json([
            'status' => true,
            'message' => 'Your forget password request sent via email. Please check your email.',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset User Password",
     *     description="Resets the user's password.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="your_reset_token"),
     *             @OA\Property(property="password", type="string", example="new_password"),
     *             @OA\Property(property="password_confirmation", type="string", example="new_password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token or password mismatch"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->userService->resetPwd($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'User password successful reset.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
