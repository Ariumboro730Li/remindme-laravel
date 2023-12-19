<?php

namespace Tests\Unit\Authentication;

use App\Services\API\Authentication\LoginService;
use Tests\TestCase;
use App\Services\API\Authentication\RefreshSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;

class RefreshSessionTest extends TestCase
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

    public function testRefreshTokenSuccess(){
        $request = $this->setRequest();
        $loginService = app(LoginService::class, ['request' => $request]);
        $response = $loginService->login();
        $responseData = json_decode($response->getContent(), true);

        $request = new Request([
            'refresh_token' => $responseData['data']['refresh_token']
        ]);
        $refreshReponse = app(RefreshSessionService::class, ['request' => $request])->refreshSession();
        $responseDataRefresh = json_decode($refreshReponse->getContent(), true);
        $this->assertTrue($responseDataRefresh['ok']);
        $this->assertArrayHasKey('access_token', $responseDataRefresh['data']);
    }

    public function testRefreshTokenFailed(){
        $request = new Request([
            'refresh_token' =>  "token_invalido"
        ]);
        $refreshReponse = app(RefreshSessionService::class, ['request' => $request])->refreshSession();
        $responseDataRefresh = json_decode($refreshReponse->getContent(), true);
        $this->assertFalse($responseDataRefresh['ok']);
        $this->assertEquals([
            'ok' => false,
            'err' => 'ERR_INVALID_REFRESH_TOKEN',
            'msg' => 'invalid refresh token',
        ], $responseDataRefresh);
    }

}
