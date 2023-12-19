<?php

namespace Tests\Unit\Reminder;

use App\Models\Reminder;
use App\Models\User;
use App\Services\API\Reminder\DeleteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;

class DeleteTest extends TestCase
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

    public function testDeleteSuccess()
    {
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new Request([
            "id" => $reminder->id
        ]);
        $service = app(DeleteService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertTrue($responseData['ok']);
    }

    public function testDeleteFailed(){
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new Request([
            "id" => -90
        ]);
        $service = app(DeleteService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertFalse($responseData['ok']);
    }
}
