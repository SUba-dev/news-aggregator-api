<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\User;
use Mockery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Exceptions\CustomException;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class AuthServiceTest extends TestCase
{
    // use RefreshDatabase;

    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    /** @test 
     * User register
     */
    public function test_register_user()
    {
        $faker = Faker::create();
        $data = [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => 'password123',
        ];

        $user = $this->authService->registerUser($data);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $this->assertTrue(Hash::check($data['password'], $user->password));
    }


    /** @test 
     * User login success
     */
    public function test_login_user_success()
    {
        $user = User::factory()->create();

        Auth::shouldReceive('attempt')->with(['email' => $user->email, 'password' => 'password'])->andReturnTrue();

        $loggedInUser = $this->authService->login([
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertEquals($user->id, $loggedInUser->id);
    }

    /** @test 
     * User login failure
     */
    public function test_login_user_failure()
    {
        $user = User::factory()->create();

        Auth::shouldReceive('attempt')
            ->with(['email' => $user->email, 'password' => 'wrong-password'])
            ->andReturnFalse();

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authService->login([
            'email' => $user->email,
            'password' => 'wrong-password', 
        ]);
    }
}
