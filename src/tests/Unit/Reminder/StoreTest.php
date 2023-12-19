<?php

namespace Tests\Unit\Reminder;

use App\Http\Requests\ReminderStoreRequest;
use App\Models\User;
use App\Repositories\API\Reminders\ReminderRepository;
use App\Services\API\Reminder\StoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;

class StoreTest extends TestCase
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

    public function testReminderRequestRules(){
        $request = new ReminderStoreRequest();
        $this->assertEquals(
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'remind_at' => 'required|integer',
                'event_at' => 'required|integer',
            ],
            $request->rules(),
        );
    }

    public function testReminderRequestValidation()
    {
        $request = new ReminderStoreRequest();
        $validator = Validator::make([
            "title" => "Test",
            "description" => "Test",
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains('remind_at', $validator->errors()->keys());

    }
    public function testFailedStore()
    {

        $user = User::factory()->create();
        Auth::login($user); // Log in the user

        $request = new ReminderStoreRequest([
            "title" => "Test",
            "description" => "Test",
        ]);

        $service = app(StoreService::class, ['request' => $request])->handle($request);
        $response = json_decode($service->getContent(), true);
        $this->assertFalse($response['ok']);
    }

    public function testSuccessStore()
    {
        $user = User::factory()->create();
        Auth::login($user); // Log in the user

        $request = new ReminderStoreRequest([
            "title" => "Test",
            "description" => "Test",
            "remind_at" => 1632837600,
            "event_at" => 1632837600,
        ]);

        $service = app(StoreService::class, ['request' => $request])->handle($request);
        $response = json_decode($service->getContent(), true);
        $this->assertTrue($response['ok']);
    }
}
