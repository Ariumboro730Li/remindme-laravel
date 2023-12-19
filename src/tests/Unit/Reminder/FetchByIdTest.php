<?php

namespace Tests\Unit\Reminder;

use App\Models\Reminder;
use App\Models\User;
use App\Services\API\Reminder\FetchByIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;

class FetchByIdTest extends TestCase
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

    public function testFetchByIdSuccess()
    {
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new Request([
            "id" => $reminder->id
        ]);
        $service = app(FetchByIdService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertTrue($responseData['ok']);
    }

    public function testFetchByIdFailed(){
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new Request([
            "id" => -90
        ]);
        $service = app(FetchByIdService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertFalse($responseData['ok']);
    }
}
