<?php

namespace Tests\Unit\Reminder;

use App\Models\User;
use App\Repositories\API\Reminders\ReminderRepository;
use Tests\TestCase;
use App\Services\API\Reminder\FetchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;

class FetchTest extends TestCase
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

    public function testFetchSuccess()
    {
        $user = User::whereNotNull('email')->first();
        Auth::login($user); // Log in the user
        $request = new Request([
            "limit" => 1
        ]);
        $service = app(FetchService::class, [
            'request' => $request
        ])->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertTrue($responseData['ok']);
    }

    public function testFetchFailed()
    {
        $user = User::factory()->create();
        Auth::login($user); // Log in the user
        $request = new Request([
            "limit" => 10
        ]);
        $service = app(FetchService::class)->handle($request);
        $responseData = json_decode($service->getContent(), true);
        $this->assertFalse($responseData['ok']);
    }

    public function testRepositoryFetch()
    {
        $repo = app(ReminderRepository::class)->fetch(1, 10);
        $this->assertNotNull($repo); // Assert that the repository is not null
    }
}
