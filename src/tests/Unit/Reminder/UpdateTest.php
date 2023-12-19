<?php

namespace Tests\Unit\Reminder;

use App\Models\Reminder;
use App\Models\User;
use App\Services\API\Reminder\UpdateService;
// use Illuminate\Http\Request;
use App\Http\Requests\ReminderUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;

class UpdateTest extends TestCase
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

    public function testUpdateSuccess()
    {
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new ReminderUpdateRequest([
            "id" => $reminder->id,
            "title" => "test update",
            "description" => "test update"
        ]);
        $service = app(UpdateService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertTrue($responseData['ok']);
    }

    public function testUpdateFailed(){
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new ReminderUpdateRequest([
            "id" => -90
        ]);
        $service = app(UpdateService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertFalse($responseData['ok']);
    }

    public function testUpdateFailedInvalidData()
    {
        $reminder = Reminder::factory()->create();
        $user = User::where("id", $reminder->user_id)->first();
        Auth::login($user); // Log in the user
        $request = new ReminderUpdateRequest([
            "id" => $reminder->id,
            "title" => "test update",
            "description" => "test update",
            "remind_at" => "test"
        ]);
        $service = app(UpdateService::class, ['request' => $request])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertFalse($responseData['ok']);
    }
}
