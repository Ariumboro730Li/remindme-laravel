<?php

namespace Tests\Unit\Authentication;

use Tests\TestCase;
use App\Services\API\Authentication\LoginService;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Artisan;

class LoginTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        Artisan::call('passport:install');

         // Seed the database with users
        $this->withoutMiddleware();
        // Seed the database with users
        $this->seed(DatabaseSeeder::class);
    }

    public function setRequest($email = 'bob@mail.com', $password = 123456){
        $request = new Request([
            'email' => $email,
            'password' => $password
        ]);
        return $request;
    }

    public function testCheckCredentialsTrue()
    {
        $request = $this->setRequest();
        $loginService = app(LoginService::class, ['request' => $request]);
        $this->assertTrue($loginService->checkCredentials());
    }

    public function testCheckCredentialsFalse()
    {
        $request = $this->setRequest($password = 123458);
        $loginService = app(LoginService::class, ['request' => $request]);
        $this->assertFalse($loginService->checkCredentials());
    }

    public function testGetActiveClient(){
        $request = new Request();
        $loginService = app(LoginService::class, ['request' => $request]);
        $response = $loginService->getActiveClient();
        $this->assertNotNull($response);
    }

    public function testLoginSuccess(){
        $request = $this->setRequest();
        $loginService = app(LoginService::class, ['request' => $request]);
        $response = $loginService->login();
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['ok']);
        $this->assertArrayHasKey('user', $responseData['data']);
        $this->assertArrayHasKey('access_token', $responseData['data']);
        $this->assertArrayHasKey('refresh_token', $responseData['data']);
    }

    public function testLoginFailed(){
        $request = $this->setRequest($password = 123458);
        $loginService = app(LoginService::class, ['request' => $request]);
        $response = $loginService->login();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals([
            'ok' => false,
            'err' => 'ERR_INVALID_CREDS',
            'msg' => 'incorrect username or password',
        ], $responseData);
        $this->assertEquals(401, $response->getStatusCode());
    }

}
