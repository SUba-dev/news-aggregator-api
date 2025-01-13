<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthService
{
    

    /**
     * Create User
     * @param array $data
     * @return User
     */
    public function registerUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }


    /**
     * Login User
     * @param array $data
     * @return User
     */
    public function login(array $data)
    {
        if(!Auth::attempt($data)){
            throw new CustomException('Invalid credentials', 401);
        }
        $user = User::where('email', $data['email'])->first();

        // if (!$user || !Hash::check($data['password'], $user->password)) {
        //     throw new CustomException('Invalid credentials', 401);
        // }
        $user->tokens()->delete();
        return $user;
    }


    /**
     * Logout User
     * @param User $User
     * @return bool
     */
    public function logout($user)
    {
        return $user->tokens()->delete();
    }

    /**
     * Forget Password User
     * @param string $email
     * @return User
     */
    public function forgotPassword(string $email)
    {
            $user = User::where('email', $email)->first();

            if (!$user) {
                throw new CustomException('User not found', 401);
            }

            $token = Str::random(60);

            return $user->forceFill([
                'remember_token' => $token,
            ])->save();

            // Need to handle the forget pwd email service. 
    }


    /**
     * Reset Password User
     * @param array $data
     * @return User
     */
    public function resetPassword(array $data)
    {
            $user = User::where('remember_token', $data['token'])->first();

            if (!$user) {
                throw new CustomException('Invalid token', 401);
            }

           return $user->forceFill([
                'password' => Hash::make($data['password']),
                'remember_token' => null,
            ])->save();
    }


    public function resetPwd(array $data)
    {
        try {
            $status = Password::sendResetLink($data);

            if ($status !== Password::RESET_LINK_SENT) {
                throw new CustomException(__($status), 400);
            }

            return response()->json([
                'message' => __($status),
            ]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to reset password: ' . $e->getMessage(), 500);
        }
    }
}
